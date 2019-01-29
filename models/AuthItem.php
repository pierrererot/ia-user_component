<?php

namespace app\modules\user\models;

use app\modules\ia\IAModule;
use app\modules\ia\lib\TreeNode;
use app\modules\user\lib\enums\AuthItemType;
use app\modules\user\models\query\AuthItemQuery;
use app\modules\user\UserModule;
use yii\db\ActiveRecord;
use yii\rbac\Item;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property resource $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property User[] $users
 * @property AuthRule $ruleName
 * @property AuthItem[] $children
 * @property AuthItem[] $parents
 * @property AuthItem[] $roles
 * @property AuthItem[] $permissions
 */
class AuthItem extends ActiveRecord
{
    const DEPTH_UNSET = -1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            ['name', 'unique'],
            // rule_name a une contrainte de clé étrangère et n'accepte pas la chaine vide
            ['rule_name', 'filter', 'filter' => function ($value) {
                return $value == '' ? null : $value;
            }],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::class, 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => IAModule::t('labels', 'Name'),
            'type' => IAModule::t('labels', 'Type'),
            'description' => IAModule::t('labels', 'Description'),
            'rule_name' => UserModule::t('labels', 'Rule Name'),
            'data' => UserModule::t('labels', 'Data'),
            'created_at' => IAModule::t('labels', 'Created At'),
            'updated_at' => IAModule::t('labels', 'Updated At'),
            //
            'parentPermission' => UserModule::t('labels', 'Parent Permission'),
            'permissions' => UserModule::t('labels', 'Permissions'),
        ];
    }

    /**
     * @inheritdoc
     * @return AuthItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AuthItemQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::class, ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('auth_assignment', ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::class, ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(AuthItem::class, ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(AuthItem::class, ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->getChildren()->andWhere(['type' => AuthItemType::ROLE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->getChildren()->andWhere(['type' => AuthItemType::PERMISSION]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Pour la compatibilité avec les types attendus dans les méthodes du AuthManager, il faut parfois transformer un AuthItem en yii\rbac\Item.
     * Cette méthode sert à cela.
     *
     * @return Item
     */
    public function asRbacItem()
    {
        $out = new Item();
        $out->type = $this->type;
        $out->name = $this->name;
        $out->description = $this->description;
        $out->ruleName = $this->rule_name;
        $out->data = $this->data;
        $out->createdAt = $this->created_at;
        $out->updatedAt = $this->updated_at;
        return $out;
    }

    /**
     * Renvoie un arbre contenant avec $this comme racine et, si $withChildren vaut true (valeur par défaut), la liste des noeuds descendants
     *
     * @param bool $withChildren true => récursif
     * @return TreeNode
     */
    public function getTreeNode($withChildren = true)
    {
        $tree = new TreeNode(['id' => $this->name, 'data' => $this]);
        if (!$withChildren) {
            return $tree;
        }

        foreach (static::find()->childrenFrom($this->name)->all() as $child) {
            $tree->addChild($child->getTreeNode());
        }

        return $tree;
    }

    /**
     * Renvoie le premier parent de la liste.
     * NB : par convention, on a une arborescence et non pas un réseau, donc le premier parent suffit si c'est un droit.
     * Par contre, on ne renvoie pas les parents de type 'role'
     *
     * @return AuthItem|null
     */
    public function getParentPermission()
    {
        foreach ($this->parents as $parent) {
            if ($parent->type == AuthItemType::PERMISSION) {
                return $parent;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getParentPermissionName()
    {
        $parent = $this->getParentPermission();
        return $parent ? $parent->name : '';
    }

    /**
     * Renvoie le premier parent de la liste.
     * NB : par convention, on a une arborescence et non pas un réseau, donc le premier parent suffit si c'est un rôle.
     * Par contre, on ne renvoie pas les parents de type 'droit'
     *
     * @return AuthItem|null
     */
    public function getParentRole()
    {
        foreach ($this->parents as $parent) {
            if ($parent->type == AuthItemType::ROLE) {
                return $parent;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getParentRoleName()
    {
        $parent = $this->getParentPermission();
        return $parent ? $parent->name : '';
    }

}
