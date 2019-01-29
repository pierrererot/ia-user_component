<?php

namespace app\modules\user\models;

use app\modules\ia\IAModule;
use app\modules\user\lib\enums\AuthItemType;
use app\modules\user\lib\enums\TokenType;
use app\modules\user\lib\enums\UserStatus;
use app\modules\user\models\query\AuthAssignmentQuery;
use app\modules\user\models\query\UserQuery;
use Yii;
use app\modules\user\UserModule;
use app\modules\ia\IAModule as IA;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property integer $status @see enum UserStatus
 * @property string $email
 * @property string $password_hash
 * @property string $password_updated_at
 * @property integer $password_usage
 * @property string $auth_key
 * @property string $confirmed_at
 * @property string $blocked_at
 * @property integer $registered_from
 * @property integer $logged_in_from
 * @property string $logged_in_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Profile[] $profiles
 * @property Profile $profile
 * @property AuthAssignment[] $authorizations
 * @property AuthItem[] $roles
 * @property AuthItem[] $permissions
 */
class User extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_ACTIVATE = 'activate';
    const SCENARIO_PASSWORD = 'password';

    /** @var  string */
    public $email_confirm;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $out = parent::scenarios();
        $out[static::SCENARIO_CREATE] = ['email'];
        $out[static::SCENARIO_REGISTER] = ['email'];
        $out[static::SCENARIO_ACTIVATE] = ['email', 'password_hash', 'password_updated_at', 'password_usage'];
        $out[static::SCENARIO_PASSWORD] = ['password_hash', 'password_updated_at', 'password_usage'];
        return $out;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique'],
            ['email_confirm', 'compare', 'compareAttribute' => 'email'],
            ['status', 'in', 'range' => UserStatus::getKeys()],
            ['password_usage', 'integer'],
            [['registered_from', 'logged_in_from', 'auth_key', 'password_hash'], 'string'],
            //
            [['confirmed_at', 'blocked_at', 'logged_in_at', 'password_updated_at', 'created_at', 'updated_at'], 'safe'],
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
        UserModule::registerTranslations();
        return [
            'id' => Yii::t('labels', 'ID'),
            'created_at' => IA::t('labels', 'Created At'),
            'updated_at' => IA::t('labels', 'Updated At'),
            //
            'auth_key' => UserModule::t('labels', 'Auth Key'),
            'blocked_at' => UserModule::t('labels', 'Blocked At'),
            'confirmed_at' => UserModule::t('labels', 'Confirmed At'),
            'email' => UserModule::t('labels', 'Email'),
            'email_confirm' => UserModule::t('labels', 'Email (confirm)'),
            'logged_in_at' => UserModule::t('labels', 'Logged In At'),
            'logged_in_from' => UserModule::t('labels', 'Logged In From'),
            'password_hash' => UserModule::t('labels', 'Password'),
            'password_updated_at' => UserModule::t('labels', 'Password Updated At'),
            'password_usage' => UserModule::t('labels', 'Password Usage'),
            'registered_from' => UserModule::t('labels', 'Registered From'),
            'status' => IAModule::t('labels', 'Status'),
        ];
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Dans le modèle, on peut avoir plusieurs profils par user...
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::class, ['user_id' => 'id']);
    }

    /**
     * ... mais dans la pratique, on n'a qu'un profil par user.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizations()
    {
        return $this->hasMany(AuthAssignment::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(AuthItem::class, ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'id'])
            ->where(['auth_item.type' => AuthItemType::ROLE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(AuthItem::class, ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'id'])
            ->where(['auth_item.type' => AuthItemType::PERMISSION]);
    }

    ///////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $token = Token::find()->byType(TokenType::ACCESS)->byCode($token)->one();
        return $token ? static::findIdentity($token->user_id) : null;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key = $authKey;
    }

    /**
     * @return string
     */
    public function formatName()
    {
        return $this->profile ? $this->profile->formatName() : '';
    }

    /**
     * @param bool $mailtoLink
     * @return string
     */
    public function formatNameAndMail($mailtoLink = true)
    {
        $name = $this->formatName();
        return $mailtoLink ?
            sprintf('%s (<a href="mailto:%s">%s</a>)', $name, $this->email, $this->email)
            : sprintf('%s (%s)', $name, $this->email);
    }

    /**
     *
     */
    public function afterDelete()
    {
        parent::afterDelete();

        // On supprime les entrées associées dans la table auth_assignment
        AuthAssignmentQuery::deleteByUser($this->id);
    }

    /**
     * Renvoie true si le user a le rôle $roleName
     *
     * @param string $roleName @see lib/enums/AppRoles
     * @return bool
     */
    public function hasRole($roleName)
    {
        $auth = Yii::$app->getAuthManager();
        return $auth->checkAccess($this->id, $roleName);
    }

    /**
     * Renvoie true si le user a au moins un des rôles de la liste $roleNames
     *
     * @param string[] $roleNames @see lib/enums/AppRoles
     * @return bool
     */
    public function hasOneRoleIn(array $roleNames)
    {
        $auth = Yii::$app->getAuthManager();
        $out = false;
        foreach ($roleNames as $roleName) {
            if ($auth->checkAccess($this->id, $roleName)) {
                $out = true;
                break;
            }
        }

        return $out;
    }

    /**
     * Renvoie true si l'utilisateur a le droit $name
     * NB : on ne vérifie que les droits directment attribués (ce n'est pas l'équivaleent de $webUser->can($name))
     * => potentiellement source d'erreurs =>
     * @deprecated
     *
     * @param string $name
     * @return bool
     */
    public function hasPermission($name)
    {
        foreach ($this->permissions as $it) {
            if ($it->name == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Renvoie l'âge du mot de passe en nombre de jours, relativement au timestamp $refTime (timestamp courant par défaut)
     *
     * @param int $refTime Timestamp
     * @return int
     */
    public function passwordAgeInDays($refTime = null)
    {
        if (is_null($refTime)) {
            $refTime = time();
        }

        $mdpCreationTime = strtotime($this->password_updated_at);
        $diffSeconds = $refTime - $mdpCreationTime;
        return floor($diffSeconds / (60 * 60 * 24));
    }

    /**
     * Bloque un compte utilisateur en renseignant une date de blocage
     * Raisons possibles : mot de passe expiré, trop d'erreurs de saisie du mdp, etc...
     *
     * @return boolean
     */
    public function blockUser()
    {
        $this->blocked_at = date('Y-m-d  H:i:s');
        return $this->save();
    }

    /**
     * Débloque un compte utilisateur en vidant la date de blocage
     *
     * @return boolean
     */
    public function unblockUser()
    {
        $this->blocked_at = null;
        return $this->save();
    }

    /**
     * @return bool
     */
    public function isBlocked()
    {
        return $this->blocked_at != '';
    }

}
