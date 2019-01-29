<?php

namespace app\modules\user\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\Blacklist]].
 *
 * @see \app\modules\user\models\Blacklist
 */
class BlacklistQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\modules\user\models\Blacklist[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\modules\user\models\Blacklist|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return $this
     */
    public function enabled($enabled = true)
    {
        return $this->andWhere(['enabled' => $enabled]);
    }

    /**
     * @param string $ip
     * @return $this
     */
    public function byIP($ip)
    {
        return $this->andWhere(['ip' => $ip]);
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function sinceNbSeconds($seconds)
    {
        $sqlDate = date('Y-m-d H:i:s', time() - $seconds);
        return $this->andWhere("created_at >= '$sqlDate'");
    }
}
