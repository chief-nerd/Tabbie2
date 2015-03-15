<?php

	namespace frontend\controllers;

	use common\models\Tournament;
	use Yii;
	use common\models\User;
	use common\models\search\UserSearch;
	use yii\web\NotFoundHttpException;
	use yii\filters\VerbFilter;
	use common\models\InSociety;
	use common\models\Society;
	use yii\filters\AccessControl;

	/**
	 * UserController implements the CRUD actions for User model.
	 */
	class UserController extends \yii\web\Controller {

		public function behaviors() {
			return [
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [
						[
							'allow' => true,
							'actions' => ['create'],
							'roles' => [''],
						],
						[
							'allow' => true,
							'actions' => ['view', 'list'],
							'roles' => ['@'],
						],
						[
							'allow' => true,
							'actions' => ['update', 'societies'],
							'matchCallback' => function ($rule, $action) {
								return (Yii::$app->user->id == Yii::$app->request->get("id") || Yii::$app->user->isAdmin());
							}
						],
						[
							'allow' => true,
							'actions' => ['setlanguage'],
							'matchCallback' => function ($rule, $action) {
								$tournament = Tournament::findByPk(Yii::$app->request->get("tournament"));
								if ($tournament instanceof Tournament)
									return Yii::$app->user->isLanguageOfficer($tournament);
								else
									return false;
							}
						],
						[
							'allow' => true,
							'actions' => ['index', 'delete'],
							'matchCallback' => function ($rule, $action) {
								return (Yii::$app->user->isAdmin());
							}
						],
					],
				],
				'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
						'delete' => ['post'],
					],
				],
			];
		}

		/**
		 * Lists all User models.
		 * @return mixed
		 */
		public function actionIndex() {
			$searchModel = new UserSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
		 * Displays a single User model.
		 *
		 * @param integer $id
		 *
		 * @return mixed
		 */
		public function actionView($id) {
			$model = $this->findModel($id);
			if ($society = $model->getInSocieties()->where(["ending" => null])->one())
				$model->societies_id = $society->society->fullname;

			return $this->render('view', [
				'model' => $model,
			]);
		}

		/**
		 * Creates a new User model.
		 * If creation is successful, the browser will be redirected to the 'view' page.
		 * @return mixed
		 */
		public function actionCreate() {
			$model = new User();

			if ($model->load(Yii::$app->request->post())) {
				$model->setPassword($model->email);
				$model->generateAuthKey();
				if ($model->save()) {
					print_r($model->attributes);
					if ($model->societies_id > 0) {
						$inSociety = new InSociety();
						$inSociety->user_id = $model->id;
						$inSociety->society_id = $model->societies_id;
						$inSociety->starting = date("Y-m-d");
						if (!$inSociety->save()) {
							Yii::warning("inSociety not saved! Errors: " . print_r($inSociety->getErrors(), true), __METHOD__);
							Yii::$app->session->addFlash("warning", Yii::t("app", "Society connection not saved"));
						}
						else {
							Yii::$app->session->addFlash("success", Yii::t("app", "User successfully saved!"));
						}
					}
					return $this->redirect(['view', 'id' => $model->id]);
				}
				else {
					Yii::warning("User Model not saved! Errors: " . print_r($model->getErrors(), true), __METHOD__);
					Yii::$app->session->addFlash("error", Yii::t("app", "User not saved!"));
				}
			}

			return $this->render('create', [
				'model' => $model,
			]);
		}

		/**
		 * Updates an existing User model.
		 * If update is successful, the browser will be redirected to the 'view' page.
		 *
		 * @param integer $id
		 *
		 * @return mixed
		 */
		public function actionUpdate($id) {
			$model = $this->findModel($id);
			if ($society = $model->getInSocieties()->where(["ending" => null])->one())
				$model->societies_id = $society->society->id;

			if ($model->load(Yii::$app->request->post())) {
				if ($model->save()) {
					if ($model->societies_id > 0) {
						$found = false;
						$InSociety = $model->getInSocieties()->all();
						/* @var $in InSociety */
						foreach ($InSociety as $in) {
							if ($in->society_id == $model->societies_id) {
								$found = true;
							}
							else {
								$in->ending = date("Y-m-d");
								$in->save();
							}
						}
						if (!$found) {
							$InSociety = new InSociety();
							$InSociety->user_id = $model->id;
							$InSociety->society_id = $model->societies_id;
							$InSociety->starting = date("Y-m-d");
							if (!$InSociety->save()) {
								Yii::warning("Error saving InSociety " . print_r($InSociety->getErrors(), true));
								Yii::$app->session->addFlash("warning", Yii::t("app", "Society Connection not saved!"));
							}
						}
					}
					Yii::$app->session->addFlash("success", Yii::t("app", "User successfully updated!"));
					return $this->redirect(['view', 'id' => $model->id]);
				}
				else
					Yii::$app->session->setFlash("error", print_r($model->getErrors(), true));
			}

			return $this->render('update', [
				'model' => $model,
			]);
		}

		/**
		 * Deletes an existing User model.
		 * If deletion is successful, the browser will be redirected to the 'index' page.
		 *
		 * @param integer $id
		 *
		 * @return mixed
		 */
		public function actionDelete($id) {
			//$this->findModel($id)->delete();

			return $this->redirect(['index']);
		}

		/**
		 * Returns 20 users in an JSON List
		 *
		 * @param type $search
		 * @param type $id
		 */
		public function actionList($search = null, $id = null) {
			$out = ['more' => false];
			if (!is_null($search)) {
				$query = new \yii\db\Query;
				$query->select(["id", "concat(givenname, ' ', surename) as text", "picture"])
				      ->from('user')
				      ->where('concat(givenname, \' \', surename) LIKE "%' . $search . '%"')
				      ->limit(20);
				$command = $query->createCommand();
				$data = $command->queryAll();
				$out['results'] = array_values($data);
			}
			elseif ($id > 0) {
				$out['results'] = ['id' => $id, 'text' => User::findOne($id)->name];
			}
			else {
				$out['results'] = ['id' => 0, 'text' => 'No matching records found'];
			}
			echo \yii\helpers\Json::encode($out);
		}

		/**
		 * Returns 20 societies in an JSON List
		 *
		 * @param type $search
		 * @param type $id
		 */
		public function actionSocieties($search = null, $id = null) {
			$out = ['more' => false];
			if (!is_null($search)) {
				$query = new \yii\db\Query;
				$query->select(["id", "fullname as text"])
				      ->from('society')
				      ->where('fullname LIKE "%' . $search . '%"')
				      ->limit(20);
				$command = $query->createCommand();
				$data = $command->queryAll();
				$out['results'] = array_values($data);
			}
			elseif ($id > 0) {
				$out['results'] = ['id' => $id, 'text' => Society::findOne($id)->fullname];
			}
			else {
				$out['results'] = ['id' => 0, 'text' => 'No matching records found'];
			}
			echo \yii\helpers\Json::encode($out);
		}

		/**
		 * Sets the Users Language Level
		 * ONLY by Language Officer!
		 *
		 * @param integer $id
		 * @param string $status
		 * @param integer $tournament
		 *
		 * @return \yii\web\Response
		 */
		public function actionSetlanguage($id, $status, $tournament) {

			$user = User::findOne($id);
			if ($user instanceof User) {
				switch ($status) {
					case "ENL":
						$user->language_status = User::LANGUAGE_ENL;
						break;
					case "ESL":
						$user->language_status = User::LANGUAGE_ESL;
						break;
					case "EFL":
						$user->language_status = User::LANGUAGE_EFL;
						break;
					default:
						Yii::$app->session->addFlash("error", Yii::t("app", "Not a valid Language Options in params"));
				}
				$user->language_status_by_id = Yii::$app->user->id;
				$user->language_status_update = date("Y-m-d H:i:s");

				if ($user->save())
					Yii::$app->session->addFlash("success", Yii::t("app", "Language Settings saved"));
				else
					Yii::$app->session->addFlash("error", Yii::t("app", "Error saving Language Settings"));
			}
			else
				Yii::$app->session->addFlash("error", Yii::t("app", "User not found!"));

			return $this->redirect(['language/index', "tournament_id" => $tournament]);
		}

		/**
		 * Finds the User model based on its primary key value.
		 * If the model is not found, a 404 HTTP exception will be thrown.
		 *
		 * @param integer $id
		 *
		 * @return User the loaded model
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		protected function findModel($id) {
			if (($model = User::findOne($id)) !== null) {
				return $model;
			}
			else {
				throw new NotFoundHttpException('The requested page does not exist.');
			}
		}

	}
