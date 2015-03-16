<?php

namespace frontend\controllers;

use common\models\Tournament;
use Yii;
use yii\web\Controller;

class BaseController extends Controller {

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

}
