<?php

namespace app\modules\user\models\query;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\User]].
 *
 * @see \app\modules\user\models\User
 */
class UserQuery extends ActiveQuery
{

    /**
     * @inheritdoc
     * @return \app\modules\user\models\User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\models\User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function byEmail($email)
    {
        return $this->andWhere(['email' => $email]);
    }

    /**
     * Sélectionne les utilisateurs possédant le droit $itemName
     *
     * @param string $itemName
     * @return $this
     */
    public function byAuthItem($itemName)
    {
        return $this->innerJoinWith('authorizations')->andWhere(['item_name' => $itemName]);
    }

    ///////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    /**
     * Remet à jour les flags sur l'utilisation du mot de passe : date de mise à jour, nombre d'utilisations
     *
     * @param int $userId
     * @param int $time Timestamp correspondant à l'heure de mise à jour du mot de passe
     * @return int
     * @throws \yii\db\Exception
     */
    public static function resetPasswordInformations($userId, $time)
    {
        $sqlDate = date('Y-m-d H:i:s', $time);
        $sql = "UPDATE user SET password_updated_at = '$sqlDate', password_usage = 0 WHERE id = :id";
        return Yii::$app->db->createCommand($sql, ['id' => $userId])->execute();
    }
}
