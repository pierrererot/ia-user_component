<?php

namespace app\modules\user\models\form;

use app\modules\user\models\Password;
use app\modules\user\models\User;
use app\modules\user\UserModule;
use app\modules\user\validators\PasswordStructureValidator;
use Exception;
use Yii;
use yii\base\Model;

/**
 * Class PasswordForm
 * @package app\modules\user\models
 */
class PasswordForm extends Model
{
    /** @var string $password */
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->controller->module->passwordSecurity['min_length'], 'max' => Yii::$app->controller->module->passwordSecurity['max_length']],
            ['password', PasswordStructureValidator::class],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => UserModule::t('labels', 'Password'),
        ];
    }

    /**
     * Mise à jour du mot de passe de l'utilisateur
     *
     * @param User $user
     * @return bool
     */
    public function resetPassword(User $user)
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var UserModule $userModule */
        $userModule = Yii::$app->getModule('userModule');
        if (!$userModule->canReUsePassword) {
            foreach (Password::find()->where(['user_id' => $user->id])->all() as $password) {
                if (Yii::$app->getSecurity()->validatePassword($this->password, $password->password_hash)) {
                    $this->addError('password', UserModule::t('messages', "This password has already be used"));
                    return false;
                }
            }
        }

        try {
            $user->scenario = User::SCENARIO_PASSWORD;
            $user->password_hash = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $user->password_updated_at = date('Y-m-d H:i:s');
            $user->password_usage = 0;
            $user->confirmed_at = date('Y-m-d H:i:s');
            if ($user->save()) {
                $password = Yii::createObject(Password::class);
                $password->user_id = $user->id;
                $password->password_hash = $user->password_hash;
                $password->save();
                return true;
            }
        } catch (Exception $x) {
            Yii::error($x);
        }

        return false;
    }
}