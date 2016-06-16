<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Feedback;

/**
 * FeedbackSearch represents the model behind the search form about `\common\models\feedback`.
 */
class FeedbackSearch extends Feedback
{
    public $to;
    public $from;
	public $round_number;
	public $venue_name;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'debate_id', 'round_number'], 'integer'],
            [['venue_name', 'to', 'from'], 'string'],
			[['time'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
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
	public function search($params, $tournament_id)
	{
		$query = Feedback::find()->joinWith(['debate' => function ($query) {
			$query->joinWith(['round']);
			$query->joinWith(['venue']);
		}])->where(["debate.tournament_id" => $tournament_id]);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => 20,
			],
		]);

		$dataProvider->setSort([
			'defaultOrder' => ['time' => SORT_DESC],
			'attributes'   => [
				'venue_name'   => [
					'asc'  => ['CHAR_LENGTH(venue.name), venue.name' => SORT_ASC],
					'desc' => ['CHAR_LENGTH(venue.name) DESC, venue.name' => SORT_DESC],
				],
				'round_number' => [
                    'asc' => ['round.label' => SORT_ASC],
                    'desc' => ['round.label' => SORT_DESC],
				],
				'time' => [
					'asc'  => ['time' => SORT_ASC],
					'desc' => ['time' => SORT_DESC],
				]
			]
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id'                   => $this->id,
			'debate_id'            => $this->debate_id,
			'time'                 => $this->time,
            'round.label' => $this->round_number,
			'debate.tournament_id' => $tournament_id,
		]);

		$query->andWhere(['like', 'venue.name', $this->venue_name]);

		return $dataProvider;
	}
}
