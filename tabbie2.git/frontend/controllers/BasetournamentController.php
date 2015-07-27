<?php

namespace frontend\controllers;

use common\models\Tournament;
use Yii;
use yii\web\Controller;
use yii\web\View;
use kartik\helpers\Html;

class BaseTournamentController extends Controller {

	/**
	 * Current Tournament Scope
	 * Does not exist in index and create
	 *
	 * @var Tournament
	 */
	protected $_tournament;

	/**
	 * Sets the Tournament Context
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function _setContext($id) {
		$this->_tournament = Tournament::findByPk($id);
		\Yii::trace("Set Context for " . $this->_tournament->fullname . " (" . $this->_tournament->id . ")", __METHOD__);
		if ($this->_tournament instanceof Tournament)
			return true;
		else
			return false;
	}

	/**
	 * Returns the current context
	 *
	 * @return Tournament
	 */
	public function _getContext() {
		return $this->_tournament;
	}

	public function beforeAction($action)
	{
		Yii::$app->view->on(View::EVENT_BEGIN_PAGE, function () {
			$view = Yii::$app->controller->view;
			$model = $this->_tournament;
			$view->registerMetaTag(["property" => "og:title", "content" => Yii::t("app", "{tournament} on Tabbie2", ["tournament" => $model->fullname])], "og:title");
			$view->registerMetaTag(["property" => "og:image", "content" => $model->getLogo(true)], "og:image");
			$view->registerMetaTag(["property" => "og:description", "content" =>
				Yii::t("app", "{name} is taking place from {start} to {end} hosted by {host} in {country}", [
					"name"    => $model->name,
					"start"   => Yii::$app->formatter->asDate($model->start_date, "short"),
					"end"     => Yii::$app->formatter->asDate($model->end_date, "short"),
					"host"    => Html::encode($model->hostedby->fullname),
					"country" => Html::encode($model->hostedby->country->name),
				])],
				"og:description");

			$view->registerLinkTag(["rel" => "apple-touch-icon", "href" => $model->getLogo(true)], "apple-touch-icon");
		});

		return parent::beforeAction($action);
	}
}
