<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $model app\modules\user\models\AuthItem
 * @var $parents app\modules\user\models\AuthItem[]
 *
 * @var $form yii\widgets\ActiveForm
 */


?>

<div class="permission-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'parentPermission')->dropDownList(ArrayHelper::map($parents, 'name', 'name'), ['prompt' => '']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'rule_name')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(\app\modules\ia\IAModule::t('labels', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
