<?php

namespace frontend\controllers;

use common\components\filter\UserContextFilter;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;
use common\models\User;
use yii\data\Pagination;

class HistoryController extends BaseUserController {

	public function behaviors() {
		return [
			'userFilter' => [
				'class' => UserContextFilter::className(),
			],
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index'],
						'matchCallback' => function ($rule, $action) {
							return ($this->_user->id == Yii::$app->user->id || Yii::$app->user->isAdmin());
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

	public function actionIndex() {
		$model = User::findOne($this->_user->id);

		$query = $model->getTeams()->joinWith("tournament")->orderBy(["tournament.end_date" => SORT_DESC]);

		$countQuery = clone $query;
		$pages = new Pagination([
			'totalCount' => $countQuery->count(),
			'pageSize' => Yii::$app->params["tournament_per_history"],
		]);
		$teams = $query->offset($pages->offset)
		               ->limit($pages->limit)
		               ->all();

		return $this->render("index", [
			"model" => $model,
			"teams" => $teams,
			"pages" => $pages,
		]);
	}

}
