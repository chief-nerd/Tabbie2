<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Team;
use \yii\helpers\ArrayHelper;

/**
 * TeamSearch represents the model behind the search form about `common\models\Team`.
 */
class TeamSearch extends Team {

    public $speakerName;
    public $societyName;

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
            [['name', 'speakerName', 'societyName'], 'string', 'max' => 255],
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

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'name',
                'speakerName',
                'societyName' => [
                    'asc' => ['society.fullname' => SORT_ASC],
                    'desc' => ['society.fullname' => SORT_DESC],
                    'label' => 'Society Name'
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        // filter by user name
        $query->where("(CONCAT(uA.givenname, ' ', uA.surename) LIKE '%" . $this->speakerName . "%') OR (CONCAT(uB.givenname, ' ', uB.surename) LIKE '%" . $this->speakerName . "%')");

        // filter by society name
        $query->joinWith(['society' => function ($q) {
                $q->where('society.fullname LIKE "%' . $this->societyName . '%"');
            }]);

        return $dataProvider;
    }

    public static function getSearchArray($tid) {
        $teams = Team::find()->where(["tournament_id" => $tid])->asArray()->all();

        return ArrayHelper::map($teams, "name", "name");
    }

    public static function getSpeakerSearchArray($tid) {
        $users = \common\models\User::find()
                ->select(["user.id", "CONCAT(user.givenname, ' ', user.surename) as username"])
                ->join("INNER JOIN", "team tA", "tA.speakerA_id = user.id")
                ->where(["tA.tournament_id" => $tid])
                ->union(
                \common\models\User::find()
                ->select(["user.id", "CONCAT(user.givenname, ' ', user.surename) as username"])
                ->join("INNER JOIN", "team tB", "tB.speakerB_id = user.id")
                ->where(["tB.tournament_id" => $tid])
        );
        return ArrayHelper::map($users->asArray()->all(), "username", "username");
    }

}
