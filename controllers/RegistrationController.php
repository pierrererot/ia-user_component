<?php

namespace app\modules\user\controllers;

use app\modules\ia\IAModule as IA;
use app\modules\ia\IAModule;
use app\modules\ia\lib\DisplayableException;
use app\modules\user\lib\enums\TokenType;
use app\modules\user\lib\UserEvents;
use app\modules\user\models\form\MailRequestForm;
use app\modules\user\models\form\PasswordForm;
use app\modules\user\models\Token;
use app\modules\user\models\User;
use app\modules\user\UserModule;
use app\modules\ia\lib\Flash;
use app\modules\user\filters\EnableRegistration;
use app\modules\user\models\form\RegistrationForm;
use Exception;
use Yii;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


/**
 * RegistrationController is responsible for all registration process, which includes registration of a new account,
 * resending confirmation tokens, email confirmation and registration via social networks.
 *
 * @property \app\modules\user\UserModule $module
 */
class RegistrationController extends Controller
{
    /** @var string */
    public $defaultAction = 'register';

    /** @inheritdoc */
    public function behaviors()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $enableRegistration = $this->module->enableRegistration;
        return [
            [
                'class' => EnableRegistration::class,
                'enable' => $enableRegistration,
            ],
            [
                'class' => AccessControl::class,
                'rules' => [
                    [ // actions accessibles à tout le monde
                        'allow' => true, 'actions' => ['request-new-password'],
                    ],
                    [ // actions réservées aux utilisateurs non authentifiés
                        'allow' => true, 'roles' => ['?'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Gestion du formulaire d'inscription en frontend
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionRegister()
    {
        /** @var RegistrationForm $model */
        $model = Yii::createObject('user/RegistrationForm');

        if (Yii::$app->request->isPost) {
            // Traitement du formulaire
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->loadAll(Yii::$app->request->post())) {
                    throw new DisplayableException(IAModule::t('messages', "Load error"));
                }

                if (!$model->validateAll()) {
                    throw new DisplayableException(IAModule::t('messages', "Validation error"));
                }

                Event::trigger(UserEvents::class, UserEvents::BEFORE_REGISTER, new ActionEvent($this->action, ['sender' => $model->user]));
                if ($model->saveAll(false)) {
                    // Action par défaut : envoi d'un mail avec le lien de création du mot de passe @see \app\modules\user\lib\UserEventHandler
                    $transaction->commit();
                    $model->user->refresh();
                    Event::trigger(UserEvents::class, UserEvents::AFTER_REGISTER, new ActionEvent($this->action, ['sender' => $model->user]));
                    Flash::success(UserModule::t('messages', "Your registration request has been received"));
                    return $this->redirect($this->module->redirectAfterRegister);
                }
            } catch (DisplayableException $x) {
                Flash::error($x->getMessage());
            } catch (Exception $x) {
                Yii::error($x->getmessage());
                Flash::error(IAModule::t('messages', "Server error"));
            }

            $transaction->rollBack();
        }

        // Affichage initial ou ré-affichage après arreur
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Gestion du formulaire de confirmation du mot de passe
     *
     * @param int $id
     * @param string $code
     * @return string
     * @throws Yii\web\HttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function actionConfirmPassword($id, $code)
    {
        if (!($user = Yii::createObject('user/User')->findOne(['id' => $id]))) {
            throw new NotFoundHttpException();
        }

        $token = Token::findTokenForUser(TokenType::PASSWORD, $user->id, $code, $this->module->rememberConfirmationTokenFor);
        if (!$token) {
            Flash::error(UserModule::t('messages', 'The link is invalid or expired. Please try requesting a new one'));
            return $this->redirect('/');
        }

        /** @var PasswordForm $model */
        $model = Yii::createObject('user/PasswordForm');
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                Event::trigger(UserEvents::class, UserEvents::BEFORE_CONFIRM_PASSWORD, new ActionEvent($this->action));

                // Mise à jour du mot de passe
                if ($this->processPasswordForm($model, $user, $token)) {
                    $token->delete();

                    Event::trigger(UserEvents::class, UserEvents::AFTER_CONFIRM_PASSWORD, new ActionEvent($this->action, ['sender' => $user]));
                    Flash::success(UserModule::t('messages', "Your password has been successfully created. Your account is now validated"));

                    // Mot de passe mis à jour. On connecte l'utilisateur à con compte avant de rediriger
                    Yii::$app->user->login($user, $this->module->rememberFor);
                    return $this->redirect($this->module->redirectAfterConfirmPassword);
                }
            }

            Flash::error(IA::t('messages', "There are errors in your form"));
        }

        return $this->render('password', [
            'model' => $model,
        ]);
    }

    /**
     * Gestion du formulaire de demande de ré-initialisation du mot de passe
     * Sauvegarder ce formulaire déclenche l'envoi d'un mail contenant un lien vers la page de ré-initialisation du mot de passe.
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRequestNewPassword()
    {
        /** @var MailRequestForm $model */
        $model = Yii::createObject('user/MailRequestForm');

        if (Yii::$app->request->isPost || !Yii::$app->user->isGuest) {
            // Traitement du formulaire
            if ($model->load(Yii::$app->request->post()) || !Yii::$app->user->isGuest) {
                if (!Yii::$app->user->isGuest) {
                    /** @noinspection PhpUndefinedFieldInspection */
                    $model->email = Yii::$app->user->identity->email;
                    if ($this->module->logoutAfterUpdateAccount) {
                        Yii::$app->user->logout();
                    }
                }
                // Envoi d'un mail avec le lien (action par défaut)
                // @see \app\modules\user\lib\UserEventHandler
                Event::trigger(UserEvents::class, UserEvents::REQUEST_NEW_PASSWORD, new ActionEvent($this->action, ['sender' => $model]));
                return $this->redirect(Yii::$app->request->getReferrer());
            }

            Flash::error(IA::t('messages', "There are errors in your form"));
        }

        return $this->render('requestNewPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Gestion du formulaire de ré-initialisation du mot de passe
     *
     * @param int $id
     * @param string $code
     * @param int $type
     * @return string
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function actionResetPassword($id, $code, $type)
    {
        /** @var User $user */
        if (!($user = Yii::createObject('user/User')->findOne(['id' => $id]))) {
            throw new NotFoundHttpException();
        }

        $token = Token::findTokenForUser($type, $user->id, $code, $this->module->rememberPasswordTokenFor);
        if (!$token) {
            Flash::error(UserModule::t('messages', 'The link is invalid or expired. Please try requesting a new one'));
            return $this->redirect('/');
        }

        /** @var PasswordForm $model */
        $model = Yii::createObject('user/PasswordForm');
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                Event::trigger(UserEvents::class, UserEvents::BEFORE_RESET_PASSWORD, new ActionEvent($this->action));

                // Mise à jour du mot de passe
                if ($this->processPasswordForm($model, $user, $token)) {
                    // Si le token a été obtenu après expiration du mot de passe, on débloque le compte de l'utilisateur
                    if ($type == TokenType::UNBLOCK) {
                        $user->unblockUser();
                    }

                    $token->delete();

                    Event::trigger(UserEvents::class, UserEvents::AFTER_RESET_PASSWORD, new ActionEvent($this->action, ['sender' => $user]));
                    Flash::success(UserModule::t('messages', "Your password has been successfully updated"));

                    // Mot de passe mis à jour ? On connecte l'utilisateur à son compte avant de rediriger
                    Yii::$app->user->login($user, $this->module->rememberFor);
                    return $this->redirect([$this->module->redirectAfterResetPassword]);
                }
            }

            Flash::error(IA::t('messages', "There are errors in your form"));
        }

        return $this->render('password', [
            'model' => $model,
        ]);
    }

    /**
     * Traitement du formulaire de mise à jour du mot de passe : mise à jour, suppression du jeton
     * @internal Le formulaire doit avoir été validé en amont
     *
     * @param PasswordForm $model
     * @param User $user
     * @param Token $token
     * @return bool
     * @throws \Throwable
     */
    protected function processPasswordForm(PasswordForm $model, User $user, Token $token)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->resetPassword($user)) {
                throw new Exception('!$model->resetPassword()');
            }

