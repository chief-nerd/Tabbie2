<?php

use yii\db\Schema;
use yii\db\Migration;

class m150920_172853_utf8_translation extends Migration {

	public function up() {
		$this->execute("ALTER TABLE `message`
		CHANGE COLUMN `translation` `translation` TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL COMMENT ''");
	}

	public function down() {

	}
}
