<?php

namespace backend\controllers;

use common\components\ObjectError;
use common\models\Message;
use common\models\search\MessageSearch;
use Yii;
use common\models\Language;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LanguageController implements the CRUD actions for Language model.
 */
class LanguageController extends Controller {

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions'       => ['index'],
						'allow'         => true,
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isLanguageMaintainer(false) || Yii::$app->user->isMaintainer());
						}
					],
					[
						'actions'       => ['view'],
						'allow'         => true,
						'matchCallback' => function ($rule, $action) {
							$lang = Yii::$app->request->get("id", false);
							return (Yii::$app->user->isLanguageMaintainer($lang) || Yii::$app->user->isMaintainer());
						}
					],
					[
						'actions'       => ['create', 'update', 'delete'],
						'allow'         => true,
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isAdmin());
						}
					],
				],
			],
			'verbs'  => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	/**
	 * Lists all Language models.
	 * @return mixed
	 */
	public function actionIndex() {
		$dataProvider = new ActiveDataProvider([
			'query' => Language::find(),
		]);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Language model.
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function actionView($id) {

		$lang = $this->findModel($id);

		// validate if there is a editable input saved via AJAX
		if (Yii::$app->request->post('hasEditable')) {
			// instantiate your book model for saving
			$messageID = unserialize(Yii::$app->request->post('editableKey'));
			$model = Message::findOne($messageID);

			// store a default json response as desired by editable
			$out = Json::encode(['output' => '', 'message' => '']);

			// fetch the first entry in posted data (there should
			// only be one entry anyway in this array for an
			// editable submission)
			// - $posted is the posted data for Book without any indexes
			// - $post is the converted array for single model validation
			$post = [];
			$posted = current(Yii::$app->request->post('Message'));
			$post['Message'] = $posted;

			// load model like any single model validation
			if ($model->load($post) && $model->save()) {

				$lang->last_update = date("Y-m-d H:i:s");
				$lang->save();

				$output = '';

				// specific use case where you need to validate a specific
				// editable column posted when you have more than one
				// EditableColumn in the grid view. We evaluate here a
				// check to see if buy_amount was posted for the Book model
				if (isset($posted['translation'])) {
					$output = trim($model->translation);
				}

				// similarly you can check if the name attribute was posted as well
				// if (isset($posted['name'])) {
				//   $output =  ''; // process as you need
				// }
				$out = Json::encode(['output' => $output, 'message' => '']);
			} else {
				Yii::error("Error saving langauge translation: " . ObjectError::getMsg($model), __METHOD__);
				$out = Json::encode(['output' => ObjectError::getMsg($model), 'message' => '']);
			}
			// return ajax json encoded response and exit
			return $out;
		}

		$searchModel = new MessageSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

		return $this->render('view', [
			'model'        => $lang,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Finds the Language model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param string $id
	 *
	 * @return Language the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Language::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Creates a new Language model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new Language();

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {

			if ($model->save()) {
				return $this->redirect(['view', 'id' => $model->language]);
			}

		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Language model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->language]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Language model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}
}
