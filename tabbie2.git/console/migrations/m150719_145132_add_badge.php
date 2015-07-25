<?php

use yii\db\Schema;
use yii\db\Migration;

class m150719_145132_add_badge extends Migration
{

	// Use safeUp/safeDown to run migration code within a transaction
	public function safeUp()
	{
		$this->addColumn("tournament", "badge", "VARCHAR(255)");
	}

	public function safeDown()
	{
		$this->dropColumn("tournament", "badge");
	}
}
