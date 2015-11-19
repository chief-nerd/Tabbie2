<?php
/**
 * MotionController.php File
 *
 * @package     Tabbie2
 * @author      jareiter
 * @version     1
 */

namespace api\controllers;


use api\models\Tournament;
use api\models\User;
use common\models\Adjudicator;
use common\models\Team;
use frontend\models\CheckinForm;
use yii\base\Exception;
use Yii;
use yii\base\NotSupportedException;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;

class CheckinController extends BaseRestController
{
    public $modelClass = 'api\models\User';

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['index'], $actions['create'], $actions['update'], $actions['view']);

        return $actions;
    }

    public function actionIndex($tournament)
    {

        $returnObject = [];
        $models = Team::find()->tournament($tournament)->all();

        foreach ($models as $model) {
            $team = [
                "type" => "Team",
                "name" => $model->name,
            ];

            if ($model->speakerA) {
                $team["speaker"][] = [
                    "name" => $model->speakerA->name,
                    "id" => CheckinForm::TEAMA . "-" . $model->id,
                    "checkedin" => $model->speakerA_checkedin,
                ];
            }
            if ($model->speakerB) {
                $team["speaker"][] = [
                    "name" => $model->speakerB->name,
                    "id" => CheckinForm::TEAMB . "-" . $model->id,
                    "checkedin" => $model->speakerB_checkedin,
                ];
            }
            $returnObject[] = $team;
        }

        $models = Adjudicator::find()->tournament($tournament)->all();

        foreach ($models as $model) {
            $adju = [
                "id" => CheckinForm::ADJU . "-" . $model->id,
                "type" => "Adjudicator",
                "name" => $model->name,
                "checkedin" => $model->checkedin,
            ];

            $returnObject[] = $adju;
        }

        return $returnObject;
    }

    public function actionView($id)
    {
        if (CheckinForm::checkNumber($id))
            throw new NotFoundHttpException("Not a valid id");

        $message = [];
        $type = substr($id, 0, 2);
        $real = intval(substr($id, 3, strlen($id)));

        switch ($type) {
            case CheckinForm::ADJU:
                $adj = Adjudicator::findOne($real);
                $message["type"] = "Adjudicator";
                if ($adj instanceof Adjudicator) {
                    $message["name"] = $adj->name;
                    if ($adj->checkedin) {
                        $message["status"] = "Checked in";
                    } else {
                        $message["status"] = "Missing";
                    }
                }
                break;
            case CheckinForm::TEAMA:
                $team = Team::findOne($real);
                $message["type"] = "Team Speaker A";
                if ($team instanceof Team && $team->speakerA instanceof \common\models\User) {
                    $message["name"] = $team->speakerA->name;
                    if ($team->speakerA_checkedin) {
                        $message["status"] = "Checked in";
                    } else {
                        $message["status"] = "Missing";
                    }
                }
                break;
            case CheckinForm::TEAMB:
                $team = Team::findOne($real);
                $message["type"] = "Team Speaker B";
                if ($team instanceof Team && $team->speakerB instanceof \common\models\User) {
                    $message["name"] = $team->speakerB->name;
                    if ($team->speakerA_checkedin) {
                        $message["status"] = "Checked in";
                    } else {
                        $message["status"] = "Missing";
                    }
                }
                break;
        }

        return $message;
    }

    public function actionUpdate($id, $key = null)
    {
        if (CheckinForm::checkNumber($id))
            throw new NotFoundHttpException("Not a valid id");

        $form = new CheckinForm([
            "number" => $id,
        ]);

        return $form->save();
    }
}