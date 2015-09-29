<?php

use yii\db\Schema;
use yii\db\Migration;

class m150929_084437_EFL extends Migration {

	public function up() {
		$this->addColumn("tournament", "has_efl", "BOOLEAN DEFAULT 0");
		$this->addColumn("tournament", "has_novice", "BOOLEAN DEFAULT 0");
	}

	public function down() {
		$this->dropColumn("tournament", "has_efl");
		$this->dropColumn("tournament", "has_novice");
	}
}
