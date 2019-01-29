<?php

namespace app\modules\user\models\form;

use app\modules\user\models\AuthItem;
use app\modules\user\UserModule;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 *
 */
class AuthItemForm extends Model
{
    /** @var string[] $permissions */
    public $permissions;

    /** @var AuthItem */
    public $authItem;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['permissions', 'exist', 'targetClass' => AuthItem::class, 'targetAttribute' => 'name', 'allowArray' => true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'permissions' => userModule::t('labels', 'Permissions'),
        ];
    }

    /**
     * Charge un AuthItem dans le formulaire
     *
     * @param AuthItem $model
     */
    public function loadAuthItem(AuthItem $model)
    {
        $this->authItem = $model;
        $this->permissions = ArrayHelper::getColumn($model->permissions, 'name');
    }

    /**
     * Crée l'item dans la base
     *
     * @param $data
     * @param null $formName
     * @return bool
     * @throws \Exception
     */
    public function load($data, $formName = null)
    {
        $loadMe = parent::load($data, $formName);
        $loadAuthItem = $this->authItem->load($data, $formName);
        return $loadMe && $loadAuthItem;
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        $validateMe = parent::validate($attributeNames, $clearErrors);
        $validateAuthItem = $this->authItem->validate($attributeNames, $clearErrors);
        return $validateMe && $validateAuthItem;
    }

    /**
     * Crée l'item dans la base
     *
     * @param $data
     * @param null $formName
     * @return bool
     * @throws \Exception
     */
    public function loadForm($data, $formName = null)
    {
        $loadMe = $this->load($data, $formName);
        $loadAuthItem = $this->authItem->load($data, $formName);
        return $loadMe && $loadAuthItem;
    }

    /**
     * Crée l'item dans la base
     *
     * @param bool $runValidation
     * @return bool
     * @throws \Exception
     */
    public function create($runValidation = true)
    {
        if (!$this->authItem->save($runValidation)) {
            return false;
        }

        if (!$this->updatePermissions()) {
            return false;
        }

        $this->authItem->refresh();
        return true;
    }

    /**
     * Met à jour l'item
     *
     * @param bool $runValidation
     * @return bool
     * @throws \Exception
     */
    public function update($runValidation = true)
    {
        if (!$this->authItem->save($runValidation)) {
            return false;
        }

        if (!$this->updatePermissions()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function updatePermissions()
    {
        $oldPermissions = ArrayHelper::getColumn($this->authItem->permissions, 'name');

        $newPermissions = array_diff($this->permissions, $oldPermissions);
        $deletedPermissions = array_diff($oldPermissions, $this->permissions);
        if ($newPermissions || $deletedPermissions) {
            $auth = Yii::$app->authManager;

            foreach ($newPermissions as $item) {
                $permissionsModel = $auth->getPermission($item);
                if (!$permissionsModel) {
                    Yii::error("Permission inconnue : $item");
                    return false;
                }

                $auth->addChild($this->authItem->asRbacItem(), $permissionsModel);
            }

            foreach ($deletedPermissions as $item) {
                $permissionsModel = $auth->getPermission($item);
                if (!$permissionsModel) {
                    Yii::error("Permission inconnue : $item");
                    return false;
                }

                $auth->removeChild($this->authItem->asRbacItem(), $permissionsModel);
            }
        }

        return true;
    }
}