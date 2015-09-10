<?php

use yii\widgets\DetailView;
use kartik\helpers\Html;
use \common\models\Team;
use common\models\Panel;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $model common\models\Tournament */

if ($model->status >= \common\models\Tournament::STATUS_CLOSED) {
    \frontend\assets\ChartAsset::register($this);
} else {
    \frontend\assets\AppAsset::register($this);
}

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tournaments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tournament-view">

    <div class="tabarea">
        <? if ($model->status >= \common\models\Tournament::STATUS_CLOSED) {
            $items = [
                    [
                            'label'   => Yii::t("app", "Overview"),
                            'content' => $this->render("_view_overview", compact("model")),
                            'options' => ['id' => 'overview'],
                    ],
                    [
                            'label'       => Yii::t("app", "Motions"),
                            'linkOptions' => ['data-url' => \yii\helpers\Url::to(['stats/motion', "tournament_id" => $model->id])],
                            'options'     => ['id' => 'motions'],
                    ],
                    [
                            'label'       => Yii::t("app", "Team Tab"),
                            'linkOptions' => ['data-url' => \yii\helpers\Url::to(['stats/team-tab', "tournament_id" => $model->id])],
                            'options'     => ['id' => 'team-tab'],
                    ],
                    [
                            'label'       => Yii::t("app", "Speaker Tab"),
                            'linkOptions' => ['data-url' => \yii\helpers\Url::to(['stats/speaker-tab', "tournament_id" => $model->id])],
                            'options'     => ['id' => 'speaker-tab'],
                    ],
                    [
                            'label'       => Yii::t("app", "Out-Rounds"),
                            'linkOptions' => ['data-url' => \yii\helpers\Url::to(['stats/outrounds', "tournament_id" => $model->id])],
                            'options'     => ['id' => 'outrounds'],
                    ],
                    [
                            'label'       => Yii::t("app", "Breaking Adjudicators"),
                            'linkOptions' => ['data-url' => \yii\helpers\Url::to(['stats/breaking-adjudicators', "tournament_id" => $model->id])],
                            'options'     => ['id' => 'breaking-adjudicators'],
                    ],
                /*[
					'label' => Yii::t("app", "Speaks Distrubution"),
					'linkOptions' => ['data-url' => \yii\helpers\Url::to(['stats/speaks', "tournament_id" => $model->id])],
					'options' => ['id' => 'speaks-distribution'],
				],*/
            ];
            echo TabsX::widget([
                    'items'        => $items,
                    'position'     => TabsX::POS_ABOVE,
                    'align'        => TabsX::ALIGN_CENTER,
                    'pluginEvents' => [
                            "tabsX.success" => "function() {
                                if (typeof init == 'function') {
                                   init();
                                }
                            }",
                    ],
            ]);

        } else {
            echo $this->render("_view_overview", compact("model"));
        }
        ?>
    </div>
</div>

<!-- Google Structured Data -->
<script type="application/ld+json">
<?= $model->getSchema() ?>



</script>
