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
			[['id', 'tournament_id', 'active'], 'integer'],
			[['name'], 'safe'],
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
		$query = Venue::find()->tournament($this->tournament_id);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => Yii::$app->params["venues_per_page"],
			],
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id' => $this->id,
			'tournament_id' => $this->tournament_id,
			'active' => $this->active,
		]);

		$query->andFilterWhere(['like', 'name', $this->name]);

		return $dataProvider;
	}

	public function getSearchArray($tid, $keys = false) {

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
