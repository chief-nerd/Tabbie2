<?php

namespace common\components\filter;

use Yii;
use yii\base\ActionFilter;

class TournamentContextFilter extends ActionFilter {

    public function beforeAction($action) {
        $tournament = null;
        $tournamnet_id = null;
        if (isset($_REQUEST['id'])) {

            $tournamnet_id = (int) $_REQUEST['id'];
            if ($action->controller->hasMethod('setTournament') && $action->controller->hasMethod('getTournament')) {
                $action->controller->setTournament($tournamnet_id);
                return true;
            } else
                throw new \yii\web\HttpException(500, 'This filter was not properly setup');
        }
        throw new \yii\web\HttpException(500, 'This filter was wronly applied, id missing');
    }

}
