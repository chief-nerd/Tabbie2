<?php

use common\models\Tournament;
use common\models\Team;

/** @var Tournament $model */
$reg_info_text = "";
$role = $model->user_role();
if ($role != false) {
    if ($role instanceof Team) {
        $with = "";
        if ($role->speakerA_id == Yii::$app->user->id) {
            //I am speaker A
            if (isset($role->speakerB->name)) {
                $with = Yii::t("app", "together with {teammate}", [
                    "teammate" => $role->speakerB->name,
                ]);
            } else {
                $with = Yii::t("app", "as ironman");
            }
        } else {
            //I am speaker B
            if (isset($role->speakerA->name)) {
                $with = Yii::t("app", "together with {teammate}", [
                    "teammate" => $role->speakerA->name,
                ]);
            } else {
                $with = Yii::t("app", "as ironman");
            }
        }

        $reg_info_text .= Yii::t("app", "You are registered as team <br> '{team}' {with} for {society}", [
            "team" => $role->name,
            "with" => $with,
            "society" => $role->society->fullname,
        ]);
    } elseif ($role instanceof \common\models\Adjudicator) {
        $reg_info_text .= Yii::t("app", "You are registered as adjudicator for {society}", [
            "society" => $role->society->fullname,
        ]);
    }
}

if (strlen($reg_info_text) > 0):
    ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo Yii::t("app", "Registration Information") ?></h3>
        </div>
        <div class="panel-body">
            <?php echo $reg_info_text ?>
        </div>
    </div>
<? endif; ?>