<?php

namespace app\modules\user\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\Profile]].
 *
 * @see \app\modules\user\models\Profile
 */
class ProfileQuery extends ActiveQuery
{

    /**
     * @inheritdoc
     * @return \app\modules\user\models\Profile[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\models\Profile|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
