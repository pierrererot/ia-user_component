<?php

namespace app\modules\user\models\form;

use app\modules\user\models\Profile;
use app\modules\user\models\User;
use Yii;
use yii\base\Model;


/**
 * Class RegistrationForm
 * @package app\models
 *
 * Gestion du formulaire d'inscription
 */
class RegistrationForm extends Model
{
    /** @var User */
    public $user = null;

    /** @var Profile */
    public $profile = null;

    /**
     * RegistrationForm constructor.
     * @param array $config
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->setModels();
    }

    /**
     * Initialise les modèles gérés par le formulaire
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function setModels()
    {
        $this->user = Yii::createObject('user/User');
        $this->user->setScenario(User::SCENARIO_REGISTER);

        $this->profile = Yii::createObject('user/Profile');
        $this->profile->setScenario(Profile::SCENARIO_REGISTER);
    }

    /**
     * @param array $data
     * @param null $formName
     * @return bool
     */
    public function loadAll($data, $formName = null)
    {
        $loadParent = parent::load($data, $formName);
        $loadUser = $this->user->load($data, $formName);
        $loadProfile = $this->profile->load($data, $formName);
        return $loadParent && $loadUser && $loadProfile;
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validateAll($attributeNames = null, $clearErrors = true)
    {
        $validateParent = parent::validate($attributeNames, $clearErrors);
        $validateUser = $this->user->validate($attributeNames, $clearErrors);
        $validateProfile = $this->profile->validate($attributeNames, $clearErrors);
        return $validateParent && $validateUser && $validateProfile;
    }

    /**
     * Inscription d'un nouvel utilisateur.
     * @see \app\modules\user\lib\UserEventHandler
     *
     * @param bool $validateAttributes
     * @return bool
     */
    public function saveAll($validateAttributes = true)
    {
        // Création du modèle User
        if (!$this->beforeCreateUser()) {
            return false;
        }

        if (!$this->user->save($validateAttributes)) {
            return false;
        }

        if (!$this->afterCreateUser()) {
            return false;
        }

        // Création du modèle Profile associé
        if (!$this->beforeCreateProfile()) {
            return false;
        }

        if (!$this->profile->save($validateAttributes)) {
            return false;
        }

        if (!$this->afterCreateProfile()) {
            return false;
        }

        // Autres traitements
        if (!$this->completeRegistration()) {
            return false;
        }

        return true;
    }

    /**
     * Actions supplémentaires à encapsuler dans la transaction
     *
     * @return bool
     */
    protected function completeRegistration()
    {
        return true;
    }

    /**
     * @return boolean
     */
    protected function beforeCreateUser()
    {
        $this->user->registered_from = Yii::$app->request->getUserIP();
        $this->user->password_hash = '';
        $this->user->auth_key = '';
        return true;
    }

    /**
     * @return boolean
     */
    protected function afterCreateUser()
    {
        return true;
    }

    /**
     * @return boolean
     */
    protected function beforeCreateProfile()
    {
        $this->profile->user_id = $this->user->id;
        return true;
    }

    /**
     * @return boolean
     */
    protected function afterCreateProfile()
    {
        return true;
    }

}
