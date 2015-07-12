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
  `url_slug`               VARCHAR(255)
                           CHARACTER SET 'utf8'
                           COLLATE 'utf8_unicode_ci' NOT NULL,
  `auth_key` VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `password_hash` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `password_reset_token` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `email` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `role` SMALLINT(6) NOT NULL DEFAULT '10',
  `status` SMALLINT(6) NOT NULL DEFAULT '10',
  `givenname` VARCHAR(255) NULL,
  `surename` VARCHAR(255) NULL,
  `gender`                 INT                       NOT NULL DEFAULT 0,
  `language_status`        INT(11)                   NOT NULL DEFAULT 0,
  `language_status_by_id`  INT(11) UNSIGNED          NULL,
  `language_status_update` DATETIME                  NULL,
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
  `name`      VARCHAR(100) NULL,
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
  `id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(255) NULL,
  `abr`  VARCHAR(45)  NULL,
  `city` VARCHAR(255) NULL,
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
  `accessToken`      VARCHAR(100) NOT NULL,
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
  `active` TINYINT(1) NOT NULL DEFAULT 1,
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
  `group` VARCHAR(100) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_venue_tournament1_idx` (`tournament_id` ASC),
  INDEX `order` (`group` ASC),
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
  `og_irregular` TINYINT NOT NULL DEFAULT 0,
  `og_place` TINYINT NOT NULL,
  `oo_A_speaks` TINYINT NOT NULL,
  `oo_B_speaks` TINYINT NOT NULL,
  `oo_irregular` TINYINT NOT NULL DEFAULT 0,
  `oo_place` TINYINT NOT NULL,
  `cg_A_speaks` TINYINT NOT NULL,
  `cg_B_speaks` TINYINT NOT NULL,
  `cg_irregular` TINYINT NOT NULL DEFAULT 0,
  `cg_place` TINYINT NOT NULL,
  `co_A_speaks` TINYINT NOT NULL,
  `co_B_speaks` TINYINT NOT NULL,
  `co_irregular` TINYINT NOT NULL DEFAULT 0,
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
  `feedback_id` INT UNSIGNED NOT NULL,
  `question_id` INT UNSIGNED NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_answer_questions1_idx` (`question_id` ASC),
  INDEX `fk_answer_feedback1_idx` (`feedback_id` ASC),
  CONSTRAINT `fk_answer_questions1`
  FOREIGN KEY (`question_id`)
    REFERENCES `tabbie`.`question` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_answer_feedback1`
    FOREIGN KEY (`feedback_id`)
    REFERENCES `tabbie`.`feedback` (`id`)
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
  `adjudicator_to_id` INT UNSIGNED NOT NULL,
  `tournament_id` INT UNSIGNED NOT NULL,
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

