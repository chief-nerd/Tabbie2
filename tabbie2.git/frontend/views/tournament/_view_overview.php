<?
use kartik\helpers\Html;
use yii\widgets\DetailView;
use common\models\Panel;
use common\models\Team;

/** @var common\models\Tournament $model */
?>
<div class="row" id="tournament_title">
    <div class="col-xs-12 col-md-12 col-lg-8" style="margin-bottom: 20px">
        <div class="col-xs-6 col-sm-2 block-center">
            <?= $model->getLogoImage("auto", "100") ?>
        </div>
        <div class="col-xs-6 col-sm-10 tournament_title">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>
    <?php
    if ($model->status === \common\models\Tournament::STATUS_RUNNING): ?>
        <div class="col-xs-12 col-md-8 col-lg-8">
            <?
            $info = $model->getLastDebateInfo(Yii::$app->user->id);
            $button_output_buffer = "";
            $text_output_buffer = "";

            if ($info) {
                if (Yii::$app->user->hasChairedLastRound($info) && !$info['debate']->result instanceof \common\models\Result) {
                    $button_output_buffer .= Html::a(
                        Html::icon("envelope") . "&nbsp;" . Yii::t('app', 'Result'),
                        ['result/create', "id" => $info['debate']->id, "tournament_id" => $model->id],
                        ['class' => 'btn btn-success']);
                }
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

                    if (count($refs) > 0) {

                        $item = [];
                        foreach ($refs as $ref) {
                            $param = $ref;
                            unset($param["debate"]);
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
            } else {
                /** @var Tournament $model */
                $role = $model->user_role();
                if ($role != false) {
                    if ($role instanceof Team) {

                        $with = "";
                        if ($role->speakerA_id == Yii::$app->user->id) {
                            if (isset($role->speakerB->name)) {
                                //I am speaker A
                                $with = Yii::t("app", "together with {teammate}", [
                                    "teammate" => $role->speakerB->name,
                                ]);
                            } else {
                                $with = Yii::t("app", "as ironman");
                            }
                        } else {
                            if (isset($role->speakerA->name)) {
                                //I am speaker B
                                $with = Yii::t("app", "together with {teammate}", [
                                    "teammate" => $role->speakerA->name,
                                ]);
                            } else {
                                $with = Yii::t("app", "as ironman");
                            }
                        }

                        $text_output_buffer .= Yii::t("app", "You are registered as team <br> '{team}' {with} for {society}", [
                            "team" => $role->name,
                            "with" => $with,
                            "society" => $role->society->fullname,
                        ]);
                    } elseif ($role instanceof \common\models\Adjudicator) {
                        $text_output_buffer .= Yii::t("app", "You are registered as adjudicator for {society}", [
                            "society" => $role->society->fullname,
                        ]);
                    }
                }
            }
            if ($model->isConvenor(Yii::$app->user->id) || $model->isTabMaster(Yii::$app->user->id)) {
                //$button_output_buffer .= "&nbsp;" . Html::a(Html::icon("film") . "&nbsp;" . Yii::t('app', 'Display Draw'), ['public/rounds', "tournament_id" => $model->id, "accessToken" => $model->accessToken], ['class' => 'btn btn-default']);
            }

            if (strlen($text_output_buffer) > 0):
                ?>
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo Yii::t("app", "Registration Information") ?></h3>
                    </div>
                    <div class="panel-body">
                        <?php echo $text_output_buffer ?>
                    </div>
                </div>
            <? endif;

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

            <? if ($info): ?>
                <div class="panel panel-default debate-info">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo Yii::t("app", "Round #{num} Info", ["num" => $info["debate"]->round->number]) ?></h3>
                    </div>
                    <div class="panel-body">
                        <?php
                        if ($info["type"] == "team") {
                            $pos = Team::getPosLabel($info["pos"]);
                        }

                        if ($info["type"] == "judge") {
                            $pos = Panel::getFunctionLabel($info["pos"]);
                        }
                        ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <?php echo Yii::t("app", "You are <b>{pos}</b> in room <b>{room}</b>.", [
                                    "pos" => strtolower($pos),
                                    "room" => $info["debate"]->venue->name,
                                ]) ?></div>
                            <div class="col-xs-12 col-sm-6">
                                <?php echo Yii::t("app", "Round starts at: <b>{time}</b>", [
                                    "time" => Yii::$app->formatter->asTime($info["debate"]->round->prep_started . "+15min", "short"),
                                ]) ?>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px">
                            <div class="col-xs-12">
                                <?php echo Yii::t("app", "Motion") ?>:<br>
                                <?php echo $info["debate"]->round->motion ?>
                            </div>
                            <? if ($info["debate"]->round->infoslide): ?>
                                <div class="col-xs-12">
                                    <?php echo Yii::t("app", "InfoSlide") ?>:<br>
                                    <?php echo $info["debate"]->round->motion ?>
                                </div>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
                <? if ($info["pos"] == Panel::FUNCTION_CHAIR && $info["debate"] instanceof \common\models\Debate): ?>
                    <div class="panel panel-default debate-teams">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo Yii::t("app", "Round #{num} Teams", ["num" => $info["debate"]->round->number]) ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="row" style="margin-top: 10px">
                                <? foreach (Team::getPos() as $index => $pos): ?>
                                    <div class="team col-xs-12 col-sm-6">
                                        <?= Team::getPosLabel($index) ?>: <br>
                                        <?php if ($info["debate"]->{$pos . "_team"} instanceof Team): ?>
                                            <?= $info["debate"]->{$pos . "_team"}->name ?><br/>
                                            <?= ($info["debate"]->{$pos . "_team"}->speakerA instanceof \common\models\User) ?
                                                $info["debate"]->{$pos . "_team"}->speakerA->givenname : ""
                                            ?>
                                            <?= ($info["debate"]->{$pos . "_team"}->speakerB instanceof \common\models\User) ?
                                                " & " . $info["debate"]->{$pos . "_team"}->speakerB->givenname : ""
                                            ?>
                                        <? endif; ?>
                                    </div>
                                <? endforeach; ?>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
            <? endif; ?>
        </div>
    <? endif; ?>
    <div class="col-xs-12 col-md-4 col-lg-4">
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'hostedby.fullname:text:Hosted By',
                [
                    "attribute" => 'convenor',
                    'label' => "Convenor",
                    'format' => 'raw',
                    'value' => implode(", ", \yii\helpers\ArrayHelper::getColumn($model->convenors, "name")),
                ],
                [
                    "attribute" => 'CATeam',
                    'label' => "CA Team",
                    'format' => 'raw',
                    'value' => implode(", ", \yii\helpers\ArrayHelper::getColumn($model->cAs, "name")),
                ],
                [
                    "attribute" => 'tabmaster',
                    'label' => "Tab Master",
                    'format' => 'raw',
                    'value' => implode(", ", \yii\helpers\ArrayHelper::getColumn($model->tabmasters, "name")),
                ],
                [
                    "attribute" => 'start_date',
                    'format' => 'raw',
                    'value' => Yii::$app->formatter->asDateTime($model->start_date, "short"),
                ],
                [
                    "attribute" => 'end_date',
                    'format' => 'raw',
                    'value' => Yii::$app->formatter->asDateTime($model->end_date, "short"),
                ],
                [
                    "attribute" => 'timezone',
                    'format' => 'raw',
                    'value' => $model->getFormatedTimeZone(),
                ],
            ],
        ])
        ?>
    </div>
</div>