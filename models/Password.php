<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "password".
 *
 * @property int $id
 * @property int $user_id
 * @property string $password_hash
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $user
 */
class Password extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'password';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['password_hash'], 'string', 'max' => 60],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('labels', 'ID'),
            'user_id' => Yii::t('labels', 'User ID'),
            'password_hash' => Yii::t('labels', 'Password Hash'),
            'created_at' => Yii::t('labels', 'Created At'),
            'created_by' => Yii::t('labels', 'Created By'),
            'updated_at' => Yii::t('labels', 'Updated At'),
            'updated_by' => Yii::t('labels', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\modules\user\models\query\PasswordQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\user\models\query\PasswordQuery(get_called_class());
    }
}
