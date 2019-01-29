<?php

/**
 * Formulaire de saisie du mot de passe
 *
 * @var $this yii\web\View
 * @var $model \app\modules\user\models\form\PasswordForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\modules\user\UserModule;
use app\modules\ia\IAModule as IA;

$this->title = UserModule::t('labels', 'Create password');
$this->params['breadcrumbs'][] = $this->title;

/** @var UserModule $userModule */
$userModule = Yii::$app->getModule('userModule');
?>
<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= UserModule::t('labels', "Password creation") ?></h3>
            </div>

            <div class="panel-body">
                <?php $form = ActiveForm::begin(); ?>

                <div class="notes">
                    <ul>
                        <li><?= UserModule::t('messages', "{0} characters at least", $userModule->passwordSecurity['min_length']) ?></li>
                        <li><?= UserModule::t('messages', "1 lower case letter at least") ?></li>
                        <li><?= UserModule::t('messages', "1 upper case letter at least") ?></li>
                        <li><?= UserModule::t('messages', "1 digit at least") ?></li>
                        <li><?= UserModule::t('messages', "1 special character at least") ?></li>
                    </ul>
                </div>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="form-group">
                    <?= Html::submitButton('<i class="fa fa-save"></i>' . IA::t('labels', 'Save'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
