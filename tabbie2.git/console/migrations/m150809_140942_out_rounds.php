<?php

use yii\db\Schema;
use yii\db\Migration;

class m150809_140942_out_rounds extends Migration
{
	public function up()
	{
		$this->addColumn("round", "type", "TINYINT(4) NOT NULL DEFAULT 0 AFTER `tournament_id`");
		$this->addColumn("round", "level", "TINYINT(4) NOT NULL DEFAULT 0 AFTER `type`");
		$this->execute("ALTER TABLE `tabbie`.`round`
                        CHANGE COLUMN `number` `label` VARCHAR(100) NOT NULL ;");

		$this->execute("ALTER TABLE `tabbie`.`adjudicator`
                        ADD COLUMN `breaking` TINYINT(4) NOT NULL DEFAULT 0 AFTER `active`;");
	}

	public function down()
	{
		$this->dropColumn("round", "type");
		$this->dropColumn("round", "level");
		$this->execute("ALTER TABLE `tabbie`.`round`
                        CHANGE COLUMN `label` `number` INTEGER(8) NOT NULL;");
	}
}
