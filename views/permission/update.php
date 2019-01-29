<?php

use app\modules\user\UserModule;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $model app\modules\user\models\AuthItem
 * @var $parents app\modules\user\models\AuthItem[]
 */

$this->title = UserModule::t('labels', 'Update Permission');
$this->params['breadcrumbs'][] = ['label' => UserModule::t('labels', 'Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="permission-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'parents' => $parents,
    ]) ?>

</div>
