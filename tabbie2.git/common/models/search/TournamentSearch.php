<?php

namespace common\models\search;

use common\models\Round;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Tournament;

/**
 * TournamentSearch represents the model behind the search form about `common\models\Tournament`.
 */
class TournamentSearch extends Tournament
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'convenor_user_id', 'tabmaster_user_id'], 'integer'],
			[['name', 'start_date', 'end_date', 'logo', 'time'], 'safe'],
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
	public function search($params)
	{
		$query = Tournament::find()->joinWith('hostedby')->where("end_date >= now()");

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => Yii::$app->params["tournament_per_page"],
			],
		]);

		$dataProvider->setSort([
			'defaultOrder' => ['start_date' => SORT_ASC],
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id'                => $this->id,
			'convenor_user_id'  => $this->convenor_user_id,
			'tabmaster_user_id' => $this->tabmaster_user_id,
			'start_date'        => $this->start_date,
			'end_date'          => $this->end_date,
			'time'              => $this->time,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'logo', $this->logo]);

		return $dataProvider;
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function searchArchive($params)
	{
		$query = Tournament::find()->joinWith('hostedby')->where("end_date < now()");

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => Yii::$app->params["tournament_per_page"],
			],
		]);

		$dataProvider->setSort([
			'defaultOrder' => ['start_date' => SORT_DESC],
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id'                => $this->id,
			'convenor_user_id'  => $this->convenor_user_id,
			'tabmaster_user_id' => $this->tabmaster_user_id,
			'start_date'        => $this->start_date,
			'end_date'          => $this->end_date,
			'time'              => $this->time,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'logo', $this->logo]);

		return $dataProvider;
	}

	public function getRoundOptions($tournament)
	{
		$t = Round::find()->where(["tournament_id" => $tournament])->asArray()->all();

		$filter = [];
		foreach ($t as $v) {
			$filter[$v["number"]] = $v["number"];
		}

		return $filter;
	}
}
