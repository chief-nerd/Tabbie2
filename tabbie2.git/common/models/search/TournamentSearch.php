<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Tournament;

/**
 * TournamentSearch represents the model behind the search form about `common\models\Tournament`.
 */
class TournamentSearch extends Tournament
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'convenor_user_id', 'tabmaster_user_id'], 'integer'],
            [['name', 'start_date', 'end_date', 'logo', 'time'], 'safe'],
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
        $query = Tournament::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'convenor_user_id' => $this->convenor_user_id,
            'tabmaster_user_id' => $this->tabmaster_user_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'logo', $this->logo]);

        return $dataProvider;
    }
}
