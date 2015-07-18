<?php
	/**
	 * TabbieExport.php File
	 * @package  Tabbie2
	 * @author   jareiter
	 * @version
	 */

	namespace common\components;


	use yii\base\Component;
	use common\models;
	use yii\helpers\ArrayHelper;

	class TabbieExport extends Component
	{

		private function strquote($str)
		{
			return "'" . $str . "'";
		}

		public function generateSQL($tournament)
		{
			$sqlFile = [];
			$sqlFile[] = "USE database tabbie;";
			$sqlFile[] = "";

			$sqlFile[] = "DROP TABLE IF EXISTS `configure_adjud_draw`;";
			$sqlFile[] = "CREATE TABLE `configure_adjud_draw` (
  `param_name` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `param_value` DOUBLE DEFAULT NULL,
  PRIMARY KEY (`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Adjudicator Draw Parameter Table';";

			$values = [];
			$energy_conf = models\EnergyConfig::find()->tournament($tournament->id)->asArray()->all();

			$tabbie_keys = [
				"university_conflict" => models\EnergyConfig::get("society_strike", $tournament->id),
				"team_conflict" => models\EnergyConfig::get("team_strike", $tournament->id),
				"chair_not_perfect" => models\EnergyConfig::get("chair_not_perfect", $tournament->id),
				"chair_not_ciaran_perfect" => 0.4,
				"panel_steepness" => 0.2,
				"panel_strength_not_perfect" => 1,
				"panel_size_not_perfect" => 0,
				"panel_size_out_of_bounds" => 1000,
				"adjudicator_met_adjudicator" => models\EnergyConfig::get("judge_met_judge", $tournament->id),
				"adjudicator_met_team" => models\EnergyConfig::get("judge_met_team", $tournament->id),
				"trainee_in_chair" => 300,
				"watcher_not_in_chair" => models\EnergyConfig::get("non_chair", $tournament->id),
				"watched_not_watched" => 150,
				"lock" => 0,
				"draw_table_speed" => 8,
			];
			foreach($tabbie_keys as $k => $v)
			{
				$values[] = "(".implode(",", [
						$this->strquote($k),
						$v
					]).")";
			}

			$sqlFile[] = (!empty($values)) ? "INSERT INTO `configure_adjud_draw` VALUES ".implode(",", $values) : "";

			$sqlFile[] = "";
			/** ADJUDICATORS */
			$sqlFile[] = "DROP TABLE IF EXISTS `adjudicator`;";
			$sqlFile[] = "CREATE TABLE `adjudicator` (
  `adjud_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `univ_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
  `adjud_name` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ranking` MEDIUMINT(9) NOT NULL DEFAULT '0',
  `active` ENUM('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `adjud_specialneeds` ENUM('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `status` ENUM('normal','trainee','watcher','watched') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `conflicts` VARCHAR(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`adjud_id`),
  UNIQUE KEY `adjud_name` (`adjud_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Adjudicator Table';";

			$adju = models\Adjudicator::find()->tournament($tournament->id)->all();
			$values = [];
			foreach ($adju as $a) {
				$society[$a->society->abr] = $a->society;
				$values[] = "(" . implode(",", [
						$a->id,
						$a->society->id,
						$this->strquote(substr($a->user->name,0,99)),
						$a->strength*10,
						($a->active) ? "'Y'" : "'N'",
						"'N'", //@todo Special needs
						"'normal'",
						"NULL",
					]) . ")";
			}
			$sqlFile[] = (!empty($values)) ? "INSERT INTO `adjudicator` VALUES ".implode(",", $values) : "";

			$sqlFile[] = "";
			$sqlFile[] = "DROP TABLE IF EXISTS `judgestrikes`;";
			$sqlFile[] = "CREATE TABLE `judgestrikes` (
  `judgestrike_id` INT(5) NOT NULL AUTO_INCREMENT,
  `adjud_id` VARCHAR(5) NOT NULL,
  `judge_id` VARCHAR(5) NOT NULL,
  PRIMARY KEY (`judgestrike_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

			$adju_strike = models\AdjudicatorStrike::find()->tournament($tournament->id)->all();
			$i = 1;
			$values = [];
			foreach ($adju_strike as $a) {
				$values[] = "(" . implode(",", [
						$i,
						$a->adjudicator_id,
						$a->adjudicator_id1,
					]) . ")";
				$i++;
			}
			$sqlFile[] = (!empty($values)) ? "INSERT INTO `adjudicator` VALUES ".implode(",", $values) : "";
			/** TEAMS */

			$sqlFile[] = "";
			$sqlFile[] = "DROP TABLE IF EXISTS `speaker`;";
			$sqlFile[] = "CREATE TABLE `speaker` (
  `speaker_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `team_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
  `speaker_name` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `speaker_esl` CHAR(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `speaker_novice` CHAR(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `speaker_specialneeds` CHAR(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`speaker_id`),
  UNIQUE KEY `team_id` (`team_id`,`speaker_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Speaker Table';";

			$sqlFile[] = "DROP TABLE IF EXISTS `team`;";
			$sqlFile[] = "CREATE TABLE `team` (
  `team_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `univ_id` mediumint(9) NOT NULL DEFAULT '0',
  `team_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `esl` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `composite` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `novice` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `specialneeds` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`team_id`),
  UNIQUE KEY `univ_id` (`univ_id`,`team_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Team Table';";

			$teams = models\Team::find()->tournament($tournament->id)->all();
			$counter= [];
			foreach ($teams as $t) {
				$society[$t->society->abr] = $t->society;

				$speaker[] = $t->speakerA;
				$speaker[] = $t->speakerB;

				$counter[$t->society->id] = ((isset($counter[$t->society->id])) ? $counter[$t->society->id] : 0) + 1;
				$sqlFile[] = "INSERT INTO team VALUES(".implode(",", [
						$t->id,
						$t->society->id,
						"'".chr(64+$counter[$t->society->id])."'",
						($t->language_status == models\User::LANGUAGE_ESL) ? "'esl'" : "'non'",
						($t->active) ? "'Y'" : "'N'",
						"'N'",
						(isset($t->novice) && $t->novice) ? "'Y'" : "'N'", //Not yet implemented
						"'N'"
					]).");";

				foreach(models\Team::getSpeaker() as $p)
				{
					$sp = $t->{"speaker".$p};
					if($sp instanceof models\User) { //Could be iron man
 						$sqlFile[] = "INSERT INTO speaker VALUES(" . implode(",", [
								$sp->id,
								$t->id,
								$this->strquote(substr($sp->name,0,99)),
								($sp->language_status == models\User::LANGUAGE_ESL) ? "'Y'" : "'N'",
								(isset($sp->novice) && $sp->novice) ? "'Y'" : "'N'", //Not yet implemented
								"'N'",
							]) . ");";
					}
				}
			}

			$sqlFile[] = "";
			$sqlFile[] = "DROP TABLE IF EXISTS `university`;";
			$sqlFile[] = "CREATE TABLE `university` (
  `univ_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `univ_name` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `univ_code` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`univ_id`),
  UNIQUE KEY `univ_code` (`univ_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='University Table';";

			foreach($society as $s)
			{
				$sqlFile[] = "INSERT INTO university VALUES(".implode(",", [
						$s->id,
						$this->strquote(substr($s->fullname,0,99)),
						$this->strquote($s->abr)
					]).");";
			}

			/** Venues */
			$sqlFile[] = "";
			$sqlFile[] = "DROP TABLE IF EXISTS `venue`;";
			$sqlFile[] = "CREATE TABLE `venue` (
  `venue_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `venue_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `venue_location` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `active` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `specialneeds` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`venue_id`),
  UNIQUE KEY `venue_name` (`venue_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Venue Table';";

			$venues = models\Venue::find()->tournament($tournament->id)->all();
			foreach ($venues as $v) {
				$sqlFile[] = "INSERT INTO venue VALUES(".implode(",", [
						$v->id,
						$this->strquote(substr($v->name,0,49)),
						$this->strquote(substr($v->group,0,49)),
						($v->active) ? "'Y'" : "'N'",
						"'N'",
					]).");";
			}

			$sqlFile[] = "";
			$sqlFile[] = "DROP TABLE IF EXISTS `motions`;";
			$sqlFile[] = "CREATE TABLE `motions` (
  `round_no` SMALLINT(6) NOT NULL DEFAULT '0',
  `motion` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `info_slide` ENUM('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `info` TEXT COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`round_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";


			$sqlFile[] = "";
			$sqlFile[] = "DROP TABLE IF EXISTS `results`;";
			$sqlFile[] = "CREATE TABLE `results` (
	`round_no` MEDIUMINT(9) NOT NULL,
	`debate_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`first` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`second` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`third` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`fourth` MEDIUMINT(9) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`debate_id`, `round_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Team results';
";

			$sqlFile[] = "DROP TABLE IF EXISTS `speaker_results`;";
			$sqlFile[] = "CREATE TABLE `speaker_results` (
	`round_no` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`speaker_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`debate_id` MEDIUMINT(9) NOT NULL DEFAULT '0',
	`points` SMALLINT(9) NOT NULL DEFAULT '0',
	PRIMARY KEY (`speaker_id`, `round_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Speaker results';
";
			foreach ($tournament->rounds as $round) {
				/** ROUND */
				/** @var models\Round $round */

				$sqlFile[] = "DROP TABLE IF EXISTS `draw_round_$round->number`;";
				$sqlFile[] = "CREATE TABLE `draw_round_$round->number` (
  `debate_id` MEDIUMINT(9) NOT NULL,
  `og` MEDIUMINT(9) NOT NULL,
  `oo` MEDIUMINT(9) NOT NULL,
  `cg` MEDIUMINT(9) NOT NULL,
  `co` MEDIUMINT(9) NOT NULL,
  `venue_id` MEDIUMINT(9) NOT NULL,
  PRIMARY KEY (`debate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
				$sqlFile[] = "";

				$sqlFile[] = "DROP TABLE IF EXISTS `adjud_round_$round->number`;";
				$sqlFile[] = "CREATE TABLE `adjud_round_1` (
  `debate_id` mediumint(9) NOT NULL,
  `adjud_id` mediumint(9) NOT NULL,
  `status` enum('chair','panelist','trainee') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

				$sqlFile[] = "";
				$sqlFile[] = "DROP TABLE IF EXISTS `result_round_$round->number`;";
				$sqlFile[] = "CREATE TABLE `result_round_$round->number`(
	 `debate_id` mediumint(9) NOT NULL DEFAULT '0',
  `first` mediumint(9) NOT NULL DEFAULT '0',
  `second` mediumint(9) NOT NULL DEFAULT '0',
  `third` mediumint(9) NOT NULL DEFAULT '0',
  `fourth` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`debate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

				$sqlFile[] = "";
				$sqlFile[] = "DROP TABLE IF EXISTS `speaker_round_$round->number`;";
				$sqlFile[] = "CREATE TABLE `speaker_round_$round->number` (
  `speaker_id` mediumint(9) NOT NULL DEFAULT '0',
  `debate_id` mediumint(9) NOT NULL DEFAULT '0',
  `points` smallint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`speaker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

				$sqlFile[] = "";
				$sqlFile[] = "INSERT INTO `motions` VALUES (".$round->number.",'".addslashes($round->motion)."','".(($round->infoslide) ? 'Y' : 'N')."','".addslashes($round->infoslide)."')";

				foreach ($round->debates as $debate) {
					/** DEBATE */
					/** @var models\Debate $debate */

					$sqlFile[] = "";
					$values = [$debate->id];
					$values[1] = $debate->og_team_id;
					$values[2] = $debate->oo_team_id;
					$values[3] = $debate->cg_team_id;
					$values[4] = $debate->co_team_id;
					$sqlFile[] = "INSERT INTO `draw_round_$round->number` VALUES (".implode(",", $values).")";

					$panel = $debate->panel;
					$first = true;
					foreach($panel->getAdjudicatorsObjects() as $adj)
					{
						if($first) $pos = 'chair'; else $pos="panelist";
						$sqlFile[] = "INSERT INTO `adjud_round_$round->number` VALUES ($debate->id,$adj->id,'$pos')";
						$first = false;
					}

					/** RESULT */
					if ($debate->result instanceof models\Result) // There might not be a result yet
					{
						/** @var models\Result $result */
						$result = $debate->result;

						$values = [$debate->id];
						$values[$result->og_place] = $debate->og_team_id;
						$values[$result->oo_place] = $debate->oo_team_id;
						$values[$result->cg_place] = $debate->cg_team_id;
						$values[$result->co_place] = $debate->co_team_id;

						$sqlFile[] = "INSERT INTO result_round_$round->number VALUES(" . implode(",", $values) . ")";

						foreach(models\Team::getPos() as $pos)
						{
							foreach(models\Team::getSpeaker() as $sp)
							{
								$speakerID = $debate->{$pos."_team"}->{"speaker".$sp."_id"};
								if($speakerID) { //ironman
									$points = $result->{$pos . "_" . $sp . "_speaks"};
									$sqlFile[] = "INSERT INTO `speaker_round_$round->number` VALUES ($speakerID,$debate->id,$points)";
								}
							}
						}
					}
				}
				$sqlFile[] = "";
			}

			$sqlFile[] = "DROP TABLE IF EXISTS `users`;";
			$sqlFile[] = "CREATE TABLE `users` (
  `user_id` MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_password` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_group` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='University Table';";
			$sqlFile[] = "INSERT INTO `users` VALUES (1,'admin','d033e22ae348aeb5660fc2140aec35850c4da997','admin');";
			return $sqlFile;
		}
	}