<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>

Hello <?= Html::encode($user->username) ?>,<br>
<br>
Follow the link below to reset your password:<br>
<?= Html::a(Html::encode($resetLink), $resetLink) ?><br>
