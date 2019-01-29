<?php

/**
 * Affiche la ligne correspondant au droit $model dans le tableau décrivant l'arborescence des droits
 *
 * @var $model \app\modules\user\models\AuthItem
 * @var $showRoles boolean
 * @var $showPermissions boolean
 * @var $manageRoles boolean
 * @var $managePermissions boolean
 */

use app\modules\user\lib\enums\AuthItemType;
use yii\helpers\Html;

require_once(__DIR__ . '/_authItemNodeDisplay_css.php');

if ($model->type == AuthItemType::ROLE) {
    if (!$showRoles) {
        return;
    }

    $controllerPath = "/userModule/role";
} else {
    if (!$showPermissions) {
        return;
    }

    $controllerPath = "/userModule/permission";
}
?>

<span class="auth-item-node">
    <span class="name">
        <?= $model->name ?>
    </span>
    <?php if($model->type == AuthItemType::ROLE && $manageRoles || $model->type == AuthItemType::PERMISSION && $managePermissions) : ?>
    <span class="actions">
        <?= Html::a("<span class=\"glyphicon glyphicon-pencil\"></span>", ["$controllerPath/update", 'id' => $model->name], ['']) ?>
    </span>
    <?php endif ?>
</span>
