<?php

namespace common\components\filter;

use Yii;
use yii\base\ActionFilter;

class TournamentContextFilter extends ActionFilter {

    public function beforeAction($action) {
        $tournament = null;
        $tournamnet_id = null;
        if (isset($_GET["tournament_id"])) {
            $tournamnet_identifier = (int) Yii::$app->getRequest()->getQueryParam("tournament_id", null);
            if ($action->controller->hasMethod('_setContext') && $action->controller->hasMethod('_getContext')) {
                $action->controller->_setContext($tournamnet_identifier);
                return true;
            } else
                throw new \yii\web\HttpException(500, 'This filter was not properly setup');
        }
        throw new \yii\web\HttpException(500, 'This filter was wronly applied, id missing');
    }

}