-- -----------------------------------------------------
-- Data for table `tabbie`.`country`
-- -----------------------------------------------------
START TRANSACTION;
USE `tabbie`;
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (1, 'Afghanistan', 'af', 'afg', 24);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (2, 'Aland Islands', 'ax', 'ala', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (3, 'Albania', 'al', 'alb', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (4, 'Algeria', 'dz', 'dza', 41);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (5, 'American Samoa', 'as', 'asm', 34);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (6, 'Andorra', 'ad', 'and', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (7, 'Angola', 'ao', 'ago', 43);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (8, 'Anguilla', 'ai', 'aia', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (9, 'Antarctica', 'aq', 'NULL', 71);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (10, 'Antigua and Barbuda', 'ag', 'atg', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (11, 'Argentina', 'ar', 'arg', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (12, 'Armenia', 'am', 'arm', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (13, 'Aruba', 'aw', 'abw', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (14, 'Australia', 'au', 'aus', 31);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (15, 'Austria', 'at', 'aut', 12);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (16, 'Azerbaijan', 'az', 'aze', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (17, 'Bahamas', 'bs', 'bhs', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (18, 'Bahrain', 'bh', 'bhr', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (19, 'Bangladesh', 'bd', 'bgd', 24);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (20, 'Barbados', 'bb', 'brb', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (21, 'Belarus', 'by', 'blr', 14);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (22, 'Belgium', 'be', 'bel', 12);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (23, 'Belize', 'bz', 'blz', 52);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (24, 'Benin', 'bj', 'ben', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (25, 'Bermuda', 'bm', 'bmu', 51);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (26, 'Bhutan', 'bt', 'btn', 24);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (27, 'Bolivia, Plurinational State of', 'bo', 'bol', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (28, 'Bonaire, Sint Eustatius and Saba', 'bq', 'bes', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (29, 'Bosnia and Herzegovina', 'ba', 'bih', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (30, 'Botswana', 'bw', 'bwa', 45);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (31, 'Bouvet Island', 'bv', 'NULL', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (32, 'Brazil', 'br', 'bra', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (33, 'British Indian Ocean Territory', 'io', 'NULL', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (34, 'Brunei Darussalam', 'bn', 'brn', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (35, 'Bulgaria', 'bg', 'bgr', 14);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (36, 'Burkina Faso', 'bf', 'bfa', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (37, 'Burundi', 'bi', 'bdi', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (38, 'Cambodia', 'kh', 'khm', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (39, 'Cameroon', 'cm', 'cmr', 43);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (40, 'Canada', 'ca', 'can', 51);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (41, 'Cape Verde', 'cv', 'cpv', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (42, 'Cayman Islands', 'ky', 'cym', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (43, 'Central African Republic', 'cf', 'caf', 43);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (44, 'Chad', 'td', 'tcd', 43);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (45, 'Chile', 'cl', 'chl', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (46, 'China', 'cn', 'chn', 22);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (47, 'Christmas Island', 'cx', 'NULL', 31);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (48, 'Cocos (Keeling) Islands', 'cc', 'NULL', 31);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (49, 'Colombia', 'co', 'col', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (50, 'Comoros', 'km', 'com', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (51, 'Congo', 'cg', 'cog', 43);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (52, 'Congo, The Democratic Republic of the', 'cd', 'cod', 43);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (53, 'Cook Islands', 'ck', 'cok', 34);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (54, 'Costa Rica', 'cr', 'cri', 52);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (55, 'Cote d\'Ivoire', 'ci', 'civ', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (56, 'Croatia', 'hr', 'hrv', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (57, 'Cuba', 'cu', 'cub', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (58, 'Curacao', 'cw', 'cuw', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (59, 'Cyprus', 'cy', 'cyp', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (60, 'Czech Republic', 'cz', 'cze', 14);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (61, 'Denmark', 'dk', 'dnk', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (62, 'Djibouti', 'dj', 'dji', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (63, 'Dominica', 'dm', 'dma', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (64, 'Dominican Republic', 'do', 'dom', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (65, 'Ecuador', 'ec', 'ecu', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (66, 'Egypt', 'eg', 'egy', 41);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (67, 'El Salvador', 'sv', 'slv', 52);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (68, 'Equatorial Guinea', 'gq', 'gnq', 43);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (69, 'Eritrea', 'er', 'eri', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (70, 'Estonia', 'ee', 'est', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (71, 'Ethiopia', 'et', 'eth', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (72, 'Falkland Islands (Malvinas)', 'fk', 'flk', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (73, 'Faroe Islands', 'fo', 'fro', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (74, 'Fiji', 'fj', 'fji', 33);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (75, 'Finland', 'fi', 'fin', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (76, 'France', 'fr', 'fra', 12);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (77, 'French Guiana', 'gf', 'guf', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (78, 'French Polynesia', 'pf', 'pyf', 34);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (79, 'French Southern Territories', 'tf', 'NULL', 71);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (80, 'Gabon', 'ga', 'gab', 43);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (81, 'Gambia', 'gm', 'gmb', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (82, 'Georgia', 'ge', 'geo', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (83, 'Germany', 'de', 'deu', 12);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (84, 'Ghana', 'gh', 'gha', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (85, 'Gibraltar', 'gi', 'gib', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (86, 'Greece', 'gr', 'grc', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (87, 'Greenland', 'gl', 'grl', 51);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (88, 'Grenada', 'gd', 'grd', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (89, 'Guadeloupe', 'gp', 'glp', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (90, 'Guam', 'gu', 'gum', 32);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (91, 'Guatemala', 'gt', 'gtm', 52);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (92, 'Guernsey', 'gg', 'ggy', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (93, 'Guinea', 'gn', 'gin', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (94, 'Guinea-Bissau', 'gw', 'gnb', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (95, 'Guyana', 'gy', 'guy', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (96, 'Haiti', 'ht', 'hti', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (97, 'Heard Island and McDonald Islands', 'hm', 'NULL', 71);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (98, 'Holy See (Vatican City State)', 'va', 'vat', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (99, 'Honduras', 'hn', 'hnd', 52);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (100, 'China, Hong Kong Special Administrative Region', 'hk', 'hkg', 22);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (101, 'Hungary', 'hu', 'hun', 14);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (102, 'Iceland', 'is', 'isl', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (103, 'India', 'in', 'ind', 24);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (104, 'Indonesia', 'id', 'idn', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (105, 'Iran, Islamic Republic of', 'ir', 'irn', 24);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (106, 'Iraq', 'iq', 'irq', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (107, 'Ireland', 'ie', 'irl', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (108, 'Isle of Man', 'im', 'imn', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (109, 'Israel', 'il', 'isr', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (110, 'Italy', 'it', 'ita', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (111, 'Jamaica', 'jm', 'jam', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (112, 'Japan', 'jp', 'jpn', 22);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (113, 'Jersey', 'je', 'jey', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (114, 'Jordan', 'jo', 'jor', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (115, 'Kazakhstan', 'kz', 'kaz', 21);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (116, 'Kenya', 'ke', 'ken', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (117, 'Kiribati', 'ki', 'kir', 32);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (118, 'Korea, Democratic People\'s Republic of', 'kp', 'prk', 22);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (119, 'Korea, Republic of', 'kr', 'kor', 22);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (120, 'Kuwait', 'kw', 'kwt', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (121, 'Kyrgyzstan', 'kg', 'kgz', 21);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (122, 'Lao People\'s Democratic Republic', 'la', 'lao', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (123, 'Latvia', 'lv', 'lva', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (124, 'Lebanon', 'lb', 'lbn', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (125, 'Lesotho', 'ls', 'lso', 45);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (126, 'Liberia', 'lr', 'lbr', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (127, 'Libyan Arab Jamahiriya', 'ly', 'lby', 41);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (128, 'Liechtenstein', 'li', 'lie', 12);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (129, 'Lithuania', 'lt', 'ltu', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (130, 'Luxembourg', 'lu', 'lux', 12);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (131, 'China, Macau Special Administrative Region', 'mo', 'mac', 22);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (132, 'Macedonia, The former Yugoslav Republic of', 'mk', 'mkd', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (133, 'Madagascar', 'mg', 'mdg', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (134, 'Malawi', 'mw', 'mwi', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (135, 'Malaysia', 'my', 'mys', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (136, 'Maldives', 'mv', 'mdv', 24);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (137, 'Mali', 'ml', 'mli', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (138, 'Malta', 'mt', 'mlt', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (139, 'Marshall Islands', 'mh', 'mhl', 32);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (140, 'Martinique', 'mq', 'mtq', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (141, 'Mauritania', 'mr', 'mrt', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (142, 'Mauritius', 'mu', 'mus', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (143, 'Mayotte', 'yt', 'myt', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (144, 'Mexico', 'mx', 'mex', 52);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (145, 'Micronesia, Federated States of', 'fm', 'fsm', 32);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (146, 'Moldova, Republic of', 'md', 'mda', 14);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (147, 'Monaco', 'mc', 'mco', 12);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (148, 'Mongolia', 'mn', 'mng', 22);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (149, 'Montenegro', 'me', 'mne', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (150, 'Montserrat', 'ms', 'msr', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (151, 'Morocco', 'ma', 'mar', 41);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (152, 'Mozambique', 'mz', 'moz', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (153, 'Myanmar', 'mm', 'mmr', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (154, 'Namibia', 'na', 'nam', 45);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (155, 'Nauru', 'nr', 'nru', 32);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (156, 'Nepal', 'np', 'npl', 24);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (157, 'Netherlands', 'nl', 'nld', 12);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (158, 'New Caledonia', 'nc', 'ncl', 33);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (159, 'New Zealand', 'nz', 'nzl', 31);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (160, 'Nicaragua', 'ni', 'nic', 52);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (161, 'Niger', 'ne', 'ner', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (162, 'Nigeria', 'ng', 'nga', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (163, 'Niue', 'nu', 'niu', 34);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (164, 'Norfolk Island', 'nf', 'nfk', 31);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (165, 'Northern Mariana Islands', 'mp', 'mnp', 32);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (166, 'Norway', 'no', 'nor', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (167, 'Oman', 'om', 'omn', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (168, 'Pakistan', 'pk', 'pak', 24);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (169, 'Palau', 'pw', 'plw', 32);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (170, 'State of Palestine, \"non-state entity\"', 'ps', 'pse', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (171, 'Panama', 'pa', 'pan', 52);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (172, 'Papua New Guinea', 'pg', 'png', 33);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (173, 'Paraguay', 'py', 'pry', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (174, 'Peru', 'pe', 'per', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (175, 'Philippines', 'ph', 'phl', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (176, 'Pitcairn', 'pn', 'pcn', 34);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (177, 'Poland', 'pl', 'pol', 14);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (178, 'Portugal', 'pt', 'prt', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (179, 'Puerto Rico', 'pr', 'pri', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (180, 'Qatar', 'qa', 'qat', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (181, 'Reunion', 're', 'reu', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (182, 'Romania', 'ro', 'rou', 14);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (183, 'Russian Federation', 'ru', 'rus', 14);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (184, 'Rwanda', 'rw', 'rwa', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (185, 'Saint Barthelemy', 'bl', 'blm', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (186, 'Saint Helena, Ascension and Tristan Da Cunha', 'sh', 'shn', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (187, 'Saint Kitts and Nevis', 'kn', 'kna', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (188, 'Saint Lucia', 'lc', 'lca', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (189, 'Saint Martin (French Part)', 'mf', 'maf', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (190, 'Saint Pierre and Miquelon', 'pm', 'spm', 51);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (191, 'Saint Vincent and The Grenadines', 'vc', 'vct', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (192, 'Samoa', 'ws', 'wsm', 34);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (193, 'San Marino', 'sm', 'smr', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (194, 'Sao Tome and Principe', 'st', 'stp', 43);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (195, 'Saudi Arabia', 'sa', 'sau', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (196, 'Senegal', 'sn', 'sen', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (197, 'Serbia', 'rs', 'srb', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (198, 'Seychelles', 'sc', 'syc', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (199, 'Sierra Leone', 'sl', 'sle', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (200, 'Singapore', 'sg', 'sgp', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (201, 'Sint Maarten (Dutch Part)', 'sx', 'sxm', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (202, 'Slovakia', 'sk', 'svk', 14);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (203, 'Slovenia', 'si', 'svn', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (204, 'Solomon Islands', 'sb', 'slb', 33);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (205, 'Somalia', 'so', 'som', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (206, 'South Africa', 'za', 'zaf', 45);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (207, 'South Georgia and The South Sandwich Islands', 'gs', 'NULL', 71);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (208, 'South Sudan', 'ss', 'ssd', 41);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (209, 'Spain', 'es', 'esp', 13);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (210, 'Sri Lanka', 'lk', 'lka', 24);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (211, 'Sudan', 'sd', 'sdn', 41);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (212, 'Suriname', 'sr', 'sur', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (213, 'Svalbard and Jan Mayen', 'sj', 'sjm', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (214, 'Swaziland', 'sz', 'swz', 45);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (215, 'Sweden', 'se', 'swe', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (216, 'Switzerland', 'ch', 'che', 12);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (217, 'Syrian Arab Republic', 'sy', 'syr', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (218, 'Taiwan, Province of China', 'tw', 'NULL', 22);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (219, 'Tajikistan', 'tj', 'tjk', 21);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (220, 'Tanzania, United Republic of', 'tz', 'tza', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (221, 'Thailand', 'th', 'tha', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (222, 'Timor-Leste', 'tl', 'tls', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (223, 'Togo', 'tg', 'tgo', 42);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (224, 'Tokelau', 'tk', 'tkl', 34);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (225, 'Tonga', 'to', 'ton', 34);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (226, 'Trinidad and Tobago', 'tt', 'tto', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (227, 'Tunisia', 'tn', 'tun', 41);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (228, 'Turkey', 'tr', 'tur', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (229, 'Turkmenistan', 'tm', 'tkm', 21);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (230, 'Turks and Caicos Islands', 'tc', 'tca', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (231, 'Tuvalu', 'tv', 'tuv', 34);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (232, 'Uganda', 'ug', 'uga', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (233, 'Ukraine', 'ua', 'ukr', 14);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (234, 'United Arab Emirates', 'ae', 'are', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (235, 'United Kingdom of Great Britain and Northern Irela', 'gb', 'gbr', 11);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (236, 'United States', 'us', 'usa', 51);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (237, 'United States Minor Outlying Islands', 'um', 'NULL', 51);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (238, 'Uruguay', 'uy', 'ury', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (239, 'Uzbekistan', 'uz', 'uzb', 21);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (240, 'Vanuatu', 'vu', 'vut', 33);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (241, 'Venezuela, Bolivarian Republic of', 've', 'ven', 61);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (242, 'Viet Nam', 'vn', 'vnm', 25);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (243, 'Virgin Islands, British', 'vg', 'vgb', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (244, 'Virgin Islands, U.S.', 'vi', 'vir', 53);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (245, 'Wallis and Futuna', 'wf', 'wlf', 34);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (246, 'Western Sahara', 'eh', 'esh', 41);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (247, 'Yemen', 'ye', 'yem', 23);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (248, 'Zambia', 'zm', 'zmb', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`)
VALUES (249, 'Zimbabwe', 'zw', 'zwe', 44);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (250, 'Unknow', 'xx', 'xxx', 0);
INSERT INTO `tabbie`.`country` (`id`, `name`, `alpha_2`, `alpha_3`, `region_id`) VALUES (251, 'Kosovo', 'xk', NULL, 13);

COMMIT;


-- -----------------------------------------------------
-- Data for table `tabbie`.`society`
-- -----------------------------------------------------
START TRANSACTION;
USE `tabbie`;
INSERT INTO `tabbie`.`society` (`id`, `fullname`, `abr`, `city`, `country_id`)
VALUES (NULL, 'Debattierklub Wien', 'DKW', 'Vienna', 15);

COMMIT;

