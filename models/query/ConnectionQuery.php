<?php

namespace app\modules\user\models\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\Connection]].
 *
 * @see \app\modules\user\models\Connection
 */
class ConnectionQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\modules\user\models\Connection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\modules\user\models\Connection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function byUsername($name)
    {
        return $this->andWhere(['username' => $name]);
    }

    /**
     * @param string $ip
     * @return $this
     */
    public function byIP($ip)
    {
        return $this->andWhere(['from_ip' => $ip]);
    }

    /**
     * @param int $status @see /lib/enums/ConnectionStatus
     * @return $this
     */
    public function byStatus($status)
    {
        return $this->andWhere(['status' => $status]);
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
