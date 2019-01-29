<?php

namespace app\modules\user\widgets;

use yii\base\Widget;
use yii\rbac\Permission;

/**
 * Class AuthItemNodeDisplay
 * @package app\widgets
 *
 */
class AuthItemNodeDisplay extends Widget
{
    /** @var  string */
    public $templateName = 'authItemNodeDisplay';

    /** @var Permission $model */
    public $model;

    /** @var bool */
    public $showRoles = true;

    /** @var bool */
    public $manageRoles = true;

    /** @var bool */
    public $showPermissions = true;

    /** @var bool */
    public $managePermissions = true;

    /**
     * @return string
     */
    public function run()
    {
        return $this->render($this->templateName, [
            'model' => $this->model,
            'showRoles' => $this->showRoles,
            'showPermissions' => $this->showPermissions,
            'manageRoles' => $this->manageRoles,
            'managePermissions' => $this->managePermissions,
        ]);
    }
}