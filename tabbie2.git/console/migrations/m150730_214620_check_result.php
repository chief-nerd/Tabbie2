<?php

use yii\db\Schema;
use yii\db\Migration;

class m150730_214620_check_result extends Migration
{
	public function up()
	{
		$this->addColumn("result", "checked", "TINYINT DEFAULT 0");
	}

	public function down()
	{
		$this->dropColumn("result", "checked");
	}
}
