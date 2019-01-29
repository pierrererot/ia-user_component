<?php

use yii\helpers\Html;

/**
 * @var $url string
 */

?>

<div>
    <p>
        Bonjour,
    </p>

    <p>
        votre compte a bien été créé sur l'application <?= Yii::$app->name ?>.
    </p>

    <p>
        Pour finaliser votre inscription, veuillez suivre le lien ci-dessous et saisir votre mot de passe :
    </p>

    <p>
        <?= Html::a($url, $url) ?>
    </p>

    <p>
        Si vous n'arrivez pas à cliquer sur ce lien, vous pouvez le copier dans la barre d'adresse de votre navigateur.
    </p>
</div>
