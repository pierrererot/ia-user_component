<?php

namespace app\modules\user\models;

use app\modules\user\models\query\BlacklistQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "blacklist".
 *
 * @property int $id
 * @property int $ip
 * @property int $enabled
 * @property string $created_at
 * @property string $updated_at
 */
class Blacklist extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blacklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['enabled', 'boolean'],
            ['ip', 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(), [
                [
                    'class' => TimestampBehavior::class,
                    'value' => function () {
                        return date('Y-m-d H:i:s');
                    },
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('labels', 'ID'),
            'ip' => Yii::t('labels', 'Ip'),
            'enabled' => Yii::t('labels', 'Enabled'),
            'created_at' => Yii::t('labels', 'Created At'),
            'updated_at' => Yii::t('labels', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return BlacklistQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BlacklistQuery(get_called_class());
    }
}
