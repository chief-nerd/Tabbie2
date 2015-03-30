<?php

namespace common\models\search;

use common\models\Adjudicator;
use common\models\Tournament;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use yii\helpers\ArrayHelper;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User {

	/**
	 * Full Name for WHERE clausel
	 *
	 * @var String
	 */
	public $name;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['language_status'], 'integer'],
			[['name'], 'string'],
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
		$query = User::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => Yii::$app->params["users_per_page"],
			],
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id' => $this->id,
			'role' => $this->role,
			'status' => $this->status,
			'last_change' => $this->last_change,
			'time' => $this->time,
		]);

		$query->andFilterWhere(['like', 'username', $this->username])
		      ->andFilterWhere(['like', 'email', $this->email])
		      ->andFilterWhere(['like', "CONCAT(givenname, ' ', surename)", $this->name]);

		return $dataProvider;
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function searchTournament($params, $tournamentid) {
		$query = User::findByTournament($tournamentid);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => Yii::$app->params["users_per_page"],
			],
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$this->setFilter($query);

		foreach ($query->union as $union) {
			$this->setFilter($union["query"]);
		}

		return $dataProvider;
	}

	private function setFilter($query) {
		$query->andFilterWhere([
			'user.language_status' => $this->language_status,
		]);
		$query->andFilterWhere(['like', "CONCAT(givenname, ' ', surename)", $this->name]);
	}

	public static function getArray() {
		$user = User::find()->all();
		return ArrayHelper::map($user, "name", "name");
	}

	public function getSearchTournamentArray($tournamentid) {
		$user = User::findByTournament($tournamentid)->all();
		return ArrayHelper::map($user, "name", "name");
	}

}
