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
    public $name;
    public $societyName;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['id', 'integer'],
            [['societyName', 'strength'], 'safe'],
            ['name', 'string', 'max' => 255]
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
        $query = Adjudicator::find()->where(["tournament_id" => $this->tournament_id]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'name' => [
                    'asc' => ['user.surename' => SORT_ASC],
                    'desc' => ['user.surename' => SORT_DESC],
                    'label' => 'Name'
                ],
                'strength',
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

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        switch (substr($this->strength, 0, 1)) {
            case ">":
            case "<":
                $query->andWhere("strength $this->strength");
                break;
            default:
                $query->andFilterWhere([
                    'strength' => $this->strength
                ]);
                break;
        }

        // filter by user name
        $query->joinWith(['user' => function ($q) {
                $q->where('CONCAT(user.givenname, " ", user.surename) LIKE "%' . $this->name . '%"');
            }]);

        // filter by society name
        $query->joinWith(['society' => function ($q) {
                $q->where('society.fullname LIKE "%' . $this->societyName . '%"');
            }]);

        return $dataProvider;
    }

    public static function getSearchArray($tid) {
        $adjudicators = Adjudicator::find()->joinWith("user")->where(["tournament_id" => $tid])->all();
        foreach ($adjudicators as $a) {
            $filter[$a->name] = $a->name;
        }
        return $filter;
    }

}
