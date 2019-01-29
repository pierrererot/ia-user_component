<?php

namespace app\modules\user\lib;

use app\modules\ia\lib\Flash;
use app\modules\user\controllers\MyAccountController;
use app\modules\user\lib\enums\ConnectionStatus;
use app\modules\user\lib\enums\TokenType;
use app\modules\user\models\Blacklist;
use app\modules\user\models\Connection;
use app\modules\user\models\form\MailRequestForm;
use app\modules\user\models\Token;
use app\modules\user\models\User;
use Yii;
use app\modules\user\UserModule;
use app\modules\ia\IAModule as IA;
use yii\base\ActionEvent;
use yii\base\Application;
use yii\base\Component;
use yii\base\Event;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UserEvent;


/**
 * Class UserEventHandler
 * @package app\lib
 *
 * Gestion des événements associés aux utilisateurs
 */
class UserEventHandler extends Component
{
    /**
     * Stockage des utilisateurs récupérés dans onBeforeUpdateUser()
     * @var array id => User
     */
    protected $userDirtyAttributes = [];

    /** @var  UserEventHandler */
    protected static $singleton;

    /**
     * Singleton à appeler au lancement de l'application ou du module pour qu'on puisse s'abonner aux événements qui nous intéressent
     * @see \app\components\Application::init()
     *
     * @return UserEventHandler
     * @throws NotSupportedException
     */
    public static function singleton()
    {
        if (!static::$singleton) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }

    /**
     * UserEventHandler constructor.
     *
     * @param array $config
     * @throws NotSupportedException
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        if (!static::$singleton) {
            static::$singleton = $this;
            $this->subscribeEvents();
        } else {
            throw new NotSupportedException(IA::t('messages', "Only one instance allowed"));
        }
    }

    /**
     * Abonnement aux événements à traiter
     */
    protected function subscribeEvents()
    {
        Event::on(Application::class, Application::EVENT_BEFORE_REQUEST, [$this, 'onBeforeRequest']);

        Event::on(UserEvents::class, UserEvents::AFTER_REGISTER, [$this, 'onAfterRegister']);
        Event::on(UserEvents::class, UserEvents::AFTER_CREATE_USER, [$this, 'onAfterCreateUser']);
        Event::on(UserEvents::class, UserEvents::BEFORE_UPDATE_USER, [$this, 'onBeforeUpdateUser']);
        Event::on(UserEvents::class, UserEvents::AFTER_UPDATE_USER, [$this, 'onAfterUpdateUser']);
        Event::on(UserEvents::class, UserEvents::BEFORE_UPDATE_OWN_USER, [$this, 'onBeforeUpdateOwnUser']);
        Event::on(UserEvents::class, UserEvents::AFTER_UPDATE_OWN_USER, [$this, 'onAfterUpdateOwnUser']);

        Event::on(UserEvents::class, UserEvents::REQUEST_NEW_PASSWORD, [$this, 'onRequestNewPassword']);
        Event::on(UserEvents::class, UserEvents::REQUEST_NEW_CONFIRMATION_LINK, [$this, 'onRequestNewConfirmationLink']);

        //Suivi des connexions
        if (!Yii::$app->request->getIsConsoleRequest()) {
            $webUserClass = get_class(Yii::$app->user);
            Event::on($webUserClass, $webUserClass::EVENT_AFTER_LOGIN, [$this, 'onAfterLogin']);
            Event::on(UserEvents::class, UserEvents::PASSWORD_FAILURE, [$this, 'onPasswordFailure']);
        }
    }

