<?php

namespace app\modules\user\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\Token]].
 *
 * @see \app\modules\user\models\Token
 */
class TokenQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\modules\user\models\Token[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\models\Token|array|null
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
     * @param string $code
     * @return $this
     */
    public function byCode($code)
    {
        return $this->andWhere(['code' => $code]);
    }

    /**
     * @param int $type
     * @return $this
     */
    public function byType($type)
    {
        return $this->andWhere(['type' => $type]);
    }

    /**
     * @param string $sqlDate
     * @return $this
     */
    public function createdAfter($sqlDate)
    {
        return $this->andWhere(['>', 'created_at', $sqlDate]);
    }
}
