<?php
use  yii\widgets\DetailView;

?>

<div class="site-profile">
    <h1><?= $model->name ?></h1>

    <?php
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'apiUser.rl_timestamp',
            'apiUser.rl_remaining',
        ],
    ])
    ?>

    <hr>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">JWT Token for Header Authorization</h3>
        </div>
        <div class="panel-body" style="overflow-wrap: break-word">
            <?= $model->apiUser->getAuthorization() ?>
        </div>
    </div>
</div>