<?php

use yii\db\Schema;
use yii\db\Migration;

class m150726_200220_tabmaster_ca extends Migration
{
	public function up()
	{
		$this->execute("CREATE TABLE IF NOT EXISTS `ca` (
		  `user_id` INT(11) UNSIGNED NOT NULL,
		  `tournament_id` INT UNSIGNED NOT NULL,
		  PRIMARY KEY (`user_id`, `tournament_id`),
		  INDEX `fk_user_has_tournament_tournament2_idx` (`tournament_id` ASC),
		  INDEX `fk_user_has_tournament_user2_idx` (`user_id` ASC),
		  CONSTRAINT `fk_user_has_tournament_user2`
			FOREIGN KEY (`user_id`)
			REFERENCES `user` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_user_has_tournament_tournament2`
			FOREIGN KEY (`tournament_id`)
			REFERENCES `tournament` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8
		COLLATE = utf8_general_ci;");

		$this->execute("CREATE TABLE IF NOT EXISTS `tabmaster` (
		  `user_id` INT(11) UNSIGNED NOT NULL,
		  `tournament_id` INT UNSIGNED NOT NULL,
		  PRIMARY KEY (`user_id`, `tournament_id`),
		  INDEX `fk_user_has_tournament_tournament3_idx` (`tournament_id` ASC),
		  INDEX `fk_user_has_tournament_user3_idx` (`user_id` ASC),
		  CONSTRAINT `fk_user_has_tournament_user3`
			FOREIGN KEY (`user_id`)
			REFERENCES `user` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_user_has_tournament_tournament3`
			FOREIGN KEY (`tournament_id`)
			REFERENCES `tournament` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8
		COLLATE = utf8_general_ci;");

		$this->execute("CREATE TABLE IF NOT EXISTS `convenor` (
		  `tournament_id` INT(10) UNSIGNED NOT NULL,
		  `user_id` INT(11) UNSIGNED NOT NULL,
		  PRIMARY KEY (`tournament_id`, `user_id`),
		  INDEX `fk_tournament_has_user_user1_idx` (`user_id` ASC),
		  INDEX `fk_tournament_has_user_tournament1_idx` (`tournament_id` ASC),
		  CONSTRAINT `fk_tournament_has_user_tournament1`
			FOREIGN KEY (`tournament_id`)
			REFERENCES `tabbie`.`tournament` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		  CONSTRAINT `fk_tournament_has_user_user1`
			FOREIGN KEY (`user_id`)
			REFERENCES `tabbie`.`user` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB
		DEFAULT CHARACTER SET = utf8
		COLLATE = utf8_general_ci;");

		/** Tabmaster */

		$tournaments = \common\models\Tournament::find()->all();

		$error_save = false;
		$run = false;

		foreach ($tournaments as $t) {
			if (isset($t->tabmaster_user_id)) {
				$run = true;
				/** @var \common\models\Tournament $t */
				$tm = new \common\models\Tabmaster([
					"user_id"       => $t->tabmaster_user_id,
					"tournament_id" => $t->id,
				]);
				if (!$tm->save())
					$error_save = true;
			}
		}

		if (!$error_save && $run) {
			$this->dropForeignKey('fk_tournament_user2', "tournament");
			$this->dropIndex('fk_tournament_user2_idx', "tournament");
			$this->dropColumn("tournament", "tabmaster_user_id");
		}

		/** Convenor */
		$error_save = false;
		$run = false;
		foreach ($tournaments as $t) {
			if (isset($t->convenor_user_id)) {
				$run = true;
				/** @var \common\models\Tournament $t */
				$con = new \common\models\Convenor([
					"user_id"       => $t->convenor_user_id,
					"tournament_id" => $t->id,
				]);
				if (!$con->save())
					$error_save = true;
			}
		}

		if (!$error_save && $run) {
			$this->dropForeignKey('fk_tournament_user1', "tournament");
			$this->dropIndex('fk_tournament_user1_idx', "tournament");
			$this->dropColumn("tournament", "convenor_user_id");
		}

		/** CAs */
		$error_save = false;
		foreach ($tournaments as $t) {
			/** @var \common\models\Tournament $t */
			/** @var \common\models\Adjudicator $adju */
			$adju = \common\models\Adjudicator::find()->tournament($t->id)->andWhere("strength >= 90")->all();
			foreach ($adju as $a) {
				$ca = new \common\models\Ca([
					"user_id"       => $a->user->id,
					"tournament_id" => $t->id,
				]);
				if (!$ca->save())
					$error_save = true;
			}
		}
	}

	public function down()
	{
		/* too lazy */
		$this->addColumn("tournament", "tabmaster_user_id", "INTEGER");
		$this->dropTable("ca");
		$this->dropTable("tabmaster");
	}
}
