<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Adjudicator;

/**
 * AdjudicatorSearch represents the model behind the search form about `common\models\Adjudicator`.
 */
class AdjudicatorSearch extends Adjudicator
{

	public $tournament_id;
	public $name;
	public $societyName;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'active', 'can_chair', 'are_watched'], 'integer'],
			[['societyName', 'strength'], 'safe'],
			['name', 'string', 'max' => 255]
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
		$query = Adjudicator::find()
			->joinWith("user", ["user.id" => "adjudicator.user_id"])
			->joinWith("society", ["society.id" => "adjudicator.society_id"])
			->where(["adjudicator.tournament_id" => $this->tournament_id]);


		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => Yii::$app->params["adjudicators_per_page"],
			],
		]);

		$dataProvider->setSort([
			'attributes' => [
				'id',
				'active',
				'can_chair',
				'are_watched',
				'name'        => [
					'asc'   => ['user.surename' => SORT_ASC],
					'desc'  => ['user.surename' => SORT_DESC],
					'label' => 'Name'
				],
				'strength',
				'societyName' => [
					'asc'   => ['society.fullname' => SORT_ASC],
					'desc'  => ['society.fullname' => SORT_DESC],
					'label' => 'Society Name'
				]
			]
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id'          => $this->id,
			'active'      => $this->active,
			'are_watched' => $this->are_watched,
			'can_chair'   => $this->can_chair,
		]);

		switch (substr($this->strength, 0, 1)) {
			case ">":
			case "<":
				$query->andWhere("strength $this->strength");
				break;
			default:
				$query->andFilterWhere([
					'strength' => $this->strength
				]);
				break;
		}

		// filter by user name
		if ($this->name)
			$query->andWhere('CONCAT(user.givenname, " ", user.surename) LIKE "%' . $this->name . '%"');

		if ($this->societyName)
			$query->andWhere('society.fullname LIKE "%' . $this->societyName . '%"');

		return $dataProvider;
	}

	public static function getSearchArray($tid)
	{
		$adjudicators = Adjudicator::find()->joinWith("user")->where(["tournament_id" => $tid])->all();
		$filter = [];
		foreach ($adjudicators as $a) {
			$filter[$a->name] = $a->name;
		}

		return $filter;
	}

}
