<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Debate;

/**
 * DebateSearch represents the model behind the search form about `\common\models\Debate`.
 */
class DebateSearch extends Debate
{

	public $venue;
	public $adjudicator;
	public $team;
	public $language_status;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'round_id', 'tournament_id', 'og_team_id', 'oo_team_id', 'cg_team_id', 'co_team_id', 'panel_id', 'venue_id', 'og_feedback', 'oo_feedback', 'cg_feedback', 'co_feedback'], 'integer'],
			[['venue', 'team', 'adjudicator'], 'string'],
			[['time', 'highestPoints', 'language_status'], 'safe'],
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
	public function search($params, $tid, $rid, $displayed = true)
	{
		$query = Debate::find()
			->leftJoin("venue", "venue.id = debate.venue_id")
			->leftJoin("team as ogteam", "ogteam.id = debate.og_team_id")
			->leftJoin("team as ooteam", "ooteam.id = debate.oo_team_id")
			->leftJoin("team as cgteam", "cgteam.id = debate.cg_team_id")
			->leftJoin("team as coteam", "coteam.id = debate.co_team_id")
			->leftJoin("adjudicator_in_panel", "adjudicator_in_panel.panel_id = debate.panel_id")
			->leftJoin("adjudicator", "adjudicator.id = adjudicator_in_panel.adjudicator_id")
			->leftJoin("user", "user.id = adjudicator.user_id")
			->where(["debate.tournament_id" => $tid, "debate.round_id" => $rid]);

		if (!$displayed)
			$query->andWhere(["displayed" => 0]);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			/*'pagination' => [
				'pageSize' => PHP_INT_MAX,
			],*/
		]);

		$dataProvider->setSort([
			'defaultOrder' => ['highestPoints' => SORT_ASC],
			'attributes'   => [
				'venue'           => [
					'asc'  => ['CHAR_LENGTH(venue.name), venue.name' => SORT_ASC],
					'desc' => ['CHAR_LENGTH(venue.name) DESC, venue.name' => SORT_DESC],
				],
				'og_team.name'    => [
					'asc'  => ['ogteam.name' => SORT_ASC],
					'desc' => ['ogteam.name' => SORT_DESC],
				],
				'oo_team.name'    => [
					'asc'  => ['ooteam.name' => SORT_ASC],
					'desc' => ['ooteam.name' => SORT_DESC],
				],
				'cg_team.name'    => [
					'asc'  => ['cgteam.name' => SORT_ASC],
					'desc' => ['cgteam.name' => SORT_DESC],
				],
				'co_team.name'    => [
					'asc'  => ['coteam.name' => SORT_ASC],
					'desc' => ['coteam.name' => SORT_DESC],
				],
				'language_status' => [
					'asc'  => ['GREATEST(ogteam.language_status, ooteam.language_status, cgteam.language_status, coteam.language_status)' => SORT_ASC],
					'desc' => ['GREATEST(ogteam.language_status, ooteam.language_status, cgteam.language_status, coteam.language_status)' => SORT_DESC],
				],
				'highestPoints'   => [
					'asc'  => ['GREATEST(ogteam.points, ooteam.points, cgteam.points, coteam.points)' => SORT_DESC],
					'desc' => ['GREATEST(ogteam.points, ooteam.points, cgteam.points, coteam.points)' => SORT_ASC],
				],
			]
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'id'            => $this->id,
			'round_id'      => $this->round_id,
			'tournament_id' => $this->tournament_id,
			'og_team_id'    => $this->og_team_id,
			'oo_team_id'    => $this->oo_team_id,
			'cg_team_id'    => $this->cg_team_id,
			'co_team_id'    => $this->co_team_id,
			'panel_id'      => $this->panel_id,
			'venue_id'      => $this->venue_id,
			'og_feedback'   => $this->og_feedback,
			'oo_feedback'   => $this->oo_feedback,
			'cg_feedback'   => $this->cg_feedback,
			'co_feedback'   => $this->co_feedback,
			'time'          => $this->time,
		]);

		$query->andWhere("ogteam.name LIKE '%" . $params["DebateSearch"]["team"] . "%' OR " .
			"ooteam.name LIKE '%" . $params["DebateSearch"]["team"] . "%' OR " .
			"cgteam.name LIKE '%" . $params["DebateSearch"]["team"] . "%' OR " .
			"coteam.name LIKE '%" . $params["DebateSearch"]["team"] . "%'"
		);

		if ($this->language_status)
			$query->andWhere("ogteam.language_status = " . $this->language_status . " OR " .
				"ooteam.language_status = " . $this->language_status . " OR " .
				"cgteam.language_status = " . $this->language_status . " OR " .
				"coteam.language_status = " . $this->language_status
			);

		$query->andWhere(["like", "CONCAT(user.givenname, ' ', user.surename)", $params["DebateSearch"]["adjudicator"]]);
		$query->andWhere(["like", "venue.name", $params["DebateSearch"]["venue"]]);

		$query->andWhere(["round_id" => $rid]);

		return $dataProvider;
	}

	public static function getAdjudicatorSearchArray($tournamentid)
	{

		$adjs = \common\models\Adjudicator::find()->where(["tournament_id" => $tournamentid])->all();
		$filter = [];
		foreach ($adjs as $adj) {
			$filter[$adj->name] = $adj->name;
		}

		return $filter;
	}

	public static function getTeamSearchArray($tournamentid)
	{

		$teams = \common\models\Team::find()->where(["tournament_id" => $tournamentid])->all();

		$filter = [];
		foreach ($teams as $team) {
			$filter[$team->name] = $team->name;
		}

		return $filter;
	}

}
