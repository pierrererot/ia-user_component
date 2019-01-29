<?php

namespace app\modules\user\lib\enums;

use app\modules\ia\helpers\EnumHelper;
use app\modules\ia\IAModule;
use app\modules\user\UserModule;


/**
 * Class UserStatus
 * @package app\modules\user\enumerations
 */
class UserStatus extends EnumHelper
{
    const PENDING_REGISTRATION = 1;
    const ENABLED = 10;
    const DISABLED = 20;
    const UNSUBSCRIBED = 30; // l'utilisateur s'est désinscrit du site
    const REFUSED_INVITATION = 31; // l'utilisateur a refusé une invitation à s'inscrire

    /**
     * @return array
     */
    public static function getList()
    {
        return [
            static::PENDING_REGISTRATION => UserModule::t('labels', "Pending Registration"),
            static::ENABLED => IAModule::t('labels', 'Enabled'),
            static::DISABLED => IAModule::t('labels', 'Disabled'),
            static::UNSUBSCRIBED => IAModule::t('labels', 'Unsubscribed'),
            static::REFUSED_INVITATION => IAModule::t('labels', 'Refused Invitation'),
        ];
    }
}