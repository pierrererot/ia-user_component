<?php

namespace app\modules\user\models\form;

use app\modules\user\models\User;
use app\modules\user\UserModule;
use yii\base\Model;


/**
 * Class LoginForm
 * @package app\models
 */
class LoginForm extends Model
{
    /** @var string */
    public $email;

    /** @var string */
    public $password;

    /** @var  User */
    public $user;

    /**
     * Surcharge les règles de validation de la classe de base
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['password', 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => UserModule::t('labels', 'Password'),
            'email' => UserModule::t('labels', 'Email'),
        ];
    }

}
