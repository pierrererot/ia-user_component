<?php

namespace app\modules\user\models\query;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\Password]].
 *
 * @see \app\modules\user\models\Password
 */
class PasswordQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\modules\user\models\Password[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\modules\user\models\Password|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
