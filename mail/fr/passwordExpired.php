<?php

use yii\helpers\Html;

/**
 * Signale l'expiration d'un mot de passe et donnant un lien vers la page de ré-initialisation du mot de passe
 *
 * @var $url string
 * @var $reason string
 */

?>

<div>
    <p>
        Bonjour,
    </p>

    <p>
        votre mot de passe sur l'application <?= Yii::$app->name ?> a expiré pour la raison suivante :
    </p>

    <p>
        <?= $reason ?>
    </p>

    <p>
        Pour le renouveler, veuillez suivre le lien ci-dessous et saisir un nouveau mot de passe :
    </p>

    <p>
        <?= Html::a($url, $url) ?>
    </p>

    <p>
        Si vous n'arrivez pas à cliquer sur ce lien, vous pouvez le copier dans la barre d'adresse de votre navigateur.
    </p>
</div>
