<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\user\UserModule;

/**
 * Affichage
 * @var $this yii\web\View
 * @var $model app\modules\user\models\User
 */

$this->title = $model->email;
$this->params['breadcrumbs'][] = ['label' => UserModule::t('labels', 'My Account'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(UserModule::t('labels', 'Update My Profile'), ['update-profile'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(UserModule::t('labels', 'Update My Email'), ['update-user'], ['class' => 'btn btn-info']) ?>
        <?= Html::a(UserModule::t('labels', 'Request new password'), ['/new-password'], ['class' => 'btn btn-info']) ?>
    </p>

    <?= /** @noinspection PhpUnhandledExceptionInspection */
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'email:email',
            'password_updated_at',
            'password_usage',
            [
                'label' => UserModule::t('labels', "First Name"),
                'value' => $model->profile ? $model->profile->first_name : '',
            ],
            [
                'label' => UserModule::t('labels', "Last Name"),
                'value' => $model->profile ? $model->profile->last_name : '',
            ],
            [
                'label' => UserModule::t('labels', "Cellphone"),
                'value' => $model->profile ? $model->profile->cellphone : '',
            ],
            [
                'label' => UserModule::t('labels', "Landline Phone"),
                'value' => $model->profile ? $model->profile->landline_phone : '',
            ],
            [
                'label' => UserModule::t('labels', "Fax"),
                'value' => $model->profile ? $model->profile->fax : '',
            ],
        ],
    ]) ?>

</div>
