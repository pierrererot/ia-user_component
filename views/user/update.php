<?php

use yii\helpers\Html;
use app\modules\ia\IAModule as IA;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = Yii::t('labels', 'Update {modelClass}: ', [
    'modelClass' => 'User',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => IA::t('labels', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('labels', 'Update');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
