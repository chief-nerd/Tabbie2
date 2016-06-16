<?php
/**
 * MotionController.php File
 *
 * @package     Tabbie2
 * @author      jareiter
 * @version     1
 */

namespace api\controllers;

use common\models\AdjudicatorInPanel;
use common\models\Panel;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

class AdjudicatorController extends BaseRestController
{
    public $modelClass = 'api\models\Adjudicator';

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['index'], $actions['create'], $actions['update']);

        return $actions;
    }

    public function actionMove($adjudicator_id, $from_panel, $to_panel, $to_function = Panel::FUNCTION_WING)
    {
        $adjuInPanel = AdjudicatorInPanel::find()->where([
            "adjudicator_id" => $adjudicator_id,
            "panel_id" => $from_panel,
        ])->one();

        if ($adjuInPanel) {
            if ($adjuInPanel->function == Panel::FUNCTION_CHAIR) {
                //is chair in from_panel .. set strongest wing to chair in from_panel

                $strongestAdju = AdjudicatorInPanel::find()
                    ->joinWith("adjudicator")
                    ->where([
                        "panel_id" => $from_panel,
                    ])
                    ->orderBy(["strength" => SORT_ASC])
                    ->one();
                $strongestAdju->function = Panel::FUNCTION_CHAIR;
                if (!$strongestAdju->save()) {
                    throw new Exception("Can't save new chair in from_panel: " . $strongestAdju->getErrors());
                }
            }
            $adjuInPanel->panel_id = $to_panel;
            $adjuInPanel->function = $to_function;

            if ($to_function == Panel::FUNCTION_CHAIR) {
                //is set as chair in new panel ... set old_chair in to_panel to wing
                //set ALL chairs (there should only be one, but nevermind) to wings. This heals bad data.
                AdjudicatorInPanel::updateAll([
                    "function" => Panel::FUNCTION_WING,
                ], [
                    "panel_id" => $from_panel,
                    "function" => Panel::FUNCTION_CHAIR,
                ]);
            }

            if ($adjuInPanel->save()) {
                //adju moved, return OK
                return [
                    "status" => 200,
                    "message" => "Adjudicator successfully moved",
                ];
            } else {
                //error
                return [
                    "status" => 500,
                    "messages" => $adjuInPanel->getErrors(),
                ];
            }

        } else {
            throw new NotFoundHttpException('Could not find adjudicator #' . $adjudicator_id . 'in panel #' . $from_panel);
        }
    }
}