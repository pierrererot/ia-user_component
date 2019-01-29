<?php

namespace app\modules\user\lib\enums;

use app\modules\ia\helpers\EnumHelper;
use app\modules\user\UserModule;


/**
 * Class TokenType
 * @package app\modules\user\enumerations
 */
class TokenType extends EnumHelper
{
    const REGISTRATION = 1; // validation d'un compte préalablement créé
    const ACCESS = 2;// jeton d'accès à un service
    const PASSWORD = 3; // gestion du mdp (mise à jour, reset)
    const UNBLOCK = 4; // jeton de débloquage d'un compte
    const CONNECTION_REQUEST = 5; // demande de mise en relation entre users déjà connus
    const ACCOUNT_CREATION = 6; // invitation à créer son compte
    //
    const OTHER_REQUEST = 99;

    /**
     * @return array
     */
    public static function getList()
    {
        return [
            static::REGISTRATION => UserModule::t('labels', 'Registration'),
            static::ACCESS => UserModule::t('labels', 'Access'),
            static::PASSWORD => UserModule::t('labels', 'Password'),
            static::UNBLOCK => UserModule::t('labels', 'Unblock'),
            static::CONNECTION_REQUEST => UserModule::t('labels', 'Connection Request'),
            static::ACCOUNT_CREATION => UserModule::t('labels', 'Account Creation'),
            static::OTHER_REQUEST => UserModule::t('labels', 'Other Request'),
        ];
    }
}