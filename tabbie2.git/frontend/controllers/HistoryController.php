<?php

namespace frontend\controllers;

use common\components\filter\UserContextFilter;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;
use common\models\User;
use yii\data\Pagination;

class HistoryController extends BaseuserController
{

	public function behaviors()
	{
		return [
			'userFilter' => [
				'class' => UserContextFilter::className(),
			],
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow'   => true,
						'actions' => ['index'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_user->id == Yii::$app->user->id || Yii::$app->user->isAdmin());
						}
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		$model = User::findOne($this->_user->id);

		$query = $model->getTeams()->joinWith("tournament")->orderBy(["tournament.end_date" => SORT_DESC]);

		$dataProvider = new ActiveDataProvider([
			"query" => $query,
			'pagination' => [
				'pageSize' => Yii::$app->params["tournament_per_history"],
			],
		]);

		return $this->render("index", [
			"model" => $model,
			"dataProvider" => $dataProvider,
		]);
	}

}
