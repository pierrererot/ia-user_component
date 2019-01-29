<?php
/**
 * Created by PhpStorm.
 * User: Christophe
 * Date: 15/12/2017
 * Time: 17:33
 */

namespace app\modules\user\helpers;


use app\modules\user\lib\enums\UserStatus;
use app\modules\user\models\Profile;
use app\modules\user\models\User;
use Yii;

class DefaultFixturesHelper
{
    /**
     * @throws \yii\base\Exception
     */
    public static function createSuperadmin()
    {
        $data = [
            'scenario' => User::SCENARIO_CREATE,
            'email' => 'superadmin@inadvans.com',
            'status' => UserStatus::ENABLED,
            'password_hash' => '',
            'auth_key' => '',
            'registered_from' => '',
        ];
        $user = new User($data);
        $user->save();

        $data = [
            'scenario' => Profile::SCENARIO_CREATE,
            'last_name' => 'Administrateur',
            'first_name' => 'Super',
            'user_id' => $user->id,
        ];
        $profile = new Profile($data);
        $profile->save();

        $user->scenario = User::SCENARIO_PASSWORD;
        $user->password_hash = Yii::$app->getSecurity()->generatePasswordHash('AaaaBbbbaa11!!');
        $user->password_updated_at = date('Y:m:d H:i:s');
        $user->password_usage = 0;
        $user->confirmed_at = date('Y-m-d H:i:s');
        $user->save();
    }

    /**
     *
     */
    public static function removeSuperadmin()
    {
        $auth = Yii::$app->getAuthManager();

        $user = User::find()->byEmail('superadmin@inadvans.com')->one();
        $role = $auth->getRole('superadmin');

        if ($user) {
            $auth->revoke($role, $user->id);
        }

        $auth->remove($role);
    }

    /**
     * @throws \Exception
     */
    public static function createSuperadminPrivilege()
    {
        $auth = Yii::$app->getAuthManager();

        // Création du rôle 'superadmin'
        $role = $auth->createRole('superadmin');
        $role->description = 'Super administrateur';
        $auth->add($role);
    }

    public static function removeSuperadminPrivilege()
    {
        $auth = Yii::$app->getAuthManager();

        $role = $auth->getRole('superadmin');
        $auth->remove($role);
    }

    /**
     * @throws \Exception
     */
    public static function createUserManagementPrivileges()
    {
        $auth = Yii::$app->getAuthManager();

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Gérer les utilisateurs';
        $auth->add($manageUsers);

        $permission = $auth->createPermission('createUser');
        $permission->description = 'Créer un utilisateur';
        $auth->add($permission);
        $auth->addChild($manageUsers, $permission);

        $permission = $auth->createPermission('updateUser');
        $permission->description = 'Modifier un utilisateur';
        $auth->add($permission);
        $auth->addChild($manageUsers, $permission);

        $permission = $auth->createPermission('deleteUser');
        $permission->description = 'Supprimer un utilisateur';
        $auth->add($permission);
        $auth->addChild($manageUsers, $permission);

        $role = $auth->getRole('superadmin');
        $auth->addChild($role, $manageUsers);
    }

    /**
     *
     */
    public static function removeUserManagementPrivileges()
    {
        $auth = Yii::$app->getAuthManager();

        $permission = $auth->getPermission('createUser');
        $auth->remove($permission);
        $permission = $auth->getPermission('updateUser');
        $auth->remove($permission);
        $permission = $auth->getPermission('deleteUser');
        $auth->remove($permission);
        $permission = $auth->getPermission('manageUsers');
        $auth->remove($permission);
    }

    /**
     * Crée les permissions de base pour la gestion du rbac et les affecte au superadmin
     *
     * @throws \Exception
     */
    public static function createPrivilegesManagementPrivileges()
    {
        $auth = Yii::$app->getAuthManager();
        $role = $auth->getRole('superadmin');

        $permission = $auth->createPermission('managePrivileges');
        $permission->description = 'Gérer les droits';
        $auth->add($permission);
        $auth->addChild($role, $permission);

        $permission = $auth->createPermission('manageRoles');
        $permission->description = 'Gérer les profils';
        $auth->add($permission);

        $role = $auth->getRole('superadmin');
        $auth->addChild($role, $permission);
    }

    /**
     * Supprime les permissions de base pour la gestion du rbac
     */
    public static function removePrivilegesManagementPrivileges()
    {
        $auth = Yii::$app->getAuthManager();

        $permission = $auth->getPermission('managePrivileges');
        $auth->remove($permission);

        $permission = $auth->getPermission('manageRoles');
        $auth->remove($permission);
    }

    /**
     * @throws \Exception
     */
    public static function setRoles()
    {
        $auth = Yii::$app->getAuthManager();
        $role = $auth->getRole('superadmin');
        $user = User::find()->byEmail('superadmin@inadvans.com')->one();
        $auth->assign($role, $user->id);
    }

}