<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Panel;

/**
 * PanelSearch represents the model behind the search form about `\common\models\Panel`.
 */
class PanelSearch extends Panel {
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'strength', 'tournament_id', 'used', 'is_preset'], 'integer'],
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
	public function search($params, $tournament_id) {
		$query = Panel::find()->where(["tournament_id" => $tournament_id, "is_preset" => true, 'used' => 0]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id' => $this->id,
			'strength' => $this->strength,
			'time' => $this->time,
			'tournament_id' => $this->tournament_id,
			'used' => $this->used,
			'is_preset' => $this->is_preset,
		]);

		return $dataProvider;
	}
}
