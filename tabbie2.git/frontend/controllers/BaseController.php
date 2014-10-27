<?php

namespace frontend\controllers;

use Yii;
use common\models\Tournament;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class BaseController extends Controller {

    /**
     * Current Tournament Scope
     * Does not exist in index and create
     * @var Tournament
     */
    protected $_tournament;

    /**
     * Sets the Tournament Context
     * @param integer $id
     * @return boolean
     */
    public function _setContext($id) {
        $this->_tournament = Tournament::findByPk($id);
        if ($this->_tournament instanceof Tournament)
            return true;
        else
            return false;
    }

    /**
     * Returns the current context
     * @return Tournament
     */
    public function _getContext() {
        return $this->_tournament;
    }

}
