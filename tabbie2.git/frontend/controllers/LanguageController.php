<?php

	namespace frontend\controllers;

	use Yii;
	use yii\filters\VerbFilter;
	use common\components\filter\TournamentContextFilter;
	use yii\filters\AccessControl;
	use common\models\search\UserSearch;

	/**
	 * Site controller
	 */
	class LanguageController extends BaseController {

		/**
		 * @inheritdoc
		 */
		public function behaviors() {
			return [
				'tournamentFilter' => [
					'class' => TournamentContextFilter::className(),
				],
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [
						[
							'allow' => true,
							'actions' => ['index'],
							'matchCallback' => function ($rule, $action) {
								return (Yii::$app->user->isLanguageOfficer($this->_tournament));
							}
						],
					],
				],
				'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
						'logout' => ['post'],
					],
				],
			];
		}

		public function actionIndex() {
			$searchModel = new UserSearch();
			$dataProvider = $searchModel->searchTournament(Yii::$app->request->queryParams, $this->_tournament->id);

			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}
	}
