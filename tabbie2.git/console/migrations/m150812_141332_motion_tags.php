<?php

use yii\db\Schema;
use yii\db\Migration;

class m150812_141332_motion_tags extends Migration
{
	public function up()
	{
		$this->execute("CREATE TABLE IF NOT EXISTS `motion_tag` (
						  `id` INT(11) NOT NULL AUTO_INCREMENT,
						  `name` VARCHAR(255) NOT NULL,
 						  `abr` VARCHAR(100) NULL DEFAULT NULL,
						  PRIMARY KEY (`id`))
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;");

		$this->execute("CREATE TABLE IF NOT EXISTS `tag` (
						  `motion_tag_id` INT(11) NOT NULL,
						  `round_id` INT(10) UNSIGNED NOT NULL,
						  PRIMARY KEY (`motion_tag_id`, `round_id`),
						  INDEX `fk_motion_tag_has_round_round1_idx` (`round_id` ASC),
						  INDEX `fk_motion_tag_has_round_motion_tag1_idx` (`motion_tag_id` ASC),
						  CONSTRAINT `fk_motion_tag_has_round_motion_tag1`
							FOREIGN KEY (`motion_tag_id`)
							REFERENCES `motion_tag` (`id`)
							ON DELETE NO ACTION
							ON UPDATE NO ACTION,
						  CONSTRAINT `fk_motion_tag_has_round_round1`
							FOREIGN KEY (`round_id`)
							REFERENCES `round` (`id`)
							ON DELETE NO ACTION
							ON UPDATE NO ACTION)
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;");

		$colum = [
			"name",
			"abr"
		];

		$values = [
			['International Relations', 'IR'],
			['First Person', null],
			['Arts', null],
			['Economics', null],
			['Business', null],
			['Morality', null],
			['Religion', null],
			['Social Justice', null],
			['Law', null],
			['Human Rights', null],
			['Military', null],
			['War', null],
			['Nationalism', null],
			['Utilitarianism', 'Util'],
			['Civil Responsibility', null],
			['Meritocracy', null],
			['Moral Hazard', null],
			['Feminist', null],
			['Space', null],
			['Education', null],
			['Sport', null],
			['Culture', null],
			['Politics', null],
			['Environment', null],
			['Medicine', null],
			['Asia', null],
			['European Union', 'EU'],
			['United States', 'US'],
			['United Nations', 'UN'],
			['Freedom of Speech', 'FoS'],
			['News & Media', null],
		];

		$this->batchInsert("motion_tag", $colum, $values);

		$this->execute("CREATE TABLE IF NOT EXISTS `tabbie`.`legacy_motion` (
						  `id` INT(11) NOT NULL,
						  `motion` TEXT NOT NULL,
						  `language` VARCHAR(2) NOT NULL DEFAULT 'en',
						  `time` DATE NOT NULL,
						  `infoslide` TEXT NULL DEFAULT NULL,
						  `tournament` VARCHAR(255) NOT NULL,
						  `round` VARCHAR(100) NULL DEFAULT NULL,
						  `link` VARCHAR(255) NULL DEFAULT NULL,
						  `by_user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
						  PRIMARY KEY (`id`),
						  INDEX `fk_legacy_motion_user1_idx` (`by_user_id` ASC),
						  CONSTRAINT `fk_legacy_motion_user1`
							FOREIGN KEY (`by_user_id`)
							REFERENCES `tabbie`.`user` (`id`)
							ON DELETE NO ACTION
							ON UPDATE NO ACTION)
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;");

		$this->execute("CREATE TABLE IF NOT EXISTS `tabbie`.`legacy_tag` (
						  `motion_tag_id` INT(11) NOT NULL,
						  `legacy_motion_id` INT(11) NOT NULL,
						  PRIMARY KEY (`motion_tag_id`, `legacy_motion_id`),
						  INDEX `fk_motion_tag_has_legacy_motion_legacy_motion1_idx` (`legacy_motion_id` ASC),
						  INDEX `fk_motion_tag_has_legacy_motion_motion_tag1_idx` (`motion_tag_id` ASC),
						  CONSTRAINT `fk_motion_tag_has_legacy_motion_motion_tag1`
							FOREIGN KEY (`motion_tag_id`)
							REFERENCES `tabbie`.`motion_tag` (`id`)
							ON DELETE NO ACTION
							ON UPDATE NO ACTION,
						  CONSTRAINT `fk_motion_tag_has_legacy_motion_legacy_motion1`
							FOREIGN KEY (`legacy_motion_id`)
							REFERENCES `tabbie`.`legacy_motion` (`id`)
							ON DELETE NO ACTION
							ON UPDATE NO ACTION)
						ENGINE = InnoDB
						DEFAULT CHARACTER SET = utf8
						COLLATE = utf8_general_ci;");
	}

	public function down()
	{
		$this->dropTable("tag");
		$this->dropTable("legacy_tag");
		$this->dropTable("legacy_motion");
		$this->dropTable("motion_tag");

	}
}
