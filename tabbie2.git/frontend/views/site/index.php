<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = Yii::$app->params["slogan"];
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome to <?= Yii::$app->params["appName"] ?>!</h1>

        <p class="lead"><?= Yii::$app->params["slogan"] ?><br>Start by choosing an option:</p>

        <p>
            <?= Html::a(Yii::t("app", "View existing Tournament"), ['tournament/index'], ["class" => "btn btn-lg btn-success"]) ?>
            &nbsp;&nbsp;or&nbsp;&nbsp;
            <?= Html::a(Yii::t("app", "Create new Tournament"), ['tournament/create'], ["class" => "btn btn-lg btn-primary"]) ?>
        </p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>New</h2>

                <p>All new on a centraliesed webserver. <br>Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                    sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.
                    At vero eos et accusam et justo duo dolores et ea rebum.</p>

            </div>
            <div class="col-lg-4">
                <h2>Way</h2>

                <p>Viewer / App / Modules ... there are many ways to perfection<br>Lorem ipsum dolor sit amet,
                    consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna
                    aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
            </div>
            <div class="col-lg-4">
                <h2>Tabbing</h2>

                <p>Rock solid tabbing algorithm based on the famous Tabbie software<br>Lorem ipsum dolor sit amet,
                    consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna
                    aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
            </div>
        </div>

    </div>
</div>
