<?php

use app\modules\user\UserModule;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = UserModule::t('labels', 'Create User');
$this->params['breadcrumbs'][] = ['label' => UserModule::t('labels', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
