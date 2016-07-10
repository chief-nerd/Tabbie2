<?php

use yii\db\Schema;
use yii\db\Migration;

class m151119_155725_api extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS `api_user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `access_token` VARCHAR(45) NULL DEFAULT NULL,
  `rl_timestamp` TIMESTAMP NOT NULL DEFAULT NOW(),
  `rl_remaining` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_api-user_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_api-user_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
");
    }

    public function down()
    {
        $this->dropTable("api_user");
    }

}
