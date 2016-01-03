<?php

namespace common\models;

use kartik\helpers\Html;
use Yii;
use yii\caching\DbDependency;
use yii\data\ArrayDataProvider;

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
class PublishTabSpeaker extends \yii\db\ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'publish_tab_speaker';
	}

	/**
	 * @inheritdoc
	 * @return TournamentQuery
	 */
	public static function find()
	{
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
			'id'            => Yii::t('app', 'ID'),
			'tournament_id' => Yii::t('app', 'Tournament') . ' ' . Yii::t('app', 'ID'),
			'user_id'       => Yii::t('app', 'User') . ' ' . Yii::t('app', 'ID'),
			'enl_place'     => Yii::t('app', 'ENL Place'),
			'esl_place'     => Yii::t('app', 'ESL Place'),
			'cache_results' => Yii::t('app', 'Cache Results'),
		];
	}

	public static function getDataProvider($_tournament, $live = false) {

		$key = $_tournament->cacheKey("speakerTabADP");
		$dependency = new DbDependency([
			"sql" => "SELECT count(*) FROM result LEFT JOIN debate ON result.debate_id = debate.id WHERE tournament_id = " . $_tournament->id
		]);
		$cache = Yii::$app->cache;

		$dataProvider = $cache->get($key);
		if ($dataProvider === false || $live) {

			$lines = PublishTabSpeaker::generateSpeakerTab($_tournament, $live);

			$attributes = [
				'enl_place',
				'esl_place',
				'efl_place',
				'novice_place',
				'points',
				'speaks',
					'avg',
				'object.speaker.name',
				'object.team.name',
			];

			foreach ($_tournament->inrounds as $r) {
				$attributes[] = 'results_array.' . $r->number;
			}

			$dataProvider = new ArrayDataProvider([
				'allModels' => $lines,
				'sort' => [
					'attributes' => $attributes,
				],
				'pagination' => [
					'pageSize' => 99999,
				],
			]);

			$cache->set($key, $dataProvider, 3600, $dependency);
		}
		return $dataProvider;
	}

	/**
	 * @param Tournament $_tournament
	 *
	 * @return \common\models\TabLine[]
	 */
	public static function generateSpeakerTab($_tournament, $live = false)
	{
		$key = $_tournament->cacheKey("speakerTab");
		$dependency = new DbDependency([
			"sql" => "SELECT count(*) FROM result LEFT JOIN debate ON result.debate_id = debate.id WHERE tournament_id = " . $_tournament->id
		]);
		$cache = Yii::$app->cache;

		$lines = $cache->get($key);
		if ($lines === false || $live) {

			$lines = [];

			$results = Result::find()
				->leftJoin("debate", "debate.id = result.debate_id")
				->leftJoin("round", "round.id = debate.round_id")
				->where([
					"debate.tournament_id" => $_tournament->id,
					"round.type"           => Round::TYP_IN,
				])->all();

			$teams = Team::find()->where(["tournament_id" => $_tournament->id])->all();


			foreach ($teams as $team) {
				/** @var \common\models\Team $team */
				if ($team->speakerA_id) {
					$lines[$team->speakerA_id] = new TabLine([
						"object" => ["team" => $team->toArray(), "speaker" => $team->speakerA->toArray()],
						"points" => 0,
						"speaks" => 0,
							"avg" => 0,
					]);
				}
				if ($team->speakerB_id) {
					$lines[$team->speakerB_id] = new TabLine([
						"object" => ["team" => $team->toArray(), "speaker" => $team->speakerB->toArray()],
						"points" => 0,
							"avg" => 0,
					]);
				}
			}

			foreach ($results as $result) {
				/* @var $result \common\models\Result */
				foreach (Team::getPos() as $p) {

					foreach (Team::getSpeaker() as $s) {
						if ($result->debate->{$p . "_team"}->{"speaker" . $s . "_id"}) {
							$line = $lines[$result->debate->{$p . "_team"}->{"speaker" . $s . "_id"}];

							$line->points = $line->points + $result->getPoints($p);
							$line->results_array[$result->debate->round->number] = $result->getSpeakerSpeaksText($p, $s);
							$line->speaks = $line->speaks + $result->getSpeakerSpeaks($p, $s);
							$line->avg = number_format($line->speaks / (count($line->results_array)), 1);

							$lines[$result->debate->{$p . "_team"}->{"speaker" . $s . "_id"}] = $line;
						}
					}
				}
			}

			usort($lines, 'common\models\PublishTabSpeaker::rankSpeaker');

			$i = 1;
			$jumpover = 0;
			$last_line = null;
			foreach ($lines as $index => $line) {
				if ($last_line !== null)
					if (!($lines[$last_line]->speaks == $lines[$index]->speaks)) {
						$i++;
						if ($jumpover > 0) {
							$i = $i + $jumpover;
							$jumpover = 0;
						}
					} else {
						$jumpover++;
					}

				$last_line = $index;
				$line->enl_place = $i;
				$lines[$index] = $line;
			}

			if ($_tournament->has_esl) {
				$i = 0;
				$jumpover = 0;
				$last_line = null;
				foreach ($lines as $index => $line) {
					if ($line->object["speaker"]["language_status"] >= User::LANGUAGE_ESL) {
						if ($line->object["speaker"]["id"]) {
							if ($last_line !== null) {
								if (!($lines[$last_line]->points == $lines[$index]->points &&
										$lines[$last_line]->speaks == $lines[$index]->speaks)
								) {
									$i++;
									if ($jumpover > 0) {
										$i = $i + $jumpover;
										$jumpover = 0;
									}
								} else {
									$jumpover++;
								}
							} else $i++;

							$last_line = $index;
							$line->esl_place = $i;
							$lines[$index] = $line;
						}
					}
				}
			}

			if ($_tournament->has_efl) {
				$i = 0;
				$jumpover = 0;
				$last_line = null;
				foreach ($lines as $index => $line) {
					if ($line->object["speaker"]["language_status"] >= User::LANGUAGE_EFL) {
						if ($line->object["speaker"]["id"]) {
							if ($last_line !== null) {
								if (!($lines[$last_line]->points == $lines[$index]->points &&
										$lines[$last_line]->speaks == $lines[$index]->speaks)
								) {
									$i++;
									if ($jumpover > 0) {
										$i = $i + $jumpover;
										$jumpover = 0;
									}
								} else {
									$jumpover++;
								}
							} else $i++;

							$last_line = $index;
							$line->efl_place = $i;
							$lines[$index] = $line;
						}
					}
				}
			}
			if ($_tournament->has_novice) {
				$i = 0;
				$jumpover = 0;
				$last_line = null;
				foreach ($lines as $index => $line) {
					if ($line->object["speaker"]["language_status"] == User::LANGUAGE_NOVICE) {
						if ($line->object["speaker"]["id"]) {
							if ($last_line !== null) {
								if (!($lines[$last_line]->points == $lines[$index]->points &&
										$lines[$last_line]->speaks == $lines[$index]->speaks)
								) {
									$i++;
									if ($jumpover > 0) {
										$i = $i + $jumpover;
										$jumpover = 0;
									}
								} else {
									$jumpover++;
								}
							} else $i++;

							$last_line = $index;
							$line->novice_place = $i;
							$lines[$index] = $line;
						}
					}
				}
			}

			$cache->set($key, $lines, 3600, $dependency);
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
	public static function rankSpeaker($a, $b)
	{
		$as = $a["speaks"];
		$bs = $b["speaks"];

		return (($as < $bs) ? 1 : (($as > $bs) ? -1 : 0));
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournament()
	{
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}

}
