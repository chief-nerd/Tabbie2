<?php

namespace common\models\search;

use common\models\Country;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Society;

/**
 * SocietySearch represents the model behind the search form about `\common\models\Society`.
 */
class SocietySearch extends Society {

	public $country_name;
	public $country_region;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id'], 'integer'],
			[['fullname', 'abr', 'city', 'country_name', 'country_region'], 'string'],
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
		$query = Society::find()->joinWith("country");

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$dataProvider->setSort([
			'attributes' => [
				'fullname',
				'abr',
				'city',
				'country_name' => [
					'asc' => ['country.name' => SORT_ASC],
					'desc' => ['country.name' => SORT_DESC],
					'label' => 'Country'
				],
				'country_region' => [
					'asc' => ['country.region_id' => SORT_ASC],
					'desc' => ['country.region_id' => SORT_DESC],
					'label' => 'Region'
				]
			]
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id' => $this->id,
		]);

		$query->andFilterWhere(['like', 'fullname', $this->fullname])
		      ->andFilterWhere(['like', 'abr', $this->abr])
			->andFilterWhere(['like', 'city', $this->city]);

		$query->joinWith(['country' => function ($q) {
			$q->where(['like', 'country.name', $this->country_name]);
		}]);

		if ($this->country_region) {
			$query->joinWith(['country' => function ($q) {

				$region = Country::getRegionLabel();
				$keys = [];
				foreach ($region as $k => $r) {
					if (strstr($r, $this->country_region) !== false)
						$keys[] = $k;
				}

				$q->where(['in', 'region_id', $keys]);
			}]);
		}

		return $dataProvider;
	}

	public static function getTournamentSearchArray($tid) {
		$tournament = \common\models\Tournament::findByPk($tid);
		return \yii\helpers\ArrayHelper::map($tournament->societies, "fullname", "fullname");
	}

	public static function getSearchArray() {
		$society = Society::find()->asArray()->all();
		return \yii\helpers\ArrayHelper::map($society, "id", "fullname");
	}
}
