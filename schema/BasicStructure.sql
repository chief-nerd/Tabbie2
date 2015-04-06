SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `tabbie` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `tabbie` ;

-- -----------------------------------------------------
-- Table `tabbie`.`migration`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`migration` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`migration` (
  `version` VARCHAR(180) NOT NULL,
  `apply_time` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`version`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;


-- -----------------------------------------------------
-- Table `tabbie`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`user` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`user` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `auth_key` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `password_hash` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `password_reset_token` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `email` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `role` SMALLINT(6) NOT NULL DEFAULT '10',
  `status` SMALLINT(6) NOT NULL DEFAULT '10',
  `givenname` VARCHAR(255) NULL,
  `surename` VARCHAR(255) NULL,
  `gender`                 INT              NOT NULL DEFAULT 0,
  `language_status`        INT(11)          NOT NULL DEFAULT 0,
  `language_status_by_id`  INT(11) UNSIGNED NULL,
  `language_status_update` DATETIME         NULL,
  `picture` VARCHAR(255) NULL,
  `last_change` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_user1_idx` (`language_status_by_id` ASC),
  CONSTRAINT `fk_user_user1`
  FOREIGN KEY (`language_status_by_id`)
  REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`country`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`country`;

CREATE TABLE IF NOT EXISTS `tabbie`.`country` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`      VARCHAR(50)  NULL,
  `alpha_2`   VARCHAR(2)   NULL,
  `alpha_3`   VARCHAR(3)   NULL,
  `region_id` INT(11)      NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`society`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`society`;

CREATE TABLE IF NOT EXISTS `tabbie`.`society` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname`   VARCHAR(255) NULL,
  `abr`        VARCHAR(45)  NULL,
  `city`       VARCHAR(255) NULL,
  `country_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `adr_UNIQUE` (`abr` ASC),
  INDEX `fk_society_country1_idx` (`country_id` ASC),
  CONSTRAINT `fk_society_country1`
  FOREIGN KEY (`country_id`)
  REFERENCES `tabbie`.`country` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`tournament`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`tournament` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`tournament` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `url_slug` VARCHAR(100) NOT NULL,
  `status`           INT(11)      NOT NULL DEFAULT 0,
  `convenor_user_id` INT(11) UNSIGNED NOT NULL,
  `tabmaster_user_id` INT(11) UNSIGNED NOT NULL,
  `hosted_by_id`     INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `logo` VARCHAR(255) NULL,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tabAlgorithmClass` VARCHAR(100) NOT NULL DEFAULT 'StrictWUDCRules',
  `expected_rounds`  INT(11)      NOT NULL DEFAULT 6,
  `has_esl`          TINYINT(1)   NOT NULL DEFAULT 0,
  `has_final`        TINYINT(1)   NOT NULL DEFAULT 1,
  `has_semifinal`    TINYINT(1)   NOT NULL DEFAULT 1,
  `has_quarterfinal` TINYINT(1)   NOT NULL DEFAULT 0,
  `has_octofinal`    TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_tournament_user1_idx` (`convenor_user_id` ASC),
  INDEX `fk_tournament_user2_idx` (`tabmaster_user_id` ASC),
  UNIQUE INDEX `slug_UNIQUE` (`url_slug` ASC),
  INDEX `fk_tournament_society1_idx` (`hosted_by_id` ASC),
  CONSTRAINT `fk_tournament_user1`
    FOREIGN KEY (`convenor_user_id`)
    REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tournament_user2`
    FOREIGN KEY (`tabmaster_user_id`)
    REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tournament_society1`
  FOREIGN KEY (`hosted_by_id`)
  REFERENCES `tabbie`.`society` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`adjudicator`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`adjudicator` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`adjudicator` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tournament_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `active`    TINYINT(1) NOT NULL DEFAULT 1,
  `strength` TINYINT NULL,
  `society_id` INT UNSIGNED NOT NULL,
  `can_chair` TINYINT(1) NOT NULL DEFAULT 1,
  `are_watched` TINYINT(1) NOT NULL DEFAULT 0,
  `checkedin` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_adjudicator_username1_idx` (`user_id` ASC),
  INDEX `fk_adjudicator_tournament1_idx` (`tournament_id` ASC),
  INDEX `fk_adjudicator_society1_idx` (`society_id` ASC),
  CONSTRAINT `fk_adjudicator_username1`
    FOREIGN KEY (`user_id`)
    REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_tournament1`
    FOREIGN KEY (`tournament_id`)
    REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_society1`
    FOREIGN KEY (`society_id`)
    REFERENCES `tabbie`.`society` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`team`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`team` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`team` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `tournament_id` INT UNSIGNED NOT NULL,
  `active`             TINYINT(1)   NOT NULL DEFAULT 1,
  `speakerA_id`        INT UNSIGNED NULL,
  `speakerB_id`        INT UNSIGNED NULL,
  `society_id` INT UNSIGNED NOT NULL,
  `isSwing`            TINYINT(1)   NOT NULL DEFAULT 0,
  `language_status`    TINYINT      NOT NULL DEFAULT 0,
  `points`             INT(11)      NOT NULL DEFAULT 0,
  `speakerA_speaks`    INT(11)      NOT NULL DEFAULT 0,
  `speakerB_speaks`    VARCHAR(45)  NOT NULL DEFAULT 0,
  `speakerA_checkedin` TINYINT(1)   NOT NULL DEFAULT 0,
  `speakerB_checkedin` TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_team_username_idx` (`speakerA_id` ASC),
  INDEX `fk_team_username1_idx` (`speakerB_id` ASC),
  INDEX `fk_team_tournament1_idx` (`tournament_id` ASC),
  INDEX `fk_team_society1_idx` (`society_id` ASC),
  CONSTRAINT `fk_team_username`
    FOREIGN KEY (`speakerA_id`)
    REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_username1`
    FOREIGN KEY (`speakerB_id`)
    REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_tournament1`
    FOREIGN KEY (`tournament_id`)
    REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_society1`
    FOREIGN KEY (`society_id`)
    REFERENCES `tabbie`.`society` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`round`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`round` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`round` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` INT NOT NULL,
  `tournament_id` INT UNSIGNED NOT NULL,
  `energy`       INT(11) NOT NULL DEFAULT 0,
  `motion` TEXT NOT NULL,
  `infoslide` TEXT NULL,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published` TINYINT(1) NOT NULL DEFAULT 0,
  `displayed` TINYINT(1) NOT NULL DEFAULT 0,
  `closed` TINYINT(1) NOT NULL DEFAULT 0,
  `prep_started` DATETIME NULL,
  `finished_time` DATETIME NULL,
  `lastrun_temp` FLOAT   NOT NULL DEFAULT 1.0,
  PRIMARY KEY (`id`),
  INDEX `fk_round_tournament1_idx` (`tournament_id` ASC),
  CONSTRAINT `fk_round_tournament1`
    FOREIGN KEY (`tournament_id`)
    REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`venue`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`venue` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`venue` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tournament_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `fk_venue_tournament1_idx` (`tournament_id` ASC),
  CONSTRAINT `fk_venue_tournament1`
    FOREIGN KEY (`tournament_id`)
    REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`panel`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`panel` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`panel` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `strength` INT NOT NULL DEFAULT 0,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tournament_id` INT UNSIGNED NOT NULL,
  `used` TINYINT(1) NOT NULL DEFAULT 1,
  `is_preset` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_panel_tournament1_idx` (`tournament_id` ASC),
  CONSTRAINT `fk_panel_tournament1`
    FOREIGN KEY (`tournament_id`)
    REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`debate`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`debate` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`debate` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `round_id` INT UNSIGNED NOT NULL,
  `tournament_id` INT UNSIGNED NOT NULL,
  `og_team_id` INT UNSIGNED NOT NULL,
  `oo_team_id` INT UNSIGNED NOT NULL,
  `cg_team_id` INT UNSIGNED NOT NULL,
  `co_team_id` INT UNSIGNED NOT NULL,
  `panel_id` INT UNSIGNED NOT NULL,
  `venue_id` INT UNSIGNED NOT NULL,
  `energy` INT(11) NOT NULL DEFAULT 0,
  `og_feedback` TINYINT(1) NOT NULL DEFAULT 0,
  `oo_feedback` TINYINT(1) NOT NULL DEFAULT 0,
  `cg_feedback` TINYINT(1) NOT NULL DEFAULT 0,
  `co_feedback` TINYINT(1) NOT NULL DEFAULT 0,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `messages` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_debate_venue1_idx` (`venue_id` ASC),
  INDEX `fk_debate_panel1_idx` (`panel_id` ASC),
  CONSTRAINT `fk_debate_venue1`
    FOREIGN KEY (`venue_id`)
    REFERENCES `tabbie`.`venue` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_debate_panel1`
    FOREIGN KEY (`panel_id`)
    REFERENCES `tabbie`.`panel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`result`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`result` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`result` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `debate_id` INT UNSIGNED NOT NULL,
  `og_A_speaks` TINYINT NOT NULL,
  `og_B_speaks` TINYINT NOT NULL,
  `og_place` TINYINT NOT NULL,
  `oo_A_speaks` TINYINT NOT NULL,
  `oo_B_speaks` TINYINT NOT NULL,
  `oo_place` TINYINT NOT NULL,
  `cg_A_speaks` TINYINT NOT NULL,
  `cg_B_speaks` TINYINT NOT NULL,
  `cg_place` TINYINT NOT NULL,
  `co_A_speaks` TINYINT NOT NULL,
  `co_B_speaks` TINYINT NOT NULL,
  `co_place` TINYINT NOT NULL,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entered_by_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_result_debate1_idx` (`debate_id` ASC),
  UNIQUE INDEX `debate_id_UNIQUE` (`debate_id` ASC),
  INDEX `fk_result_user1_idx` (`entered_by_id` ASC),
  CONSTRAINT `fk_result_debate1`
    FOREIGN KEY (`debate_id`)
    REFERENCES `tabbie`.`debate` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_result_user1`
  FOREIGN KEY (`entered_by_id`)
  REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`in_society`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`in_society` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`in_society` (
  `user_id` INT(10) UNSIGNED NOT NULL,
  `society_id` INT UNSIGNED NOT NULL,
  `starting` DATE NOT NULL,
  `ending` DATE NULL,
  PRIMARY KEY (`society_id`, `user_id`),
  INDEX `fk_username_has_university_university1_idx` (`society_id` ASC),
  INDEX `fk_username_has_university_username1_idx` (`user_id` ASC),
  CONSTRAINT `fk_username_in_society_society1`
    FOREIGN KEY (`society_id`)
    REFERENCES `tabbie`.`society` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_username_in_society_username1`
    FOREIGN KEY (`user_id`)
    REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`special_needs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`special_needs` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`special_needs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`username_has_special_needs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`username_has_special_needs` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`username_has_special_needs` (
  `username_id` INT UNSIGNED NOT NULL,
  `special_needs_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`username_id`, `special_needs_id`),
  INDEX `fk_username_has_special_needs_special_needs1_idx` (`special_needs_id` ASC),
  INDEX `fk_username_has_special_needs_username1_idx` (`username_id` ASC),
  CONSTRAINT `fk_username_has_special_needs_username1`
    FOREIGN KEY (`username_id`)
    REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_username_has_special_needs_special_needs1`
    FOREIGN KEY (`special_needs_id`)
    REFERENCES `tabbie`.`special_needs` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`venue_provides_special_needs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`venue_provides_special_needs` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`venue_provides_special_needs` (
  `venue_id` INT UNSIGNED NOT NULL,
  `special_needs_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`venue_id`, `special_needs_id`),
  INDEX `fk_venue_has_special_needs_special_needs1_idx` (`special_needs_id` ASC),
  INDEX `fk_venue_has_special_needs_venue1_idx` (`venue_id` ASC),
  CONSTRAINT `fk_venue_has_special_needs_venue1`
    FOREIGN KEY (`venue_id`)
    REFERENCES `tabbie`.`venue` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_venue_has_special_needs_special_needs1`
    FOREIGN KEY (`special_needs_id`)
    REFERENCES `tabbie`.`special_needs` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`question`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`question` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`question` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `text` VARCHAR(255) NOT NULL,
  `type` INT NOT NULL,
  `apply_T2C` TINYINT(1) NOT NULL DEFAULT 0,
  `apply_C2W` TINYINT(1) NOT NULL DEFAULT 0,
  `apply_W2C` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`tournament_has_question`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`tournament_has_question` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`tournament_has_question` (
  `tournament_id` INT UNSIGNED NOT NULL,
  `questions_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`tournament_id`, `questions_id`),
  INDEX `fk_tournament_has_questions_questions1_idx` (`questions_id` ASC),
  INDEX `fk_tournament_has_questions_tournament1_idx` (`tournament_id` ASC),
  CONSTRAINT `fk_tournament_has_questions_tournament1`
    FOREIGN KEY (`tournament_id`)
    REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tournament_has_questions_questions1`
    FOREIGN KEY (`questions_id`)
    REFERENCES `tabbie`.`question` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`adjudicator_in_panel`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`adjudicator_in_panel` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`adjudicator_in_panel` (
  `adjudicator_id` INT UNSIGNED NOT NULL,
  `panel_id` INT UNSIGNED NOT NULL,
  `function` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `got_feedback` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`adjudicator_id`, `panel_id`),
  INDEX `fk_adjudicator_has_panel_panel1_idx` (`panel_id` ASC),
  INDEX `fk_adjudicator_has_panel_adjudicator1_idx` (`adjudicator_id` ASC),
  CONSTRAINT `fk_adjudicator_has_panel_adjudicator1`
    FOREIGN KEY (`adjudicator_id`)
    REFERENCES `tabbie`.`adjudicator` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_has_panel_panel1`
    FOREIGN KEY (`panel_id`)
    REFERENCES `tabbie`.`panel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`feedback`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`feedback` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`feedback` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `debate_id` INT UNSIGNED NOT NULL,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_feedback_debate1_idx` (`debate_id` ASC),
  CONSTRAINT `fk_feedback_debate1`
    FOREIGN KEY (`debate_id`)
    REFERENCES `tabbie`.`debate` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`answer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`answer` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`answer` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `questions_id` INT UNSIGNED NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_answer_questions1_idx` (`questions_id` ASC),
  CONSTRAINT `fk_answer_questions1`
    FOREIGN KEY (`questions_id`)
    REFERENCES `tabbie`.`question` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`feedback_has_answer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`feedback_has_answer` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`feedback_has_answer` (
  `feedback_id` INT UNSIGNED NOT NULL,
  `answer_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`feedback_id`, `answer_id`),
  INDEX `fk_feedback_has_answer_answer1_idx` (`answer_id` ASC),
  INDEX `fk_feedback_has_answer_feedback1_idx` (`feedback_id` ASC),
  CONSTRAINT `fk_feedback_has_answer_feedback1`
    FOREIGN KEY (`feedback_id`)
    REFERENCES `tabbie`.`feedback` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_feedback_has_answer_answer1`
    FOREIGN KEY (`answer_id`)
    REFERENCES `tabbie`.`answer` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`energy_config`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`energy_config` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`energy_config` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(100) NOT NULL,
  `tournament_id` INT UNSIGNED NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `value` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_energy_config_tournament1_idx` (`tournament_id` ASC),
  CONSTRAINT `fk_energy_config_tournament1`
    FOREIGN KEY (`tournament_id`)
    REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`team_strike`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`team_strike` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`team_strike` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `team_id` INT UNSIGNED NOT NULL,
  `adjudicator_id` INT UNSIGNED NOT NULL,
  `tournament_id` INT UNSIGNED NOT NULL,
  INDEX `fk_team_strike_team1_idx` (`team_id` ASC),
  PRIMARY KEY (`id`),
  INDEX `fk_team_strike_adjudicator1_idx` (`adjudicator_id` ASC),
  INDEX `fk_team_strike_tournament1_idx` (`tournament_id` ASC),
  CONSTRAINT `fk_team_strike_team1`
    FOREIGN KEY (`team_id`)
    REFERENCES `tabbie`.`team` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_strike_adjudicator1`
    FOREIGN KEY (`adjudicator_id`)
    REFERENCES `tabbie`.`adjudicator` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_strike_tournament1`
  FOREIGN KEY (`tournament_id`)
  REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`adjudicator_strike`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`adjudicator_strike` ;

