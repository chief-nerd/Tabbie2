<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "publish_tab_speaker".
 *
 * @property integer    $id
 * @property integer    $tournament_id
 * @property integer    $user_id
 * @property string     $enl_place
 * @property string     $esl_place
 * @property string     $cache_results
 * @property User       $user
 * @property Tournament $tournament
 */
class PublishTabSpeaker extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'publish_tab_speaker';
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
			[['tournament_id', 'user_id'], 'required'],
			[['tournament_id', 'user_id', 'esl_place', 'enl_place', 'speaks'], 'integer'],
			[['cache_results'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'ID'),
			'tournament_id' => Yii::t('app', 'Tournament ID'),
			'user_id' => Yii::t('app', 'User ID'),
			'enl_place' => Yii::t('app', 'ENL Place'),
			'esl_place' => Yii::t('app', 'ESL Place'),
			'cache_results' => Yii::t('app', 'Cache Result'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'user_id']);
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
	public static function generateSpeakerTab($_tournament) {
		$results = Result::find()->leftJoin("debate", "debate.id = result.debate_id")->where([
			"debate.tournament_id" => $_tournament->id,
		])->all();

		$teams = Team::find()->where(["tournament_id" => $_tournament->id])->all();

		$lines = [];

		foreach ($teams as $team) {
			/** @var \common\models\Team $team */
			$lines[$team->speakerA_id] = new TabLine([
				"object" => $team->speakerA,
				"points" => 0,
				"speaks" => 0,
			]);
			$lines[$team->speakerB_id] = new TabLine([
				"object" => $team->speakerB,
				"points" => 0,
				"speaks" => 0,
			]);
		}

		foreach ($results as $result) {
			/* @var $result \common\models\Result */
			foreach (Team::getPos() as $p) {

				foreach (Team::getSpeaker() as $s) {
					$line = $lines[$result->debate->{$p . "_team"}->{"speaker" . $s . "_id"}];

					$line->points = $line->points + $result->getPoints($p);
					$line->results_array[$result->debate->round->number] = $result->getSpeakerSpeaks($p, $s);
					$line->speaks = $line->speaks + $result->getSpeakerSpeaks($p, $s);
					$lines[$result->debate->{$p . "_team"}->{"speaker" . $s . "_id"}] = $line;
				}
			}
		}

		usort($lines, 'common\models\PublishTabSpeaker::rankSpeaker');

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
					if ($line->object->id) {
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
		}

		return $lines;
	}

	/**
	 * Rank by Speaker Points
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public static function rankSpeaker($a, $b) {
		$as = $a["speaks"];
		$bs = $b["speaks"];
		return (($as < $bs) ? 1 : (($as > $bs) ? -1 : 0));
	}

}
