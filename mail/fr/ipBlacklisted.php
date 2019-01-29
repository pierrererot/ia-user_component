<?php

/**
 * Informe un admin qu'une IP a été blacklistée
 *
 * @var $ip string
 * @var $durationInMinutes int
 */

?>

<div>
    <p>
        Bonjour,
    </p>

    <p>
        suite a de nombreuses erreurs sur la saisie de mots de passe, l'adresse IP <?= $ip ?> a été blacklistée le <?= date('d-m-Y à H:i:s') ?> pour <?= $durationInMinutes ?>
        minutes.
    </p>
</div>
