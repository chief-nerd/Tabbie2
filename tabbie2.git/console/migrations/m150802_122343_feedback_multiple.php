<?php

use yii\db\Schema;
use yii\db\Migration;

class m150802_122343_feedback_multiple extends Migration
{
	public function up()
	{
		$this->execute("ALTER TABLE `tabbie`.`feedback`
						ADD COLUMN `to_type` TINYINT(4) NULL DEFAULT NULL AFTER `time`,
						ADD COLUMN `to_id` INTEGER NULL DEFAULT NULL AFTER `to_type`;");
	}

	public function down()
	{
		$this->dropColumn("feedback", "to_type");
		$this->dropColumn("feedback", "to_id");

		return false;
	}
}
