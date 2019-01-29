<?php

namespace app\modules\user\models\search;

use app\modules\user\models\Token;
use app\modules\user\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TokenSearch represents the model behind the search form about `app\modules\user\models\Token`.
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $code
 * @property integer $type
 * @property string $data
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class TokenSearch extends Token
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type'], 'integer'],
            [['code', 'data'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params)
    {
        /** @var Token $model */
        $model = Yii::createObject('user/Token');
        $query = $model->find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'type' => $this->type,
            'code' => $this->code,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);
        return $dataProvider;
    }
}
