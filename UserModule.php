<?php

namespace app\modules\user;

use app\modules\user\lib\UserMail;
use app\modules\user\lib\UserEventHandler;
use app\modules\user\models\AuthItem;
use app\modules\user\models\form\AuthItemForm;
use app\modules\user\models\form\LoginForm;
use app\modules\user\models\form\MailRequestForm;
use app\modules\user\models\form\PasswordForm;
use app\modules\user\models\form\RegistrationForm;
use app\modules\user\models\Profile;
use app\modules\user\models\search\UserSearch;
use app\modules\user\models\Token;
use app\modules\user\models\User;
use Yii;
use yii\base\Module;

/**
 * user module definition class
 */
class UserModule extends Module
{
    const VERSION = "1.0.0";
    const DATE_VERSION = "2018-05-16";

    //
    // Configuration
    //

    /** @var bool Active ou désactive les page d'inscription */
    public $enableRegistration = true;

    /** @var string Route de redirection après inscription */
    public $redirectAfterRegister = '/';

    /** @var string Route de redirection après authentification */
    public $redirectAfterLogin = '/';

    /** @var string Route de redirection après avoir créé son mot de passe */
    public $redirectAfterConfirmPassword = '/';

    /** @var string Route de redirection après avoir changé son mot de passe */
    public $redirectAfterResetPassword = '/';

    /** @var string Route de redirection après déconnexion */
    public $redirectAfterLogout;

    /** @var bool Indique s'il faut obliger l'utilisateur à renouveler son mot de passe après qu'il a modifié son email */
    public $resetPasswordAfterEmailChange = false;

    /** @var int Durée de validité d'un jeton de confirmation, en secondes */
    public $rememberConfirmationTokenFor = 3600; // 60*60 secondes = 1h

    /** @var int Durée de validité d'un jeton de reset du mot de passe, en secondes */
    public $rememberPasswordTokenFor = 3600; // 60*60 secondes = 1h

    /** @var int Durée d'une session, en secondes */
    public $rememberFor = 86400; // 60*60*24 secondes = 1j

    /** @var bool $logoutAfterUpdateAccount true => déconnexion après la modification d'informations sensibles sur mon compte (l'adresse mail, notamment) */
    public $logoutAfterUpdateAccount = true;

    /** @var bool $canReUsePassword true => on peut ré-utiliser un pwd déjà utilisé, false => il faut impérativement en changer */
    public $canReUsePassword = false;


    /** @var array */
    public $passwordUsage = [];
    protected $default_passwordUsage = [
        'check' => true,// true pour vérifier le nombre d'utilisations du mot de passe, false pour ignorer cette règle
        'nbDaysMax' => 100, // nombre de jours avant invalidation
        'nbConnectionsMax' => 90, // nombre de connexions avant invalidation
    ];

    /** @var array */
    public $passwordFailures = [];
    protected $default_passwordFailures = [
        'check' => true,// true pour vérifier les échecs sur la saisie des mots de passe, false pour ignorer cette règle
        'nbFailuresAllowed' => 5, // nombre d'erreurs autorisées avant alerte
        'referenceDelayInMinutes' => 5, // nombre de minutes de référence pour le comptage des erreurs
        'sendMailForPasswordReset' => false,
    ];

    /** @var array */
    public $ipBlacklisting = [];
    protected $default_ipBlacklisting = [
        'check' => true, // true pour vérifier les échecs sur la saisie des mots de passe, false pour ignorer cette règle
        'nbFailuresAllowed' => 15, // nombre d'erreurs autorisées avant alerte
        'referenceDelayInMinutes' => 30, // nombre de minutes de référence pour le comptage des erreurs
        'durationInMinutes' => 60,
        'notifyAdmin' => true,
        'redirectTo' => 'site/nope',
    ];

    public $passwordSecurity = [];
    protected $default_passwordSecurity = [
        'min_length' => 8,
        'max_length' => 75
    ];

    /** @var array Configuration du UserMail */
    public $mailer = [
        'viewPath' => '@app/modules/user/mail', // renseigner le path des templates des mails en cas de surcharge. Par défaut, on va les chercher dans /mail
    ];

    /**
     * @var array Mapping des classes créées dans le module
     * @see Yii::createObject()
     */
    public $classMap = [];

    //
    //
    //


    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        // Initialisation des tableaux de la configuration
        // @todo cbn voir si on ne peut pas gérer ça plus facilement avec un fichier de config au niveau du module
        $this->passwordUsage = is_array($this->passwordUsage) ? array_merge($this->default_passwordUsage, $this->passwordUsage) : $this->default_passwordUsage;
        $this->passwordFailures = is_array($this->passwordFailures) ? array_merge($this->default_passwordFailures, $this->passwordFailures) : $this->default_passwordFailures;
        $this->ipBlacklisting = is_array($this->ipBlacklisting) ? array_merge($this->default_ipBlacklisting, $this->ipBlacklisting) : $this->default_ipBlacklisting;
        $this->passwordSecurity = is_array($this->passwordSecurity) ? array_merge($this->default_passwordSecurity, $this->passwordSecurity) : $this->default_passwordSecurity;

        // Enregistrement du mapping des classes pour les objets du module
        $this->registerClassDefinitions();

        // Accès simplifié aux traductions
        static::registerTranslations();

        // Lancement du gestionnaire d'événements (singleton)
        Yii::createObject('user/UserEventHandler');
    }

    /**
     * Déclaration des ressources pour les chaines et leur traduction
     */
    public static function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/user/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/user/messages',
            'fileMap' => [
                'modules/user/labels' => 'labels.php',
                'modules/user/messages' => 'messages.php',
            ],
        ];
    }

    /**
     * Raccourci pour la fonction de traduction
     *
     * @param string $category
     * @param string $message
     * @param array $params
     * @param string $language
     * @return mixed
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/user/' . $category, $message, $params, $language);
    }

    /**
     * Fusion les définitions de classes issues de la configuration avec les définitions fournies par défaut
     * Enregistrement des définitions dans le DI de l'application
     */
    protected function registerClassDefinitions()
    {
        $defaultClassMap = [
            //
            'user/AuthItem' => AuthItem::class,
            'user/AuthItemForm' => AuthItemForm::class,
            'user/LoginForm' => LoginForm::class,
            'user/MailRequestForm' => MailRequestForm::class,
            'user/PasswordForm' => PasswordForm::class,
            'user/Profile' => Profile::class,
            'user/RegistrationForm' => RegistrationForm::class,
            'user/Token' => Token::class,
            'user/User' => User::class,
            'user/UserSearch' => UserSearch::class,
            //
            'user/UserEventHandler' => UserEventHandler::class,
            'user/UserMail' => UserMail::class,
        ];

        foreach ($defaultClassMap as $key => $classDefinition) {
            if (!array_key_exists($key, $this->classMap)) {
                $this->classMap[$key] = $classDefinition;
            }
        }

        Yii::$container->setDefinitions($this->classMap);
    }

}
