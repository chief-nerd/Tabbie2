<?php

use yii\db\Schema;
use yii\db\Migration;

class m150729_104352_accepted extends Migration
{
	public function up()
	{
		$this->execute("
                ALTER TABLE `tabbie`.`team_strike`
                ADD COLUMN `accepted` TINYINT(1) NOT NULL DEFAULT 1 AFTER `user_clash_id`;");
		$this->execute("
                ALTER TABLE `tabbie`.`adjudicator_strike`
                ADD COLUMN `accepted` TINYINT(1) NOT NULL DEFAULT 1 AFTER `user_clash_id`;
                    ");
	}

	public function down()
	{
		echo "m150729_104352_accepted cannot be reverted.\n";

		return false;
	}

	/*
	// Use safeUp/safeDown to run migration code within a transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
