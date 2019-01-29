<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\search\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'password_hash') ?>

    <?= $form->field($model, 'auth_key') ?>

    <?php // echo $form->field($model, 'confirmation_token') ?>

    <?php // echo $form->field($model, 'confirmation_sent_at') ?>

    <?php // echo $form->field($model, 'confirmed_at') ?>

    <?php // echo $form->field($model, 'unconfirmed_email') ?>

    <?php // echo $form->field($model, 'recovery_token') ?>

    <?php // echo $form->field($model, 'recovery_sent_at') ?>

    <?php // echo $form->field($model, 'blocked_at') ?>

    <?php // echo $form->field($model, 'registered_from') ?>

    <?php // echo $form->field($model, 'logged_in_from') ?>

    <?php // echo $form->field($model, 'logged_in_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('labels', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('labels', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
