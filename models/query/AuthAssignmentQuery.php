<?php

namespace app\modules\user\models\query;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\AuthAssignment]].
 *
 * @see \app\modules\user\models\AuthAssignment
 */
class AuthAssignmentQuery extends ActiveQuery
{

    /**
     * @inheritdoc
     * @return \app\modules\user\models\AuthAssignment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\models\AuthAssignment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function byUser($userId)
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    /**
     * @param string $item
     * @return $this
     */
    public function byAuthItem($item)
    {
        return $this->andWhere(['item_name' => $item]);
    }

    ////////////////////////////////////////////
    ////////////////////////////////////////////
    ////////////////////////////////////////////

    /**
     * @param int $userId
     * @return int
     */
    public static function deleteByUser($userId)
    {
        return Yii::$app->db
            ->createCommand("DELETE FROM auth_assignment WHERE user_id = :id")
            ->bindParam('id', $userId)
            ->execute();
    }
}
