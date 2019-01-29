<?php

namespace app\modules\user\models;

use app\modules\user\models\query\AuthItemChildQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_item_child".
 *
 * @property string $parent
 * @property string $child
 *
 * @property AuthItem $parentItem
 * @property AuthItem $childItem
 */
class AuthItemChild extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item_child';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'string', 'max' => 64],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::class, 'targetAttribute' => ['parent' => 'name']],
            [['child'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::class, 'targetAttribute' => ['child' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parent' => Yii::t('labels', 'Parent'),
            'child' => Yii::t('labels', 'Child'),
        ];
    }

    /**
     * @inheritdoc
     * @return AuthItemChildQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AuthItemChildQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentItem()
    {
        return $this->hasOne(AuthItem::class, ['name' => 'parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildItem()
    {
        return $this->hasOne(AuthItem::class, ['name' => 'child']);
    }


    /////////////////////////////////////////////
    /////////////////////////////////////////////
    /////////////////////////////////////////////

}
