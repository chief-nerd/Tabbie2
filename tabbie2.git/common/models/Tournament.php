<?php

namespace common\models;

use JmesPath\Tests\_TestJsonStringClass;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "tournament".
 *
 * @property integer                  $id
 * @property string                   $url_slug
 * @property integer                  $status
 * @property integer                  $hosted_by_id
 * @property string                   $name
 * @property string                   $fullname
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
 * @property string                   $accessToken
 * @property string                   $badge
 * @property Adjudicator[]            $adjudicators
 * @property Panel[]                  $panels
 * @property Round[]                  $rounds
 * @property Team[]                   $teams
 * @property User[]                   $convenors
 * @property User[]                   $tabmasters
 * @property User[]                   $cas
 * @property TournamentHasQuestion[]  $tournamentHasQuestions
 * @property Question[]               $questions
 * @property Venue[]                  $venues
 */
class Tournament extends \yii\db\ActiveRecord
{

	const STATUS_CREATED = 0;
	const STATUS_RUNNING = 1;
	const STATUS_CLOSED = 2;
	const STATUS_HIDDEN = 3;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'tournament';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['url_slug', 'hosted_by_id', 'name', 'start_date', 'end_date'], 'required'],
			[['hosted_by_id', 'expected_rounds', 'status'], 'integer'],
			[['start_date', 'end_date', 'time', 'has_esl', 'has_final', 'has_semifinal', 'has_octofinal', 'has_quarterfinal'], 'safe'],
			[['url_slug', 'name', 'tabAlgorithmClass', 'accessToken'], 'string', 'max' => 100],
			[['logo', 'badge'], 'string', 'max' => 255],
			[['url_slug'], 'unique']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'                => Yii::t('app', 'Tournament ID'),
			'hosted_by_id'      => Yii::t('app', 'Hosted by'),
			'name'              => Yii::t('app', 'Tournament Name'),
			'start_date'        => Yii::t('app', 'Start Date'),
			'end_date'          => Yii::t('app', 'End Date'),
			'logo'              => Yii::t('app', 'Logo'),
			'time'              => Yii::t('app', 'Time'),
			'url_slug'          => Yii::t('app', 'URL Slug'),
			'tabAlgorithmClass' => Yii::t('app', 'Tab Algorithm'),
			'expected_rounds'   => Yii::t("app", "Expected number of rounds"),
			'has_esl'           => Yii::t("app", "Show ESL Ranking"),
			'has_final'         => Yii::t("app", 'Is there a grand final'),
			'has_semifinal'     => Yii::t("app", 'Is there a semifinal'),
			'has_quarterfinal'  => Yii::t("app", 'Is there a quarterfinal'),
			'has_octofinal'     => Yii::t("app", 'Is there a octofinal'),
			'accessToken'       => Yii::t("app", 'Access Token'),
			'badge'             => Yii::t("app", 'Participant Badge'),
		];
	}

	public static function findByUrlSlug($slug)
	{
		return Tournament::findOne(["url_slug" => $slug]);
	}

	/**
	 * Find a Tournament by Primary Key
	 *
	 * @param integer $id
	 *
	 * @uses Tournamnet::findOne
	 * @return null|Tournamnet
	 */
	public static function findByPk($id)
	{
		$tournament = Yii::$app->cache->get("tournament" . $id);
		if (!$tournament instanceof Tournament) {
			$tournament = Tournament::findOne(["id" => $id]);
			Yii::$app->cache->set("tournament" . $id, $tournament, 120);
		}

		return $tournament;
	}

	/**
	 * Check if user is the tabmaster of the torunament
	 *
	 * @param int $userID
	 *
	 * @return boolean
	 */
	public function isTabMaster($userID)
	{
		$count = Tabmaster::find()->tournament($this->id)->andWhere(["user_id" => $userID])->count();
		if ($count > 0) {
			return true;
		} else if (Yii::$app->user->isAdmin()) //Admin secure override
			return true;
		else
			return false;
	}

	/**
	 * Check if user is the CA of the torunament
	 *
	 * @param int $userID
	 *
	 * @return boolean
	 */
	public function isCA($userID)
	{
		$count = Ca::find()->tournament($this->id)->andWhere(["user_id" => $userID])->count();
		if ($count > 0) {
			return true;
		} else if (Yii::$app->user->isAdmin()) //Admin secure override
			return true;
		else
			return false;
	}

	/**
	 * Check if user is the convenor of the torunament
	 *
	 * @param int $userID
	 *
	 * @return boolean
	 */
	public function isConvenor($userID)
	{
		$count = Convenor::find()->tournament($this->id)->andWhere(["user_id" => $userID])->count();
		if ($count > 0) {
			return true;
		} else if (Yii::$app->user->isAdmin()) //Admin secure override
			return true;

		return false;
	}

	public function isLanguageOfficer($userID)
	{
		if ($this->status != Tournament::STATUS_CLOSED) {
			$count = LanguageOfficer::find()->tournament($this->id)->andWhere(["user_id" => $userID])->count();
			if ($count > 0
			) {
				\Yii::trace("User is LanguageOfficer for Tournament #" . $this->id, __METHOD__);

				return true;
			} else if (Yii::$app->user->isAdmin()) //Admin secure override
				return true;
		}

		return false;
	}

	/**
	 * Check if user is registered
	 *
	 * @param integer $userID
	 *
	 * @return bool
	 */
	public function isRegistered($userID)
	{

		if (Yii::$app->user->isAdmin() || $this->isConvenor($userID) || $this->isLanguageOfficer($userID) || $this->isTabMaster($userID))
			return true;

		if ($this->isTeam($userID) || $this->isAdjudicator($userID))
			return true;

		return false;

	}

	/**
	 * Check if user is Team
	 *
	 * @param $userID
	 *
	 * @return bool
	 */
	public function isTeam($userID)
	{
		if ($this->isTeamA($userID) || $this->isTeamB($userID))
			return true;

		return false;
	}

	/**
	 * Check if user is Team A
	 *
	 * @param $userID
	 *
	 * @return bool
	 */
	public function isTeamA($userID)
	{
		//check if Team
		$team = Team::find()->tournament($this->id)
			->andWhere(["speakerA_id" => $userID])
			->count();
		if ($team > 0)
			return true;

		return false;
	}

	/**
	 * Check if user is Team B
	 *
	 * @param $userID
	 *
	 * @return bool
	 */
	public function isTeamB($userID)
	{
		//check if Team
		$team = Team::find()->tournament($this->id)
			->andWhere(["speakerB_id" => $userID])
			->count();
		if ($team > 0)
			return true;

		return false;
	}

	/**
	 * Check if user is Adjudicator
	 *
	 * @param $userID
	 *
	 * @return bool
	 */
	public function isAdjudicator($userID)
	{
		//check if Adjudicator
		$adju = Adjudicator::find()->tournament($this->id)
			->andWhere(["user_id" => $userID])
			->count();
		if ($adju > 0)
			return true;

		return false;
	}

	public function getStatusOptions($id = null)
	{
		$options = [
			self::STATUS_CREATED => Yii::t("app", "Created"),
			self::STATUS_RUNNING => Yii::t("app", "Running"),
			self::STATUS_CLOSED  => Yii::t("app", "Closed"),
		];

		return ($id) ? $options[$id] : $options;
	}

	/**
	 * Generate a unique URL SLUG ... never fails  ;)
	 */
	public function generateUrlSlug()
	{
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
	 * Generate an accessURL for Runners and DrawDisplay
	 *
	 * @return string
	 */
	public function generateAccessToken()
	{
		return $this->accessToken = substr(md5(uniqid(mt_rand(), true)), 0, 10);
	}

	/**
	 * Validate an AccessToken with the object
	 *
	 * @param $testToken
	 *
	 * @return bool
	 */
	public function validateAccessToken($testToken)
	{
		if ($testToken == false) return false;

		if ($testToken == $this->accessToken)
			return true;

		return false;
	}

	/**
	 * Call before model save
	 *
	 * @param type $insert
	 *
	 * @return boolean
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			if ($insert === true) //Do only on new Records
			{
				$this->generateUrlSlug();
				$this->generateAccessToken();
			}

			return true;
		}

		return false;
	}

	public function getFullname()
	{
		return $this->name . " " . Yii::$app->formatter->asDate($this->end_date, "Y");
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicators()
	{
		return $this->hasMany(Adjudicator::className(), ['tournament_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getHostedby()
	{
		return $this->hasOne(Society::className(), ['id' => 'hosted_by_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEnergyConfigs()
	{
		return $this->hasMany(EnergyConfig::className(), ['tournament_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRounds()
	{
		return $this->hasMany(Round::className(), ['tournament_id' => 'id']);
	}

	/**
	 * Get's the last round
	 *
	 * @return Round
	 */
	public function getLastRound()
	{
		return $this->getRounds()->where(["displayed" => 1])->orderBy(['id' => SORT_DESC])->one();
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTeams()
	{
		return $this->hasMany(Team::className(), ['tournament_id' => 'id']);
	}

	public function getCAs()
	{
		return $this->hasMany(User::className(), ['id' => 'user_id'])
			->viaTable('ca', ['tournament_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getConvenors()
	{
		return $this->hasMany(User::className(), ['id' => 'user_id'])
			->viaTable('convenor', ['tournament_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTabmasters()
	{
		return $this->hasMany(User::className(), ['id' => 'user_id'])
			->viaTable('tabmaster', ['tournament_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournamentHasQuestions()
	{
		return $this->hasMany(TournamentHasQuestion::className(), ['tournament_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getQuestions($type)
	{

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

	public function getSocieties()
	{
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
	public function getVenues()
	{
		return $this->hasMany(Venue::className(), ['tournament_id' => 'id']);
	}

	/**
	 * Get the panels in that tournament
	 *
	 * @return type
	 */
	public function getPanels()
	{
		return $this->hasMany(Panel::className(), ['tournament_id' => 'id']);
	}

	public function getLogo($absolute = false)
	{
		if ($this->logo !== null) {
			if ($absolute && substr($this->logo, 0, 4) != "http")
				return Url::to($this->logo, true);
			else
				return $this->logo;
		} else {
			$defaultPath = Yii::getAlias("@frontend/assets/images/") . "default-tournament.png";

			return Yii::$app->assetManager->publish($defaultPath)[1];
		}
	}

	public function getLogoImage($width_max = null, $height_max = null, $options = [])
	{

		$alt = ($this->name) ? $this->getFullname() : "";
		$img_options = array_merge($options, ["alt"    => $alt,
											  "style"  => "max-width: " . $width_max . "px; max-height: " . $height_max . "px;",
											  "width"  => $width_max,
											  "height" => $height_max,
		]);
		$img_options["class"] = "img-responsive img-rounded center-block" . (isset($img_options["class"]) ? " " . $img_options["class"] : "");

		return Html::img($this->getLogo(), $img_options);
	}

	/**
	 * Get the Badge URL
	 * @return mixed|string
	 */
	public function getBadge()
	{
		if ($this->badge !== null) {
			if (substr($this->badge, 0, 4) != "http")
				return Url::to($this->badge, true);
			else
				return $this->badge;
		} else {
			return "";
		}
	}

	/**
	 * @param Tournament $model
	 *
	 * @return array|false
	 */
	public function getLastDebateInfo($id)
	{
		if (!is_int($id)) return false;

		$lastRound = $this->getLastRound();

		if ($lastRound) {
			foreach ($lastRound->getDebates()->all() as $debate) {

				/** check teams* */
				if ($debate->isOGTeamMember($id))
					return ['type' => 'team', 'debate' => $debate, 'pos' => Team::OG];
				if ($debate->isOOTeamMember($id))
					return ['type' => 'team', 'debate' => $debate, 'pos' => Team::OO];
				if ($debate->isCGTeamMember($id))
					return ['type' => 'team', 'debate' => $debate, 'pos' => Team::CG];
				if ($debate->isCOTeamMember($id))
					return ['type' => 'team', 'debate' => $debate, 'pos' => Team::CO];

				/** check judges * */
				foreach ($debate->panel->adjudicatorInPanels as $judge) {
					if ($judge->adjudicator->user_id == $id) {
						if ($judge->function == Panel::FUNCTION_CHAIR)
							$pos = Panel::FUNCTION_CHAIR;
						else
							$pos = Panel::FUNCTION_WING;

						return ['type' => 'judge', 'debate' => $debate, 'pos' => $pos];
					}
				}
			}
		}

		return false;
	}

	public static function getTabAlgorithmOptions()
	{
		$algos = [];
		$files = scandir(Yii::getAlias("@algorithms/algorithms/"));
		foreach ($files as $className) {
			if (substr($className, 0, 1) == ".") continue;
			$filename = pathinfo($className)['filename'];
			$class = Tournament::getTabAlgorithm($filename);
			if ($class::version() !== null)
				$algos[$filename] = $class::title() . " (v" . $class::version() . ")";
		}

		return $algos;
	}

	/**
	 * Get a new Instance of the Tab Algorithm
	 *
	 * @return \common\components\TabAlgorithm
	 */
	public function getTabAlgorithmInstance()
	{
		return Tournament::getTabAlgorithm($this->tabAlgorithmClass);
	}

	public static function getTabAlgorithm($algoClass)
	{
		$algoName = 'algorithms\\algorithms\\' . $algoClass;

		return new $algoName();
	}

	/**
	 * Get the Amount of Teams breaking in this tournament
	 *
	 * @return int
	 */
	public function getAmountBreakingTeams()
	{
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
	public function saveLogo($file)
	{
		if ($file) {
			$path = "tournaments/TournamentLogo-" . $this->url_slug . "." . $file->extension;
			$this->logo = Yii::$app->s3->save($file, $path);
		}
	}

	/**
	 * Save a Tournament Badge
	 *
	 * @param \yii\web\UploadedFile $file
	 */
	public function saveBadge($file)
	{
		if ($file) {
			$path = "badges/Badge-" . $this->url_slug . "." . $file->extension;
			$this->badge = Yii::$app->s3->save($file, $path);
		}
	}

	/**
	 * @return bool
	 */
	public function user_role()
	{
		$id = Yii::$app->user->id;
		if (!is_int($id)) return false;

		$team = Team::find()
			->tournament($this->id)
			->andWhere("speakerA_id = $id OR speakerB_id = $id")
			->one();

		if ($team instanceof Team)
			return $team;

		$adju = Adjudicator::find()->tournament($this->id)->andWhere(["user_id" => $id])->one();
		if ($adju instanceof Adjudicator)
			return $adju;

		return false;
	}

}
