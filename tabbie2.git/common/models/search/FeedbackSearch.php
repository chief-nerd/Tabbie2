<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Feedback;

/**
 * FeedbackSearch represents the model behind the search form about `\common\models\feedback`.
 */
class FeedbackSearch extends Feedback {

	public $round_number;
	public $venue_name;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'debate_id', 'round_number'], 'integer'],
			['venue_name', 'string'],
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
	public function search($params) {
		$query = Feedback::find()->joinWith(['debate' => function ($query) {
			$query->joinWith(['round']);
			$query->joinWith(['venue']);
		}]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 20,
			],
		]);

		$dataProvider->setSort([
			'defaultOrder' => ['venue_name' => SORT_ASC],
			'attributes' => [
				'venue_name' => [
					'asc' => ['CHAR_LENGTH(venue.name), venue.name' => SORT_ASC],
					'desc' => ['CHAR_LENGTH(venue.name) DESC, venue.name' => SORT_DESC],
				],
				'round_number' => [
					'asc' => ['round.number' => SORT_ASC],
					'desc' => ['round.number' => SORT_DESC],
				],
			]
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id' => $this->id,
			'debate_id' => $this->debate_id,
			'time' => $this->time,
			'round.number' => $this->round_number,
		]);

		$query->andWhere(['like', 'venue.name', $this->venue_name]);

		return $dataProvider;
	}
}
