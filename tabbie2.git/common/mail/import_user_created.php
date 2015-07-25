<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$userlink = \yii\helpers\Url::to(["user/view", "id" => $user->id], true);
$tournamentlink = \yii\helpers\Url::to(["tournament/view", "id" => $tournament->id], true);
$support = Yii::$app->params["supportEmail"];
?>

Hi<?= Html::encode($user->givenname) ?>,<br>
<br>
I am writing you because you successfully registered for the<?= Html::a(Html::encode($tournament->getFullname()), $tournamentlink) ?>.
<br>
Since this tournament is running on<?= Html::a(Yii::$app->params["appName"], Yii::$app->params["appUrl"]) ?>, a new user account has been created for you with the following login credentials:
<br>
<br>
Username/Email:<?= $user->email ?><br>
Temporary Password:<?= $password ?><br>
<br>
Please login and fill the remaining fields of your profile (and change your password) at:<br>
<?= Html::a(Html::encode($userlink), $userlink) ?><br>
<br>
Then bring your smartphone/pad/laptop to the competition to enter eBallots and Feedback directly at:<br>
<?= Html::a(Html::encode($tournamentlink), $tournamentlink) ?><br>
<br>
If you have any question please feel free to write to our support team at:<br>
<?= Html::mailto(Html::encode($support), $support) ?><br>
<br>
Happy debating!<br>
The<?= Yii::$app->params["appName"] ?>Team<br>