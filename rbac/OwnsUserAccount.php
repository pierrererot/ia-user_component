<?php

namespace app\modules\user\rbac;

use yii\helpers\ArrayHelper;
use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Class OwnsUserAccount
 * @package app\modules\user\rbac
 *
 * Règle permettant de s'assurer que l'utilisateur est bien le propriétaire du compte
 */
class OwnsUserAccount extends Rule
{
    public $name = 'ownsUserAccount';

    /**
     * @param string|int $userId the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($userId, $item, $params)
    {
        $accountId = ArrayHelper::getValue($params, 'accountUserId');
        return $accountId == $userId;
    }

}
