<?php

use yii\db\Schema;
use yii\db\Migration;

class m150606_121905_Venue_group extends Migration {
	public function up() {
		$this->addColumn("venue", "group", "VARCHAR(100)");
	}

	public function down() {
		$this->dropColumn("venue", "group");
	}
}
