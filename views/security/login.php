<?php

/**
 * @var $this yii\web\View
 * @var $model app\modules\user\models\form\LoginForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\user\UserModule;

$this->title = UserModule::t('labels', 'Sign In');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= $this->title ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                ]); ?>

                <?= $form->field($model, 'email')->textInput() ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= Html::submitButton(UserModule::t('labels', 'Sign In'), ['class' => 'btn btn-success btn-block']) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <p class="text-center">
            <?= Html::a(UserModule::t('messages', 'Lost password ?'), ['/user/registration/request-new-password']) ?>
        </p>
        <p class="text-center">
            <?= Html::a(UserModule::t('messages', 'Lost confirmation mail ?'), ['/user/registration/request-new-confirmation-link']) ?>
        </p>
    </div>
</div>
