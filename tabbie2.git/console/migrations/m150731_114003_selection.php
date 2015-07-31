<?php

use yii\db\Schema;
use yii\db\Migration;

class m150731_114003_selection extends Migration
{
	public function up()
	{
		$this->addColumn("question", "param", "VARCHAR(255) DEFAULT NULL");
	}

	public function down()
	{
		$this->dropColumn("question", "param");
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
