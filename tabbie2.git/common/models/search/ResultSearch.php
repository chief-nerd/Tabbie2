<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Result;

/**
 * ResultSearch represents the model behind the search form about `\common\models\Result`.
 */
class ResultSearch extends Result
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'debate_id', 'og_speaks', 'og_place', 'oo_speaks', 'oo_place', 'cg_speaks', 'cg_place', 'co_speaks', 'co_place'], 'integer'],
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
        $query = Result::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'debate_id' => $this->debate_id,
            'og_speaks' => $this->og_speaks,
            'og_place' => $this->og_place,
            'oo_speaks' => $this->oo_speaks,
            'oo_place' => $this->oo_place,
            'cg_speaks' => $this->cg_speaks,
            'cg_place' => $this->cg_place,
            'co_speaks' => $this->co_speaks,
            'co_place' => $this->co_place,
            'time' => $this->time,
        ]);

        return $dataProvider;
    }
}
