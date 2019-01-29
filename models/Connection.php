<?php

namespace app\modules\user\models;

use app\modules\user\lib\enums\ConnectionStatus;
use app\modules\user\models\query\ConnectionQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "connection".
 *
 * @property int $id
 * @property int $status
 * @property string $username
 * @property int $from_ip
 * @property string $created_at
 * @property string $updated_at
 */
class Connection extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'connection';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::$app->get('dbLog');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['username', 'from_ip'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('labels', 'ID'),
            'status' => Yii::t('labels', 'Status'),
            'username' => Yii::t('labels', 'Username'),
            'from_ip' => Yii::t('labels', 'From Ip'),
            'created_at' => Yii::t('labels', 'Created At'),
            'updated_at' => Yii::t('labels', 'Updated At'),
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
     * @return ConnectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConnectionQuery(get_called_class());
    }

    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////

    /**
     * @param string $username
     * @param string $ip
     * @return bool
     */
    public function loginFailure($username, $ip)
    {
        $this->status = ConnectionStatus::FAILURE;
        $this->username = $username;
        $this->from_ip = $ip;
        return $this->save();
    }

    /**
     * @param string $username
     * @param string $ip
     * @return bool
     */
    public function loginSuccess($username, $ip)
    {
        $this->status = ConnectionStatus::SUCCESS;
        $this->username = $username;
        $this->from_ip = $ip;
        return $this->save();
    }
}
