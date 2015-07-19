<?php

	namespace common\models;

	use Yii;
	use yii\base\NotSupportedException;
	use yii\behaviors\TimestampBehavior;
	use yii\db\ActiveRecord;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\web\IdentityInterface;

	/**
	 * User model
	 * This is the model class for table "user". It represents a single user in the system.
	 *
	 * @see Team
	 * @see Adjudicator
	 * @property integer        $id
	 * @property integer        $url_slug
	 * @property string         $auth_key
	 * @property string         $password_hash
	 * @property string         $password_reset_token
	 * @property string         $email
	 * @property integer        $role
	 * @property integer        $status
	 * @property integer        $last_change
	 * @property string         $givenname
	 * @property string         $surename
	 * @property integer        $gender
	 * @property integer        $language_status
	 * @property integer        $language_status_by_id
	 * @property integer        $language_status_update
	 * @property string         $picture
	 * @property string         $time
	 * @property Adjudicator[]  $adjudicators
	 * @property InSociety[]    $inSocieties
	 * @property Society[]      $societies
	 * @property Team[]         $teams
	 * @property SpecialNeeds[] $specialNeeds
	 * @property string         $name
	 */
	class User extends ActiveRecord implements IdentityInterface
	{

		const STATUS_DELETED = 0;
		const STATUS_ACTIVE = 10;

		const ROLE_PLACEHOLDER = 9;
		const ROLE_USER = 10;
		const ROLE_TABMASTER = 11;
		const ROLE_ADMIN = 12;

		const GENDER_NOTREVEALING = 0;
		const GENDER_MALE = 1;
		const GENDER_FEMALE = 2;
		const GENDER_OTHER = 3;

		const LANGUAGE_NONE = 0;
		const LANGUAGE_ENL = 1;
		const LANGUAGE_ESL = 2;
		const LANGUAGE_EFL = 3;

		const NONE = "(not set)";

		public $societies_id;

		public $password;
		public $password_repeat;

		/**
		 * @inheritdoc
		 */
		public static function tableName()
		{
			return 'user';
		}

		/**
		 * @inheritdoc
		 */
		public function behaviors()
		{
			return [
				'timestamp' => [
					'class'      => TimestampBehavior::className(),
					'attributes' => [
						ActiveRecord::EVENT_BEFORE_INSERT => 'time',
						ActiveRecord::EVENT_BEFORE_UPDATE => 'last_change',
					],
					'value'      => function () {
						return date('Y-m-d H:i:s');
					}, // unix timestamp
				],
			];
		}

		/**
		 * @inheritdoc
		 */
		public function rules()
		{
			return [
				[['auth_key', 'password_hash', 'email'], 'required'],

				[['password', 'password_repeat'], 'string', 'on' => 'first_login'],
				[['password', 'password_repeat'], 'required', 'on' => 'first_login'],

				[['id', 'role', 'status', 'language_status', 'language_status_by_id'], 'integer'],
				[['picture'], 'string'],
				[['url_slug', 'password_hash', 'password_reset_token', 'email', 'givenname', 'surename'], 'string', 'max' => 255],

				['status', 'default', 'value' => self::STATUS_ACTIVE],
				['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

				['url_slug', 'validateIsUrlAllowed'],

				['password_repeat', 'compare', 'compareAttribute' => 'password'],

				['role', 'default', 'value' => self::ROLE_USER],
				['role', 'in', 'range' => [self::ROLE_PLACEHOLDER, self::ROLE_USER, self::ROLE_TABMASTER, self::ROLE_ADMIN]],

				['gender', 'default', 'value' => self::GENDER_NOTREVEALING],
				['gender', 'in', 'range' => [self::GENDER_MALE, self::GENDER_FEMALE, self::GENDER_OTHER, self::GENDER_NOTREVEALING]],

				['language_status', 'default', 'value' => self::LANGUAGE_NONE],
				['language_status', 'in', 'range' => [self::LANGUAGE_NONE, self::LANGUAGE_ENL, self::LANGUAGE_ESL, self::LANGUAGE_EFL]],

				[['password', 'password_repeat', 'url_slug', 'auth_key', 'time', 'last_change', 'societies_id', 'language_status_update'], 'safe'],
			];
		}

		/**
		 * @inheritdoc
		 */
		public function beforeSave($insert)
		{
			if ($insert) {
				$this->last_change = $this->time = date("Y-m-d H:i:s");
			}

			return true;
		}

		/**
		 * Check if the URL is allowed or if there are any conflicts with Actions
		 *
		 * @param type $attribute
		 * @param type $params
		 */
		public function validateIsUrlAllowed($attribute, $params)
		{
			foreach (get_class_methods(\frontend\controllers\UserController::className()) as $key => $value) {
				if (substr($value, 0, 6) == "action" && $value != "actions") {
					$actions[] = strtolower(substr($value, 6));
				}
			}

			if (in_array($this->$attribute, $actions)) {
				$this->addError($attribute, Yii::t("app", 'This URL-slug is not allowed.'));
			}
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels()
		{
			return [
				'id'                   => Yii::t('app', 'ID'),
				'url_slug'             => Yii::t('app', 'URL Slug'),
				'auth_key'             => Yii::t('app', 'Auth Key'),
				'password_hash'        => Yii::t('app', 'Password Hash'),
				'password_reset_token' => Yii::t('app', 'Password Reset Token'),
				'email'                => Yii::t('app', 'Email'),
				'role'                 => Yii::t('app', 'Account Role'),
				'status'               => Yii::t('app', 'Account Status'),
				'last_change'          => Yii::t('app', 'Last Change'),
				'givenname'            => Yii::t('app', 'Givenname'),
				'surename'             => Yii::t('app', 'Surname'),
				'language_status'      => Yii::t('app', 'Language Status'),
				'picture'              => Yii::t('app', 'Picture'),
				'time'                 => Yii::t('app', 'Time'),
			];
		}

		/**
		 * @inheritdoc
		 */
		public static function findIdentity($id)
		{
			return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
		}

		/**
		 * @inheritdoc
		 */
		public static function findIdentityByAccessToken($token, $type = null)
		{
			throw new NotSupportedException(Yii::t("app", '"findIdentityByAccessToken" is not implemented.'));
		}

		/**
		 * Finds user by email
		 *
		 * @param string $email
		 *
		 * @return static|null
		 */
		public static function findByEmail($email)
		{
			return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
		}

		/**
		 * Finds user by url_slug
		 *
		 * @param string $slug
		 *
		 * @return static|null
		 */
		public static function findbyUrlSlug($slug)
		{
			return static::findOne(['url_slug' => $slug, 'status' => self::STATUS_ACTIVE]);
		}

		/**
		 * Finds user by password reset token
		 *
		 * @param string $token password reset token
		 *
		 * @return static|null
		 */
		public static function findByPasswordResetToken($token)
		{
			if (!static::isPasswordResetTokenValid($token)) {
				return null;
			}

			return static::findOne([
				'password_reset_token' => $token,
				'status'               => self::STATUS_ACTIVE,
			]);
		}

		/**
		 * Finds all User for a specific Tournament
		 *
		 * @param $tournamentid Tournament ID
		 *
		 * @return \yii\db\ActiveQuery
		 */
		public static function findByTournament($tournamentid)
		{
			return static::find()
				->rightJoin("adjudicator", "user_id = user.id")
				->where(["adjudicator.tournament_id" => $tournamentid])
				->union(
					static::find()
						->rightJoin("team", "team.speakerA_id = user.id")
						->where(["team.tournament_id" => $tournamentid]), true
				)
				->union(
					static::find()
						->rightJoin("team", "team.speakerB_id = user.id")
						->where(["team.tournament_id" => $tournamentid]), true
				);
		}

		/**
		 * Finds out if password reset token is valid
		 *
		 * @param string $token password reset token
		 *
		 * @return boolean
		 */
		public static function isPasswordResetTokenValid($token)
		{
			if (empty($token)) {
				return false;
			}
			$expire = Yii::$app->params['user.passwordResetTokenExpire'];
			$parts = explode('_', $token);
			$timestamp = (int)end($parts);

			return $timestamp + $expire >= time();
		}

		/**
		 * @inheritdoc
		 */
		public function getId()
		{
			return $this->getPrimaryKey();
		}

		/**
		 * @inheritdoc
		 */
		public function getAuthKey()
		{
			return $this->auth_key;
		}

		/**
		 * @inheritdoc
		 */
		public function validateAuthKey($authKey)
		{
			return $this->getAuthKey() === $authKey;
		}

		/**
		 * Validates password
		 *
		 * @param string $password password to validate
		 *
		 * @return boolean if password provided is valid for current user
		 */
		public function validatePassword($password)
		{
			return Yii::$app->security->validatePassword($password, $this->password_hash);
		}

		/**
		 * Generates password hash from password and sets it to the model
		 *
		 * @param string $password
		 */
		public function setPassword($password)
		{
			$this->password_hash = Yii::$app->security->generatePasswordHash($password);
		}

		/**
		 * Generates "remember me" authentication key
		 */
		public function generateAuthKey()
		{
			$this->auth_key = Yii::$app->security->generateRandomString();
		}

		public function generateUrlSlug()
		{
			$candidate = str_replace(" ", ".", $this->givenname) . "." . str_replace(" ", ".", $this->surename);
			$counter = 0;
			do {
				$found = static::findbyUrlSlug($candidate);
				if (count($found) > 0) {
					$counter++;
					$candidate = $candidate . "." . $counter;
				}
			} while (count($found) > 0);

			return $this->url_slug = $candidate;
		}

		/**
		 * Generates new password reset token
		 */
		public function generatePasswordResetToken()
		{
			$this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
		}

		/**
		 * Removes password reset token
		 */
		public function removePasswordResetToken()
		{
			$this->password_reset_token = null;
		}

		/**
		 * Returns the full name of the User
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->givenname . " " . $this->surename;
		}

		/**
		 * Returns all Adjudicator for this user
		 *
		 * @return \yii\db\ActiveQuery
		 */
		public function getAdjudicators()
		{
			return $this->hasMany(Adjudicator::className(), ['user_id' => 'id']);
		}

		/**
		 * Returns all Teams for this user
		 *
		 * @return type
		 */
		public function getTeams()
		{
			return Team::find()->andWhere(["speakerA_id" => $this->id])->orWhere(["speakerB_id" => $this->id]);
		}

		/**
		 * Returns all InSociety Connections for this user
		 *
		 * @return \yii\db\ActiveQuery
		 */
		public function getInSocieties()
		{
			return $this->hasMany(InSociety::className(), ['user_id' => 'id']);
		}

		/**
		 * Returns all Societies for this user
		 *
		 * @return \yii\db\ActiveQuery
		 */
		public function getSocieties()
		{
			return $this->hasMany(Society::className(), ['id' => 'society_id'])
				->viaTable('in_society', ['user_id' => 'id']);
		}

		/**
		 * Returns all the Special Needs for a User
		 *
		 * @return \yii\db\ActiveQuery
		 */
		public function getSpecialNeeds()
		{
			return $this->hasMany(SpecialNeeds::className(), ['id' => 'special_needs_id'])
				->viaTable('username_has_special_needs', ['username_id' => 'id']);
		}

		public static function getRoleOptions($none = false)
		{
			$options = [
				self::ROLE_PLACEHOLDER => self::getRoleLabel(User::ROLE_PLACEHOLDER),
				self::ROLE_USER        => self::getRoleLabel(User::ROLE_USER),
				self::ROLE_TABMASTER   => self::getRoleLabel(User::ROLE_TABMASTER),
				self::ROLE_ADMIN       => self::getRoleLabel(User::ROLE_ADMIN),
			];
			if ($none) {
				$options = ["" => " "] + $options;
			}

			return $options;
		}

		public static function getRoleLabel($id)
		{
			switch ($id) {
				case self::ROLE_PLACEHOLDER:
					return Yii::t("app", "Placeholder");
				case self::ROLE_USER:
					return Yii::t("app", "User");
				case self::ROLE_TABMASTER:
					return Yii::t("app", "Tabmaster");
				case self::ROLE_ADMIN:
					return Yii::t("app", "Admin");
			}
		}

		public static function getStatusOptions()
		{
			return [
				self::STATUS_ACTIVE  => self::getStatusLabel(User::STATUS_ACTIVE),
				self::STATUS_DELETED => self::getStatusLabel(User::STATUS_DELETED),
			];
		}

		public static function getStatusLabel($id)
		{
			switch ($id) {
				case self::STATUS_ACTIVE:
					return Yii::t("app", "Active");
				case self::STATUS_DELETED:
					return Yii::t("app", "Deleted");
			}
		}

		public static function genderOptions()
		{
			return [
				self::GENDER_NOTREVEALING => Yii::t("app", "Not revealing"),
				self::GENDER_FEMALE       => Yii::t("app", "Female"),
				self::GENDER_MALE         => Yii::t("app", "Male"),
				self::GENDER_OTHER        => Yii::t("app", "Other"),
			];
		}

		public static function generatePlaceholder($letter)
		{
			$letter = strtoupper($letter);
			$user = new User([
				"username"    => "Speaker " . $letter,
				"givenname"   => "Speaker",
				"surename"    => $letter,
				"email"       => "speaker." . $letter . "@tabbie.com",
				"role"        => User::ROLE_PLACEHOLDER,
				"status"      => User::STATUS_ACTIVE,
				"last_change" => date("Y-m-d H:i:s"),
				"time"        => date("Y-m-d H:i:s"),
			]);
			$user->setPassword($letter . $letter . $letter);
			$user->generateAuthKey();
			if (!$user->save())
				\Yii::error("Placeholder create failed: " . print_r($user->getErrors(), true));
			else
				return $user;
		}

		public static function getLanguageStatusLabel($id = null, $short = false)
		{

			if ($id === -1)
				return Yii::t("app", "mixed");

			if ($id === self::LANGUAGE_NONE)
				return Yii::t("app", "Not set");

			$status = [
				self::LANGUAGE_ENL => Yii::t("app", "ENL") . (($short) ? "" : (" ," . Yii::t("app", "English as a native language"))),
				self::LANGUAGE_ESL => Yii::t("app", "ESL") . (($short) ? "" : (" ," . Yii::t("app", "English as a second language"))),
				self::LANGUAGE_EFL => Yii::t("app", "EFL") . (($short) ? "" : (" ," . Yii::t("app", "English as a foreign language"))),
			];

			return (isset($status[$id]) ? $status[$id] : $status);
		}

		public function getGenderIcon()
		{
			switch ($this->gender) {
				case static::GENDER_MALE:
					$class = "fa fa-mars";
					break;
				case static::GENDER_FEMALE:
					$class = "fa fa-venus";
					break;
				case static::GENDER_OTHER:
					$class = "fa fa-transgender";
					break;
				case static::GENDER_NOTREVEALING:
					$class = "fa fa-circle-thin";
					break;
			}

			return "<i class='" . $class . "'></i>";
		}

		public static function getCSSGender($id)
		{
			$genders = [
				static::GENDER_MALE         => "MALE",
				static::GENDER_FEMALE       => "FEMA",
				static::GENDER_OTHER        => "OTHE",
				static::GENDER_NOTREVEALING => "NORE",
			];

			return $genders[$id];
		}

		/**
		 * Save a User Picture
		 *
		 * @param \yii\web\UploadedFile $file
		 */
		public function savePicture($file)
		{
			$path = "users/User-" . $this->url_slug . "." . $file->extension;
			$this->picture = Yii::$app->s3->save($file, $path);
		}


		public function getPicture()
		{
			if ($this->picture !== null)
				if (substr($this->picture, 0, 4) != "http")
					return Url::to('@web/uploads/' . $this->picture, true);
				else
					return $this->picture;
			else {
				return User::defaultAvatar();
			}
		}

		public static function defaultAvatar()
		{
			$defaultPath = Yii::getAlias("@frontend/assets/images/") . "default-avatar.png";

			return Yii::$app->assetManager->publish($defaultPath)[1];
		}

		public function getPictureImage($width_max = null, $height_max = null, $options = [])
		{

			$alt = ($this->name) ? $this->name : "";
			$img_options = array_merge($options, ["alt"    => $alt,
												  "style"  => "max-width: " . $width_max . "px; max-height: " . $height_max . "px;",
												  "width"  => $width_max,
												  "height" => $height_max,
			]);
			$img_options["class"] = "img-responsive img-rounded center-block" . (isset($img_options["class"]) ? " " . $img_options["class"] : "");

			return Html::img($this->getPicture(), $img_options);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getCurrentSocieties()
		{
			return Society::find()->joinWith("inSocieties")->where(
				"user_id = :uid AND in_society.starting <= :starting AND (in_society.ending IS NULL OR in_society.ending < :ending) ", [
					":uid"      => $this->id,
					":starting" => date("Y-m-d"),
					":ending"   => date("Y-m-d", strtotime(Yii::$app->params["time_to_still_consider_active_in_society"]))
				]
			);
		}

		public static function generateTempPass($length = 5)
		{
			return substr(md5(uniqid()), 0, $length);
		}

		/**
		 * Creates a new User from Import and send a mail with credentials
		 *
		 * @param string     $givenname
		 * @param string     $surename
		 * @param string     $email
		 * @param integer    $societyID
		 * @param bool       $send_mail
		 * @param Tournament $tournament
		 *
		 * @return bool|\common\models\User
		 */
		public static function NewViaImport($givenname, $surename, $email, $societyID = null, $send_mail = true, $tournament = null)
		{
			$userA = new \common\models\User();
			$userA->givenname = $givenname;
			$userA->surename = $surename;
			$userA->email = $email;
			$password = User::generateTempPass();
			$userA->setPassword($password);
			$userA->generateAuthKey();
			$userA->time = $userA->last_change = date("Y-m-d H:i:s");
			$userA->generateUrlSlug();

			if ($userA->save()) {

				if ($societyID != null) {
					$inSociety = new \common\models\InSociety();
					$inSociety->user_id = $userA->id;
					$inSociety->society_id = $societyID;
					$inSociety->starting = date("Y-m-d");
					if (!$inSociety->save()) {
						Yii::error("Import Errors inSociety: " . print_r($inSociety->getErrors(), true), __METHOD__);
						Yii::$app->session->addFlash("error", Yii::t("app", "Error saving InSociety Relation for {user}", ["user" => $userA->username]));
					}
				}

				if ($send_mail) {
					self::sendNewUserMail($userA, $password, $tournament);
				}

				return $userA;
			} else {
				Yii::error("Import Errors userA: " . print_r($userA->getErrors(), true), __METHOD__);
				Yii::$app->session->addFlash("error", Yii::t("app", "Error Saving User {user}", ["user" => $userA->username]));
			}

			return false;
		}

		/**
		 * Sends mail to the new User
		 *
		 * @param User       $user
		 * @param string     $password
		 * @param Tournament $tournament
		 */
		public static function sendNewUserMail($user, $password, $tournament = null)
		{
			\Yii::$app->mailer->compose('import_user_created', [
				'user'       => $user,
				'password'   => $password,
				'tournament' => $tournament
			])->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params["appName"] . ' support'])
				->setTo([$user->email => $user->name])
				->setSubject(Yii::t("email", '{tournament} User Account for {user_name}', [
					"tournament" => $tournament->name,
					"user_name"  => $user->name]))
				->send();
		}

	}
