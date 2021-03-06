<?php

use app\modules\user\UserModule;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $model app\modules\user\models\form\AuthItemForm
 * @var $permissions app\modules\user\models\AuthItem[]
 */

$this->title = UserModule::t('labels', 'Create Role');
$this->params['breadcrumbs'][] = ['label' => UserModule::t('labels', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'permissions' => $permissions,
    ]) ?>

</div>
