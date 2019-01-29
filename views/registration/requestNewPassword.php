<?php

/**
 * Formulaire de saisie du mail pour l'envoi d'un nouveau mot depasse
 *
 * @var $this yii\web\View
 * @var $model \app\modules\user\models\form\PasswordForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\modules\user\UserModule;
use app\modules\ia\IAModule as IA;

$this->title = UserModule::t('labels', 'Request new password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= UserModule::t('labels', "Request new password") ?></h3>
            </div>

            <div class="panel-body">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'email')->textInput() ?>

                <div class="form-group">
                    <?= Html::submitButton('<i class="fa fa-save"></i>' . IA::t('labels', 'Save'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
