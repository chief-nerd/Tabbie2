<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Venue;

/**
 * VenueSearch represents the model behind the search form about `\common\models\Venue`.
 */
class VenueSearch extends Venue {

	/**
	 * @var int
	 */
	public $tournament_id;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
            [['active'], 'integer'],
            [['name', 'group'], 'string', 'max' => 255],
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
		$query = Venue::find()->tournament($this->tournament_id)->orderBy(["group" => SORT_ASC, "name" => SORT_ASC]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => Yii::$app->params["venues_per_page"],
			],
		]);

        $dataProvider->setSort([
            'attributes' => [
                'name',
                'group',
                'active'
            ]
        ]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'active' => $this->active,
		]);

		$query->andFilterWhere(['like', 'name', $this->name]);
		$query->andFilterWhere(['like', 'group', $this->group]);

		return $dataProvider;
	}

	public static function getSearchArray($tid, $keys = false) {

		$venues = Venue::find()->where(["tournament_id" => $tid])->asArray()->all();

		$filter = [];
		foreach ($venues as $v) {
			if ($keys)
				$filter[$v["id"]] = $v["name"];
			else
				$filter[$v["name"]] = $v["name"];
		}
		return $filter;
	}

}