            if ($token->delete() === false) {
                throw new Exception('$token->delete() === false');
            }

            $transaction->commit();
            return true;
        } catch (Exception $x) {
            Yii::error($x);
            $transaction->rollBack();
        }

        return false;
    }

    /**
     * Gestion du formulaire permettant de demander l'envoi d'un nouveau lien de confirmation
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRequestNewConfirmationLink()
    {
        /** @var MailRequestForm $model */
        $model = Yii::createObject('user/MailRequestForm');

        if (Yii::$app->request->isPost) {
            // Traitement du formulaire
            if ($model->load(Yii::$app->request->post())) {
                /** @var User $user */
                $user = Yii::createObject('user/User')->find()->byEmail($model->email)->one();
                if (!$user) {
                    // RAF
                } elseif ($user->confirmed_at) {
                    // Compte déjà confirmé, inutile de renvoyer un lien
                    Flash::warning(UserModule::t('messages', "This account is already confirmed"));
                } else {
                    // Envoi du lien
                    Event::trigger(UserEvents::class, UserEvents::REQUEST_NEW_CONFIRMATION_LINK, new ActionEvent($this->action, ['sender' => $model]));
                }

                return $this->redirect(Yii::$app->request->getReferrer());
            }

            Flash::error(IA::t('messages', "There are errors in your form"));
        }

        // Affichage initial ou ré-affichage après erreur
        return $this->render('requestNewConfirmationLink', [
            'model' => $model,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

}
