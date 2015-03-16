<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Society;

/**
 * SocietySearch represents the model behind the search form about `\common\models\Society`.
 */
class SocietySearch extends Society {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
	        [['fullname', 'abr', 'city', 'country'], 'safe'],
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
        $query = Society::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'fullname', $this->fullname])
	        ->andFilterWhere(['like', 'abr', $this->abr])
                ->andFilterWhere(['like', 'city', $this->city])
                ->andFilterWhere(['like', 'country', $this->country]);

        return $dataProvider;
    }

    public static function getSearchArray($tid) {
	    $tournament = \common\models\Tournament::findByPk($tid);
        return \yii\helpers\ArrayHelper::map($tournament->societies, "fullname", "fullname");
    }

}
