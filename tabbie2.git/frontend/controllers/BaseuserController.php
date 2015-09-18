<?php

namespace frontend\controllers;

use common\models\User;
use Yii;
use yii\web\Controller;

class BaseuserController extends BaseController
{

	/**
	 * Current User Scope
	 * Does not exist in index
	 *
	 * @var User
	 */
	protected $_user;

	/**
	 * Sets the User Context
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function _setContext($id) {
		$this->_user = User::findOne($id);
		//Yii::trace("Set Context for " . $this->_user->name . " (" . $this->_user->id . ")", __METHOD__);
		if ($this->_user instanceof User)
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
		return $this->_user;
	}

}
