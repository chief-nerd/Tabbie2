<?php

use yii\db\Schema;
use yii\db\Migration;

class m150916_132342_translation extends Migration {

	public function up() {
		$this->update("user", ["role" => 90], ["role" => 12]);

		$this->execute("CREATE TABLE IF NOT EXISTS source_message (
			id INTEGER PRIMARY KEY AUTO_INCREMENT,
			category VARCHAR(32),
			message TEXT
		);");

		$this->execute("
			CREATE TABLE IF NOT EXISTS message (
				id INTEGER,
				language VARCHAR(16),
				translation TEXT,
				PRIMARY KEY (id, language),
				CONSTRAINT fk_message_source_message FOREIGN KEY (id)
					REFERENCES source_message (id) ON DELETE CASCADE ON UPDATE RESTRICT
			);");

		$this->execute("CREATE TABLE IF NOT EXISTS language (
			  `language` VARCHAR(16) NOT NULL COMMENT '',
			  `label` VARCHAR(100) NOT NULL COMMENT '',
			  `last_update` TIMESTAMP NOT NULL DEFAULT NOW() COMMENT '',
			  PRIMARY KEY (`language`)  COMMENT '')
			ENGINE = InnoDB
			DEFAULT CHARACTER SET = utf8
			COLLATE = utf8_general_ci;
			");

		$this->execute("CREATE TABLE IF NOT EXISTS language_maintainer (
		  `user_id` INT(11) UNSIGNED NOT NULL COMMENT '',
		  `language_language` VARCHAR(16) NOT NULL COMMENT '',
		  PRIMARY KEY (`user_id`, `language_language`)  COMMENT '',
		  INDEX `fk_user_has_language_language1_idx` (`language_language` ASC)  COMMENT '',
		  INDEX `fk_user_has_language_user1_idx` (`user_id` ASC)  COMMENT '',
		  CONSTRAINT `fk_user_has_language_user1`
			FOREIGN KEY (`user_id`)
			REFERENCES `user` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_user_maintaines_language`
			FOREIGN KEY (`language_language`)
			REFERENCES `language` (`language`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION);
		");
	}

	public function down() {
		$this->dropTable("language_maintainer");
		$this->dropTable("language");

		$this->dropTable("message");
		$this->dropTable("source_message");

		$this->update("user", ["role" => 12], ["role" => 90]);
	}
}
