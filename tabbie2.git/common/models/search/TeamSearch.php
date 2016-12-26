<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Team;
use \yii\helpers\ArrayHelper;

/**
 * TeamSearch represents the model behind the search form about `common\models\Team`.
 */
class TeamSearch extends Team
{

	public $speakerName;
	public $societyName;

	/**
	 * @var int
	 */
	public $tournament_id;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'active', 'tournament_id', 'speakerA_id', 'speakerB_id', 'language_status'], 'integer'],
			[['name', 'speakerName', 'societyName'], 'string', 'max' => 255],
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
		$query = Team::find()
			->joinWith("speakerA")
			->joinWith("speakerB")
			->where(["tournament_id" => $this->tournament_id]);


        if (!$this->societyName) {
            $query->joinWith("society");
        }

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => Yii::$app->params["teams_per_page"],
			],
		]);

		$dataProvider->setSort([
			'attributes' => [
				'id',
				'active',
				'name',
				'language_status',
				'speakerName',
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


		// filter by user name
		$query->andWhere(["like", "CONCAT(uA.givenname, ' ', uA.surename)", $this->speakerName]);
		$query->orWhere(["like", "CONCAT(uB.givenname, ' ', uB.surename)", $this->speakerName]);

        // filter by society name
        if ($this->societyName) {
            $query->joinWith(['society' => function ($q) {
                $q->where(['like', 'society.fullname', $this->societyName]);
            }]);
        }

		$query->andFilterWhere(['id' => $this->id]);
		$query->andFilterWhere(['active' => $this->active]);
		$query->andFilterWhere(['team.language_status' => $this->language_status]);
		$query->andFilterWhere(['like', 'name', $this->name]);

		//Filter for Tournament scope
		//@todo Unknow why this line is neccessary -> siehe self:50
		$query->andWhere(["tournament_id" => $this->tournament_id]);

		//echo $query->createCommand()->sql;

		return $dataProvider;
	}

    public static function getSearchArray($tid, $keys = false)
	{
		$teams = Team::find()->where(["tournament_id" => $tid])->asArray()->all();

        if ($keys)
            return ArrayHelper::map($teams, "id", "name");
        else
            return ArrayHelper::map($teams, "name", "name");
	}

	public static function getSpeakerSearchArray($tid)
	{
		$users = \common\models\User::find()
			->select(["user.id", "CONCAT(user.givenname, ' ', user.surename) as username"])
			->join("INNER JOIN", "team tA", "tA.speakerA_id = user.id")
			->where(["tA.tournament_id" => $tid])
			->union(
				\common\models\User::find()
					->select(["user.id", "CONCAT(user.givenname, ' ', user.surename) as username"])
					->join("INNER JOIN", "team tB", "tB.speakerB_id = user.id")
					->where(["tB.tournament_id" => $tid])
			);

		return ArrayHelper::map($users->asArray()->all(), "username", "username");
	}

}
