<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\user\commands;

use app\modules\user\helpers\DefaultFixturesHelper;
use Exception;
use yii\console\Controller;

/**
 *
 */
class DefaultFixturesController extends Controller
{
    /**
     * @throws Exception
     */
    public function actionCreateSuperadmin()
    {
        DefaultFixturesHelper::createSuperadmin();
    }

    /**
     * @throws Exception
     */
    public function actionCreateSuperadminPrivilege()
    {
        DefaultFixturesHelper::createSuperadminPrivilege();
    }

    /**
     * @throws Exception
     */
    public function actionCreateUserManagementPrivileges()
    {
        DefaultFixturesHelper::createUserManagementPrivileges();
    }

    /**
     * @throws Exception
     */
    public function actionCreatePrivilegesManagementPrivileges()
    {
        DefaultFixturesHelper::createPrivilegesManagementPrivileges();
    }

    /**
     *
     */
    public function actionRemoveSuperadmin()
    {
        DefaultFixturesHelper::removeSuperadmin();
    }

    /**
     *
     */
    public function actionRemoveUserManagementPrivileges()
    {
        DefaultFixturesHelper::removeUserManagementPrivileges();
    }

    /**
     *
     */
    public function actionRemovePrivilegesManagementPrivileges()
    {
        DefaultFixturesHelper::removePrivilegesManagementPrivileges();
    }
}
