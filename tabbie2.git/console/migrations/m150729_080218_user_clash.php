<?php

use yii\db\Schema;
use yii\db\Migration;

class m150729_080218_user_clash extends Migration
{
	public function up()
	{
		$this->execute("ALTER TABLE `tabbie`.`team_strike`
          ADD COLUMN `user_clash_id` INT(11) NULL DEFAULT NULL AFTER `tournament_id`,
          ADD INDEX `fk_team_strike_user_clash1_idx` (`user_clash_id` ASC);");

		$this->execute("ALTER TABLE `tabbie`.`adjudicator_strike`
          ADD COLUMN `user_clash_id` INT(11) NULL DEFAULT NULL AFTER `tournament_id`,
          ADD INDEX `fk_adjudicator_strike_user_clash1_idx` (`user_clash_id` ASC);");

		$this->execute("
        CREATE TABLE IF NOT EXISTS `tabbie`.`user_clash` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `user_id` INT(11) UNSIGNED NOT NULL,
          `clash_with` INT(11) UNSIGNED NOT NULL,
          `reason` VARCHAR(255) NULL DEFAULT NULL,
          `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          INDEX `fk_user_has_user_user2_idx` (`clash_with` ASC),
          INDEX `fk_user_has_user_user1_idx` (`user_id` ASC),
          CONSTRAINT `fk_user_has_user_user1`
            FOREIGN KEY (`user_id`)
            REFERENCES `tabbie`.`user` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_user_has_user_user2`
            FOREIGN KEY (`clash_with`)
            REFERENCES `tabbie`.`user` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION)
        ENGINE = InnoDB
        DEFAULT CHARACTER SET = utf8
        COLLATE = utf8_unicode_ci;");

		$this->execute("
        ALTER TABLE `tabbie`.`team_strike`
        ADD CONSTRAINT `fk_team_strike_user_clash1`
          FOREIGN KEY (`user_clash_id`)
          REFERENCES `tabbie`.`user_clash` (`id`)
          ON DELETE NO ACTION
          ON UPDATE NO ACTION;");

		$this->execute("
          ALTER TABLE `tabbie`.`adjudicator_strike`
          ADD CONSTRAINT `fk_adjudicator_strike_user_clash1`
            FOREIGN KEY (`user_clash_id`)
            REFERENCES `tabbie`.`user_clash` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION;");

	}

	public function down()
	{
		echo "m150729_080218_user_clash cannot be reverted.\n";

		return false;
	}
}
