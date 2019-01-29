<?php

namespace UserStatus;

use EnumHelper\EnumHelper as EnumHelper;
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
            static::ENABLED => UserModule::t('labels', 'Enabled'),
            static::DISABLED => UserModule::t('labels', 'Disabled'),
            static::UNSUBSCRIBED => UserModule::t('labels', 'Unsubscribed'),
            static::REFUSED_INVITATION => UserModule::t('labels', 'Refused Invitation'),
        ];
    }
}