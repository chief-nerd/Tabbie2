<?php

use yii\db\Schema;
use yii\db\Migration;

class m150815_200040_delete_user_clash extends Migration
{
	public function up()
	{
		$this->execute("ALTER TABLE `tabbie`.`team_strike`
					DROP FOREIGN KEY `fk_team_strike_user_clash1`;

					ALTER TABLE `tabbie`.`adjudicator_strike`
					DROP FOREIGN KEY `fk_adjudicator_strike_user_clash1`;

					ALTER TABLE `tabbie`.`team_strike`
					ADD CONSTRAINT `fk_team_strike_user_clash1`
					  FOREIGN KEY (`user_clash_id`)
					  REFERENCES `tabbie`.`user_clash` (`id`)
					  ON DELETE SET NULL
					  ON UPDATE NO ACTION;

					ALTER TABLE `tabbie`.`adjudicator_strike`
					ADD CONSTRAINT `fk_adjudicator_strike_user_clash1`
					  FOREIGN KEY (`user_clash_id`)
					  REFERENCES `tabbie`.`user_clash` (`id`)
					  ON DELETE SET NULL
					  ON UPDATE NO ACTION;");
	}

	public function down()
	{
		return true;
	}
}
