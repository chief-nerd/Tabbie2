<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?> :: <?= Html::encode(Yii::$app->params["appName"]) ?></title>
        <?php $this->head() ?>
    </head>
    <?
    if ($this->context->hasMethod("_getContext")) {
        $tournament = $this->context->_getContext();
        if ($tournament instanceof \common\models\Tournament && (Yii::$app->user->isTabMaster($tournament) || Yii::$app->user->isAdmin())) {
            $addclass = "movedown";
        }
    }
    ?>
    <body class="<?= isset($addclass) ? $addclass : "" ?>">
        <?php $this->beginBody() ?>
        <div class="flashes">
            <?= Alert::widget() ?>
        </div>
        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->params["appName"],
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems = [
                ['label' => 'Home', 'url' => ['/site/index']],
                ['label' => 'About', 'url' => ['/site/about']],
                ['label' => 'Tournaments', 'url' => ['tournament/index']],
                ['label' => 'Contact', 'url' => ['/site/contact']],
            ];
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
                $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
            } else {
                $menuItems[] = [
                    'label' => Yii::$app->user->getModel()->name . "'s Profile",
                    'url' => ['user/view', 'id' => Yii::$app->user->id],
                ];
                $menuItems[] = [
                    'label' => 'Logout',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav menu navbar-right'],
                'items' => $menuItems,
            ]);
            NavBar::end();
            ?>
            <?
            /* @var $tournament common\models\Tournament */
            if ($this->context->hasMethod("_getContext")) {
                $tournament = $this->context->_getContext();
                if ($tournament instanceof \common\models\Tournament && (Yii::$app->user->isTabMaster($tournament) || Yii::$app->user->isAdmin())) {

                    NavBar::begin([
                        'brandLabel' => $tournament->name . " Tabmaster",
                        'brandUrl' => Yii::$app->urlManager->createUrl(["tournament/view", "id" => $tournament->id]),
                        'options' => [
                            'class' => 'navbar navbar-default navbar-fixed-top tabmaster',
                        ],
                    ]);

                    $menuItems = [
                        ['label' => 'Venues', 'url' => ["venue/index", "tournament_id" => $tournament->id]],
                        ['label' => 'Teams', 'url' => ["team/index", "tournament_id" => $tournament->id]],
                        ['label' => 'Adjudicators', 'url' => ['adjudicator/index', "tournament_id" => $tournament->id]],
                        ['label' => 'Rounds', 'url' => ['round/index', "tournament_id" => $tournament->id]],
                        ['label' => 'Draws', 'url' => ['draw/index', "tournament_id" => $tournament->id]],
                        ['label' => 'Feedback', 'url' => ['feedback/index', "tournament_id" => $tournament->id]],
                    ];

                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav navbar-right'],
                        'items' => $menuItems,
                    ]);
                    NavBar::end();
                }
            }
            ?>
            <div class="container">
                <?=
                Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left"><?= Yii::$app->params["appName"] ?> &copy; <?= date('Y') ?></p>
                <p class="pull-right"><?= HTML::a("Contact", 'mailto:' . Yii::$app->params["adminEmail"]) ?></p>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
