<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\feedback;

/**
 * FeedbackSearch represents the model behind the search form about `\common\models\feedback`.
 */
class FeedbackSearch extends feedback
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'debate_id'], 'integer'],
            [['time'], 'safe'],
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
     */
    public function search($params)
    {
        $query = feedback::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'debate_id' => $this->debate_id,
            'time' => $this->time,
        ]);

        return $dataProvider;
    }
}
