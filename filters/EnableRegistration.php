<?php

namespace app\modules\user\filters;

use yii\base\ActionFilter;
use yii\web\MethodNotAllowedHttpException;

use app\modules\user\UserModule;


/**
 * Class EnableRegistration
 * @package app\modules\user\filters
 */
class EnableRegistration extends ActionFilter
{
    /** @var boolean */
    public $enable = true;

    /**
     * Si le module gère un paramètre
     *
     * @param \yii\base\Action $action
     * @return bool
     * @throws MethodNotAllowedHttpException
     */
    public function beforeAction($action)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        if (!$this->enable) {
            throw new MethodNotAllowedHttpException(UserModule::t('messages', "Registration is not allowed on this site"));
        }

        return true;
    }
}