<?php

namespace app\modules\user\models\query;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\AuthItemChild]].
 *
 * @see \app\modules\user\models\AuthItemChild
 */
class AuthItemChildQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\modules\user\models\AuthItemChild[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\models\AuthItemChild|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
