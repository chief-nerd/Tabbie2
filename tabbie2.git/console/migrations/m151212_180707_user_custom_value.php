<?php

use yii\db\Schema;
use yii\db\Migration;

class m151212_180707_user_custom_value extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS `user_attr` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` INT(10) UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `required` TINYINT(1) NOT NULL DEFAULT 0,
  `help` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_attr_tournament1_idx` (`tournament_id` ASC),
  CONSTRAINT `fk_user_attr_tournament1`
    FOREIGN KEY (`tournament_id`)
    REFERENCES `tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
");

        $this->execute("CREATE TABLE IF NOT EXISTS `user_value` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `user_attr_id` INT(11) NOT NULL,
  `value` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_value_user_attr1_idx` (`user_attr_id` ASC),
  INDEX `fk_user_value_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_user_value_user_attr1`
    FOREIGN KEY (`user_attr_id`)
    REFERENCES `user_attr` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_value_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
");
    }

    public function safeDown()
    {
        $this->dropTable("user_value");
        $this->dropTable("user_attr");
    }
}
