<?php

namespace app\modules\user\models;

use app\modules\ia\validators\CellphoneValidator;
use app\modules\ia\validators\FaxValidator;
use app\modules\ia\validators\LandlinePhoneValidator;
use app\modules\user\models\query\ProfileQuery;
use Yii;
use app\modules\user\UserModule;
use app\modules\ia\IAModule as IA;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profile".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $cellphone
 * @property string $landline_phone
 * @property string $fax
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class Profile extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_ACTIVATE = 'activate';

    /** @var  string */
    public $cellphone_confirm;

    /** @var  string */
    public $landline_phone_confirm;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $out = parent::scenarios();
        $out[static::SCENARIO_CREATE] = ['first_name', 'last_name'];
        $out[static::SCENARIO_UPDATE] = ['first_name', 'last_name'];
        $out[static::SCENARIO_REGISTER] = ['first_name', 'last_name'];
        $out[static::SCENARIO_ACTIVATE] = ['first_name', 'last_name'];
        return $out;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            ['user_id', 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            //
            [['first_name', 'last_name', 'cellphone', 'landline_phone', 'fax'], 'filter', 'filter' => 'trim'],
            [['first_name', 'last_name'], 'string', 'max' => 255],
            ['cellphone', CellphoneValidator::class],
            ['cellphone_confirm', 'compare', 'compareAttribute' => 'cellphone'],
            ['landline_phone', LandlinePhoneValidator::class],
            ['landline_phone_confirm', 'compare', 'compareAttribute' => 'landline_phone'],
            ['fax', FaxValidator::class],
            //
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('labels', 'ID'),
            'user_id' => UserModule::t('labels', 'User ID'),
            'first_name' => UserModule::t('labels', 'First Name'),
            'last_name' => UserModule::t('labels', 'Last Name'),
            'cellphone' => UserModule::t('labels', 'Cellphone'),
            'cellphone_confirm' => UserModule::t('labels', 'Cellphone (confirm)'),
            'landline_phone' => UserModule::t('labels', 'Landline Phone'),
            'landline_phone_confirm' => UserModule::t('labels', 'Landline Phone (confirm)'),
            'fax' => UserModule::t('labels', 'Fax'),
            'created_at' => IA::t('labels', 'Created At'),
            'updated_at' => IA::t('labels', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ProfileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProfileQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    public function formatName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

}
