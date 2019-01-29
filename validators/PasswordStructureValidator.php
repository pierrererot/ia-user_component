<?php

namespace app\modules\user\validators;

use app\modules\user\models\Password;
use Stringy\Stringy;
use Yii;
use yii\validators\Validator;

use app\modules\user\UserModule;


/**
 * Class PasswordStructureValidator
 * @package app\modules\ia\validators
 * @see http://www.yiiframework.com/doc-2.0/guide-input-validation.html#standalone-validators
 */
class PasswordStructureValidator extends Validator
{
    /**
     * Vérifie que le mot de passe respecte les règles suivantes :
     *      - au moins une majuscule
     *      - au moins une minuscule
     *      - au moins un chiffre
     *      - au moins un caractère spécial
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $pwd = new Stringy($model->$attribute);
        $lg = $pwd->count();

        $errors = [];

        // Au moins une minuscule
        $test = $pwd->regexReplace('[a-z]+', '');
        if ($test->count() == $lg) {
            $errors[] = UserModule::t('messages', "one lowercase character");
        }

        // Au moins une majuscule
        $test = $pwd->regexReplace('[A-Z]+', '');
        if ($test->count() == $lg) {
            $errors[] = UserModule::t('messages', "one uppercase character");
        }

        // Au moins un chiffre
        $test = $pwd->regexReplace('[0-9]+', '');
        if ($test->count() == $lg) {
            $errors[] = UserModule::t('messages', "one digit");
        }

        // Au moins un caractère spécial
        $test = $pwd->regexReplace('[^0-9a-zA-Z]+', '');
        if ($test->count() == $lg) {
            $errors[] = UserModule::t('messages', "one special character");
        }


        if ($errors) {
            $this->addError($model, $attribute, UserModule::t('messages',
                "The password must contain at least {errors}", ['errors' => implode(' + ', $errors)])
            );
        }
    }
}
