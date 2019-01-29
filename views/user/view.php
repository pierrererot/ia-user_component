<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var $model app\modules\user\models\User
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('labels', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('labels', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('labels', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('labels', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= /** @noinspection PhpUnhandledExceptionInspection */
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            'password_hash',
            'auth_key',
            'confirmation_token',
            'confirmation_sent_at',
            'confirmed_at',
            'unconfirmed_email:email',
            'recovery_token',
            'recovery_sent_at',
            'blocked_at',
            'registered_from',
            'logged_in_from',
            'logged_in_at',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
