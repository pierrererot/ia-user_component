<?php

/**
 * @var $this yii\web\View
 * @var $model app\modules\user\models\User
 * @var $form yii\widgets\ActiveForm
 */

use app\modules\ia\IAModule as IA;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? IA::t('labels', 'Create') : IA::t('labels', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
