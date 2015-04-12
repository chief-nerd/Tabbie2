<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "tournament".

 *
*@property integer                  $id
 * @property string                   $url_slug
 * @property integer                  $status
 * @property integer                  $convenor_user_id
 * @property integer                  $tabmaster_user_id
 * @property integer                  $hosted_by_id
 * @property string                   $name
 * @property string                   $start_date
 * @property string                   $end_date
 * @property string                   $logo
 * @property string                   $time
 * @property string                   $tabAlgorithmClass
 * @property integer                  $expected_rounds
 * @property integer                  $has_esl
 * @property integer                  $has_final
 * @property integer                  $has_semifinal
 * @property integer                  $has_quarterfinal
 * @property integer                  $has_octofinal
 * @property Adjudicator[]            $adjudicators
 * @property Panel[]                  $panels
 * @property Round[]                  $rounds
 * @property Team[]                   $teams
 * @property User                     $convenorUser
 * @property User                     $tabmasterUser
 * @property TournamentHasQuestions[] $tournamentHasQuestions
 * @property Questions[]              $questions
 * @property Venue[]                  $venues
 */
class Tournament extends \yii\db\ActiveRecord {

	const STATUS_CREATED = 0;
	const STATUS_RUNNING = 1;
	const STATUS_CLOSED  = 2;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'tournament';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['url_slug', 'convenor_user_id', 'tabmaster_user_id', 'hosted_by_id', 'name', 'start_date', 'end_date'], 'required'],
			[['convenor_user_id', 'tabmaster_user_id', 'hosted_by_id', 'expected_rounds', 'status'], 'integer'],
			[['start_date', 'end_date', 'time', 'has_esl', 'has_final', 'has_semifinal', 'has_octofinal', 'has_quarterfinal'], 'safe'],
			[['url_slug', 'name', 'tabAlgorithmClass'], 'string', 'max' => 100],
			[['logo'], 'string', 'max' => 255],
			[['url_slug'], 'unique']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'Tournament ID'),
			'convenor_user_id' => Yii::t('app', 'Convenor'),
			'tabmaster_user_id' => Yii::t('app', 'Tabmaster'),
			'hosted_by_id' => Yii::t('app', 'Hosted by'),
			'name' => Yii::t('app', 'Tournament Name'),
			'start_date' => Yii::t('app', 'Start Date'),
			'end_date' => Yii::t('app', 'End Date'),
			'logo' => Yii::t('app', 'Logo'),
			'time' => Yii::t('app', 'Time'),
			'url_slug' => Yii::t('app', 'URL Slug'),
			'tabAlgorithmClass' => Yii::t('app', 'Tab Algorithm'),
			'expected_rounds' => Yii::t("app", "Expected number of rounds"),
			'has_esl' => Yii::t("app", "Show ESL Ranking"),
			'has_final' => Yii::t("app", 'Is there a grand final'),
			'has_semifinal' => Yii::t("app", 'Is there a semifinal'),
			'has_quarterfinal' => Yii::t("app", 'Is there a quarterfinal'),
			'has_octofinal' => Yii::t("app", 'Is there a octofinal'),
		];
	}

	public static function findByUrlSlug($slug) {
		return Tournament::findOne(["url_slug" => $slug]);
	}

	/**
	 * Find a Tournament by Primary Key

	 *
*@param integer $id

	 *
*@uses Tournamnet::findOne
	 * @return null|Tournamnet
	 */
	public static function findByPk($id) {
		$tournament = Yii::$app->cache->get("tournament" . $id);
		if (!$tournament instanceof Tournament) {
			$tournament = Tournament::findOne(["id" => $id]);
			Yii::$app->cache->set("tournament" . $id, $tournament, 120);
		}

		return $tournament;
	}

	public function getStatusOptions($id = null) {
		$options = [
			self::STATUS_CREATED => Yii::t("app", "Created"),
			self::STATUS_RUNNING => Yii::t("app", "Running"),
			self::STATUS_CLOSED => Yii::t("app", "Closed"),
		];

		return ($id) ? $options[$id] : $options;
	}

	/**
	 * Generate a unique URL SLUG ... never fails  ;)
	 */
	public function generateUrlSlug() {
		$potential_slug = str_replace(" ", "-", $this->fullname);

		if (Tournament::findByUrlSlug($potential_slug) !== null) {
			$i = 1;
			$iterate_slug = $potential_slug . "-" . $i;
			while (Tournament::findByUrlSlug($iterate_slug) !== null) {
				$i++;
				$iterate_slug = $potential_slug . "-" . $i;
			}
			$potential_slug = $iterate_slug;
		}
		$this->url_slug = $potential_slug;
		return true;
	}

	/**
	 * Call before model save
	 *
	 * @param type $insert
	 *
	 * @return boolean
	 */
	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
			if ($insert === true) //Do only on new Records
				$this->generateUrlSlug();
			return true;
		}

		return false;
	}

	public function getFullname() {
		return $this->name . " " . Yii::$app->formatter->asDate($this->end_date, "Y");
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicators() {
		return $this->hasMany(Adjudicator::className(), ['tournament_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getHostedby() {
		return $this->hasOne(Society::className(), ['id' => 'hosted_by_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEnergyConfigs() {
		return $this->hasMany(EnergyConfig::className(), ['tournament_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRounds() {
		return $this->hasMany(Round::className(), ['tournament_id' => 'id']);
	}

	/**
	 * Get's the last round
	 *
	 * @return Round
	 */
	public function getLastRound() {
		return $this->getRounds()->where(["displayed" => 1])->orderBy(['id' => SORT_ASC])->one();
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTeams() {
		return $this->hasMany(Team::className(), ['tournament_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getConvenorUser() {
		return $this->hasOne(User::className(), ['id' => 'convenor_user_id']);
	}

	/**
	 * Returns a list of Tabmasters
	 *
	 * @return type
	 */
	public function getTabmasterOptions($includeMyself = false) {
		$tabmaster = \yii\helpers\ArrayHelper::map(User::find()->where("role>10")->all(), 'id', 'name');
		if ($includeMyself)
			$tabmaster[Yii::$app->user->id] = Yii::$app->user->getModel()->name;
		return $tabmaster;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTabmasterUser() {
		return $this->hasOne(User::className(), ['id' => 'tabmaster_user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournamentHasQuestions() {
		return $this->hasMany(TournamentHasQuestion::className(), ['tournament_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getQuestions($type) {

		switch ($type) {
			case Feedback::FROM_CHAIR:
				$field = "C2W";
				break;
			case Feedback::FROM_WING:
				$field = "W2C";
				break;
			case Feedback::FROM_TEAM:
				$field = "T2C";
				break;
			default:
				throw new Exception("Wrong Parameter");
		}

		return $this->hasMany(Question::className(), ['id' => 'questions_id'])
		            ->viaTable('tournament_has_question', ['tournament_id' => 'id'])
		            ->where(["apply_" . $field => 1]);
	}

	public function getSocieties() {
		return $this->hasMany(Society::className(), ['id' => 'society_id'])
		            ->viaTable('team', ['tournament_id' => 'id'])
		            ->union(
			            $this->hasMany(Society::className(), ['id' => 'society_id'])
			                 ->viaTable('adjudicator', ['tournament_id' => 'id'])
		            );
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getVenues() {
		return $this->hasMany(Venue::className(), ['tournament_id' => 'id']);
	}

	/**
	 * Get the panels in that tournament
	 *
	 * @return type
	 */
	public function getPanels() {
		return $this->hasMany(Panel::className(), ['tournament_id' => 'id']);
	}

	public function getLogo($absolute = false) {
		if ($this->logo !== null) {
			if ($absolute)
				return Url::to('@web' . $this->logo, true);
			else
				return $this->logo;
		}
		else {
			$defaultPath = Yii::getAlias("@frontend/assets/images/") . "default-tournament.png";
			return Yii::$app->assetManager->publish($defaultPath)[1];
		}
	}

	public function getLogoImage($width_max = null, $height_max = null, $options = []) {

		$alt = ($this->name) ? $this->getFullname() : "";
		$img_options = array_merge($options, ["alt" => $alt,
			"style" => "max-width: " . $width_max . "px; max-height: " . $height_max . "px;",
			"width" => $width_max,
			"height" => $height_max,
		]);
		$img_options["class"] = "img-responsive img-rounded center-block" . (isset($img_options["class"]) ? " " . $img_options["class"] : "");
		return Html::img($this->getLogo(), $img_options);
	}

	public static function getTabAlgorithmOptions() {
		return Yii::$app->params["tabAlgorithmOptions"];
	}

	/**
	 * Get a new Instance of the Tab Algorithm
	 *
	 * @return \common\components\TabAlgorithm
	 */
	public function getTabAlgorithmInstance() {
		$algoClass = $this->tabAlgorithmClass;
		$algoName = "common\components\algorithms\\" . $algoClass;
		return new $algoName();
	}

	public function getSocietiesOptions() {
		$choices = [];
		/* @var $user User */
		$user = Yii::$app->user->getModel();
		$societies = $user->getCurrentSocieties()->asArray()->all();
		return ArrayHelper::map($societies, "id", "fullname");
	}

	/**
	 * Get the Amount of Teams breaking in this tournament
	 *
	 * @return int
	 */
	public function getAmountBreakingTeams() {
		if ($this->has_octofinal)
			return 32;
		if ($this->has_quarterfinal)
			return 16;
		if ($this->has_semifinal)
			return 8;
		if ($this->has_final)
			return 4;

		return 0;
	}

	/**
	 * Save a Tournament Logo
	 *
	 * @param \yii\web\UploadedFile $file
	 */
	public function saveLogo($file) {
		$path = "/uploads/tournaments/TournamentLogo-" . $this->url_slug . "." . $file->extension;
		$this->logo = $file && $file->saveAs(Yii::getAlias("@frontend/web") . $path) ? $path : null;
	}

}
