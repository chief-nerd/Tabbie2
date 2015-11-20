<?php
/**
 * Created by IntelliJ IDEA.
 * User: jareiter
 * Date: 29/09/15
 * Time: 15:46
 */

namespace console\controllers;

use common\models\Tournament;
use yii\console\Controller;

/**
 * Deploy to AWS Controller
 *
 * Class DeployController
 * @package console\controllers
 */
class DeployController extends Controller
{

    public $defaultAction = "start";

    /**
     * Deploy to ElasticBeansTalk
     */
    public function actionStart()
    {
        echo "Start deploying ....\n";

        system("pwd");
        return true;
    }
}