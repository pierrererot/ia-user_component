<?php

/**
 * @var $url string
 */

use app\modules\user\UserModule;

$appName = Yii::$app->params['mails']['applicationShortName'];
$companyName = Yii::$app->params['mails']['companyName'];
/** @var UserModule $userModule */
$userModule = Yii::$app->getModule('user');
?>

<h1>
    <?= UserModule::t('mails', "Creation of your user account on {0}", Yii::$app->params['mails']['fromAdminToUser']) ?>
</h1>

<div class="body">
    <p>
        Bonjour,
    </p>

    <p>
        Votre compte utilisateur <?= $appName ?> vient d'être créé par l'administrateur <?= $companyName ?>.
    </p>

    <p>
        Pour l'utiliser, vous devez définir votre mot de passe dans les <?= $userModule->rememberConfirmationTokenFor / 3600 ?>
        heures en vous connectant à l'adresse <a href='<?= $url ?>'><?= $appName ?></a>
    </p>
</div>