    /**
     * @return UserMail
     * @throws \yii\base\InvalidConfigException
     */
    protected function getMailer()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject('user/UserMail');
    }

    ///////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////

    /**
     * Actions à effectuer après l'inscription d'un utilisateur
     *  - envoi d'un mail avec le lien pour confirmer son mot de passe
     *  - affichage d'un message flash
     *
     * @param ActionEvent $event
     * @throws \yii\base\InvalidConfigException
     */
    public function onAfterRegister(ActionEvent $event)
    {
        /** @var User $user */
        $user = $event->sender;

        $to = $user->email;
        $token = Token::generateTokenForUser($user->id, TokenType::PASSWORD);
        $url = Url::to(['/userModule/registration/confirm-password', 'code' => $token->code, 'id' => $user->id], true);

        $this->getMailer()->passwordConfirmationLink($to, $user, $token, $url);
        Flash::success(UserModule::t('messages',
            "You are now registered on our site. We are sending you a mail with the instructions to activate your account"));
    }

    /**
     * Actions à effectuer après la demande d'un nouveau lien de confirmation
     *  - renvoi d'un mail de confirmation
     *  - affichage d'un message flash
     *
     * @param ActionEvent $event
     * @throws \yii\base\InvalidConfigException
     */
    public function onRequestNewConfirmationLink(ActionEvent $event)
    {
        /** @var MailRequestForm $model */
        $model = $event->sender;

        $to = $model->email;
        $user = Yii::createObject('user/User')->find()->byEmail($to)->one();
        if ($user) {
            $token = Token::generateTokenForUser($user->id, TokenType::REGISTRATION);
            $url = Url::to(['/userModule/registration/confirm-password', 'code' => $token->code, 'id' => $user->id], true);

            $this->getMailer()->passwordConfirmationLink($to, $user, $token, $url);
            Flash::success(UserModule::t('messages',
                "We are sending you a new mail with the instructions to activate your account"));
        }
    }

    /**
     * Actions à effectuer après la demande d'un nouveau lien pour un mot de passe
     *  - envoi d'un mail avec le lien pour confirmer son mot de passe
     *  - affichage d'un message flash
     *
     * @param ActionEvent $event
     * @throws \yii\base\InvalidConfigException
     */
    public function onRequestNewPassword(ActionEvent $event)
    {
        /** @var MailRequestForm $model */
        $model = $event->sender;

        $to = $model->email;
        $user = Yii::createObject('user/User')->find()->byEmail($to)->one();
        if ($user) {
            $token = Token::generateTokenForUser($user->id, TokenType::PASSWORD);
            $url = Url::to(['/userModule/registration/reset-password', 'code' => $token->code, 'type' => TokenType::PASSWORD, 'id' => $user->id], true);

            $this->getMailer()->passwordResetLink($to, $user, $url);
            Flash::success(UserModule::t('messages', "We are sending you a mail with the instructions to reset your password"));
        }
    }

    /**
     * Actions à effectuer après la connexion d'un utilisateur
     *
     * @param UserEvent $event
     * @throws \yii\base\InvalidConfigException
     */
    public function onAfterLogin(UserEvent $event)
    {
        /** @var User $user */
        $user = $event->identity;
        $user->logged_in_from = Yii::$app->request->getUserIP();
        $user->logged_in_at = date('Y-m-d H:i:s');
        ++$user->password_usage;
        if (!$user->save()) {
            Yii::error('!$user->identity->save()');
        }

        // Vérifications sur la durée de vie du mot de passe
        /** @var UserModule $userModule */
        $userModule = Yii::$app->getModule('userModule');
        if ($userModule->passwordUsage['check']) {
            $reason = '';
            if ($user->password_usage >= $userModule->passwordUsage['nbConnectionsMax']) {
                $reason = UserModule::t('messages', "Your password has been used more than {0} times", [$userModule->passwordUsage['nbConnectionsMax']]);
            } elseif ($user->passwordAgeInDays() >= $userModule->passwordUsage['nbDaysMax']) {
                $reason = UserModule::t('messages', "Your password is older than {0} days", [$userModule->passwordUsage['nbDaysMax']]);
            }

            // Si $reason est renseigné, le pwd doit être désactivé et il y a un mail à envoyer
            if ($reason) {
                $user->blockUser();
                $token = Token::generateTokenForUser($user->id, TokenType::UNBLOCK);
                $url = Url::to(['/userModule/registration/reset-password', 'code' => $token->code, 'type' => TokenType::UNBLOCK, 'id' => $user->id], true);

                $this->getMailer()->passwordExpired($user->email, $user, $url, $reason);
                Flash::warning(UserModule::t('messages', "Your password will expired after this session") . '. '
                    . UserModule::t('messages', "We are sending you a mail with the instructions to reset your password"));
            }
        }

        (new Connection())->loginSuccess($user->email, Yii::$app->request->getRemoteIP());
    }

    /**
     * Actions à effectuer après la création d'un compte utilisateur par un admin
     *  - envoi d'un mail avec le lien pour confirmer son mot de passe
     *
     * @param ActionEvent $event
     * @throws \yii\base\InvalidConfigException
     */
    public function onAfterCreateUser(ActionEvent $event)
    {
        /** @var User $user */
        $user = $event->sender;

        $to = $user->email;
        $token = Token::generateTokenForUser($user->id, TokenType::REGISTRATION);
        $url = Url::to(['/userModule/registration/confirm-password', 'code' => $token->code, 'id' => $user->id], true);
        $this->getMailer()->userAccountCreation($to, $user, $url);
    }

    /**
     * Actions à effectuer avant la mise à jour en backend d'un compte utilisateur
     *  - on mémorise l'utilisateur qui va être modifié
     *
     * @param ActionEvent $event
     */
    public function onBeforeUpdateUser(ActionEvent $event)
    {
        /** @var User $user */
        $user = $event->sender;
        $this->userDirtyAttributes[$user->id] = $user->getDirtyAttributes();
    }

    /**
     * Actions à effectuer après la mise à jour en backend d'un compte utilisateur
     *  - selon la configuration du module, reset possible du mot de passe
     *
     * @param ActionEvent $event
     * @throws \yii\base\InvalidConfigException
     */
    public function onAfterUpdateUser(ActionEvent $event)
    {
        /** @var User $user */
        $user = $event->sender;

        $dirtyAttributes = ArrayHelper::getValue($this->userDirtyAttributes, $user->id, []);
        if (array_key_exists('email', $dirtyAttributes) && UserModule::getInstance()->resetPasswordAfterEmailChange) {
            // Si le mail a changé, il faut ré-initialiser le mot de passe
            $to = $user->email;
            $token = Token::generateTokenForUser($user->id, TokenType::PASSWORD);
            $url = Url::to(['/userModule/registration/reset-password', 'code' => $token->code, 'type' => TokenType::PASSWORD, 'id' => $user->id], true);

            $user->password_hash = '';
            $user->save();
            $this->getMailer()->accountUpdatedByAdmin($to, $user, $token, $url);
        }
    }

    /**
     * Actions à effectuer avant la mise à jour de son propre compte utilisateur
     *  - on mémorise l'utilisateur qui va être modifié
     *
     * @param ActionEvent $event
     */
    public function onBeforeUpdateOwnUser(ActionEvent $event)
    {
        /** @var User $user */
        $user = $event->sender;
        $this->userDirtyAttributes[$user->id] = $user->getDirtyAttributes();
    }

    /**
     * Actions à effectuer après la mise à jour de son propre compte utilisateur
     *  - selon la configuration du module, reset possible du mot de passe
     *
     * @param ActionEvent $event
     * @throws \yii\base\InvalidConfigException
     */
    public function onAfterUpdateOwnUser(ActionEvent $event)
    {
        /** @var User $user */
        $user = $event->sender;

        $dirtyAttributes = ArrayHelper::getValue($this->userDirtyAttributes, $user->id, []);
        if (array_key_exists('email', $dirtyAttributes) && UserModule::getInstance()->resetPasswordAfterEmailChange) {
            // Si le mail a changé, il faut ré-initialiser le mot de passe
            $to = $user->email;
            $token = Token::generateTokenForUser($user->id, TokenType::PASSWORD);
            $url = Url::to(['/userModule/registration/reset-password', 'code' => $token->code, 'type' => TokenType::PASSWORD, 'id' => $user->id], true);

            $user->password_hash = '';
            $user->save();
            /** @var MyAccountController */
            $controller = $event->action->controller;
            /** @noinspection PhpUndefinedFieldInspection */
            $controller->afterEventRedirectTo = ['/user/security/logout'];
            Flash::success(UserModule::t('messages', "You have to reset your password. Please check your mails for further instructions"));
            $this->getMailer()->accountUpdatedByOwner($to, $user, $token, $url);
        }
    }

    /**
     * @param ActionEvent $event
     * @throws \yii\base\InvalidConfigException
     */
    public function onPasswordFailure(ActionEvent $event)
    {
        /** @var User $user */
        $user = $event->sender;
        $ip = Yii::$app->request->getRemoteIP();
        (new Connection())->loginFailure($user->email, $ip);

        // Si le nombre d'échecs dépasse le seuil critique, on bloque le compte
        /** @var UserModule $userModule */
        $userModule = Yii::$app->getModule(('userModule'));
        if ($userModule->passwordFailures['check']) {
            $nbSeconds = $userModule->passwordFailures['referenceDelayInMinutes'] * 60;
            $nbFailures = Connection::find()->byUsername($user->email)->byStatus(ConnectionStatus::FAILURE)->sinceNbSeconds($nbSeconds)->count();

            if ($nbFailures >= $userModule->passwordFailures['nbFailuresAllowed']) {
                $user->blockUser();
                $msg = UserModule::t('messages', "Too many incorrect login attempts");

                if ($userModule->passwordFailures['sendMailForPasswordReset']) {
                    $token = Token::generateTokenForUser($user->id, TokenType::UNBLOCK);
                    $url = Url::to(['/userModule/registration/reset-password', 'code' => $token->code, 'type' => TokenType::UNBLOCK, 'id' => $user->id], true);

                    $this->getMailer()->passwordExpired($user->email, $user, $url, UserModule::t('messages', "Too many incorrect login attempts"));
                    $msg .= '. ' . UserModule::t('messages', "We are sending you a mail with the instructions to reset your password");
                }

                Flash::warning($msg);
            }
        }

        // Si une IP fait trop d'erreurs sur les mots de passe, on la met en liste noire
        if ($userModule->ipBlacklisting['check']) {
            $nbSeconds = $userModule->ipBlacklisting['referenceDelayInMinutes'] * 60;
            $nbFailures = Connection::find()->byIP($ip)->byStatus(ConnectionStatus::FAILURE)->sinceNbSeconds($nbSeconds)->count();

            if ($nbFailures >= $userModule->ipBlacklisting['nbFailuresAllowed']) {
                $user->blockUser();
                $blacklist = new Blacklist();
                $blacklist->ip = $ip;
                $blacklist->save();

                if ($userModule->ipBlacklisting['notifyAdmin']) {
                    $this->getMailer()->ipBlacklisted($ip, $userModule->ipBlacklisting['durationInMinutes']);
                }

                Flash::error(UserModule::t('messages', "Too many incorrect login attempts"));
            }
        }
    }

    /**
     * @param Event $event
     * @throws \yii\base\InvalidConfigException
     */
    public function onBeforeRequest(Event $event)
    {
        if (Yii::$app->request->getIsConsoleRequest()) {
            return;
        }

        /** @var UserModule $userModule */
        $userModule = Yii::$app->getModule('userModule');

        // On vérifie s'il y a un blacklistage, sauf si on est sur la page de redirection après blacklistage, évidemment
        if ($userModule->ipBlacklisting['check']) {
            if (Yii::$app->request->getPathInfo() != $userModule->ipBlacklisting['redirectTo']) {
                $remoteIP = Yii::$app->request->getRemoteIP();

                // S'il y a un blacklistage sur cette IP, on vérifie s'il a écu ou non
                $blacklisted = Blacklist::find()->byIP($remoteIP)->enabled()->one();
                if ($blacklisted) {
                    $blTime = strtotime($blacklisted->updated_at);
                    $refTime = time() - $userModule->ipBlacklisting['durationInMinutes'] * 60;
                    if ($blTime <= $refTime) {
                        // La période de blacklistage a échu : on l'annule
                        $blacklisted->enabled = false;
                        if (!$blacklisted->save()) {
                            Yii::error("!\$blacklisted->save() pour l'IP $remoteIP");
                        }
                    } else {
                        // Le blacklistage est en cours sur cette IP, on redirige
                        Yii::$app->response->redirect(Url::to($userModule->ipBlacklisting['redirectTo']));
                    }
                }
            }
        }
    }
}