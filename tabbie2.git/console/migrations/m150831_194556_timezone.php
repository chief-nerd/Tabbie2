<?php

use yii\db\Migration;

class m150831_194556_timezone extends Migration
{
	public function up()
	{
		$this->execute("ALTER TABLE `tournament`
                        ADD COLUMN `timezone` VARCHAR(100) NOT NULL COMMENT '' AFTER `end_date`;");
		\common\models\Tournament::updateAll(["timezone" => "Europe/Vienna"], ["timezone" => ""]);
	}

	public function down()
	{
		$this->dropColumn("tournament", "timezone");
	}
}
