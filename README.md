Module : USER
=============

### Installation

### Configuration

### Extensions

- execut/yii2-widget-bootstraptreeview

@see https://github.com/execut/yii2-widget-bootstraptreeview


### Dépendances

- ia

### Développement

**Surcharge des contrôleurs**

Si une application utilise un contrôleur héritant de l'un des contrôleurs du module, mettre en place les options de configuration : 

```
'controllerMap' => [
    'user' => 'app\controllers\UserController',
],
```

**Personnalisation des vues du module**

Pour surcharger une vue, mettre en place un thème dans la configuration de l'application
@see http://www.yiiframework.com/doc-2.0/guide-output-theming.html

```
'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [
                '@app/modules/atu/views' => '@app/views/atu',
            ],
        ],
    ],
],
```

**Personnalisation des classes du module**

Pour que le module utilise une classe spécifique à la place d'une de ses propres classes, il faut passer par Yii::$container et la liste de définitions de 
classes qui lui a été injectée. 

Si la classe concernée n'est pas encore enregistrée dans le Container : 

- renseigner le classMap du module dans la configuration. @see app\modules\user\Module::registerClassDefinitions() pour les définitions par défaut
- dans les fichiers du module, remplacer les appels à new() par des appels à Yii::createObject(). NB : cela permettra aussi à la méthode IA::className() de fonctionner avec 
cette nouvelle classe

 
Une fois la classe concernée enregistrée dans le Container : 

- dans son propre code, créer les instances de cette classe avec Yii::createObject() ou avec new() selon les préférences. Par cohérence, il vaut mieux utiliser 
Yii::createObject()

```
// Utilisation d'une autre classe que celle fournie par le module : configuration...
'modules' => [
    'user' => [
        'class' => 'app\modules\atu\Module',
        'classMap' => [
            'atu/RegistrationForm' => 'app\models\form\RegistrationForm',
        ],
    ],
],

// ... puis dans le code : 
Yii::createObject('atu/RegistrationForm');
```

NB : pour alléger l'écriture du module, seule une partie des classes qu'il déclare ont été enregistrées dans le Container. Ne pas hésiter à faire y ajouter d'autres classes au 
fur et à mesure que les besoin s'en feront sentir. 

### Description

Un utilisateur est identifié par son email & authentifié avec son mot de passe.

Un utilisateur peut avoir plusieurs profils.

NB : Un seul profil géré pour le moment >> Rendre le nombre de profils configurable + maj gestion du profil    

### Workflow

**Demande re ré-initialisation du mot de passe**

- Cliquer sur le lien "nouveau mot de passe" provoque l'affichage d'un formulaire permettant de saisir son email
- Un mail avec un lien de ré-initialisation est envoyé à cette adresse (sans changer le mot de passe actuel). Validité du lien limitée (jeton)
- Cliquer sur ce lien dans le mail amène sur la page de ré-initialisation du mot de passe

