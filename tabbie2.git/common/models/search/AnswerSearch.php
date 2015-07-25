<?php

namespace common\models\search;

use common\models\Answer;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Feedback;

/**
 * FeedbackSearch represents the model behind the search form about `\common\models\feedback`.
 */
class AnswerSearch extends Feedback
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'debate_id'], 'integer'],
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
	public function searchByAdjudicator($params)
	{

		$query = Answer::find()
			->joinWith(['feedback' => function ($query) {
				$query->joinWith(['debate' => function ($query) {
					$query->joinWith(['adjudicators']);
				}]);
			}])
			->orderBy("question_id"); //->where(["adjudicator.id" => $params["AnswerSearch"]["id"]])->orderBy("question_id");

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => 9999,
			],
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'adjudicator.id' => $this->id,
		]);

		return $dataProvider;
	}
}
