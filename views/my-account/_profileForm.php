<?php

/**
 * Formulaire de mise à jour d'un profil utilisateur
 * NB : on ne gère qu'un seul profil pour le moment
 *
 * @var $this yii\web\View
 * @var $model app\models\Profile
 * @var $form yii\widgets\ActiveForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\ia\IAModule as IA;

?>

<div class="profile-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'first_name')->textInput() ?>

    <?= $form->field($model, 'last_name')->textInput() ?>

    <?= $form->field($model, 'cellphone')->textInput() ?>

    <?= $form->field($model, 'landline_phone')->textInput() ?>

    <?= $form->field($model, 'fax')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? IA::t('labels', 'Create') : IA::t('labels', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