CREATE TABLE IF NOT EXISTS `tabbie`.`adjudicator_strike` (
  `adjudicator_from_id` INT UNSIGNED NOT NULL,
  `adjudicator_to_id`   INT UNSIGNED NOT NULL,
  `tournament_id`       INT UNSIGNED NOT NULL,
  PRIMARY KEY (`adjudicator_from_id`, `adjudicator_to_id`),
  INDEX `fk_adjudicator_has_adjudicator_adjudicator2_idx` (`adjudicator_to_id` ASC),
  INDEX `fk_adjudicator_has_adjudicator_adjudicator1_idx` (`adjudicator_from_id` ASC),
  INDEX `fk_adjudicator_strike_tournament1_idx` (`tournament_id` ASC),
  CONSTRAINT `fk_adjudicator_has_adjudicator_adjudicator1`
  FOREIGN KEY (`adjudicator_from_id`)
    REFERENCES `tabbie`.`adjudicator` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_has_adjudicator_adjudicator2`
  FOREIGN KEY (`adjudicator_to_id`)
    REFERENCES `tabbie`.`adjudicator` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_strike_tournament1`
  FOREIGN KEY (`tournament_id`)
  REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`publish_tab_team`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`publish_tab_team`;

CREATE TABLE IF NOT EXISTS `tabbie`.`publish_tab_team` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `tournament_id` INT UNSIGNED NOT NULL,
  `team_id`       INT UNSIGNED NOT NULL,
  `enl_place`     INT          NOT NULL,
  `esl_place`     VARCHAR(45)  NULL,
  `cache_results` TEXT         NOT NULL,
  `speaks`        INT(11)      NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_publish_tab_team_team1_idx` (`team_id` ASC),
  INDEX `fk_publish_tab_team_tournament1_idx` (`tournament_id` ASC),
  CONSTRAINT `fk_publish_tab_team_team1`
  FOREIGN KEY (`team_id`)
  REFERENCES `tabbie`.`team` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_publish_tab_team_tournament1`
  FOREIGN KEY (`tournament_id`)
  REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`publish_tab_speaker`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`publish_tab_speaker`;

CREATE TABLE IF NOT EXISTS `tabbie`.`publish_tab_speaker` (
  `id`            INT              NOT NULL AUTO_INCREMENT,
  `tournament_id` INT UNSIGNED     NOT NULL,
  `user_id`       INT(11) UNSIGNED NOT NULL,
  `enl_place`     INT              NOT NULL,
  `esl_place`     INT              NULL,
  `cache_results` TEXT             NOT NULL,
  `speaks`        INT(11)          NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_publish_tab_speaker_user1_idx` (`user_id` ASC),
  INDEX `fk_publish_tab_speaker_tournament1_idx` (`tournament_id` ASC),
  CONSTRAINT `fk_publish_tab_speaker_user1`
  FOREIGN KEY (`user_id`)
  REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_publish_tab_speaker_tournament1`
  FOREIGN KEY (`tournament_id`)
  REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tabbie`.`language_officer`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tabbie`.`language_officer`;

CREATE TABLE IF NOT EXISTS `tabbie`.`language_officer` (
  `user_id`       INT(11) UNSIGNED NOT NULL,
  `tournament_id` INT UNSIGNED     NOT NULL,
  PRIMARY KEY (`user_id`, `tournament_id`),
  INDEX `fk_user_has_tournament_tournament1_idx` (`tournament_id` ASC),
  INDEX `fk_user_has_tournament_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_user_has_tournament_user1`
  FOREIGN KEY (`user_id`)
  REFERENCES `tabbie`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_tournament_tournament1`
  FOREIGN KEY (`tournament_id`)
  REFERENCES `tabbie`.`tournament` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
