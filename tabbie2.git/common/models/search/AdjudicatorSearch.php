<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Adjudicator;

/**
 * AdjudicatorSearch represents the model behind the search form about `common\models\Adjudicator`.
 */
class AdjudicatorSearch extends Adjudicator {

    public $tournament_id;
    public $judge_name;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'strength'], 'integer'],
            ['judge_name', 'string', 'max' => 255]
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
    public function search($params) {
        $query = Adjudicator::find()->joinWith("user")->where(["tournament_id" => $this->tournament_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'strength' => $this->strength,
        ]);
        $query->andFilterWhere(["like", "CONCAT(user.givenname, ' ', user.surename)", $this->judge_name]);

        return $dataProvider;
    }

}
