<?php

namespace app\modules\user\lib\enums;

use app\modules\ia\helpers\EnumHelper;
use app\modules\user\UserModule;


/**
 * Class AuthItemType
 * @package app\modules\user\enumerations
 *
 * NB : par rapport aux constantes déclarées dans Item, permet de gérer les libellés
 */
class AuthItemType extends EnumHelper
{
    const ROLE = 1;
    const PERMISSION = 2;

    /**
     * @return array
     */
    public static function getList()
    {
        return [
            static::ROLE => UserModule::t('labels', 'Role'),
            static::PERMISSION => UserModule::t('labels', 'Permission'),
        ];
    }
}