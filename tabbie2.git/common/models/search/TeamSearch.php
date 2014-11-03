<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Team;

/**
 * TeamSearch represents the model behind the search form about `common\models\Team`.
 */
class TeamSearch extends Team {

    public $speaker_name;

    /**
     * @var int
     */
    public $tournament_id;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'tournament_id', 'speakerA_id', 'speakerB_id'], 'integer'],
            [['name', 'speaker_name'], 'string', 'max' => 255],
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
        $query = Team::find()->joinWith("speakerA")->joinWith("speakerB")->where(["tournament_id" => $this->tournament_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andWhere("(CONCAT(uA.givenname, ' ', uA.surename) LIKE '%" . $this->speaker_name . "%') OR (CONCAT(uB.givenname, ' ', uB.surename) LIKE '%" . $this->speaker_name . "%')");

        //print_r($query);

        return $dataProvider;
    }

}
