<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Debate;

/**
 * DebateSearch represents the model behind the search form about `\common\models\Debate`.
 */
class DebateSearch extends Debate {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'round_id', 'tournament_id', 'og_team_id', 'oo_team_id', 'cg_team_id', 'co_team_id', 'panel_id', 'venue_id', 'og_feedback', 'oo_feedback', 'cg_feedback', 'co_feedback'], 'integer'],
            [['time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params, $tid, $rid, $displayed = true) {
        $query = Debate::find()->where(["tournament_id" => $tid, "round_id" => $rid]);

        if (!$displayed)
            $query->andWhere(["displayed" => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 99999999999,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'round_id' => $this->round_id,
            'tournament_id' => $this->tournament_id,
            'og_team_id' => $this->og_team_id,
            'oo_team_id' => $this->oo_team_id,
            'cg_team_id' => $this->cg_team_id,
            'co_team_id' => $this->co_team_id,
            'panel_id' => $this->panel_id,
            'venue_id' => $this->venue_id,
            'og_feedback' => $this->og_feedback,
            'oo_feedback' => $this->oo_feedback,
            'cg_feedback' => $this->cg_feedback,
            'co_feedback' => $this->co_feedback,
            'time' => $this->time,
        ]);

        return $dataProvider;
    }

}
