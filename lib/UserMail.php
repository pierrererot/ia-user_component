<?php

namespace app\modules\user\lib;

use app\modules\user\models\User;
use Yii;
use yii\base\Component;
use app\modules\user\UserModule;


/**
 * Class UserMail
 * @package app\modules\user\lib
 *
 * Envoi des mails liés au module "utilisateur" : inscription, création de compte etc...
 */
class UserMail extends Component
{
    //
    // Configuration
    //

    /** @var bool */
    public $logErrors = true;

    /** @var string */
    public $defaultMailViews = '@app/modules/user/mail';

    //////////////////
    //////////////////
    //////////////////
    //////////////////

    /**
     * Renvoie le chemin d'accès au fichier de template, en cherchant d'abord la version surchargée si elle existe. Si elle n'existe pas,
     * renvoie le chemin d'accès au template par défaut (qui doit en ce cas avoir été fourni dans le module)
     *
     * @param string $templateName
     * @return string
     */
    protected function getTemplatePath($templateName)
    {
        /** @var UserModule $module */
        $module = Yii::$app->getModule('userModule');

        // Si le fichier existe dans le path des templates renseigné dans la configuration, on l'utilise.
        // Sinon, on récupère le fichier dans les templates du module User
        $filename = Yii::getAlias($module->mailer['viewPath'] . "/$templateName.php");
        if (is_file($filename)) {
            $out = $module->mailer['viewPath'] . "/$templateName";
        } else {
            $out = $this->defaultMailViews . "/$templateName";
        }

        return $out;
    }

    //
    //
    //

    /**
     * @param string $to
     * @param User $user
     * @param string $token
     * @param string $url
     * @return bool
     */
    public function passwordConfirmationLink($to, $user, $token, $url)
    {
        $ok = Yii::$app->mailer->compose($this->getTemplatePath('registration'), compact('user', 'token', 'url'))
            ->setFrom(Yii::$app->params['mails']['adminEmail'])
            ->setTo($to)
            ->setSubject(UserModule::t('messages', "Your confirmation link"))
            ->send();

        if (!$ok) {
            Yii::error([$to, $user, $token, $url]);
        }

        return $ok;
    }

    /**
     * Courrier après demande de ré-initialisation du mot de passe
     *
     * @param string $to
     * @param User $user
     * @param string $url
     * @return bool
     */
    public function passwordResetLink($to, $user, $url)
    {
        $ok = Yii::$app->mailer->compose($this->getTemplatePath('resetPassword'), compact('user', 'url'))
            ->setFrom(Yii::$app->params['mails']['adminEmail'])
            ->setTo($to)
            ->setSubject(UserModule::t('messages', "Your reset password link"))
            ->send();

        if (!$ok) {
            Yii::error([$to, $user, $url]);
        }

        return $ok;
    }

    /**
     * Courrier après expiration du mot de passe
     *
     * @param string $to
     * @param User $user
     * @param string $url
     * @param string $reason
     * @return bool
     */
    public function passwordExpired($to, $user, $url, $reason)
    {
        $ok = Yii::$app->mailer->compose($this->getTemplatePath('passwordExpired'), compact('user', 'url', 'reason'))
            ->setFrom(Yii::$app->params['mails']['adminEmail'])
            ->setTo($to)
            ->setSubject(Yii::t('messages', "Your password has expired"))
            ->send();

        if (!$ok) {
            Yii::error([$to, $user, $url]);
        }

        return $ok;
    }

    /**
     * @param string $to
     * @param User $user
     * @param string $url
     * @return bool
     */
    public function userAccountCreation($to, $user, $url)
    {
        $ok = Yii::$app->mailer->compose($this->getTemplatePath('accountCreation'), compact('url'))
            ->setFrom(Yii::$app->params['mails']['fromAdminToUser'])
            ->setTo($to)
            ->setSubject(UserModule::t('messages', "Creation of your user account on {0}", [Yii::$app->params['mails']['applicationShortName']]))
            ->send();

        if (!$ok) {
            Yii::error([$to, $user, $url]);
        }

        return $ok;
    }

    /**
     * @param string $to
     * @param User $user
     * @param string $token
     * @param string $url
     * @return bool
     */
    public function accountUpdatedByAdmin($to, $user, $token, $url)
    {
        $ok = Yii::$app->mailer->compose($this->getTemplatePath('accountUpdatedByAdmin'), compact('url'))
            ->setFrom(Yii::$app->params['mails']['fromAdminToUser'])
            ->setTo($to)
            ->setSubject(UserModule::t('messages', "Your user account on {0} has been updated", [Yii::$app->params['mails']['applicationShortName']]))
            ->send();

        if (!$ok) {
            Yii::error([$to, $user, $token, $url]);
        }

        return $ok;
    }

    /**
     * @param string $to
     * @param User $user
     * @param string $token
     * @param string $url
     * @return bool
     */
    public function accountUpdatedByOwner($to, $user, $token, $url)
    {
        $ok = Yii::$app->mailer->compose($this->getTemplatePath('accountUpdatedByOwner'), compact('url'))
            ->setFrom(Yii::$app->params['mails']['fromAdminToUser'])
            ->setTo($to)
            ->setSubject(UserModule::t('messages', "You have updated your user account on {0}", [Yii::$app->params['mails']['applicationShortName']]))
            ->send();

        if (!$ok) {
            Yii::error([$to, $user, $token, $url]);
        }

        return $ok;
    }

    /**
     * @param string $ip
     * @param int $durationInMinutes
     * @return bool
     */
    public function ipBlacklisted($ip, $durationInMinutes)
    {
        $ok = Yii::$app->mailer->compose($this->getTemplatePath('ipBlacklisted'), compact('ip', 'durationInMinutes'))
            ->setFrom(Yii::$app->params['mails']['fromAdminToUser'])
            ->setTo(Yii::$app->params['mails']['adminEmail'])
            ->setSubject(UserModule::t('messages', "An IP has been blacklisted on {0}", [Yii::$app->params['mails']['applicationShortName']]))
            ->send();

        if (!$ok) {
            Yii::error([$ip, $durationInMinutes]);
        }

        return $ok;
    }

}