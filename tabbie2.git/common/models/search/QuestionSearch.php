<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\question;

/**
 * QuestionSearch represents the model behind the search form about `\common\models\question`.
 */
class QuestionSearch extends question {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'type', 'apply_T2C', 'apply_C2W', 'apply_W2C'], 'integer'],
            [['text'], 'safe'],
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
    public function search($params, $tournament_id) {
        $query = question::find()->joinWith("tournamentHasQuestion");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andWhere(["tournament_id" => $tournament_id]);

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'apply_T2C' => $this->apply_T2C,
            'apply_C2W' => $this->apply_C2W,
            'apply_W2C' => $this->apply_W2C,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }

}
