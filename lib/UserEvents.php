<?php

namespace app\modules\user\lib;

/**
 * Class UserEvents
 * @package app\modules\user\lib
 *
 * Liste des événements exploités dans ce module
 * NB : pour la clarté du code, il est préférable de centraliser ici les événements que l'on crée en rapport avec le module "utilisateurs" et de réserver les
 * événements associés à une classe à des cas particuliers, notamment quans ces événements ne sont pas censés recevoir des abonnés en dehors de cette classe
 */
class UserEvents
{
    // Création d'un compte utilisateur en backend
    const BEFORE_CREATE_USER = 'beforeCreateUser';
    const AFTER_CREATE_USER = 'afterCreateUser';

    // Mise à jour d'un compte utilisateur en backend par un admin
    const BEFORE_UPDATE_USER = 'beforeUpdateUser';
    const AFTER_UPDATE_USER = 'afterUpdateUser';

    // Mise à jour d'un compte utilisateur par son possesseur
    const BEFORE_UPDATE_OWN_USER = 'beforeUpdateOwnUser';
    const AFTER_UPDATE_OWN_USER = 'afterUpdateOwnUser';

    // Inscription d'un utilisateur depuis le frontend
    const BEFORE_REGISTER = 'beforeRegister';
    const AFTER_REGISTER = 'afterRegister';

    // Activation d'un compte utilisateur après invitation
    const BEFORE_REGISTER_INVITED_USER = 'beforeRegisterInvitedUser';
    const AFTER_REGISTER_INVITED_USER = 'afterRegisterInvitedUser';

    // Confirmation ou mise àjour du mot de passe
    const BEFORE_CONFIRM_PASSWORD = 'beforeConfirmPassword';
    const AFTER_CONFIRM_PASSWORD = 'afterConfirmPassword';
    const BEFORE_RESET_PASSWORD = 'beforeResetPassword';
    const AFTER_RESET_PASSWORD = 'afterResetPassword';

    // Saisie d'un mot de passe (pour la connexion, en général)
    const PASSWORD_SUCCESS = 'passwordSuccess';
    const PASSWORD_FAILURE = 'passwordFailure';

    // Demande d'un nouveau lien
    const REQUEST_NEW_CONFIRMATION_LINK = 'requestNewConfirmationLink';
    const REQUEST_NEW_PASSWORD = 'requestNewPassword';
}