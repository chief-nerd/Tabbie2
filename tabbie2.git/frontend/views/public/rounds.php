<?php

use yii\helpers\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->title = "Rounds";
?>
<div class="text-center">
	<?= Html::img($tournament->logo, ["width" => "200px", "style" => "margin-top: 40px;"]); ?>
</div>
<br>
<div class="row">
	<div class="col-xs-12 text-center rounds"
		 data-href="<?= yii\helpers\Url::to(["public/rounds", "tournament_id" => $tournament->id, "accessToken" => $tournament->accessToken]); ?>">
		<?= $already ?>
	</div>
</div>

