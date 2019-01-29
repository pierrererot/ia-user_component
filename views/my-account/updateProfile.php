<?php

use yii\helpers\Html;
use app\modules\ia\IAModule as IA;
use app\modules\user\UserModule;

/**
 * @var $this yii\web\View
 * @var $model app\modules\user\models\User
 */

$this->title = UserModule::t('labels', 'Update My Profile');

$this->params['breadcrumbs'][] = ['label' => UserModule::t('labels', 'My Account'), 'url' => ['index']];
$this->params['breadcrumbs'][] = IA::t('labels', 'Update');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('/profile/_profileForm', [
        'model' => $model,
    ]) ?>

</div>
