<?php

namespace app\modules\user\models;

use app\modules\ia\IAModule;
use app\modules\user\models\query\TokenQuery;
use Carbon\Carbon;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "token".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $code
 * @property integer $type
 * @property string $data
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class Token extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['code', 'required'],
            [['user_id', 'type'], 'integer'],
            [['code', 'data'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => Yii::t('labels', 'User ID'),
            'code' => IAModule::t('labels', 'Code'),
            'type' => IAModule::t('labels', 'Type'),
            'data' => IAModule::t('labels', 'Data'),
            'created_at' => IAModule::t('labels', 'Created At'),
            'updated_at' => IAModule::t('labels', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return TokenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TokenQuery(get_called_class());
    }

    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////

    /**
     * Génère un jeton pour le user indiqué
     *
     * @param int $userId
     * @param int $type @see user/lib/enums/TokenType
     * @param array $data
     * @return null|static
     */
    public static function generateTokenForUser($userId, $type, $data = [])
    {
        $token = new static(['user_id' => $userId, 'type' => $type, 'code' => uniqid()]);
        if ($data) {
            $token->data = serialize($data);
        }

        if ($token->save()) {
            return $token;
        }

        return null;
    }

    /**
     * Génère un jeton non associé à un user de la base de données
     *
     * @param int $type @see user/lib/enums/TokenType
     * @param array $data
     * @return null|static
     */
    public static function generateToken($type, $data = [])
    {
        $token = new static(['type' => $type, 'code' => uniqid()]);
        if ($data) {
            $token->data = serialize($data);
        }

        if ($token->save()) {
            return $token;
        }

        return null;
    }

    /**
     * Cherche un jeton associé au user indiqué et correspondant aux critères $type et $code
     * Si une durée de validité est indiquée, on teste aussi l'expiration du jeton
     *
     * @param int $type
     * @param int $userId
     * @param string $code
     * @param int $duration
     * @return Token|array|null
     */
    public static function findTokenForUser($type, $userId, $code, $duration = 0)
    {
        $query = static::find()->byUser($userId)->byCode($code)->byType($type);
        if ($duration) {
            $createdAfter = Carbon::now()->subSeconds($duration)->toDateTimeString();
            $query->createdAfter($createdAfter);
        }

        return $query->one();
    }

    /**
     * Cherche un jeton correspondant aux critères $type et $code
     * Si une durée de validité est indiquée, on teste aussi l'expiration du jeton
     *
     * @param int $type
     * @param string $code
     * @param int $duration
     * @return Token|array|null
     */
    public static function findToken($type, $code, $duration = 0)
    {
        $query = static::find()->byCode($code)->byType($type);
        if ($duration) {
            $createdAfter = Carbon::now()->subSeconds($duration)->toDateTimeString();
            $query->createdAfter($createdAfter);
        }

        return $query->one();
    }

    /**
     * Désérialise et renvoie les données qui ont été sérialisées dans $data
     * @internal Alias de data(), requis car Yii appelle $token->data dans ses traitements (notamment pour la validation)
     *
     * @return array
     */
    public function getData()
    {
        return unserialize($this->data);
    }

    /**
     * Désérialise et renvoie les données qui ont été sérialisées dans $data
     *
     * @return array
     */
    public function data()
    {
        return unserialize($this->data);
    }

    /**
     * Renvoie tous les jetons de la liste $tokens dont la data satisfait aux critères $filters
     *
     * @param Token[] $tokens
     * @param array $filters tableau associatif [clé => valeur].
     * @return Token[]
     */
    public static function filterAllWithDataFilter(array $tokens, array $filters)
    {
        $out = [];

        /** @var Token $token */
        foreach ($tokens as $token) {
            $data = $token->data();
            $ok = true;

            foreach ($filters as $key => $value) {
                // Chaque critère exprimé sous forme clé => valeur doit être présent dans data
                if (ArrayHelper::getValue($data, $key, null) !== $value) {
                    $ok = false;
                    break;
                }
            }

            // Si tous les critères sont satisfaits, on garde le jeton
            if ($ok) {
                $out[] = $token;
            }
        }

        return $out;
    }

    /**
     * Renvoie le premier jeton de la liste $tokens dont la data satisfait aux critères $filters
     *
     * @param Token[] $tokens
     * @param array $filters tableau associatif [clé => valeur].
     * @return Token
     */
    public static function filterOneWithDataFilter(array $tokens, array $filters)
    {
        $availableTokens = static::filterAllWithDataFilter($tokens, $filters);
        return $availableTokens ? $availableTokens[0] : null;
    }
}
