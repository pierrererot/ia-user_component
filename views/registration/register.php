<?php

/**
 * @var $this yii\web\View
 * @var $model app\modules\user\models\form\RegistrationForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\user\UserModule;

$this->title = UserModule::t('labels', 'Register');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= $this->title ?></h3>
            </div>

            <div class="panel-body">
                <?php $form = ActiveForm::begin(['id' => 'registration-form']); ?>

                <?= $form->field($model->user, 'email') ?>

                <?= $form->field($model->profile, 'first_name') ?>

                <?= $form->field($model->profile, 'last_name') ?>

                <?= Html::submitButton(UserModule::t('labels', 'Sign up'), ['class' => 'btn btn-success btn-block']) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <p class="text-center">
            <?= Html::a(UserModule::t('labels', 'Already registered ? Sign in'), ['/userModule/security/login']) ?>
        </p>
    </div>
</div>
