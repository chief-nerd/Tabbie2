<?php
/**
 * Created by IntelliJ IDEA.
 * User: jakob
 * Date: 01/01/16
 * Time: 23:31
 */

use kartik\helpers\Html;
use common\models\Tournament;

/** @var Tournament $model */

$button_output_buffer = "";

/** eBALLOTS */
if (Yii::$app->user->hasChairedLastRound($info) && !$info['debate']->result instanceof \common\models\Result) {
    $button_output_buffer .= Html::a(
        Html::icon("envelope") . "&nbsp;" . Yii::t('app', 'Result'),
        ['result/create', "id" => $info['debate']->id, "tournament_id" => $model->id],
        ['class' => 'btn btn-success']);
}

/** eFEEDBACK */
$refs = $model->hasOpenFeedback(Yii::$app->user->id);
if (is_array($refs) && $model->getTournamentHasQuestions()->count() > 0) {

    $ref = array_shift($refs);
    $param = $ref;
    unset($param["debate"]);
    $param["id"] = $ref["debate"]->id;

    $content_div[] = Html::a(
        Html::icon("comment") . "&nbsp;" . Yii::t('app', 'Feedback #{num}', ['num' => $ref["debate"]->round->label]),
        array_merge(['feedback/create', "tournament_id" => $model->id], $param),
        [
            "class" => "btn btn-success",
            "style" => "width: " . ((count($refs) > 0) ? 85 : 100) . "%",
        ]
    );

    $items = [];
    if (count($refs) > 0) {

        $item = [];
        foreach ($refs as $ref) {
            $param = $ref;
            unset($param["debate"]);
            if (!$ref["debate"] instanceof \common\models\Debate) continue;
            $param["id"] = $ref["debate"]->id;

            $items[] = [
                "label" => Html::icon("comment") . "&nbsp;" . Yii::t('app', 'Feedback #{num}', ['num' => $ref["debate"]->round->label]),
                "url" => array_merge(['feedback/create', "tournament_id" => $model->id], $param)
            ];
        }

        $toggle[] = HTML::tag("span", "", ["class" => "caret"]);
        $toggle[] = HTML::tag("span", "Toggle Dropdown", ["class" => "sr-only"]);

        $content_div[] = Html::tag("button", implode(" ", $toggle), [
            "type" => "button",
            "class" => "btn btn-success dropdown-toggle",
            "style" => "width: 15%;",
            "data-toggle" => "dropdown",
            "aria-haspopup" => "true",
            "aria-expanded" => "false"
        ]);

        $content_div[] = \yii\bootstrap\Dropdown::widget([
            "items" => $items,
            "encodeLabels" => false
        ]);
    }

    $wrapper_div = Html::tag("div", implode(" ", $content_div), [
        "class" => "btn-group splitbutton",
    ]);

    $button_output_buffer .= $wrapper_div;
}

//Do we have a button to display?
if (strlen($button_output_buffer) > 0):
    ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo Yii::t("app", "Enter Information") ?></h3>
        </div>
        <div class="panel-body">
            <div class="btn-group btn-group-justified" role="group" aria-label="action-panel">
                <?php echo $button_output_buffer ?>
            </div>
        </div>
    </div>
<? endif; ?>