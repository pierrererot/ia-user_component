<?php

namespace app\modules\user\models\form;

use app\modules\user\UserModule;
use yii\base\Model;


/**
 * Class MailRequestForm
 * @package app\models
 */
class MailRequestForm extends Model
{
    /** @var string */
    public $email;

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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => UserModule::t('labels', 'Email'),
        ];
    }

}
