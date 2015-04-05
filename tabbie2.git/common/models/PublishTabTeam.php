<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "publish_tab_team".
 *
 * @property integer    $id
 * @property integer    $tournament_id
 * @property integer    $team_id
 * @property integer    $enl_place
 * @property integer    $esl_place
 * @property string     $cache_results
 * @property integer    $speaks
 * @property Team       $team
 * @property Tournament $tournament
 */
class PublishTabTeam extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'publish_tab_team';
	}

	/**
	 * @inheritdoc
	 * @return TournamentQuery
	 */
	public static function find() {
		return new TournamentQuery(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['tournament_id', 'team_id'], 'required'],
			[['tournament_id', 'team_id', 'enl_place', 'esl_place', 'speaks'], 'integer'],
			[['cache_results'], 'string']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'ID'),
			'tournament_id' => Yii::t('app', 'Tournament ID'),
			'team_id' => Yii::t('app', 'Team ID'),
			'enl_place' => Yii::t('app', 'ENL Place'),
			'esl_place' => Yii::t('app', 'ESL Place'),
			'cache_results' => Yii::t('app', 'Cache Results'),
			'speaks' => Yii::t('app', 'Speaker Points'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTeam() {
		return $this->hasOne(Team::className(), ['id' => 'team_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournament() {
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}

	/**
	 * @param Tournament $_tournament
	 *
	 * @return \common\models\TabLine[]
	 */
	public static function generateTeamTab($_tournament) {
		$results = \common\models\Result::find()->leftJoin("debate", "debate.id = result.debate_id")->where([
			"debate.tournament_id" => $_tournament->id,
		])->all();

		$teams = \common\models\Team::find()->where(["tournament_id" => $_tournament->id])->all();

		$lines = [];

		foreach ($teams as $team) {
			$lines[$team->id] = new \common\models\TabLine([
				"object" => $team,
				"points" => 0,
				"speaks" => 0,
			]);
		}

		foreach ($results as $result) {
			/* @var $result \common\models\Result */
			foreach (\common\models\Debate::positions() as $p) {
				$line = $lines[$result->debate->{$p . "_team_id"}];
				$line->points = $line->points + (4 - $result->{$p . "_place"});
				$line->results_array[$result->debate->round->number] = $result->{$p . "_place"};
				$line->speaks = $line->speaks + $result->{$p . "_A_speaks"} + $result->{$p . "_B_speaks"};
				$lines[$result->debate->{$p . "_team_id"}] = $line;
			}
		}

		usort($lines, 'common\models\PublishTabTeam::rankTeamsWithSpeaks');

		$i = 1;
		$jumpover = 0;
		foreach ($lines as $index => $line) {
			if (isset($lines[$index - 1]))
				if (!($lines[$index - 1]->points == $lines[$index]->points && $lines[$index - 1]->speaks == $lines[$index]->speaks)) {
					$i++;
					if ($jumpover > 0) {
						$i = $i + $jumpover;
						$jumpover = 0;
					}
				}
				else {
					$jumpover++;
				}
			$line->enl_place = $i;
			$lines[$index] = $line;
		}

		if ($_tournament->has_esl) {
			$i = 0;
			$jumpover = 0;
			foreach ($lines as $index => $line) {
				if ($line->object->language_status >= User::LANGUAGE_ESL) {
					if (isset($lines[$index - 1])) {
						if (!($lines[$index - 1]->points == $lines[$index]->points && $lines[$index - 1]->speaks == $lines[$index]->speaks)) {
							$i++;
							if ($jumpover > 0) {
								$i = $i + $jumpover;
								$jumpover = 0;
							}
						}
						else {
							$jumpover++;
						}
					}
					else $i++;

					$line->esl_place = $i;
					$lines[$index] = $line;
				}
			}
		}

		return $lines;
	}

	/**
	 * Rank Teams by points and then by Speaker points
	 *
	 * @param $a
	 * @param $b
	 *
	 * @using rankSpeaker
	 * @return int
	 */
	public function rankTeamsWithSpeaks($a, $b) {
		$ap = $a["points"];
		$as = $a["speaks"];
		$bp = $b["points"];
		$bs = $b["speaks"];
		return ($ap < $bp) ? 1 : (($ap > $bp) ? -1 : PublishTabSpeaker::rankSpeaker($a, $b));
	}

}
