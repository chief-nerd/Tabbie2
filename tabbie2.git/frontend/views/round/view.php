<?php

use kartik\grid\GridView;
use kartik\popover\PopoverX;
use kartik\sortable\Sortable;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model common\models\Round */

\frontend\assets\RoundviewAsset::register($this);

$this->title = $model->name;
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rounds'), 'url' => ['index', "tournament_id" => $tournament->id]];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="round-view">

    <h1><?= Html::encode($model->name) ?></h1>

    <div class="row">
        <div class="<?= (!$model->published) ? "col-md-12" : "col-md-6" ?>">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Yii::t("app", "Actions"); ?>
                </div>
                <div class="panel-body round-actions">
                    <? if ($tournament->status < \common\models\Tournament::STATUS_CLOSED): ?>
                        <? if (!$model->published): ?>
                            <?
                            if ($debateDataProvider->getCount() > 0)
                                echo Html::a(\kartik\helpers\Html::icon("thumbs-up") . "&nbsp" . Yii::t('app', 'Publish approved Draw'), ['publish', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-success']);
                            else
                                echo Html::a(\kartik\helpers\Html::icon("repeat") . "&nbsp" . Yii::t('app', 'Retry to generate Draw'), ['redraw', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-success loading']);
                            ?>

                            <!-- Split button -->
                            <div class="btn-group">
                                <?= Html::a(\kartik\helpers\Html::icon("cog") . "&nbsp" . Yii::t('app', 'Update Round'), ['update', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-primary']) ?>

                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only"><?= Yii::t("app", "Toggle Dropdown") ?></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <?
                                    $runs = \common\models\EnergyConfig::get("max_iterations", $tournament->id) / 2;
                                    for ($i = 1; $i <= 3; $i++): ?>
                                        <li>
                                            <?=
                                            Html::a(\kartik\helpers\Html::icon("refresh") . "&nbsp" . Yii::t('app', 'Continue Improving by') . " " . ($runs * $i / 1000) . "k", [
                                                'improve',
                                                'id' => $model->id,
                                                'runs' => ($runs * $i),
                                                'tournament_id' => $tournament->id],
                                                [
                                                    'class' => 'loading'
                                                ])
                                            ?>
                                        </li>
                                    <? endfor; ?>
                                    <li class="divider"></li>
                                    <li>
                                        <?=
                                        Html::a(\kartik\helpers\Html::icon("export") . "&nbsp" .
                                            Yii::t('app', 'Export Draw as JSON'), [
                                            'export', 'id' => $model->id, "tournament_id" => $tournament->id, "type" => "json"
                                        ])
                                        ?>
                                    </li>
                                    <li>
                                        <?=
                                        Html::a(\kartik\helpers\Html::icon("import") . "&nbsp" .
                                            Yii::t('app', 'Import Draw from JSON'), [
                                            'import', 'id' => $model->id, "tournament_id" => $tournament->id], [
                                                'data' => [
                                                    'confirm' => Yii::t('app', 'This will override the current draw! All information will be lost!'),
                                                ],
                                            ]
                                        )
                                        ?>
                                    </li>
                                </ul>
                            </div>

                            <div class="btn-group">
                                <?=
                                Html::a(\kartik\helpers\Html::icon("repeat") . "&nbsp" . Yii::t('app', 'Re-draw Round'), ['redraw', 'id' => $model->id, "tournament_id" => $tournament->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to re-draw the round? All information will be lost!'),
                                        'method' => 'post',
                                    ],
                                ]) ?>

                                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only"><?= Yii::t("app", "Toggle Dropdown") ?></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <?=
                                        Html::a(\kartik\helpers\Html::icon("trash") . "&nbsp" . Yii::t('app', 'Delete Round'), ['delete', 'id' => $model->id, "tournament_id" => $tournament->id], [
                                            'data' => [
                                                'confirm' => Yii::t('app', 'Are you sure you want to DELETE the round? All information will be lost!'),
                                                'method' => 'post',
                                            ],
                                        ])
                                        ?>
                                    </li>
                                </ul>
                            </div>
                        <? else:
                            echo $this->render("_switchAdjus", ["model" => $model]);
                        endif; ?>
                        <?= Html::a(\kartik\helpers\Html::icon("print") . "&nbsp" . Yii::t('app', 'Print Ballots'), ['printballots', 'id' => $model->id, "tournament_id" => $tournament->id], ['class' => 'btn btn-default']) ?>
                    <? endif; ?>
                </div>
            </div>
        </div>
        <? if ($model->published): ?>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Yii::t("app", "Public Access URLs"); ?>
                    </div>
                    <div class="panel-body">
                        <?
                        $url = \yii\helpers\Url::to(["public/draw",
                            "id" => $model->id,
                            "tournament_id" => $model->tournament_id,
                            "accessToken" => $tournament->accessToken
                        ], true);
                        echo \kartik\helpers\Html::a("Draw Display", $url, [
                            "class" => "btn btn-default"
                        ]);
                        ?>
                        &nbsp;
                        <?
                        $url = \yii\helpers\Url::to(["public/runner-view",
                            "id" => $model->id,
                            "tournament_id" => $model->tournament_id,
                            "accessToken" => $tournament->accessToken
                        ], true);
                        echo \kartik\helpers\Html::a("Runner Monitor", $url, [
                            "class" => "btn btn-default"
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        <? endif; ?>
    </div>
    <br>

    <div class="row">
        <div class="col-md-8 text-middle">
            <?
            $attributes = [];
            $attributes[] = [
                "label" => Yii::t("app", 'Round Status'),
                'value' => common\models\Round::statusLabel($model->status),
            ];
            $attributes[] = 'motion:ntext';

            if ($model->infoslide)
                $attributes[] = 'infoslide:ntext';

            $attributes[] = [
                "attribute" => 'energy',
                'label' => Yii::t("app", "Average Energy"),
                'format' => 'raw',
                'value' => (($debateDataProvider->getCount()) ? intval($model->energy / $debateDataProvider->getCount()) : 0),
            ];

            if ($model->displayed) {
                $attributes[] = [
                    "attribute" => 'prep_started',
                    'format' => 'raw',
                    'value' => Yii::$app->formatter->asDatetime($model->prep_started, 'short'),
                ];
            }

            $attributes[] = [
                "attribute" => 'time',
                'format' => 'raw',
                'label' => Yii::t("app", "Creation Time"),
                'value' => Yii::$app->formatter->asDatetime($model->time, 'short'),
            ];

            $attributes[] = [
                "attribute" => 'lastrun_temp',
                'format' => 'raw',
                'value' => Yii::$app->formatter->asDecimal($model->lastrun_temp, 15),
            ];

            echo DetailView::widget([
                'model' => $model,
                'attributes' => $attributes,
            ])
            ?>
        </div>
        <div class="col-md-4 text-center">
            <h3 style="margin-top:0; margin-bottom:20px;"><?= Yii::t("app", "Color Palette") ?></h3>
            <?= SwitchInput::widget([
                'name' => 'colorpattern',
                'type' => SwitchInput::RADIO,
                'value' => "strength",
                'items' => [
                    ['label' => Yii::t("app", "Strength"), 'value' => "strength"],
                    ['label' => Yii::t("app", "Gender"), 'value' => "gender"],
                    ['label' => Yii::t("app", "Regions"), 'value' => "region"],
                ],
                'pluginOptions' => ['size' => 'medium'],
                'labelOptions' => ["style" => "width: 80px"],
                'separator' => "<br/>",
                'pluginEvents' => [
                    "switchChange.bootstrapSwitch" => "function() { $('#debateDraw')[0].className = this.value; }",
                ],
            ]);
            /**
             * @TODO: Save setting while filtering and reload
             */
            ?>
        </div>
    </div>

    <a name="draw"></a>
    <?= $this->render("_filter", ["model" => $model, "debateSearchModel" => $debateSearchModel]) ?>
    <?
    $venueOptions = \common\models\search\VenueSearch::getSearchArray($model->tournament_id, true);
    $gridColumns = [
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '30px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            /*'detail'=>function ($model, $key, $index, $column) {
                return Yii::$app->controller->renderPartial('_debate_details', ['model'=>$model]);
            },*/
            'headerOptions' => ['class' => 'kartik-sheet-style'],
            'detailUrl' => \yii\helpers\Url::to(['round/debatedetails', "tournament_id" => $model->tournament_id]),
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'venue',
            'label' => Yii::t("app", 'Venue'),
            'width' => '8%',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $widget) use ($venueOptions) {
                if (!$model->round->published)
                    return $this->render("_changeVenue", [
                        "model" => $model,
                        "venueOptions" => $venueOptions
                    ]);
                else
                    return $model->venue->name;
            },
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'og_team.name',
            'label' => Yii::t("app", "OG Team"),
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'oo_team.name',
            'label' => Yii::t("app", "OO Team"),
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'cg_team.name',
            'label' => Yii::t("app", 'CG Team'),
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'co_team.name',
            'label' => Yii::t("app", 'CO Team'),
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'highestPoints',
            'label' => Yii::t("app", 'Points'),
            'width' => "80px",
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'panel.strength',
            'label' => Yii::t("app", 'Strength'),
            'width' => "80px",
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'language_status',
            'label' => Yii::t("app", 'Language'),
            'value' => function ($model, $key, $index, $widget) {
                return \common\models\User::getLanguageStatusLabel($model->getLanguage_status(), true);
            },
            'visible' => $model->tournament->has_esl,
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'label' => Yii::t("app", 'Energy'),
            'format' => 'raw',
            'value' => function ($model, $key, $index, $widget) {

                $ret = "";
                $found_warning = false;
                $found_error = false;
                $found_notice = false;
                try {
                    $msg = json_decode($model->messages);

                    foreach ($msg as $m) {
                        if (isset($m->penalty)) { //Legacy
                            if ($m->key == "error" && $m->penalty > 0) $found_error = true;
                            if ($m->key == "warning" && $m->penalty > 0) $found_warning = true;
                            if ($m->key == "notice" && $m->penalty > 0) $found_notice = true;
                        }
                    }

                    if ($found_notice)
                        $ret .= "&nbsp;" . \kartik\helpers\Html::icon("info-sign", ["class" => "text-gray"]);
                    if ($found_warning)
                        $ret .= "&nbsp;" . \kartik\helpers\Html::icon("warning-sign", ["class" => "text-warning"]);
                    if ($found_error)
                        $ret .= "&nbsp;" . \kartik\helpers\Html::icon("exclamation-sign", ["class" => "text-danger"]);

                    if (!$found_notice && !$found_warning && !$found_error)
                        $ret .= "&nbsp;" . \kartik\helpers\Html::icon("glyphicon-ok", ["class" => "text-success"]);

                } catch (\yii\base\ErrorException $ex) {
                    Yii::$app->session->addFlash("error", $ex->getMessage());

                    return \kartik\helpers\Html::icon("ban-circle", ["class" => "text-danger"]);
                }

                return $ret;
            },
            'width' => "20px",
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'panel',
            'label' => Yii::t("app", 'Adjudicator'),
            'format' => 'raw',
            'width' => '40%',
            'value' => function ($model, $key, $index, $widget) {
                /** @var \common\models\Debate $model */
                $list = [];
                $panel = common\models\Panel::findOne($model->panel_id);
                if ($panel && count($panel->adjudicators) > 0) {

                    $panel_chair = $model->getChair();
                    if ($panel_chair) {
                        $panel_chair_id = $panel_chair->id;
                    } else
                        $panel_chair_id = null;

                    foreach ($panel->getAdjudicators()->orderBy(['strength' => SORT_DESC])->all() as $adj) {

                        $popcontent = \kartik\helpers\Html::icon("refresh") . "&nbsp;" . Yii::t("app", "Loading ...");

                        $class = "label toLoad";
                        $class .= " " . common\models\Adjudicator::getCSSStrength($adj->strength);
                        if (isset($adj->society->country->region_id))
                            $class .= " " . \common\models\Country::getCSSLabel($adj->society->country->region_id);
                        if (isset($adj->user->gender))
                            $class .= " " . \common\models\User::getCSSGender($adj->user->gender);

                        $footer = Html::a(\kartik\helpers\Html::icon("folder-open") . "&nbsp;" . Yii::t("app", 'View Feedback'), ["feedback/adjudicator", "tournament_id" => $model->tournament_id, "AnswerSearch" => ["id" => $adj->id]], ['class' => 'btn btn-sm btn-default', 'target' => '_blank', 'data-pjax' => "0"]) .
                            Html::a(\kartik\helpers\Html::icon("folder-open") . "&nbsp;" . Yii::t("app", 'View User'), ["adjudicator/view", "id" => $adj->id, "tournament_id" => $model->tournament_id], ['class' => 'btn btn-sm btn-default', 'target' => '_blank', 'data-pjax' => "0"]);

                        if (!$model->round->published)
                            $footer .= Html::a(\kartik\helpers\Html::icon("eject") . "&nbsp;"
                                . Yii::t("app", 'Remove Judge'),
                                ["adjudicator/remove", "adj_id" => $adj->id, "debate_id" => $model->id, "tournament_id" => $model->tournament_id],
                                ['class' => 'btn btn-sm btn-default',
                                    'data' =>
                                        ['confirm' => Yii::t('app', 'Are you sure? You cannot put this judge back in (yet)!'),
                                            'method' => 'get'],
                                ]
                            );

                        $popup_obj = PopoverX::widget([
                            'header' => $adj->name . " " . $adj->user->getGenderIcon(),
                            'size' => 'md',
                            'placement' => PopoverX::ALIGN_BOTTOM,
                            'content' => $popcontent,
                            'footer' => $footer,
                            'toggleButton' => [
                                'label' => $adj->getName($panel->id),
                                'class' => 'btn btn-sm adj ' . $class,
                                "data-id" => $adj->id,
                                "data-strength" => $adj->strength,
                                "data-href" => yii\helpers\Url::to(["adjudicator/popup", "id" => $adj->id, "round_id" => $model->round_id, "tournament_id" => $model->tournament_id]),
                            ],
                        ]);

                        if ($adj->id == $panel_chair_id) {
                            array_unshift($list, ['content' => $popup_obj]);
                        } else
                            $list[]['content'] = $popup_obj;
                    }

                    return Sortable::widget([
                        'type' => Sortable::TYPE_GRID,
                        'items' => $list,
                        'disabled' => $model->round->published,
                        'handleLabel' => ($model->round->published) ? '' : \kartik\helpers\Html::icon("move"),
                        'connected' => true,
                        'showHandle' => true,
                        'options' => [
                            "data-panel" => $panel->id,
                            "class" => "adj_panel",
                        ],
                    ]);
                } else { // Empty panel line - but a placeholder there
                    //@todo: Allow dropping into it <ul> does not render
                    return Sortable::widget([
                        'type' => Sortable::TYPE_GRID,
                        'items' => [],
                        'disabled' => $model->round->published,
                        'handleLabel' => ($model->round->published) ? '' : \kartik\helpers\Html::icon("move"),
                        'connected' => true,
                        'showHandle' => true,
                        'options' => [
                            "data-panel" => $panel->id,
                            "class" => "adj_panel",
                        ],
                    ]);
                }

                return "";
            }
        ],
    ];

    echo GridView::widget([
        'dataProvider' => $debateDataProvider,
        'filterModel' => $debateSearchModel,
        'columns' => $gridColumns,
        'showPageSummary' => false,
        'layout' => "{items}\n{pager}",
        'bootstrap' => true,
        'pjax' => true,
        'hover' => true,
        'responsive' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 100],
        'id' => 'debateDraw',
        'options' => [
            'class' => 'strength',
            'data-href' => \yii\helpers\Url::to(["adjudicator/replace", "tournament_id" => $tournament->id]),
        ]

    ])
    ?>

</div>
