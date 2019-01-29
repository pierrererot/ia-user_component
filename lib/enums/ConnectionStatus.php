<?php

namespace app\modules\user\lib\enums;

use app\modules\ia\helpers\EnumHelper;
use app\modules\user\UserModule;


/**
 * Class ConnectionStatus
 * @package app\modules\user\enumerations
 */
class ConnectionStatus extends EnumHelper
{
    const SUCCESS = 1;
    const FAILURE = 2;

    /**
     * @return array
     */
    public static function getList()
    {
        return [
            static::SUCCESS => UserModule::t('labels', 'Success'),
            static::FAILURE => UserModule::t('labels', 'Failure'),
        ];
    }
}