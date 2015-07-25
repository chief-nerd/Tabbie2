<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Tab;
use common\models\Debate;
use common\models\PublishTabTeam;

/**
 * SocietySearch represents the model behind the search form about `\common\models\Society`.
 */
class TabTeamSearch extends Tab
{

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['tournament_id'], 'integer'],
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
	public function search($tournament_id, $params)
	{
		$query = Debate::find()->where(["tournament_id" => $tournament_id]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id' => $this->id,
		]);

		return $dataProvider;
	}

}
