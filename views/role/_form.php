<?php

/**
 * @var $this yii\web\View
 * @var $model app\modules\user\models\form\AuthItemForm
 * @var $permissions app\modules\user\models\AuthItem[]
 * @var $form yii\widgets\ActiveForm
 */

use softark\duallistbox\DualListbox;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="permission-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model->authItem, 'name')->textInput() ?>

    <?= $form->field($model->authItem, 'description')->textarea(['rows' => 3]) ?>

    <?= $form->field($model->authItem, 'rule_name')->textInput() ?>

    <?php
    // @see https://github.com/softark/yii2-dual-listbox
    echo $form->field($model, 'permissions')->widget(DualListbox::class, [
        'items' => ArrayHelper::map($permissions, 'name', 'name'),
        'options' => [
            'multiple' => true,
            'size' => 20,
        ],
        'clientOptions' => [
            'moveOnSelect' => false,
            'selectedListLabel' => Yii::t('labels', "assigned"),
            'nonSelectedListLabel' => Yii::t('labels', "available"),
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(\app\modules\ia\IAModule::t('labels', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
