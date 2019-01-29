<?php

namespace app\modules\user\controllers;

use app\modules\ia\lib\Flash;
use app\modules\user\lib\UserEvents;
use Yii;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\user\UserModule;


/**
 * Class AuthenticationController
 * @package app\modules\user\controllers
 */
class SecurityController extends Controller
{
    const PASSWORD_SUCCESS = 'passwordSuccess';
    const PASSWORD_FAILURE = 'passwordFailure';

    /** @var string */
    public $defaultAction = 'login';

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            [
                'class' => AccessControl::class,
                'rules' => [
                    // actions réservées aux utilisateurs non authentifiés
                    [
                        'actions' => ['login'],
                        'allow' => true, 'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true, 'roles' => ['@'],
                    ]
                ]
            ],
        ];
    }

    /**
     * Affiche & traite le formulaire de connexion à un compte utilisateur
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        /** @var \app\modules\user\models\form\LoginForm $model */
        $model = Yii::createObject('user/LoginForm');

        if (Yii::$app->request->isPost) {
            // Traitement du formulaire
            if ($model->load(Yii::$app->request->post())) {
                /** @var UserModule $module */
                $module = $this->module;
                if (!$model->validate()) {
                    Flash::warning(UserModule::t('messages', "Invalid input"));
                    return $this->redirect($module->redirectAfterRegister);
                }

                /** @var \app\modules\user\models\User $user */
                $user = Yii::createObject('user/User')->find()->byEmail($model->email)->one();
                if (!$user) {
                    Flash::error(UserModule::t('messages', "Unkown user"));
                    return $this->redirect($module->redirectAfterRegister);
                }

                // On vérifie que l'utilisateur a bien un compte valide
                if (!$user->confirmed_at || $user->blocked_at) {
                    Flash::error(UserModule::t('messages', "Invalid user"));
                    return $this->redirect($module->redirectAfterRegister);
                }

                // Vérification du mot de passe
                if (!Yii::$app->security->validatePassword($model->password, $user->password_hash)) {
                    Event::trigger(UserEvents::class, UserEvents::PASSWORD_FAILURE, new ActionEvent($this->action, ['sender' => $user]));
                    Flash::error(UserModule::t('messages', "Invalid password"));
                    return $this->redirect($module->redirectAfterLogin);
                }

                // Tout va bien, on peut se connecter
                if (!Yii::$app->user->login($user, $module->rememberFor)) {
                    Flash::error(UserModule::t('messages', "Login rejected"));
                    return $this->redirect($module->redirectAfterRegister);
                }

                return $this->redirect($module->redirectAfterLogin);
            }
        }

        // Affichage initial ou ré-affichage après erreur
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Déconnexion de l'utilisateur
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        // La session risque d'être détruite dans le logout. On sauvegarde les flashs en attente
        Flash::savePendingFlashes();

        if (!Yii::$app->user->logout()) {
            Flash::error(UserModule::t('messages', "Error on logout"));
        }

        Flash::reloadPendingFlashes();
        Flash::success(UserModule::t('messages', "Logout successful"));
        /** @noinspection PhpUndefinedFieldInspection */
        $redirectTo = $this->module->redirectAfterLogout ? Url::to($this->module->redirectAfterLogout) : Yii::$app->request->getReferrer();
        return $this->redirect($redirectTo);
    }

}