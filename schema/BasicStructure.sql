-- MySQL dump 10.13  Distrib 5.7.9, for osx10.9 (x86_64)
--
-- Host: localhost    Database: tabbie
-- ------------------------------------------------------
-- Server version	5.1.73-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `adjudicator`
--

DROP TABLE IF EXISTS `adjudicator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adjudicator` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tournament_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `breaking` tinyint(4) NOT NULL DEFAULT '0',
  `strength` tinyint(4) DEFAULT NULL,
  `society_id` int(10) unsigned NOT NULL,
  `can_chair` tinyint(1) NOT NULL DEFAULT '1',
  `are_watched` tinyint(1) NOT NULL DEFAULT '0',
  `checkedin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_adjudicator_username1_idx` (`user_id`),
  KEY `fk_adjudicator_tournament1_idx` (`tournament_id`),
  KEY `fk_adjudicator_society1_idx` (`society_id`),
  CONSTRAINT `fk_adjudicator_society1` FOREIGN KEY (`society_id`) REFERENCES `society` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_username1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4303 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adjudicator_in_panel`
--

DROP TABLE IF EXISTS `adjudicator_in_panel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adjudicator_in_panel` (
  `adjudicator_id` int(10) unsigned NOT NULL,
  `panel_id` int(10) unsigned NOT NULL,
  `function` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `got_feedback` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`adjudicator_id`,`panel_id`),
  KEY `fk_adjudicator_has_panel_panel1_idx` (`panel_id`),
  KEY `fk_adjudicator_has_panel_adjudicator1_idx` (`adjudicator_id`),
  CONSTRAINT `fk_adjudicator_has_panel_adjudicator1` FOREIGN KEY (`adjudicator_id`) REFERENCES `adjudicator` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_has_panel_panel1` FOREIGN KEY (`panel_id`) REFERENCES `panel` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adjudicator_strike`
--

DROP TABLE IF EXISTS `adjudicator_strike`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adjudicator_strike` (
  `adjudicator_from_id` int(10) unsigned NOT NULL,
  `adjudicator_to_id` int(10) unsigned NOT NULL,
  `tournament_id` int(10) unsigned NOT NULL,
  `user_clash_id` int(11) DEFAULT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`adjudicator_from_id`,`adjudicator_to_id`),
  KEY `fk_adjudicator_has_adjudicator_adjudicator2_idx` (`adjudicator_to_id`),
  KEY `fk_adjudicator_has_adjudicator_adjudicator1_idx` (`adjudicator_from_id`),
  KEY `fk_adjudicator_strike_tournament1_idx` (`tournament_id`),
  KEY `fk_adjudicator_strike_user_clash1_idx` (`user_clash_id`),
  CONSTRAINT `fk_adjudicator_has_adjudicator_adjudicator1` FOREIGN KEY (`adjudicator_from_id`) REFERENCES `adjudicator` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_has_adjudicator_adjudicator2` FOREIGN KEY (`adjudicator_to_id`) REFERENCES `adjudicator` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_strike_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_adjudicator_strike_user_clash1` FOREIGN KEY (`user_clash_id`) REFERENCES `user_clash` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `answer`
--

DROP TABLE IF EXISTS `answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feedback_id` int(10) unsigned NOT NULL,
  `question_id` int(10) unsigned NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `fk_answer_questions1_idx` (`question_id`),
  KEY `fk_answer_feedback1_idx` (`feedback_id`),
  CONSTRAINT `fk_answer_feedback1` FOREIGN KEY (`feedback_id`) REFERENCES `feedback` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_answer_questions1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=48524 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ca`
--

DROP TABLE IF EXISTS `ca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ca` (
  `user_id` int(11) unsigned NOT NULL,
  `tournament_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`tournament_id`),
  KEY `fk_user_has_tournament_tournament2_idx` (`tournament_id`),
  KEY `fk_user_has_tournament_user2_idx` (`user_id`),
  CONSTRAINT `fk_user_has_tournament_tournament2` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_tournament_user2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `convenor`
--

DROP TABLE IF EXISTS `convenor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `convenor` (
  `tournament_id` int(10) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`tournament_id`,`user_id`),
  KEY `fk_tournament_has_user_user1_idx` (`user_id`),
  KEY `fk_tournament_has_user_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_tournament_has_user_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tournament_has_user_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `alpha_2` varchar(2) DEFAULT NULL,
  `alpha_3` varchar(3) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country` VALUES (1,'Afghanistan','af','afg',24),(2,'Aland Islands','ax','ala',11),(3,'Albania','al','alb',13),(4,'Algeria','dz','dza',41),(5,'American Samoa','as','asm',34),(6,'Andorra','ad','and',13),(7,'Angola','ao','ago',43),(8,'Anguilla','ai','aia',53),(9,'Antarctica','aq',NULL,71),(10,'Antigua and Barbuda','ag','atg',53),(11,'Argentina','ar','arg',61),(12,'Armenia','am','arm',23),(13,'Aruba','aw','abw',53),(14,'Australia','au','aus',31),(15,'Austria','at','aut',12),(16,'Azerbaijan','az','aze',23),(17,'Bahamas','bs','bhs',53),(18,'Bahrain','bh','bhr',23),(19,'Bangladesh','bd','bgd',24),(20,'Barbados','bb','brb',53),(21,'Belarus','by','blr',14),(22,'Belgium','be','bel',12),(23,'Belize','bz','blz',52),(24,'Benin','bj','ben',42),(25,'Bermuda','bm','bmu',51),(26,'Bhutan','bt','btn',24),(27,'Bolivia, Plurinational State of','bo','bol',61),(28,'Bonaire, Sint Eustatius and Saba','bq','bes',53),(29,'Bosnia and Herzegovina','ba','bih',13),(30,'Botswana','bw','bwa',45),(31,'Bouvet Island','bv',NULL,11),(32,'Brazil','br','bra',61),(33,'British Indian Ocean Territory','io',NULL,25),(34,'Brunei Darussalam','bn','brn',25),(35,'Bulgaria','bg','bgr',14),(36,'Burkina Faso','bf','bfa',42),(37,'Burundi','bi','bdi',44),(38,'Cambodia','kh','khm',25),(39,'Cameroon','cm','cmr',43),(40,'Canada','ca','can',51),(41,'Cape Verde','cv','cpv',42),(42,'Cayman Islands','ky','cym',53),(43,'Central African Republic','cf','caf',43),(44,'Chad','td','tcd',43),(45,'Chile','cl','chl',61),(46,'China','cn','chn',22),(47,'Christmas Island','cx',NULL,31),(48,'Cocos (Keeling) Islands','cc',NULL,31),(49,'Colombia','co','col',61),(50,'Comoros','km','com',44),(51,'Congo','cg','cog',43),(52,'Congo, The Democratic Republic of the','cd','cod',43),(53,'Cook Islands','ck','cok',34),(54,'Costa Rica','cr','cri',52),(55,'Cote d\'Ivoire','ci','civ',42),(56,'Croatia','hr','hrv',13),(57,'Cuba','cu','cub',53),(58,'Curacao','cw','cuw',53),(59,'Cyprus','cy','cyp',13),(60,'Czech Republic','cz','cze',14),(61,'Denmark','dk','dnk',11),(62,'Djibouti','dj','dji',44),(63,'Dominica','dm','dma',53),(64,'Dominican Republic','do','dom',53),(65,'Ecuador','ec','ecu',61),(66,'Egypt','eg','egy',41),(67,'El Salvador','sv','slv',52),(68,'Equatorial Guinea','gq','gnq',43),(69,'Eritrea','er','eri',44),(70,'Estonia','ee','est',11),(71,'Ethiopia','et','eth',44),(72,'Falkland Islands (Malvinas)','fk','flk',61),(73,'Faroe Islands','fo','fro',11),(74,'Fiji','fj','fji',33),(75,'Finland','fi','fin',11),(76,'France','fr','fra',12),(77,'French Guiana','gf','guf',61),(78,'French Polynesia','pf','pyf',34),(79,'French Southern Territories','tf',NULL,71),(80,'Gabon','ga','gab',43),(81,'Gambia','gm','gmb',42),(82,'Georgia','ge','geo',23),(83,'Germany','de','deu',12),(84,'Ghana','gh','gha',42),(85,'Gibraltar','gi','gib',13),(86,'Greece','gr','grc',13),(87,'Greenland','gl','grl',51),(88,'Grenada','gd','grd',53),(89,'Guadeloupe','gp','glp',53),(90,'Guam','gu','gum',32),(91,'Guatemala','gt','gtm',52),(92,'Guernsey','gg','ggy',11),(93,'Guinea','gn','gin',42),(94,'Guinea-Bissau','gw','gnb',42),(95,'Guyana','gy','guy',61),(96,'Haiti','ht','hti',53),(97,'Heard Island and McDonald Islands','hm',NULL,71),(98,'Holy See (Vatican City State)','va','vat',13),(99,'Honduras','hn','hnd',52),(100,'China, Hong Kong Special Administrative Region','hk','hkg',22),(101,'Hungary','hu','hun',14),(102,'Iceland','is','isl',11),(103,'India','in','ind',24),(104,'Indonesia','id','idn',25),(105,'Iran, Islamic Republic of','ir','irn',24),(106,'Iraq','iq','irq',23),(107,'Ireland','ie','irl',11),(108,'Isle of Man','im','imn',11),(109,'Israel','il','isr',23),(110,'Italy','it','ita',13),(111,'Jamaica','jm','jam',53),(112,'Japan','jp','jpn',22),(113,'Jersey','je','jey',11),(114,'Jordan','jo','jor',23),(115,'Kazakhstan','kz','kaz',21),(116,'Kenya','ke','ken',44),(117,'Kiribati','ki','kir',32),(118,'Korea, Democratic People\'s Republic of','kp','prk',22),(119,'Korea, Republic of','kr','kor',22),(120,'Kuwait','kw','kwt',23),(121,'Kyrgyzstan','kg','kgz',21),(122,'Lao People\'s Democratic Republic','la','lao',25),(123,'Latvia','lv','lva',11),(124,'Lebanon','lb','lbn',23),(125,'Lesotho','ls','lso',45),(126,'Liberia','lr','lbr',42),(127,'Libyan Arab Jamahiriya','ly','lby',41),(128,'Liechtenstein','li','lie',12),(129,'Lithuania','lt','ltu',11),(130,'Luxembourg','lu','lux',12),(131,'China, Macau Special Administrative Region','mo','mac',22),(132,'Macedonia, The former Yugoslav Republic of','mk','mkd',13),(133,'Madagascar','mg','mdg',44),(134,'Malawi','mw','mwi',44),(135,'Malaysia','my','mys',25),(136,'Maldives','mv','mdv',24),(137,'Mali','ml','mli',42),(138,'Malta','mt','mlt',13),(139,'Marshall Islands','mh','mhl',32),(140,'Martinique','mq','mtq',53),(141,'Mauritania','mr','mrt',42),(142,'Mauritius','mu','mus',44),(143,'Mayotte','yt','myt',44),(144,'Mexico','mx','mex',52),(145,'Micronesia, Federated States of','fm','fsm',32),(146,'Moldova, Republic of','md','mda',14),(147,'Monaco','mc','mco',12),(148,'Mongolia','mn','mng',22),(149,'Montenegro','me','mne',13),(150,'Montserrat','ms','msr',53),(151,'Morocco','ma','mar',41),(152,'Mozambique','mz','moz',44),(153,'Myanmar','mm','mmr',25),(154,'Namibia','na','nam',45),(155,'Nauru','nr','nru',32),(156,'Nepal','np','npl',24),(157,'Netherlands','nl','nld',12),(158,'New Caledonia','nc','ncl',33),(159,'New Zealand','nz','nzl',31),(160,'Nicaragua','ni','nic',52),(161,'Niger','ne','ner',42),(162,'Nigeria','ng','nga',42),(163,'Niue','nu','niu',34),(164,'Norfolk Island','nf','nfk',31),(165,'Northern Mariana Islands','mp','mnp',32),(166,'Norway','no','nor',11),(167,'Oman','om','omn',23),(168,'Pakistan','pk','pak',24),(169,'Palau','pw','plw',32),(170,'State of Palestine, \"non-state entity\"','ps','pse',23),(171,'Panama','pa','pan',52),(172,'Papua New Guinea','pg','png',33),(173,'Paraguay','py','pry',61),(174,'Peru','pe','per',61),(175,'Philippines','ph','phl',25),(176,'Pitcairn','pn','pcn',34),(177,'Poland','pl','pol',14),(178,'Portugal','pt','prt',13),(179,'Puerto Rico','pr','pri',53),(180,'Qatar','qa','qat',23),(181,'Reunion','re','reu',44),(182,'Romania','ro','rou',14),(183,'Russian Federation','ru','rus',14),(184,'Rwanda','rw','rwa',44),(185,'Saint Barthelemy','bl','blm',53),(186,'Saint Helena, Ascension and Tristan Da Cunha','sh','shn',42),(187,'Saint Kitts and Nevis','kn','kna',53),(188,'Saint Lucia','lc','lca',53),(189,'Saint Martin (French Part)','mf','maf',53),(190,'Saint Pierre and Miquelon','pm','spm',51),(191,'Saint Vincent and The Grenadines','vc','vct',53),(192,'Samoa','ws','wsm',34),(193,'San Marino','sm','smr',13),(194,'Sao Tome and Principe','st','stp',43),(195,'Saudi Arabia','sa','sau',23),(196,'Senegal','sn','sen',42),(197,'Serbia','rs','srb',13),(198,'Seychelles','sc','syc',44),(199,'Sierra Leone','sl','sle',42),(200,'Singapore','sg','sgp',25),(201,'Sint Maarten (Dutch Part)','sx','sxm',53),(202,'Slovakia','sk','svk',14),(203,'Slovenia','si','svn',13),(204,'Solomon Islands','sb','slb',33),(205,'Somalia','so','som',44),(206,'South Africa','za','zaf',45),(207,'South Georgia and The South Sandwich Islands','gs',NULL,71),(208,'South Sudan','ss','ssd',41),(209,'Spain','es','esp',13),(210,'Sri Lanka','lk','lka',24),(211,'Sudan','sd','sdn',41),(212,'Suriname','sr','sur',61),(213,'Svalbard and Jan Mayen','sj','sjm',11),(214,'Swaziland','sz','swz',45),(215,'Sweden','se','swe',11),(216,'Switzerland','ch','che',12),(217,'Syrian Arab Republic','sy','syr',23),(218,'Taiwan, Province of China','tw',NULL,22),(219,'Tajikistan','tj','tjk',21),(220,'Tanzania, United Republic of','tz','tza',44),(221,'Thailand','th','tha',25),(222,'Timor-Leste','tl','tls',25),(223,'Togo','tg','tgo',42),(224,'Tokelau','tk','tkl',34),(225,'Tonga','to','ton',34),(226,'Trinidad and Tobago','tt','tto',53),(227,'Tunisia','tn','tun',41),(228,'Turkey','tr','tur',23),(229,'Turkmenistan','tm','tkm',21),(230,'Turks and Caicos Islands','tc','tca',53),(231,'Tuvalu','tv','tuv',34),(232,'Uganda','ug','uga',44),(233,'Ukraine','ua','ukr',14),(234,'United Arab Emirates','ae','are',23),(235,'United Kingdom of Great Britain and Northern Ireland','gb','gbr',11),(236,'United States of America','us','usa',51),(237,'United States Minor Outlying Islands','um',NULL,51),(238,'Uruguay','uy','ury',61),(239,'Uzbekistan','uz','uzb',21),(240,'Vanuatu','vu','vut',33),(241,'Venezuela, Bolivarian Republic of','ve','ven',61),(242,'Viet Nam','vn','vnm',25),(243,'Virgin Islands, British','vg','vgb',53),(244,'Virgin Islands, U.S.','vi','vir',53),(245,'Wallis and Futuna','wf','wlf',34),(246,'Western Sahara','eh','esh',41),(247,'Yemen','ye','yem',23),(248,'Zambia','zm','zmb',44),(249,'Zimbabwe','zw','zwe',44),(250,'Unknow','xx','xxx',0),(251,'Kosovo','xk','unk',13);
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `debate`
--

DROP TABLE IF EXISTS `debate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round_id` int(10) unsigned NOT NULL,
  `tournament_id` int(10) unsigned NOT NULL,
  `og_team_id` int(10) unsigned NOT NULL,
  `oo_team_id` int(10) unsigned NOT NULL,
  `cg_team_id` int(10) unsigned NOT NULL,
  `co_team_id` int(10) unsigned NOT NULL,
  `panel_id` int(10) unsigned NOT NULL,
  `venue_id` int(10) unsigned NOT NULL,
  `energy` int(11) NOT NULL DEFAULT '0',
  `og_feedback` tinyint(1) NOT NULL DEFAULT '0',
  `oo_feedback` tinyint(1) NOT NULL DEFAULT '0',
  `cg_feedback` tinyint(1) NOT NULL DEFAULT '0',
  `co_feedback` tinyint(1) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `messages` text,
  PRIMARY KEY (`id`),
  KEY `fk_debate_venue1_idx` (`venue_id`),
  KEY `fk_debate_panel1_idx` (`panel_id`),
  KEY `fk_debate_round1_idx` (`round_id`,`tournament_id`),
  KEY `fk_debate_team1_idx` (`og_team_id`),
  KEY `fk_debate_team2_idx` (`oo_team_id`),
  KEY `fk_debate_team3_idx` (`cg_team_id`),
  KEY `fk_debate_team4_idx` (`co_team_id`),
  CONSTRAINT `fk_debate_panel1` FOREIGN KEY (`panel_id`) REFERENCES `panel` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_debate_venue1` FOREIGN KEY (`venue_id`) REFERENCES `venue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8674 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `energy_config`
--

DROP TABLE IF EXISTS `energy_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `energy_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `tournament_id` int(10) unsigned NOT NULL,
  `label` varchar(255) NOT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_energy_config_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_energy_config_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3827 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `debate_id` int(10) unsigned NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `to_type` tinyint(4) DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `from_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_feedback_debate1_idx` (`debate_id`),
  CONSTRAINT `fk_feedback_debate1` FOREIGN KEY (`debate_id`) REFERENCES `debate` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9362 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `in_society`
--

DROP TABLE IF EXISTS `in_society`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `in_society` (
  `user_id` int(10) unsigned NOT NULL,
  `society_id` int(10) unsigned NOT NULL,
  `starting` date NOT NULL,
  `ending` date DEFAULT NULL,
  PRIMARY KEY (`society_id`,`user_id`),
  KEY `fk_username_has_university_university1_idx` (`society_id`),
  KEY `fk_username_has_university_username1_idx` (`user_id`),
  CONSTRAINT `fk_username_in_society_society1` FOREIGN KEY (`society_id`) REFERENCES `society` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_username_in_society_username1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language` (
  `language` varchar(16) NOT NULL,
  `label` varchar(100) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language`
--

LOCK TABLES `language` WRITE;
/*!40000 ALTER TABLE `language` DISABLE KEYS */;
INSERT INTO `language` VALUES ('de-DE','German (DE)','2016-01-18 16:28:28'),('es-CO','Colombian (ES)','2015-12-29 07:27:02'),('fr-FR','Frence (FR)','2016-01-05 17:23:24'),('tr-TR','Turkish (TR)','2015-09-25 14:42:06');
/*!40000 ALTER TABLE `language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `language_maintainer`
--

DROP TABLE IF EXISTS `language_maintainer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language_maintainer` (
  `user_id` int(11) unsigned NOT NULL,
  `language_language` varchar(16) NOT NULL,
  PRIMARY KEY (`user_id`,`language_language`),
  KEY `fk_user_has_language_language1_idx` (`language_language`),
  KEY `fk_user_has_language_user1_idx` (`user_id`),
  CONSTRAINT `fk_user_has_language_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_maintaines_language` FOREIGN KEY (`language_language`) REFERENCES `language` (`language`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `language_officer`
--

DROP TABLE IF EXISTS `language_officer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language_officer` (
  `user_id` int(11) unsigned NOT NULL,
  `tournament_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`tournament_id`),
  KEY `fk_user_has_tournament_tournament1_idx` (`tournament_id`),
  KEY `fk_user_has_tournament_user1_idx` (`user_id`),
  CONSTRAINT `fk_user_has_tournament_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_tournament_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `legacy_motion`
--

DROP TABLE IF EXISTS `legacy_motion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legacy_motion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `motion` text NOT NULL,
  `language` varchar(2) NOT NULL DEFAULT 'en',
  `time` date NOT NULL,
  `infoslide` text,
  `tournament` varchar(255) NOT NULL,
  `round` varchar(45) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `by_user_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_legacy_motion_user1_idx` (`by_user_id`),
  CONSTRAINT `fk_legacy_motion_user1` FOREIGN KEY (`by_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `legacy_tag`
--

DROP TABLE IF EXISTS `legacy_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legacy_tag` (
  `motion_tag_id` int(11) NOT NULL,
  `legacy_motion_id` int(11) NOT NULL,
  PRIMARY KEY (`motion_tag_id`,`legacy_motion_id`),
  KEY `fk_motion_tag_has_legacy_motion_legacy_motion1_idx` (`legacy_motion_id`),
  KEY `fk_motion_tag_has_legacy_motion_motion_tag1_idx` (`motion_tag_id`),
  CONSTRAINT `fk_motion_tag_has_legacy_motion_legacy_motion1` FOREIGN KEY (`legacy_motion_id`) REFERENCES `legacy_motion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_motion_tag_has_legacy_motion_motion_tag1` FOREIGN KEY (`motion_tag_id`) REFERENCES `motion_tag` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `language` varchar(16) NOT NULL DEFAULT '',
  `translation` text CHARACTER SET utf8,
  PRIMARY KEY (`language`,`id`),
  KEY `fk_message_source_message` (`id`),
  CONSTRAINT `fk_message_source_message` FOREIGN KEY (`id`) REFERENCES `source_message` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
INSERT INTO `message` VALUES (1,'de-DE','ID'),(2,'de-DE','Name'),(3,'de-DE','Import: {modelClass}'),(4,'de-DE','Import'),(5,'de-DE','Verein'),(6,'de-DE','Update'),(7,'de-DE','Löschen'),(8,'de-DE','Bist du sicher, dass du das Element löschen möchtest?'),(9,'de-DE','Update: {modelClass}'),(10,'de-DE','Kombiniere \'{society}\' mit ...'),(11,'de-DE','Wähle den korrekten Verein ...'),(12,'de-DE','Erstelle {modelClass}'),(13,'de-DE','Betrachte {modelClass}'),(14,'de-DE','Aktualisiere {modelClass}'),(15,'de-DE','Lösche {modelClass}'),(16,'de-DE','Neues Element hinzufügen'),(17,'de-DE','Inhalt neu laden'),(18,'de-DE','Importiere von CSV Datei'),(19,'de-DE','Erstelle'),(20,'de-DE','Sprachen'),(21,'de-DE','Erstelle Sprache'),(22,'de-DE','Spezielle Anforderungen'),(23,'de-DE','Themen Tags'),(24,'de-DE','Kombiniere Themen Tag \'{tag}\' mit ...'),(25,'de-DE','Wähle den korrekten Tag ...'),(26,'de-DE','Suche'),(27,'de-DE','Zurücksetzen'),(28,'de-DE','Erstelle Themen Tag'),(29,'de-DE','API'),(30,'de-DE','Master'),(31,'de-DE','Nachrichten'),(32,'de-DE','Nachricht erstellen'),(33,'de-DE','{count} Tags geändert'),(34,'de-DE','Datei Syntax falsch'),(35,'de-DE','Themen-Tag'),(36,'de-DE','Runde'),(37,'de-DE','Abkürzung'),(38,'de-DE','Menge'),(39,'de-DE','Eröffnende Regierung'),(40,'de-DE','Eröffnende Opposition'),(41,'de-DE','Schließende Regierung'),(42,'de-DE','Schließende Opposition'),(43,'de-DE','Team'),(44,'de-DE','Aktiv'),(45,'de-DE','Turnier'),(46,'de-DE','RednerIn'),(47,'de-DE','Verein'),(48,'de-DE','Springerteam'),(49,'de-DE','Sprachstatus'),(50,'de-DE','Alles normal'),(51,'de-DE','Wurde durch Springerteam ersetzt'),(52,'de-DE','RednerIn {letter} erschien nicht'),(53,'de-DE','Schlüssel'),(54,'de-DE','Label'),(55,'de-DE','Wert'),(56,'de-DE','Teamposition darf nicht leer sein'),(57,'de-DE','KO-Runde'),(58,'de-DE','Tabmaster-BenutzerIn'),(59,'de-DE','BenutzerIn'),(60,'de-DE','ENL-Platzierung'),(61,'de-DE','ESL-Platzierung'),(62,'de-DE','Cache Ergebnisse'),(63,'de-DE','Voller Name'),(64,'de-DE','Abkürzung'),(65,'de-DE','Stadt'),(66,'de-DE','Land'),(67,'de-DE','ER Team'),(68,'de-DE','EO Team'),(69,'de-DE','SR Team'),(70,'de-DE','SO Team'),(71,'de-DE','Jury'),(72,'de-DE','Raum'),(73,'de-DE','ER Feedback'),(74,'de-DE','EO Feedback'),(75,'de-DE','SR Feedback'),(76,'de-DE','SO Feedback'),(77,'de-DE','Zeit'),(78,'de-DE','Thema'),(79,'de-DE','Sprache'),(80,'de-DE','Datum'),(81,'de-DE','Infotext zum Thema'),(82,'de-DE','Link'),(83,'de-DE','von BenutzerIn'),(84,'de-DE','Übersetzung'),(85,'de-DE','JurorIn'),(86,'de-DE','Antwort'),(87,'de-DE','Feedback'),(88,'de-DE','Frage'),(89,'de-DE','Erstellt'),(90,'de-DE','Läuft'),(91,'de-DE','Geschlossen'),(92,'de-DE','Versteckt'),(93,'de-DE','Veranstaltet von'),(94,'de-DE','Turniername'),(95,'de-DE','Beginnt am'),(96,'de-DE','Endet am'),(97,'de-DE','Zeitzone'),(98,'de-DE','Logo'),(99,'de-DE','URL-Kürzel'),(100,'de-DE','Tab-\r\nAlgorithmus'),(101,'de-DE','Voraussichtliche Rundenanzahl'),(102,'de-DE','Zeige ESL-Platzierung'),(103,'de-DE','Es gibt ein Finale'),(104,'de-DE','Es gibt ein Halbfinale'),(105,'de-DE','Es gibt ein Viertelfinale'),(106,'de-DE','Es gibt ein Achtelfinale'),(107,'de-DE','Zugriffsschlüssel'),(108,'de-DE','TeilnehmerInnenschild'),(109,'de-DE','Alpha 2'),(110,'de-DE','Alpha 3'),(111,'de-DE','Region'),(112,'de-DE','Sprachcode'),(113,'de-DE','Abdeckung'),(114,'de-DE','Letzte Aktualisierung'),(115,'de-DE','Stärke'),(116,'de-DE','Kann hauptjurieren'),(117,'de-DE','Werden beobachtet'),(118,'de-DE','Nicht bewertet'),(121,'de-DE','Kann jurieren'),(122,'de-DE','Ordentlich'),(124,'de-DE','Hohes Potential'),(125,'de-DE','HauptjurorIn'),(126,'de-DE','Gut'),(127,'de-DE','Breakend'),(128,'de-DE','ChefjurorIn'),(129,'de-DE','Beginn'),(130,'de-DE','Ende'),(131,'de-DE','Rednerpunkte'),(132,'de-DE','Diese Email-Adresse ist bereits vergeben.'),(133,'de-DE','DebattantIn'),(134,'de-DE','Auth-Schlüssel'),(135,'de-DE','Passwort-Hash'),(136,'de-DE','Passwort-Reset-Schlüssel'),(137,'de-DE','Email'),(138,'de-DE','Account-Rolle'),(139,'de-DE','Account-Status'),(140,'de-DE','Letzte Änderung'),(141,'de-DE','Vorname'),(142,'de-DE','Nachname'),(143,'de-DE','Bild'),(144,'de-DE','Platzhalter'),(145,'de-DE','TabmasterIn'),(146,'de-DE','Admin'),(147,'de-DE','Gelöscht'),(148,'de-DE','Nicht bekanntgegeben'),(149,'de-DE','Weiblich'),(150,'de-DE','Männlich'),(151,'de-DE','Andere'),(152,'de-DE','mixed'),(153,'de-DE','Noch nicht gesetzt'),(154,'de-DE','Interview wird benötigt'),(155,'de-DE','EPL'),(157,'de-DE','ESL'),(158,'de-DE','Englisch als zweite Sprache'),(159,'de-DE','EFL'),(160,'de-DE','English als Fremdsprache'),(161,'de-DE','Nicht gesetzt'),(162,'de-DE','Fehler beim Speichern von InSociety für {user_name\r\n}'),(163,'de-DE','Fehler beim Speichern von BenutzerIn {user_name}'),(164,'de-DE','{tournament_name}: BenutzerInnen-Account für {user_name}'),(165,'de-DE','Diese URL-Abkürzung ist nicht erlaubt.'),(166,'de-DE','Veröffentlicht'),(167,'de-DE','Angezeigt'),(168,'de-DE','Begann'),(169,'de-DE','Jurierend'),(170,'de-DE','Beendet'),(171,'de-DE','Haupt'),(172,'de-DE','EinsteigerInnen'),(173,'de-DE','Finale'),(174,'de-DE','Halbfinale'),(175,'de-DE','Viertelfinale'),(176,'de-DE','Achtelfinale'),(177,'de-DE','Runde #{num}'),(178,'de-DE','Vorrunde'),(179,'de-DE','Energie'),(180,'de-DE','Infotext'),(181,'de-DE','Vorbereitungszeit begann'),(182,'de-DE','Letzte Temperatur'),(183,'de-DE','ms zur Berechnung'),(184,'de-DE','Nicht genug Teams für einen Raum - {aktiv: {teams_count})'),(185,'de-DE','Mindestens 2 JurorInnen sind nötig - (aktive: {count_adju})'),(186,'de-DE','Anzahl der Teams muss durch 4 dividierbar sein ;) - (aktive: {count_teams})'),(187,'de-DE','Nicht genug aktive Räume - (aktive: {active_rooms} benötigt: {required})'),(188,'de-DE','Nicht genug JurorInnen - (aktive: {active} benötigt: {required})'),(189,'de-DE','Nicht genug freie JurorInnen bei dieser vorkonfigurierten Jurysetzung. (füllbare Räume: {active} mindestens benötigt: {required})'),(190,'de-DE','Kann Jury nicht speichern! Fehler: {message}'),(191,'de-DE','Kann Debatte nicht speichern! Fehler: {message}'),(192,'de-DE','Kann Debatte nicht speichern! Fehler:<br>{errors}'),(193,'de-DE','Keine Debatte #{num} zum Aktualisieren gefunden'),(195,'de-DE','Typ'),(196,'de-DE','Parameter falls gebraucht'),(197,'de-DE','Passt zu Team -> HauptjurorIn'),(198,'de-DE','Passt zu HauptjurorIn -> NebenjurorIn'),(199,'de-DE','Passt zu NebenjurorIn -> HauptjurorIn'),(200,'de-DE','Nicht gut'),(201,'de-DE','Sehr gut'),(202,'de-DE','Exzellent'),(203,'de-DE','Sternen-Wertung (1-5) Feld'),(204,'de-DE','Kurzes Textfeld'),(205,'de-DE','Langes Textfeld'),(206,'de-DE','Nummernfeld'),(207,'de-DE','Kontrollboxfeld'),(208,'de-DE','Debatte'),(209,'de-DE','Feedback für ID'),(210,'de-DE','JurorInnen-Konflikt von ID'),(211,'de-DE','JurorInnen-Konflikt mit ID'),(212,'de-DE','Gruppe'),(213,'de-DE','Aktiver Raum'),(214,'de-DE','NebenjurorIn'),(215,'de-DE','Verwendet'),(216,'de-DE','Ist vorkonfigurierte Jury'),(217,'de-DE','Jury #{id} beinhaltet {amount} HauptjurorInnen'),(218,'de-DE','Kategorie'),(219,'de-DE','Nachricht'),(220,'de-DE','Funktion'),(221,'de-DE','Altes Thema'),(222,'de-DE','Fragen'),(223,'de-DE','Konflikt mit'),(224,'de-DE','Grund'),(225,'de-DE','Teamkonflikt'),(226,'de-DE','JurorInnenkonflikt'),(227,'de-DE','Kein Typ gefunden'),(228,'de-DE','ER A RednerInnenpunkte'),(229,'de-DE','ER B RednerInnenpunkte'),(230,'de-DE','ER Platzierung'),(231,'de-DE','EO A RednerInnenpunkte'),(232,'de-DE','EO B RednerInnenpunkte'),(233,'de-DE','EO Platzierung'),(234,'de-DE','SR A RednerInnenpunkte'),(235,'de-DE','SR B RednerInnenpunkte'),(236,'de-DE','SR Platzierung'),(237,'de-DE','SO A RednerInnenpunkte'),(238,'de-DE','SO B RednerInnenpunkte'),(239,'de-DE','SO Platzierung'),(240,'de-DE','Überprüft'),(241,'de-DE','Eingegeben von BenutzerIn ID'),(242,'de-DE','Gleiche Platzierungen existieren'),(243,'de-DE','Ironman durch'),(244,'de-DE','CJ'),(245,'de-DE','Passwort-Reset-Schlüssel darf nicht leer sein.'),(246,'de-DE','Falscher Passwort-Reset-Schlüssel.'),(247,'de-DE','Komma ( , ) getrennte Datei'),(248,'de-DE','Strickpunkt ( ; ) getrennte Datei'),(249,'de-DE','Tag ( ->| ) getrennte Datei'),(250,'de-DE','CSV-Datei'),(251,'de-DE','Trennzeichen'),(252,'de-DE','Markiere als Test-Datenimport (es werden keine Emails versendet)'),(253,'de-DE','BenutzerInnenname'),(254,'de-DE','Profilbild'),(255,'de-DE','Derzeitiger Verein'),(256,'de-DE','Mit welchem Geschlecht identifizierst du dich am meisten'),(257,'de-DE','Diese URL ist nicht erlaubt.'),(258,'de-DE','{adju} ist registriert!'),(259,'de-DE','{adju} wurde bereits registriert!'),(260,'de-DE','{id) ist nicht gültig! Kein Juror!'),(261,'de-DE','{speaker} wurde registriert!'),(262,'de-DE','{speaker} wurde bereits registriert!'),(263,'de-DE','{id) ist nicht gültig! Kein Team!'),(264,'de-DE','Keine gültige Eingabe'),(265,'de-DE','Überprüfungscode'),(266,'de-DE','DebReg'),(267,'de-DE','Passwort zurücksetzen für {user}'),(268,'de-DE','Benutzerin mit dieser Email-Adresse nicht gefunden'),(269,'de-DE','{object} hinzufügen'),(270,'de-DE','Verein erstellen'),(271,'de-DE','Hey cool! Du hast einen unbekannten Verein hinzugefügt!'),(272,'de-DE','Bevor wir dich verknüpfen können gib uns doch bitte noch einige Informationen zu deinem Verein:'),(273,'de-DE','Suche nach Land ...'),(274,'de-DE','Neuen Verein hinzufügen'),(275,'de-DE','Suche nach Verein ...'),(276,'de-DE','Füge Datum des Beginns hinzu ...'),(277,'de-DE','Füge Datum des Endes hinzu, falls passend ...'),(278,'de-DE','Sprachen-Beauftragte'),(279,'de-DE','Beauftragte/r'),(280,'de-DE','Jedes {object} ...'),(281,'de-DE','Sprachstatus-Bericht'),(282,'de-DE','Status'),(283,'de-DE','Beantrage ein Interview'),(284,'de-DE','Setze ENL'),(285,'de-DE','Setze ESL'),(286,'de-DE','Sprachen-Beauftragte/r'),(288,'de-DE','Hinzufügen'),(289,'de-DE','Suchen nach NutzerIn ...'),(290,'de-DE','Einchecken'),(291,'de-DE','Übermitteln'),(292,'de-DE','TeilnehmerInnenschilder erstellen'),(293,'de-DE','Nur für BenutzerIn ...'),(294,'de-DE','TeilnehmerInnenschilder drucken'),(295,'de-DE','Strichcode generieren'),(296,'de-DE','Nach BenutzerIn suchen ... oder leer lassen'),(297,'de-DE','Strichcodes drucken'),(298,'de-DE','Teams'),(299,'de-DE','Teamname'),(300,'de-DE','RednerIn A'),(301,'de-DE','RednerIn B'),(302,'de-DE','So einsenden'),(303,'de-DE','Thema:'),(304,'de-DE','Jury:'),(305,'de-DE','Aktiv schalten'),(307,'de-DE','Nach BenutzerIn suchen ...'),(308,'de-DE','Turniere'),(309,'de-DE','Überblick'),(310,'de-DE','Themen'),(311,'de-DE','Teamtab'),(312,'de-DE','RednerInnentab'),(313,'de-DE','KO-Runden'),(314,'de-DE','Breakende JurorInnen'),(315,'de-DE','So einsenden!'),(316,'de-DE','DebReg-Turnier'),(317,'de-DE','Zeige vergangene Turniere'),(318,'de-DE','JurorInnen'),(319,'de-DE','Ergebnis'),(320,'de-DE','gemeinsam mit {teammate}'),(321,'de-DE','als Ironman'),(322,'de-DE','Du bist als Team <br> \'{team}\' {with} für {society} registriert'),(323,'de-DE','Du bist als JurorIn für {society} registriert'),(325,'de-DE','Registrierungsinformationen'),(326,'de-DE','Informationen hinzufügen'),(327,'de-DE','Runde #{num} Info'),(328,'de-DE','Du bist <b>{pos}</b> im Raum <b>{room}</b>.'),(329,'de-DE','Runde beginnt um: <b>{time}</b>'),(330,'de-DE','Infotext'),(331,'de-DE','Runde #{num} Teams'),(332,'de-DE','Mein super geniales Turnier ... z.B. Vienna IV'),(333,'de-DE','Wähle die CheforganisatorInnen aus ...'),(334,'de-DE','Wähle das Datum des Beginns...'),(335,'de-DE','Wähle das Datum des Endes ...'),(336,'de-DE','ChefjurorInnen'),(337,'de-DE','Wähle deine ChefjurorInnen ...'),(338,'de-DE','Wähle deinen Tabmaster ...'),(339,'de-DE','Turnierarchiv'),(340,'de-DE','Der obige Fehler ist aufgetreten während der Server deine Anfrage bearbeitet hat.'),(341,'de-DE','Bitte kontaktiere uns, wenn du der Meinung bist, dass dies ein Serverfehler ist. Danke!'),(342,'de-DE','Passwort wiederherstellen'),(343,'de-DE','Bitte wähle dein neues Passwort:'),(344,'de-DE','Speichern'),(345,'de-DE','Registrieren'),(346,'de-DE','Bitte fülle die folgenden Felder aus, um dich zu registrieren:'),(347,'de-DE','Die meisten Zuweisungsalgorithmen dieses Systems versuchen, unter anderem eine diverse Jury zu generieren. Damit dies funktionieren kann, würden wir dich bitten, eine Option aus der Liste auszuwählen. Wir sind uns bewusst, dass durch unsere Auswahl nicht jede persönliche Präferenz abgedeckt werden kann und entschuldigen uns für fehlende Optionen. Wenn du den Eindruck hast, dass keine dieser Optionen anwendbar ist, wähle bitte <Not Revealing>. Diese Option wird niemals regulären NutzerInnen angezeigt und dient einzig Berechnungszwecken!'),(348,'de-DE','Einloggen'),(349,'de-DE','Bitte fülle die folgenden Felder aus um dich einzuloggen:'),(350,'de-DE','Falls du dein Passwort vergessen hast, kannst du {resetIt}'),(351,'de-DE','es resetten'),(352,'de-DE','Passwortreset anfordern'),(353,'de-DE','Bitte gib deine Email-Adresse ein. Wir werden dir einen Link zum Resetten deines Passworts zusenden.'),(354,'de-DE','Senden'),(355,'de-DE','Räume-CSV'),(356,'de-DE','JurorInnen-CSV'),(357,'de-DE','Team-CSV'),(358,'de-DE','erstellt'),(359,'de-DE','Beispiel einer Raum-CSV'),(360,'de-DE','Beispiel einer Team-CSV'),(361,'de-DE','Beispiel einer JurorInnen-CSV'),(362,'de-DE','Aktuelles BPS-Turnier {count, plural, =0{Tournament} =1{Tournament} other{Tournaments}}'),(363,'de-DE','Willkommen bei {appName}!'),(364,'de-DE','Zeige Turniere'),(365,'de-DE','Erstelle Turnier'),(366,'de-DE','Ehe wir dich registrieren können vervollständige bitte die Informationen zu deinem Verein:'),(367,'de-DE','Kontakt'),(368,'de-DE','Vorkonfigurierte Jury #'),(369,'de-DE','Jury'),(370,'de-DE','Durchschnittliche Jury-Stärke'),(371,'de-DE','Erstelle Jury'),(372,'de-DE','Vorkonfigurierte Jury für die nächste Runde'),(373,'de-DE','Erstelle {object} ...'),(374,'de-DE','Platz'),(375,'de-DE','Teampunkte'),(376,'de-DE','#{number}'),(377,'de-DE','Keine breakenden Juroren definiert'),(378,'de-DE','Verteilung der RednerInnenpunkte'),(379,'de-DE','Lauf'),(380,'de-DE','Eröffnende Regierung'),(381,'de-DE','Eröffnende Opposition'),(382,'de-DE','Schließende Regierung'),(383,'de-DE','Schließende Opposition'),(384,'de-DE','Zeige Infotext'),(385,'de-DE','Zeige Thema'),(386,'de-DE','Fehlende Personen'),(387,'de-DE','Markiere fehlende Personen als inaktiv'),(388,'de-DE','Ergebnisse'),(389,'de-DE','Runner Ansicht für Runde #{number}'),(390,'de-DE','Automatische Aktualisierung <i id=\'pjax-status\' class=\'\'></i>'),(391,'de-DE','Aktualisiere individuellen Konflikt'),(392,'de-DE','Individueller Konflikt'),(393,'de-DE','Es sind noch nicht alle DebattantInnen im System. :)'),(394,'de-DE','Erstelle Konflikt'),(395,'de-DE','Aktualisiere Konflikt'),(396,'de-DE','Energie-Einstellungen'),(397,'de-DE','Aktualisiere Energie Einstellungen'),(398,'de-DE','Runde #{number}'),(399,'de-DE','Runden'),(400,'de-DE','Aktionen'),(401,'de-DE','Veröffentliche Tab'),(402,'de-DE','Erneute Draw-Erstellung versuchen'),(403,'de-DE','Aktualisiere Runde'),(404,'de-DE','Drowpdown-Menü umschalten'),(405,'de-DE','Verbessere weiter um'),(407,'de-DE','Bist du sicher, dass du die Runde neu anlegen möchtest? Alle Informationen gehen dabei verloren!'),(408,'de-DE','Drucke Laufzettel'),(411,'de-DE','Rundenstatus'),(412,'de-DE','Durchschnittliche Energie'),(413,'de-DE','Erstellungszeit'),(414,'de-DE','Farbpalette'),(415,'de-DE','Geschlecht'),(416,'de-DE','Regionen'),(417,'de-DE','Punkte'),(418,'de-DE','Laden ...'),(419,'de-DE','Zeige Feedback'),(420,'de-DE','Zeige BenutzerIn'),(421,'de-DE','Tausche Raum {venue} mit'),(422,'de-DE','Wähle einen Raum ...'),(423,'de-DE','Aktualisiere {modelClass} #{number}'),(424,'de-DE','Energielevel'),(425,'de-DE','Wähle ein Team ...'),(426,'de-DE','Wähle eine Sprache ...'),(427,'de-DE','Wähle JurorIn ...'),(428,'de-DE','Tausche JurorInnen'),(429,'de-DE','Tausche JurorIn ...'),(430,'de-DE','mit'),(431,'de-DE','JurorIn ...'),(432,'de-DE','Suche nach Themen-Tag ...'),(433,'de-DE','Rang'),(434,'de-DE','Gesamt'),(435,'de-DE','Debatten-ID'),(436,'de-DE','Raum'),(437,'de-DE','KO-Runden'),(438,'de-DE','Themenarchiv'),(439,'de-DE','Systemexternes Thema'),(440,'de-DE','Dein geniales Turnier'),(441,'de-DE','Füge ein Datum ein ...'),(442,'de-DE','Runde #1 oder Finale'),(443,'de-DE','DHW ...'),(444,'de-DE','http://bitte.quelle.angeben.de'),(445,'de-DE','{modelClass} manuell einfügen'),(446,'de-DE','Optionen'),(447,'de-DE','Weiter'),(448,'de-DE','Noch keine Ergebnisse!'),(449,'de-DE','Ergebnisse in Raum: {venue}'),(450,'de-DE','Ergebnisse für {venue}'),(451,'de-DE','Tabellenansicht'),(452,'de-DE','Ergebnisse für {label}'),(453,'de-DE','Wechseln zur Raumansicht'),(454,'de-DE','Ergebnis des Springerteams'),(455,'de-DE','Zeige Details zum Ergebnis'),(456,'de-DE','Korregiere Ergebnis'),(457,'de-DE','Raumansicht'),(458,'de-DE','Wechseln zur Tabellenansicht'),(459,'de-DE','Bestätige Daten für {venue}'),(460,'de-DE','beginne neu'),(461,'de-DE','Runde {number}'),(462,'de-DE','Vielen Dank'),(463,'de-DE','Vielen Dank!'),(464,'de-DE','Ergebnisse erfolgreich gespeichert'),(465,'de-DE','Geschwindigkeits-Bonus!'),(466,'de-DE','Schneller! Hopp hopp!'),(467,'de-DE','Faulpelze! Ihr seid Letzter!'),(468,'de-DE','Du bist <b>#{place}</b> von {max}'),(469,'de-DE','Feedback übermitteln'),(470,'de-DE','Zurück zum Turnier'),(471,'de-DE','Feedbacks'),(472,'de-DE','Ziel-JurorIn'),(473,'de-DE','JurorInnen-Name ...'),(474,'de-DE','JurorIn-Feedback'),(475,'de-DE','Übermittle Feedback'),(476,'de-DE','{tournament} - Sprachen-Beauftragte/r'),(477,'de-DE','Sprachstatus überprüfen'),(478,'de-DE','enthält geheime Alientechnologie'),(479,'de-DE','Fehler berichten'),(480,'de-DE','{tournament} - Manager'),(481,'de-DE','Räume auflisten'),(482,'de-DE','Raum erstellen'),(483,'de-DE','Raum importieren'),(484,'de-DE','Teams auflisten'),(485,'de-DE','Team erstellen'),(486,'de-DE','Team importieren'),(487,'de-DE','Teamkonflikt'),(488,'de-DE','JurorInnen auflisten'),(489,'de-DE','JurorIn erstellen'),(490,'de-DE','JurorIn importieren'),(491,'de-DE','Vorkonfigurierte Jury zeigen'),(492,'de-DE','Vorkonfigurierte Jury erstellen'),(493,'de-DE','JurorInnenkonflikt'),(494,'de-DE','Turnier aktualisieren'),(495,'de-DE','Teamübersicht anzeigen'),(496,'de-DE','RednerInnenübersicht anzeigen'),(497,'de-DE','KO-Rundenübersicht anzeigen'),(498,'de-DE','Das Tab zu veröffentlichen wird das Turnier schließen und archivieren! Bist du sicher, dass du fortfahren möchtest?'),(499,'de-DE','Fehlende/r BenutzerIn'),(500,'de-DE','Eincheck-Formular'),(501,'de-DE','Teilnehmerschilder drucken'),(502,'de-DE','Einchecken zurücksetzen'),(503,'de-DE','Bist du sicher, dass du das Einchecken zurücksetzen möchtest?'),(504,'de-DE','Sync mit DebReg'),(505,'de-DE','Migriere zu Tabbie1'),(506,'de-DE','Extreme Vorsicht, junger Padawan!'),(507,'de-DE','Runden auflisten'),(508,'de-DE','Runde erstellen'),(509,'de-DE','Energie-Optionen'),(510,'de-DE','Ergebnisse anzeigen'),(511,'de-DE','Laufzettel einfügen'),(512,'de-DE','Cache korrigieren'),(513,'de-DE','Fragen einrichten'),(514,'de-DE','Jedes Feedback'),(515,'de-DE','Feedback für JurorIn'),(516,'de-DE','Über'),(517,'de-DE','Anleitung'),(518,'de-DE','BenutzerInnen'),(519,'de-DE','Registrieren'),(520,'de-DE','{user}s Profil'),(521,'de-DE','{user}s Verlauf'),(522,'de-DE','Ausloggen'),(524,'de-DE','Aktualisiere {label}'),(525,'de-DE','Nächster Schritt'),(526,'de-DE','Raum {number}'),(527,'de-DE','Räume'),(529,'de-DE','Konflikte'),(530,'de-DE','Importiere Konflikte'),(531,'de-DE','Akzeptieren'),(532,'de-DE','Ablehnen'),(533,'de-DE','Suche nach Team ...'),(534,'de-DE','Suche nach JurorIn ...'),(535,'de-DE','JurorInnenkonflikt hinzufügen'),(536,'de-DE','{modelClass} zusätzlich erstellen'),(537,'de-DE','Team aktualisieren'),(538,'de-DE','Team löschen'),(539,'de-DE','(not set)'),(540,'de-DE',NULL),(541,'de-DE','Team mit JurorIn in Konflikt setzen'),(542,'de-DE','Team aktualisieren'),(543,'de-DE','Team löschen'),(544,'de-DE','{modelClass}s Verlauf'),(545,'de-DE','Verlauf'),(546,'de-DE','Team-Überblick'),(547,'de-DE','EPL-Platzierung'),(548,'de-DE','RednerInnenpunkte des Teams'),(549,'de-DE','Aktuell ist kein veröffentlichtes Tab verfügbar'),(550,'de-DE','Kann hauptjurieren'),(551,'de-DE','Sollte nicht hauptjurieren'),(552,'de-DE','Break'),(553,'de-DE','Nicht breakend'),(554,'de-DE','Unter Beobachtung'),(555,'de-DE','Nicht unter Beobachtung'),(556,'de-DE','Beobachtung\r\n umschalten'),(557,'de-DE','Breakend umschalten'),(558,'de-DE','Beobachterstatus zurücksetzen'),(559,'de-DE','Suche nach {object} ...'),(560,'de-DE','Hauptjuriert'),(561,'de-DE','Punkte-Tendenz'),(562,'de-DE','Aktualisiere BenutzerInnen-Profil'),(563,'de-DE','Individuelle Konflikte'),(564,'de-DE','Aktualisiere Konflikt-Informationen'),(565,'de-DE','Lösche Konflikt'),(566,'de-DE','Dem System sind keine Konflikte bekannt.'),(567,'de-DE','Besuchte Debattiervereine'),(568,'de-DE','Verein zum Verlauf hinzufügen'),(569,'de-DE','nach wie vor aktiv'),(570,'de-DE','Aktualisiere Informationen zu besuchten Vereinen'),(571,'de-DE','Für {name} ein neues Passwort erzwingen'),(572,'de-DE','Abbrechen'),(573,'de-DE','Suche nach einem Turnier ...'),(574,'de-DE','Neues Passwort festlegen'),(575,'de-DE','BenutzerIn aktualisieren'),(576,'de-DE','BenutzerIn löschen'),(577,'de-DE','BenutzerIn erstellen'),(578,'de-DE','Keine zutreffende Bedingung'),(579,'de-DE','Jury bestand Überprüfung nicht. Alt: {old} / Neu: {new}'),(580,'de-DE','Kann {object} nicht speichern! Fehler: {message}'),(582,'de-DE','Keine Datei verfügbar'),(583,'de-DE','Keine passenden Einträge gefunden'),(584,'de-DE','Vielen Dank für deine Eingabe.'),(585,'de-DE','Fehler beim Speichern der Jury:'),(586,'de-DE','Jury gelöscht'),(587,'de-DE','Willkommen! Dies ist das erste Mal, dass du dich anmeldest. Bitte stelle sicher, dass all deine Informationen zutreffen.'),(588,'de-DE','Ein neuer Verein wurde gespeichert'),(589,'de-DE','Beim Entgegennehmen deiner vorherigen Eingabe ist ein Fehler aufgetreten. Bitte versuche es noch einmal.'),(590,'de-DE','BenutzerIn registriert! Willkommen, {user}!'),(591,'de-DE','Anmeldung fehlgeschlagen'),(592,'de-DE','Bitte überprüfe für weitere Anweisungen deine Emails.'),(593,'de-DE','Bitte entschuldige - für die angegebene Email-Adresse können wir kein Passwort zurücksetzen.<br>{message}'),(594,'de-DE','Das neue Passwort wurde gespeichert.'),(595,'de-DE','Neues Passwort festgelegt.'),(596,'de-DE','Fehler beim Speichern des neuen Passworts.'),(597,'de-DE','Verknüpfung mit Verein nicht gespeichert'),(598,'de-DE','BenutzerIn erfolgreich gespeichert'),(599,'de-DE','BenutzerIn nicht gespeichert!'),(600,'de-DE','Verbindung zu Verein nicht gespeichert!'),(601,'de-DE','BenutzerIn erfolgreich aktualisiert!'),(602,'de-DE','Bitte gib ein neues Passwort ein!'),(603,'de-DE','BenutzerIn gelöscht'),(604,'de-DE','Kann aufgrund von {error} nicht gelöscht werden.'),(605,'de-DE','Konnte nicht gelöscht werden, da bereits in Gebrauch.<br> {ex}'),(606,'de-DE','Checking Flags zurücksetzen'),(607,'de-DE','Es gab keinen Bedarf zurückzusetzen.'),(608,'de-DE','Bitte lege erst die breakenden JurorInnen fest - benutze hierfür das Sternensymbol in der Aktionsspalte.'),(609,'de-DE','Konnte Team nicht erstellen.'),(610,'de-DE','Fehler beim Speichern des Vereinsverhältnisses für {society}.'),(611,'de-DE','Fehler beim Speichern von Team {name}!'),(613,'de-DE','Kann Turnierverknüpfung nicht speichern.'),(614,'de-DE','Kann Frage nicht löschen.'),(615,'de-DE','Vereinsverknüpfung wurde erfolgreich erstellt'),(616,'de-DE','Verein konnte nicht gespeichert werden'),(617,'de-DE','Fehler beim wakeup'),(618,'de-DE','Vereinsinformationen aktualisiert'),(619,'de-DE','Das Tab ist veröffentlicht, das Turnier geschlossen. Auf zu einem Drink!'),(620,'de-DE','Kein/e HauptjurorIn im Panel gefunden - falscher Typ?'),(622,'de-DE','Kein gültiger Typ'),(623,'de-DE','{object} erfolgreich eingegeben'),(624,'de-DE','{object} erstellt'),(625,'de-DE','Individueller Konflikt'),(626,'de-DE','Individueller Konflikt konnte nicht gespeichert werden'),(627,'de-DE','{object} aktualisiert'),(628,'de-DE','{object} konnte nicht gespeichert werden'),(629,'de-DE','{object} gelöscht'),(630,'de-DE','{tournament} auf Tabbie2'),(631,'de-DE','{tournament} findet vom {start} bis zum {end} statt und wird ausgetragen von {host} in {country}.'),(632,'de-DE','Turnier erfolgreich erstellt'),(633,'de-DE','Turnier wurde erstellt doch die Energie-Konfiguration schluf fehl!'),(634,'de-DE','Kann Turnier nicht speichern!'),(635,'de-DE','DebReg Synchronisation erfolgreich'),(636,'de-DE','Räume getauscht'),(637,'de-DE','Fehler beim Wechseln'),(638,'de-DE','Neue Räume festgelegt'),(639,'de-DE','Fehler beim Festlegen eines neuen Raumes'),(640,'de-DE','Kann Runde nicht erstellen: Anzahl der Teams ist nicht durch 4 teilbar'),(641,'de-DE','Erfolgreich neu gesetzt in {secs}s'),(642,'de-DE','Energie in {secs}s um {diff} Punkte verbessert'),(643,'de-DE','JurorIn {n1} und {n2} getauscht'),(644,'de-DE','Konnte nicht tauschen, da: {a_panel}<br>und<br>{b_panel}'),(645,'de-DE','Zeige Runde {number}'),(646,'de-DE','Für diesen Raum konnten keine Debatten gefunden werden'),(647,'de-DE','Diese Spracheinstellung ist ungültig.'),(648,'de-DE','Team zu {status} aufgewertet'),(649,'de-DE','Spracheinstellungen gespeichert'),(650,'de-DE','Fehler beim Speichern der Spracheinstellungen'),(651,'de-DE','BenutzerIn nicht gefunden!'),(652,'de-DE','{object} erfolgreich hinzugefügt'),(653,'de-DE','Löschen erfolgreich'),(654,'de-DE','Die Syntax der Datei ist falsch! Erwartet werden 3 Spalten.'),(656,'de-DE','Fehler beim Speichern der Ergebnisse.<br>Bitte frage einen Laufzettel aus Papier an!'),(657,'de-DE','Ergebnis gespeichert. Auf zum Nächsten!'),(658,'de-DE','Debatte #{id} existiert nicht'),(659,'de-DE','Korrigiere die Teampunkte des Teams {team} von {old_points} auf {new_points}'),(660,'de-DE','Korrigiere die RednerInnenpunkte von RednerIn {pos} des Teams {team} von {old_points} auf {new_points}'),(661,'de-DE','Cache ist in Bestform. Keine Veränderungen nötig!'),(662,'de-DE','Kann Entscheidung zum Konflikt nicht speichern. {reason}'),(663,'de-DE','Nicht genügend Räume'),(664,'de-DE','Zu viele Räume'),(665,'de-DE','Maximiere Wiederholungen zum Verbessern der JurorInnenzuweisung'),(666,'de-DE','Strafwert für Teams und JurorInnen aus gleichen Vereinen'),(667,'de-DE','Beide JurorInnen sind im Konflikt'),(668,'de-DE','Team ist mit JurorIn im Konflikt.'),(669,'de-DE','JurorIn darf nicht hauptjurieren.'),(670,'de-DE','HauptjurorIn ist für die aktuelle Situation suboptimal.'),(671,'de-DE','JuroIn hat das Team bereits gesehen.'),(672,'de-DE','JurorIn hat bereits in dieser Konstellation juriert.'),(673,'de-DE','Die Jury hat eine dem Raum unangemessene Stärke.'),(674,'de-DE','Richards Geheimzutat'),(675,'de-DE','JurorIn {adju} und {team} in gleichem Verein.'),(676,'de-DE','Die JurorInnen {adju1} und {adju2} stehen manuell im Konflikt.'),(677,'de-DE','JurorIn {adju} und Team {team} stehen manuell im Konflikt.'),(678,'de-DE','JurorIn {adju} wurde als Nicht-HauptjurorIn markiert.'),(679,'de-DE','HauptjurorIn ist um {points} suboptimal.'),(680,'de-DE','Die JurorInnen {adju1} und {adju2} haben zuvor x{occ} gemeinsam juriert.'),(681,'de-DE','JurorIn {adju} hat das Team {team} zuvor x {occ} juriert.'),(682,'de-DE','Steilheits-Vergleich: {comparison_factor}, Differenz: {roomDifference}, Steilheits-Strafe: {steepnessPenalty}'),(686,'de-DE','Unbestimmt'),(687,'de-DE','Nordeuropa'),(688,'de-DE','Westeuropa'),(689,'de-DE','Südeuropa'),(690,'de-DE','Osteuropa'),(691,'de-DE','Zentralasien'),(692,'de-DE','Ostasien'),(693,'de-DE','Westasien'),(694,'de-DE','Südasien'),(695,'de-DE','Süd-Ostasien'),(696,'de-DE','Australien & Neuseeland'),(697,'de-DE','Mikronesien'),(698,'de-DE','Melanesien'),(699,'de-DE','Polynesien'),(700,'de-DE','Nordafrika'),(701,'de-DE','Westafrika'),(702,'de-DE','Zentralafrika'),(703,'de-DE','Ostafrika'),(704,'de-DE','Südafrika'),(705,'de-DE','Nordamerika'),(706,'de-DE','Mittelamerika'),(707,'de-DE','Karibik'),(708,'de-DE','Südamerika'),(709,'de-DE','Antarktis'),(710,'de-DE','Bestrafte/r JurorIn'),(711,'de-DE','Schlechte/r JurorIn'),(712,'de-DE','Ordentliche/r JurorIn'),(713,'de-DE','Durchschnittliche/r JurorIn'),(714,'de-DE','Durchschnittliche/r HauptjurorIn'),(715,'de-DE','Gute/r HauptjurorIn'),(716,'de-DE','Breakende/r HauptjurorIn'),(717,'de-DE','<b>Dieses Turnier hat noch keine Teams.</b><br>{add_button} oder {import_button}'),(718,'de-DE','Ein Team hinzufügen'),(719,'de-DE','Importiere sie als CSV-Datei'),(720,'de-DE','<b>Dieses Turnier hat noch keine Räume.</b><br>{add} oder {import}'),(721,'de-DE','Einen Raum hinzufügen'),(722,'de-DE','Importiere sie als CSV-Datei'),(723,'de-DE','<b>Diese Turnier hat keine Juroren.</b><br>{add_button} oder {import_button}'),(724,'de-DE','Importiere sie als CSV-Datei'),(725,'de-DE','Für diese Runde wurden bereits Ergebnisse eingetragen. Neusetzung nicht möglich!'),(726,'de-DE','Für diese Runde wurden bereits Ergebnisse eingetragen. Verbessern nicht möglich!'),(727,'de-DE','Feedback #{num}'),(728,'de-DE','BenutzerIn ID'),(729,'de-DE','Sprach-VerwalterIn'),(730,'de-DE','Sprach-VerwalterIn'),(731,'de-DE','Sprach-VerwalterIn erstellen'),(732,'de-DE','EFL-Ranking anzeigen'),(733,'de-DE','EinsteigerInnen-Ranking anzeigen'),(734,'de-DE','Englisch als gemeisterte Sprache'),(735,'de-DE','EIN'),(736,'de-DE','EinsteigerIn festlegen'),(737,'de-DE','ENL'),(738,'de-DE','Neue Sprache erstellen'),(739,'de-DE','Setzung als JSON exportieren'),(740,'de-DE','Das Team {name} kann nicht gelöscht werden, da es bereits in Gebrauch ist.'),(741,'de-DE','Der/die JurorIn {name} kann nicht gelöscht werden, da er/sie bereits verwendet wird.'),(742,'de-DE','Der Raum {name} kann nicht gelöscht werden, da er bereits verwendet wird.'),(743,'de-DE','Gib in 3-4 allgemeinen Stichworten an, um was es im Thema geht. Falls bereits passende Stichworte existieren, verwende bitte diese!'),(744,'de-DE','Fehler beim Speichern des benutzerdefinierten Attributs: {name}'),(745,'de-DE','Fehler beim Speichern des benutzerdefinierten Werts \'{key}\': {value}'),(746,'de-DE','BenutzerIn Attr ID'),(747,'de-DE','Turnier ID'),(748,'de-DE','Benötigt'),(749,'de-DE','Hilfe'),(750,'de-DE','Benutzerdefinierte Werte für {tournament}'),(751,'de-DE','Die Syntax der Datei passt nicht. Es werden mindestens 5 Spalten benötigt.'),(753,'de-DE','Jurierlehrling'),(754,'de-DE','Importiere {modelClass} #{number}'),(755,'de-DE','Veröffentliche bestätigte Setzung'),(756,'de-DE','Importiere Setzung aus JSON'),(757,'de-DE','Dies wird die aktuelle Setzung überschreiben! Alle Informationen gehen dabei verloren\r\n!'),(758,'de-DE','Runde neu setzen'),(759,'de-DE','Runde löschen'),(760,'de-DE','Bist du sicher, dass du die Runde LÖSCHEN willst? Alle Informationen gehen dabei verloren!'),(761,'de-DE','Die Runde ist bereits aktiv! Überschreiben mit der Eingabe ist nicht möglich.'),(762,'de-DE','Die hochgeladene Datei war leer. Bitte wähle eine andere Datei.'),(764,'de-DE','RednerInnen'),(765,'de-DE','Die Syntax der Datei ist falsch! Mindestens {min} Spalten werden benötigt; {num} vorhanden in Zeile {line}'),(766,'de-DE','Fragetext'),(767,'de-DE','Hilfstext'),(770,'de-DE','Konflikte von JurorInnen mit JurorInnen'),(771,'de-DE','Alle zulassen'),(772,'de-DE','Alle verweigern'),(773,'de-DE','Konflikte von Teams mit JurorInnen'),(774,'de-DE','Keine gültige Entscheidung'),(775,'de-DE','Importiere Wertung für {modelClass}'),(777,'de-DE','Öffentlich zugängliche URLs'),(778,'de-DE','Debatte nicht gefunden - falscher Typ?'),(783,'de-DE','Themenausgeglichenheit'),(784,'de-DE','Rundeninformation'),(785,'de-DE','Es gibt derzeit keine aktive Runde. Lade diese Seite später noch einmal neu.'),(787,'de-DE','Teilgedoppeltes Achtelfinale'),(788,'de-DE','Ersetze JurorIn {adjudicator} durch'),(789,'de-DE','Ersetze'),(790,'de-DE','Ansehen'),(791,'de-DE','Tausche Team {team} mit'),(792,'de-DE','Versuche Setzung erneut'),(793,'de-DE','AVG'),(794,'de-DE',NULL),(795,'de-DE',NULL),(796,'de-DE',NULL),(1,'es-CO','ID'),(2,'es-CO','Nombre'),(3,'es-CO','Importar {modelClass}'),(4,'es-CO','Importar'),(5,'es-CO','Sociedades de Debate'),(6,'es-CO','Actualizar'),(7,'es-CO','Eliminar'),(8,'es-CO','¿Estás seguro de que quieres eliminar este elemento?'),(9,'es-CO','Actualizar {modelClass}:'),(10,'es-CO','Fusionar las sociedades \'{society}\' en ...'),(11,'es-CO','Selecciones la Sociedad de Debate principal'),(12,'es-CO','Crear {modelClass}'),(13,'es-CO','Ver {modelClass}'),(14,'es-CO','Actualizar {modelClass}'),(15,'es-CO','Eliminar {modelClass}'),(16,'es-CO','Agregar nuevo elemento'),(17,'es-CO','Volver a cargar contenido'),(18,'es-CO','Importar archivo EXL'),(19,'es-CO','Crear'),(20,'es-CO','Idiomas'),(21,'es-CO','Crear idioma'),(22,'es-CO','Necesidades especiales'),(23,'es-CO','Etiquetas de la moción'),(24,'es-CO','Fusionar la etiqueta \'{tag}\' de la moción a'),(25,'es-CO','Seleccione la etiqueta principal'),(26,'es-CO','Buscar'),(27,'es-CO','Reiniciar'),(28,'es-CO','Crear etiqueta para la moción'),(29,'es-CO','API'),(30,'es-CO','Master'),(31,'es-CO','Mensajes'),(32,'es-CO','Crear mensaje'),(33,'es-CO','{count} Cambiar etiquetas'),(34,'es-CO','Error de archivo'),(35,'es-CO','Etiqueta de la moción'),(36,'es-CO','Ronda'),(37,'es-CO','Abreviación'),(38,'es-CO','Puntaje'),(39,'es-CO','Cámara alta de Gobierno'),(40,'es-CO','Cámara alta de Oposición'),(41,'es-CO','Cámara baja de Gobierno'),(42,'es-CO','Cámara baja de Oposición'),(43,'es-CO','Equipo'),(44,'es-CO','Activo'),(45,'es-CO','Torneo'),(46,'es-CO','Orador'),(47,'es-CO','Sociedad de Debate'),(48,'es-CO','Swing Team'),(49,'es-CO','Estatus por idioma'),(50,'es-CO','Todo normal'),(51,'es-CO','Fue remplazado por equipo swing'),(52,'es-CO','Orador {letra} no se presentó'),(53,'es-CO','Llave'),(54,'es-CO','Etiqueta'),(55,'es-CO','Valor'),(56,'es-CO','La posición del equipo no puede estar en blanco'),(57,'es-CO','Fuera de ronda'),(58,'es-CO','Usuario del Tabulador'),(59,'es-CO','Usuario'),(60,'es-CO','ENL Posición'),(61,'es-CO','ESL Posición'),(62,'es-CO','Resultados caché'),(63,'es-CO','Nombre Completo'),(64,'es-CO','Abreviación'),(65,'es-CO','Ciudad'),(66,'es-CO','País'),(67,'es-CO','Primer Gobierno'),(68,'es-CO','Primera Oposición'),(69,'es-CO','Segundo Gobierno'),(70,'es-CO','Segunda Oposición'),(71,'es-CO','Panel'),(72,'es-CO','Lugar'),(73,'es-CO','Retroalimentación\r\n CAG'),(74,'es-CO','Retroalimentación CAO'),(75,'es-CO','Retroalimentación CBG'),(76,'es-CO','Retroalimentación CBO'),(77,'es-CO','Hora'),(78,'es-CO','Moción'),(79,'es-CO','Idioma'),(80,'es-CO','Fecha'),(81,'es-CO','Diapositiva de información'),(82,'es-CO','Enlace'),(83,'es-CO','Por Usuario'),(84,'es-CO','Traducción'),(85,'es-CO','Adjudicador'),(86,'es-CO','Respuesta'),(87,'es-CO','Retroalimentación'),(88,'es-CO','Pregunta'),(89,'es-CO','Creado'),(90,'es-CO','En marcha'),(91,'es-CO','Cerrado'),(92,'es-CO','Oculto'),(93,'es-CO','Organizado por'),(94,'es-CO','Nombre del torneo'),(95,'es-CO','Fecha de inicio'),(96,'es-CO','Fecha de finalización'),(97,'es-CO','Zona horaria'),(98,'es-CO','Logo'),(99,'es-CO','URL Adjunta'),(100,'es-CO','Algoritmo de tabulación'),(101,'es-CO','Número estimado de rondas'),(102,'es-CO','Mostrar ranking ESL'),(103,'es-CO','¿Hay una gran final?'),(104,'es-CO','¿Hay semifinales?'),(105,'es-CO','¿Hay cuartos de final?'),(106,'es-CO','¿Hay octavos de final?'),(107,'es-CO','Símbolo de acceso'),(108,'es-CO','Insignia del participante'),(109,'es-CO','Alfa 2'),(110,'es-CO','Alfa 3'),(111,'es-CO','Región'),(112,'es-CO','Código de lenguaje'),(113,'es-CO','Covertura'),(114,'es-CO','Última actualización'),(115,'es-CO','Fuerza'),(116,'es-CO','Puede ser juez principal'),(117,'es-CO','está observando'),(118,'es-CO','Sin clasificar'),(121,'es-CO','Puede juzgar'),(122,'es-CO','Decente'),(124,'es-CO','Gran potencial'),(125,'es-CO','Juez principal'),(126,'es-CO','Bueno'),(127,'es-CO','Breaking'),(128,'es-CO','Jefe de adjudicación'),(129,'es-CO','Empezando'),(130,'es-CO','Finalizando'),(131,'es-CO','Puntos de orador'),(132,'es-CO','Esta dirección de correo ya está en uso'),(133,'es-CO','Debatiente'),(134,'es-CO','Clave de autenticación'),(135,'es-CO','Contraseña'),(136,'es-CO','Restablecer contraseña'),(137,'es-CO','Correo electrónico'),(138,'es-CO','Rol de la cuenta'),(139,'es-CO','Estado de la cuenta'),(140,'es-CO','Último cambio'),(141,'es-CO','Nombre'),(142,'es-CO','Apellido'),(143,'es-CO','Imagen de perfil'),(144,'es-CO','Lugar de ubicación'),(145,'es-CO','Tabulador'),(146,'es-CO','Administrador'),(147,'es-CO','Eliminado'),(148,'es-CO','No revelar'),(149,'es-CO','Femenino'),(150,'es-CO','Masculino'),(151,'es-CO','Otro'),(152,'es-CO','mezclado'),(153,'es-CO','Sin definir'),(154,'es-CO','Entrevista necesaria'),(155,'es-CO','EPL'),(157,'es-CO','ESL'),(158,'es-CO','Español como segunda lengua'),(159,'es-CO','ELE'),(160,'es-CO','Español como lengua extranjera'),(161,'es-CO','Sin asignar'),(162,'es-CO','Error guardando Relación con la sociedad para {user_name}'),(163,'es-CO','Error guardando usuario {user_name}'),(164,'es-CO','{tournament_name}: Cuenta de usuario para  {user_name}'),(165,'es-CO','Esta dirección no está permitida'),(166,'es-CO','Publicado'),(167,'es-CO','Visualizado'),(168,'es-CO','Iniciado'),(169,'es-CO','Juzgando'),(170,'es-CO','Finalizado'),(171,'es-CO','Principal'),(172,'es-CO','Principiante'),(173,'es-CO','Final'),(174,'es-CO','Semifinal'),(175,'es-CO','Cuartos de final'),(176,'es-CO','Octavos de final'),(177,'es-CO','Ronda #{num}'),(178,'es-CO','En ronda'),(179,'es-CO','Energía'),(180,'es-CO','Diapositiva de información'),(181,'es-CO','Empieza el tiempo de preparación'),(182,'es-CO','Última temperatura'),(183,'es-CO','ms a calcular'),(184,'es-CO','No hay suficientes equipos para completar la sala - (active: {teams_count})'),(185,'es-CO','Al menos dos jueces son necesarios - (active: {count_adju})'),(186,'es-CO','El número de equipos activos debe dividirse entre 4 ;) - (active: {count_teams})'),(187,'es-CO','No hay suficientes salas activas (active: {active_rooms} required: {required})'),(188,'es-CO','No hay suficientes jueces (active: {active} min-required: {required})'),(189,'es-CO','No hay jueces libres suficientes con esta configuración preseleccionada de panel (fillable rooms: {active} min-required: {required})'),(190,'es-CO','No se puede guardar el panel! Error {message}'),(191,'es-CO','No se puede guardar el debate! Error {message}'),(192,'es-CO','No se puede guardar el debate! Errores <br>{errors}'),(193,'es-CO','No se encuentra el debate #{num} para actualizar'),(195,'es-CO','Tipo'),(196,'es-CO','Parámetro si es necesario'),(197,'es-CO','Aplica al equipo -> Juez principal'),(198,'es-CO','Aplica al Juez principal -> Juez panel'),(199,'es-CO','Aplicar a juez panel -> Juez principal'),(200,'es-CO','No es bueno'),(201,'es-CO','Muy bueno'),(202,'es-CO','Excelente'),(203,'es-CO','Califique (De 1 a 5) en el campo'),(204,'es-CO','Campo para texto corto'),(205,'es-CO','Campo para texto largo'),(206,'es-CO','Número de campo'),(207,'es-CO','Casilla de verificación de campo'),(208,'es-CO','Debate'),(209,'es-CO','Retroalimentación a ID'),(210,'es-CO','Calificación del juez de ID'),(211,'es-CO','Calificación del juez a ID'),(212,'es-CO','Grupo'),(213,'es-CO','Sala activa'),(214,'es-CO','Juez panel'),(215,'es-CO','Usado'),(216,'es-CO','Es el panel predeterminado'),(217,'es-CO','Panel #{id} tiene {amount} jueces principales'),(218,'es-CO','Categoría'),(219,'es-CO','Mensaje'),(220,'es-CO','Función'),(221,'es-CO','Mociones usadas'),(222,'es-CO','Preguntas'),(223,'es-CO','En conflicto con'),(224,'es-CO','Razón'),(225,'es-CO','Equipo en conflicto'),(226,'es-CO','Juez en conflicto'),(227,'es-CO','No se encuentra el tipo'),(228,'es-CO','CAG Orador A'),(229,'es-CO','CAG Orador B'),(230,'es-CO','CAG Puesto'),(231,'es-CO','CAO Orador A'),(232,'es-CO','CAO Orador B'),(233,'es-CO','CAO Puesto'),(234,'es-CO','CBG Orador A'),(235,'es-CO','CBG Orador B'),(236,'es-CO','CBG Puesto'),(237,'es-CO','CBO Orador A'),(238,'es-CO','CBO Orador B'),(239,'es-CO','CBO Puesto'),(240,'es-CO','Revisado'),(241,'es-CO','Ingresado por usuario ID'),(242,'es-CO','Existe empate'),(243,'es-CO','Ironman by'),(244,'es-CO','Jefe de Adjudicación'),(245,'es-CO','El espacio para restablecer la contraseña no puede estár en blanco'),(246,'es-CO','Error en el restablecimiento de contraseña'),(247,'es-CO','Archivo separado por comas ( , )'),(248,'es-CO','Archivo separado por punto y coma ( ; )'),(249,'es-CO','Archivo separado por espacios tabulados ( ->| )'),(250,'es-CO','Archivo EXL'),(251,'es-CO','Delimitador'),(252,'es-CO','Marcar como datos de prueba (no enviar e-mail)'),(253,'es-CO','Nombre de usuario'),(254,'es-CO','Imagen de perfil'),(255,'es-CO','Sociedad de debate actual'),(256,'es-CO','Con qué género se siente más identificado'),(257,'es-CO','La URL no está permitida'),(258,'es-CO','{adju} Registrado'),(259,'es-CO','{adju} ya está registrado!'),(260,'es-CO','{id} número no valido! no es juez!'),(261,'es-CO','{speaker} Registrado!'),(262,'es-CO','{speaker} ya se encuentra registrado!'),(263,'es-CO','{id} número no valido! No es un equipo!'),(264,'es-CO','No es una entrada válida'),(265,'es-CO','Código de verificación'),(266,'es-CO','RegDeb'),(267,'es-CO','Contraseña reestablecida por {user}'),(268,'es-CO','No se encuentra el usuario con este E-mail'),(269,'es-CO','Agregar {object}'),(270,'es-CO','Crear sociedad de debate'),(271,'es-CO','Hey tranquilo! Estás ingresando una sociedad de debate desconocida.'),(272,'es-CO','Antes de que podamos vincularte, por favor completa la información sobre tu sociedad de debate'),(273,'es-CO','Buscar por país'),(274,'es-CO','Agregar nueva sociedad de debate'),(275,'es-CO','Buscar por sociedad de debate...'),(276,'es-CO','Ingrese la fecha de inicio...'),(277,'es-CO','Ingrese la fecha de finalización (si aplica)...'),(278,'es-CO','Idioma oficial'),(279,'es-CO','Oficial'),(280,'es-CO','Alguno \r\n{object} ...'),(281,'es-CO','Revisión del estado de idiomas'),(282,'es-CO','Estado'),(283,'es-CO','Requiere entrevista'),(284,'es-CO','Grupo ENL'),(285,'es-CO','Grupo ESL'),(286,'es-CO','Idioma oficial'),(288,'es-CO','Agregar'),(289,'es-CO','Buscar usuario...'),(290,'es-CO','Comprobar'),(291,'es-CO','Enviar'),(292,'es-CO','Generar ballots'),(293,'es-CO','Únicamente hacer por usuario...'),(294,'es-CO','Imprimir Ballots'),(295,'es-CO','Generar código de barras'),(296,'es-CO','Buscar usuario.. o dejar en blanco'),(297,'es-CO','Imprimir código de barras'),(298,'es-CO','Equipos'),(299,'es-CO','Nombre del equipo'),(300,'es-CO','Orador A'),(301,'es-CO','Orador B'),(302,'es-CO','Así queda'),(303,'es-CO','Moción:'),(304,'es-CO','Panel de jueces:'),(305,'es-CO','Palanca activa'),(307,'es-CO','Buscar usuario...'),(308,'es-CO','Torneos'),(309,'es-CO','Detalle general'),(310,'es-CO','Mociones'),(311,'es-CO','Tabla de equipos'),(312,'es-CO','Tabla de oradores'),(313,'es-CO','Fuera de rondas'),(314,'es-CO','Jueces que pasan el break'),(315,'es-CO','Así queda'),(316,'es-CO','Registro torneos de debate'),(317,'es-CO','Mostrar antiguos torneos'),(318,'es-CO','Jueces'),(319,'es-CO','Resultado'),(320,'es-CO','junto con {teammate}'),(321,'es-CO','as ironman'),(322,'es-CO','Estás registrado como equipo <br> \'{team}\' {with} por {society}'),(323,'es-CO','Usted está registrado como juez por {society}'),(325,'es-CO','Registro de información'),(326,'es-CO','Ingresar información'),(327,'es-CO','Información de la ronda #{num}'),(328,'es-CO','Estás <b>{pos}</b> en la sala <b>{room}</b>.'),(329,'es-CO','La ronda comienza a las: <b>{time}</b>'),(330,'es-CO','Diapositiva de Información'),(331,'es-CO','Equipos Ronda #{num}'),(332,'es-CO','Mi súper increíble IV Torneo... ej: Vienna IV'),(333,'es-CO','Seleccione los organizadores'),(334,'es-CO','Ingrese fecha y hora de inicio...'),(335,'es-CO','Ingrese fecha y hora de finalización...'),(336,'es-CO','Jefes de adjudicación'),(337,'es-CO','Elija sus JAs...'),(338,'es-CO','Elija su Tabulador'),(339,'es-CO','Archivo del torneo'),(340,'es-CO','Se produjo un error mientras el servidor Web procesaba su solicitud.'),(341,'es-CO','Por favor contáctenos si cree que es un problema del servidor. Gracias'),(342,'es-CO','Recuperar contraseña'),(343,'es-CO','Por favor escoja su nueva contraseña'),(344,'es-CO','Guardar'),(345,'es-CO','Registrarse'),(346,'es-CO','Por favor complete la siguiente información para registrarse:'),(347,'es-CO','La mayoría de los algoritmos de asignación están en el sistema, pero tenga presente la diversidad del panel de jueces. Para que el sistema funcione completamente, le pedimos que escoja una opción de esta lista. Somos conscientes que no todas las preferencias personales pueden tenerse en cuenta en nuestras opciones. Si cree que ninguna de las opciones es útil para usted, por favor escoja <Not Revealing>. Esta opción nunca mostrará los resultados a los usuarios y se usa únicamente para calcular.'),(348,'es-CO','Ingresar'),(349,'es-CO','Por favor ingrese la siguiente información para iniciar sesión:'),(350,'es-CO','Si olvidó su contraseña puede {resetlt}'),(351,'es-CO','Reestablecer'),(352,'es-CO','Solicitar nueva contraseña'),(353,'es-CO','Por favor ingrese su e-mail. Un enlace será enviado allí para recuperar su contraseña.'),(354,'es-CO','Enviar'),(355,'es-CO','Posiciones EXL'),(356,'es-CO','Jueces EXL'),(357,'es-CO','Equipos EXL'),(358,'es-CO','Crear'),(359,'es-CO','Posiciones de muestra EXL'),(360,'es-CO','Equipo de muestra EXL'),(361,'es-CO','Muestra Jueces EXL'),(362,'es-CO','Torneo BP Actual {count, plural, =0{Tournament} =1{Tournament} other{Tournaments}}'),(363,'es-CO','Bienvenido a {appName}!'),(364,'es-CO','Ver torneos'),(365,'es-CO','Crear torneo'),(366,'es-CO','Antes de registrarse por favor complete la información acerca de su sociedad de debate:'),(367,'es-CO','Contacto'),(368,'es-CO','Preseleccionar panel de jueces #'),(369,'es-CO','Panel de jueces'),(370,'es-CO','Calificación promedio de panel de jueces'),(371,'es-CO','Crear panel de jueces'),(372,'es-CO','Preseleccionar jueces para la próxima ronda'),(373,'es-CO','Agregar {object}\r\n...'),(374,'es-CO','Lugar'),(375,'es-CO','Puntos de Equipo'),(376,'es-CO','#{number}'),(377,'es-CO','No hay jueces que pasen el break definidos'),(378,'es-CO','Distribución de puntos de orador'),(379,'es-CO','Corre'),(380,'es-CO','Cámara Alta de Gobierno'),(381,'es-CO','Cámara Alta de Oposición'),(382,'es-CO','Cámara Baja de Gobierno'),(383,'es-CO','Cámara Baja de Oposición'),(384,'es-CO','Mostrar diapositiva de información'),(385,'es-CO','Mostrar moción'),(386,'es-CO','Usuario desaparecido'),(387,'es-CO','Marcar desaparecido como inactivo'),(388,'es-CO','Resultados'),(389,'es-CO','Corre la información de al ronda #{number}'),(390,'es-CO','Auto actualización <i id=\'pjax-status\' class=\'\'></i>'),(391,'es-CO','Actualizar enfrentamiento individual'),(392,'es-CO','Enfrentamiento individual'),(393,'es-CO','En el sistema aún no están todos los debatientes :)'),(394,'es-CO','Crear impedimento'),(395,'es-CO','Actualizar impedimento'),(396,'es-CO','Configuración de energía'),(397,'es-CO','Actualizar valores de energía'),(398,'es-CO','Ronda #{number}'),(399,'es-CO','Rondas'),(400,'es-CO','Acciones'),(401,'es-CO','Publicar la tabulación'),(402,'es-CO','Volver intentar generar ronda'),(403,'es-CO','Actualizar ronda'),(404,'es-CO','Toggle desplegable'),(405,'es-CO','Continúa mejorando por'),(407,'es-CO','Está seguro que quiere volver a sortear la ronda? Perderá toda la información!'),(408,'es-CO','Imprimir Ballots'),(411,'es-CO','Estado de la ronda'),(412,'es-CO','Puntaje promedio'),(413,'es-CO','Tiempo de preparación'),(414,'es-CO','Paleta de color'),(415,'es-CO','Género'),(416,'es-CO','Regiones'),(417,'es-CO','Puntos'),(418,'es-CO','Cargando ...'),(419,'es-CO','Ver retroalimentación'),(420,'es-CO','Ver usuario'),(421,'es-CO','Cambiar posición con {venue}'),(422,'es-CO','Elija posición ...'),(423,'es-CO','Actualizar {modelClass} #{número}'),(424,'es-CO','Nivel de puntaje'),(425,'es-CO','Elegir equipo ...'),(426,'es-CO','Elegir idioma ...'),(427,'es-CO','Elegir juez ...'),(428,'es-CO','Cambiar jueces ...'),(429,'es-CO','Cambiar este juez ...'),(430,'es-CO','con'),(431,'es-CO','con este ...'),(432,'es-CO','Buscar moción por etiqueta ...'),(433,'es-CO','Puesto'),(434,'es-CO','Total'),(435,'es-CO','Debate ID'),(436,'es-CO','Sala'),(437,'es-CO','Fuera de ronda'),(438,'es-CO','Archivo de moción'),(439,'es-CO','Moción a terceros'),(440,'es-CO','Su sensacional IV'),(441,'es-CO','Ingrese fecha ...'),(442,'es-CO','Ronda #1 o Final'),(443,'es-CO','EC ...'),(444,'es-CO','http://dar.creditos.donde.los.creditos.se.necesitan.com'),(445,'es-CO','Ingresar {modelClass} Manualmente'),(446,'es-CO','Opciones'),(447,'es-CO','Continuar'),(448,'es-CO','No hay resultados aún!'),(449,'es-CO','Resultados en la sala: {venue}'),(450,'es-CO','Resultados por posición {venue}'),(451,'es-CO','Vista de la lista'),(452,'es-CO','Resultados por {label}'),(453,'es-CO','Cambiar a vista de las posiciones'),(454,'es-CO','Puntaje del Swing Team'),(455,'es-CO','Ver detalles de los resultados'),(456,'es-CO','Resultado correcto'),(457,'es-CO','Ver posiciones'),(458,'es-CO','Cambiar a vista de la lista'),(459,'es-CO','Confirmar datos de {venue}'),(460,'es-CO','Comenzar de nuevo'),(461,'es-CO','Ronda {number}'),(462,'es-CO','Gracias'),(463,'es-CO','Gracias!'),(464,'es-CO','Resultados exitosamente guardados'),(465,'es-CO','Boooonus por velocidad!'),(466,'es-CO','Apúrate ¡vamos! ¡vamos!'),(467,'es-CO','¡Ya está!¡El último!'),(468,'es-CO','Usted es <b>#{place}</b> de {max}'),(469,'es-CO','Ingresar retroalimentación'),(470,'es-CO','Volver al torneo'),(471,'es-CO','Retroalimentaciones'),(472,'es-CO','Objetivo del juez'),(473,'es-CO','Nombre del juez'),(474,'es-CO','Retroalimentación del juez'),(475,'es-CO','Enviar retroalimentación'),(476,'es-CO','{tournament} - Idioma oficial'),(477,'es-CO','Revisar estado del idioma'),(478,'es-CO','desarrollado con tecnología alien secreta'),(479,'es-CO','Reporte error'),(480,'es-CO','{tournament} - Director'),(481,'es-CO','Lista de posiciones'),(482,'es-CO','Crear posición'),(483,'es-CO','Importar posición'),(484,'es-CO','Lista de equipos'),(485,'es-CO','Crear equipo'),(486,'es-CO','Importar Equipo'),(487,'es-CO','Puntuar equipo'),(488,'es-CO','Lista de Jueces'),(489,'es-CO','Crear Juez'),(490,'es-CO','Importar Juez'),(491,'es-CO','Ver panel de jueces preseleccionado'),(492,'es-CO','Crear panel de jueces preseleccionado'),(493,'es-CO','Puntuar adjudicador'),(494,'es-CO','Actualizar torneo'),(495,'es-CO','Mostrar Tabla de equipos'),(496,'es-CO','Mostrar tabla de oradores'),(497,'es-CO','Mostrar fuera de ronda'),(498,'es-CO','Al publicar la tabulación el torneo se cerrará y archivará! Seguro que desea continuar?'),(499,'es-CO','Usuarios desaparecidos'),(500,'es-CO','Forma de confirmación'),(501,'es-CO','Imprimir Badgets'),(502,'es-CO','Reiniciar confirmación'),(503,'es-CO','Está seguro que desea reiniciar la confirmación?'),(504,'es-CO','Sincronizar con DebReg'),(505,'es-CO','Ir a Tabbie 1'),(506,'es-CO','Mucho cuidado pequeño padawan!'),(507,'es-CO','Lista de Rondas'),(508,'es-CO','Crear ronda'),(509,'es-CO','Opciones de energía'),(510,'es-CO','Lista de resultados'),(511,'es-CO','Ingrese ballot'),(512,'es-CO','Caché correcta'),(513,'es-CO','Preguntas de configuración'),(514,'es-CO','Cada retroalimentación'),(515,'es-CO','Retroalimentacion de jueces'),(516,'es-CO','Acerca de'),(517,'es-CO','Cómo se hace'),(518,'es-CO','Usuarios'),(519,'es-CO','Registro'),(520,'es-CO','Perfil de {user}'),(521,'es-CO','Historial de {user}'),(522,'es-CO','Cerrar sesión'),(524,'es-CO','Actualizar {label}'),(525,'es-CO','Siguiente paso'),(526,'es-CO','Sala {number}'),(527,'es-CO','Posiciones'),(529,'es-CO','Puntuaciones'),(530,'es-CO','Importar puntuaciones'),(531,'es-CO','Aceptar'),(532,'es-CO','Rechazar'),(533,'es-CO','Buscar equipo...'),(534,'es-CO','Buscar juez ...'),(535,'es-CO','Puntuar jueces'),(536,'es-CO','Crear adicional {modelClass}'),(537,'es-CO','Actualizar equipo'),(538,'es-CO','Eliminar equipo'),(539,'es-CO','Buscar por juez ...'),(540,'es-CO','Buscar un juez para ...'),(541,'es-CO','Puntuar equipo con juez'),(542,'es-CO','Actualizar equipo'),(543,'es-CO','Eliminar equipo'),(544,'es-CO','{modelClass} Historial'),(545,'es-CO','Historial'),(546,'es-CO','Revisión de equipos'),(547,'es-CO','EPL Posición'),(548,'es-CO','Puntos de orador por equipo'),(549,'es-CO','No hay tabulación disponible para publicar por el momento'),(550,'es-CO','Puede ser juez principal'),(551,'es-CO','No debe ser juez principal'),(552,'es-CO','Pasa el Break'),(553,'es-CO','No pasa el break'),(554,'es-CO','Observador'),(555,'es-CO','No observador'),(556,'es-CO','Ver palanca'),(557,'es-CO','Break palanca'),(558,'es-CO','Reiniciar bandera de observación'),(559,'es-CO','Buscar {object} ...'),(560,'es-CO','Juez principal'),(561,'es-CO','Indicador'),(562,'es-CO','Actualziar perfil de usuario'),(563,'es-CO','Enfrentamiento individual'),(564,'es-CO','Actualizar información de enfrentamiento'),(565,'es-CO','Eliminar enfrentamiento'),(566,'es-CO','No se conoce enfrentamiento en el sistema.'),(567,'es-CO','Historial de la sociedad de debate'),(568,'es-CO','Agregar nueva sociedad de debate al historial'),(569,'es-CO','Continúa activo'),(570,'es-CO','Actualizar información de sociedad de debate'),(571,'es-CO','Forzar nueva contraseña para {name}'),(572,'es-CO','Cancelar'),(573,'es-CO','Buscar torneo...'),(574,'es-CO','Elegir nueva contraseña'),(575,'es-CO','Actualizar usuario'),(576,'es-CO','Eliminar usuario'),(577,'es-CO','Crear usuario'),(578,'es-CO','No coincide condición'),(579,'es-CO','No pasó la verificación de antiguedad del panel:  {old} / new: {new}'),(580,'es-CO','No se pudo guardar {object}! Error: {message}'),(582,'es-CO','No hay archivo disponible'),(583,'es-CO','No coincide busqueda de historial'),(584,'es-CO','Gracias por su registro'),(585,'es-CO','Error guardando el panel de jueces:'),(586,'es-CO','Panel de jueces eliminado'),(587,'es-CO','Bienvenido! Esta es la primera vez que ingresas, por favor confirma que tu información sea correcta'),(588,'es-CO','La nueva sociedad de debate ha sido guardada.'),(589,'es-CO','Ha ocurrido un error recibiendo tu ingreso anterior. Por favor ingréselo nuevamente'),(590,'es-CO','Usuario registrado! Bienvenido {user}'),(591,'es-CO','Falló inicio de sesión'),(592,'es-CO','Revisa tu e-mail para más instrucciones'),(593,'es-CO','Lo sentimos, no podemos recuperar la contraseña del e-mail ingresado <br>{message}'),(594,'es-CO','Nueva contraseña guardada'),(595,'es-CO','Elegir nueva contraseña'),(596,'es-CO','Error guardando nueva contraseña'),(597,'es-CO','Conexión con sociedad de debate no guardada'),(598,'es-CO','Usuario exitosamente guardado!'),(599,'es-CO','Usuario no guardado!'),(600,'es-CO','Conexión con sociedad de debate no guardada!'),(601,'es-CO','Usuario actualizado exitosamente!'),(602,'es-CO','Por favor ingrese nueva contraseña!'),(603,'es-CO','Usuario eliminado'),(604,'es-CO','No se puede eliminar debido a {error}'),(605,'es-CO','No se puede eliminar porque actualmente está siendo usado. <br> {ex}'),(606,'es-CO','Comprobando reinicio de banderas'),(607,'es-CO','No hay necesidad de reiniciar'),(608,'es-CO','Por favor elija los jueces del break primero - Use el ícono de inicio en la columna de acciones.'),(609,'es-CO','No se puede crear equipo.'),(610,'es-CO','Error guardando relación con la sociedad de debate {society}'),(611,'es-CO','Error guardando equipo {name}!'),(613,'es-CO','No se puede guardar conexión con el torneo'),(614,'es-CO','No se puede eliminar la pregunta'),(615,'es-CO','Relación con la sociedad de debate exitosamente creada'),(616,'es-CO','No se puede guardar la sociedad de debate'),(617,'es-CO','Error en la activación'),(618,'es-CO','Información de la sociedad de debate actualizada'),(619,'es-CO','Tabulación publicada y torneo cerrado. ¡Vamos por una cerveza!'),(620,'es-CO','Juez principal del panel de jueces no encontrado - Escribiste mal?'),(622,'es-CO','Invalido'),(623,'es-CO','{object} enviado exitosamente'),(624,'es-CO','{object} creado'),(625,'es-CO','Enfrentamiento individual'),(626,'es-CO','No se pudo guardar enfrentamiento individual'),(627,'es-CO','{object} actualizado'),(628,'es-CO','No se puede guardar {object}'),(629,'es-CO','{object} eliminado'),(630,'es-CO','{tournament} en Tabbie2'),(631,'es-CO','{tournament} tendrá lugar del {start} al {end} organizado por {host} en {country}'),(632,'es-CO','Torneo creado exitosamente'),(633,'es-CO','Se creo el torneo pero la configuración de la conexión falló!'),(634,'es-CO','No se puede guardar el torneo!'),(635,'es-CO','Sincronización exitosa con DebReg'),(636,'es-CO','Posiciones cambiadas'),(637,'es-CO','Error mientras se hacía la combinación'),(638,'es-CO','Nuevas posicioes seleccionadas'),(639,'es-CO','Error mientras se configuraban las nuevas posiciones'),(640,'es-CO','No se puede crear la ronda: La cantidad de equipos no se puede dividir en 4'),(641,'es-CO','Exitosamente reasignado in {sec}s'),(642,'es-CO','Mejorar la energía por {diff} puntos en {secs}'),(643,'es-CO','Juez {n1} y {n2} se cambiaron'),(644,'es-CO','No se puede cambiar debido a quel {a_panel}<br>y<br>{b_panel}'),(645,'es-CO','Mostrar ronda {number}'),(646,'es-CO','No se encontraron debates en esta ronda'),(647,'es-CO','El idioma no es una opción valida en parámetros'),(648,'es-CO','Equipo actualizado a {status}'),(649,'es-CO','Configuración de idioma guardada'),(650,'es-CO','Error guardando la configuración de idioma'),(651,'es-CO','No se encuentra usuario!'),(652,'es-CO','{object} agregado exitosamente'),(653,'es-CO','Eliminado exitosamente'),(654,'es-CO','Error en el formato! Se requieren 3 columnas'),(656,'es-CO','Error guardando resultados. <br> Por favor solicite la ballot!'),(657,'es-CO','Se guardaron los resultados! Siguiente!'),(658,'es-CO','Debate #{id} no existe'),(659,'es-CO','Números correctos de equipo para {team} desde {old_points} hasta {new_points}'),(660,'es-CO','Confirmado puntaje de orador {pos} del equipo {team} desde {old_points} hasta {new_points}'),(661,'es-CO','Caché en perfecto estado. No se necesitan cambios'),(662,'es-CO','No se puede guardar el enfrentamiento {reason}'),(663,'es-CO','No hay suficientes posiciones'),(664,'es-CO','Demasiadas posiciones'),(665,'es-CO','Número máximo de interacciones para mejorar la asignación de jueces'),(666,'es-CO','Equipo y juez en la misma penalidad'),(667,'es-CO','Ambos adjudicadores en conflicto'),(668,'es-CO','Equipo y adjudicadores en conflicto'),(669,'es-CO','Juez no está habilitado para ser juez principal'),(670,'es-CO','El juez no es la mejor opción en la situación actual'),(671,'es-CO','El juez ya ha visto al equipo'),(672,'es-CO','El juez ya ha juzgado en esta combinación'),(673,'es-CO','El panel no está bien puntuado para la sala'),(674,'es-CO','El ingrediente especial de Richard'),(675,'es-CO','Juez {adju} y {team} en la misma sociedad de debate.'),(676,'es-CO','Juez {adju1} y {adju2} enfrentados manualmente'),(677,'es-CO','Juez {adju} y equipo {team} se enfrentan manualmente'),(678,'es-CO','Juez {adju} fue etiquetado como no principal.'),(679,'es-CO','El equipo de jueces no es el mejor por puntaje {points}.'),(680,'es-CO','Juez {adju1} y {adju2} han juzgados juntos x {occ} antes'),(681,'es-CO','Juez {adju} ya juzgó el equipo {team} x {occ} antes.'),(682,'es-CO','Pendiente comparación: {comparison_factor}, Diferencia: {roomDifference}, Pendiente penalidad: {steepnessPenalty}'),(686,'es-CO','Sin definir'),(687,'es-CO','Norte de Europa'),(688,'es-CO','Oeste de Europa'),(689,'es-CO','Sur de Europa'),(690,'es-CO','Europa del Este'),(691,'es-CO','Asia central'),(692,'es-CO','Este de Asia'),(693,'es-CO','Oeste de Asia'),(694,'es-CO','Sur de Asia'),(695,'es-CO','Suseste de Asia'),(696,'es-CO','Australia y Nueva Zelanda'),(697,'es-CO','Micronesia'),(698,'es-CO','Melanesia'),(699,'es-CO','Polinesia'),(700,'es-CO','Norte de África'),(701,'es-CO','Oeste de África'),(702,'es-CO','Africa Central'),(703,'es-CO','Este de África'),(704,'es-CO','Sur de África'),(705,'es-CO','Norte América'),(706,'es-CO','Centro América'),(707,'es-CO','Caribe'),(708,'es-CO','Sur América'),(709,'es-CO','Antártida'),(710,'es-CO','Juez penalizado'),(711,'es-CO','Mal juez'),(712,'es-CO','Juez decente'),(713,'es-CO','Juez promedio'),(714,'es-CO','Juez principal promedio'),(715,'es-CO','Buen juez principal'),(716,'es-CO','Jueces principales del break'),(717,'es-CO','<b> Este torneo no tiene equipos aún. </b><br>{add_button} or {import_button}'),(718,'es-CO','Agregar equipo'),(719,'es-CO','Importarlos vía archivo EXL.'),(720,'es-CO','Este torneo no tiene lugares aún. <br>{add} or {import}'),(721,'es-CO','Agregar lugar'),(722,'es-CO','Importarlos vía archivo EXL'),(723,'es-CO','<b> Este torneo no tiene jueces aún.</b><br>{add_button} ó {import_button}.'),(724,'es-CO','Importarlos via archivo EXL'),(725,'es-CO','Ya se ingresaron los resultados para esta ronda. No se pueden cambiar!'),(726,'es-CO','Ya se ingresaron los resultados para esta ronda. No se pueden mejorar!'),(727,'es-CO','Retroalimentación #{num}'),(728,'es-CO','ID del usuario'),(729,'es-CO','Encargado del idioma'),(730,'es-CO','Encargados del idioma'),(731,'es-CO','Crear encargado del idioma'),(732,'es-CO','Mostrar ELE ranking'),(733,'es-CO','Mostrar ranking de novatos'),(734,'es-CO','Inglés como idioma de dominio'),(735,'es-CO','NOV'),(736,'es-CO','Seleccionar novato'),(737,'es-CO','ENL'),(738,'es-CO','Agregar nuevo idioma'),(739,'es-CO',NULL),(740,'es-CO',NULL),(741,'es-CO',NULL),(742,'es-CO',NULL),(743,'es-CO',NULL),(744,'es-CO',NULL),(745,'es-CO',NULL),(746,'es-CO',NULL),(747,'es-CO',NULL),(748,'es-CO',NULL),(749,'es-CO',NULL),(750,'es-CO',NULL),(751,'es-CO',NULL),(753,'es-CO',NULL),(754,'es-CO',NULL),(755,'es-CO',NULL),(756,'es-CO',NULL),(757,'es-CO',NULL),(758,'es-CO',NULL),(759,'es-CO',NULL),(760,'es-CO',NULL),(761,'es-CO',NULL),(762,'es-CO',NULL),(764,'es-CO',NULL),(765,'es-CO',NULL),(766,'es-CO',NULL),(767,'es-CO',NULL),(770,'es-CO',NULL),(771,'es-CO',NULL),(772,'es-CO',NULL),(773,'es-CO',NULL),(774,'es-CO',NULL),(775,'es-CO',NULL),(777,'es-CO',NULL),(778,'es-CO',NULL),(783,'es-CO',NULL),(784,'es-CO',NULL),(785,'es-CO',NULL),(787,'es-CO',NULL),(788,'es-CO',NULL),(789,'es-CO',NULL),(790,'es-CO',NULL),(791,'es-CO',NULL),(792,'es-CO',NULL),(793,'es-CO',NULL),(794,'es-CO',NULL),(795,'es-CO',NULL),(796,'es-CO',NULL),(1,'tr-TR','Kullanıcı Kodu'),(2,'tr-TR','İsim'),(3,'tr-TR','{modelClass}\'ı İçeri Aktar'),(4,'tr-TR','İçeri Aktar'),(5,'tr-TR','Topluluklar'),(6,'tr-TR','Güncelle'),(7,'tr-TR','Sil'),(8,'tr-TR','Bu ögeyi silmek istediğinizden emin misiniz?'),(9,'tr-TR','{modelClass}\'ı Güncelle:'),(10,'tr-TR','\'{society}\' Topluluğunu Şununla Birleştir: ...'),(11,'tr-TR','Bir Ana Topluluk Seç ...'),(12,'tr-TR','{modelClass} Oluştur'),(13,'tr-TR','{modelClass}\'ı Görüntüle'),(14,'tr-TR','{modelClass}\'ı Güncelle'),(15,'tr-TR','{modelClass}\'ı Sil'),(16,'tr-TR','Yeni öge ekle'),(17,'tr-TR','İçeriği yeniden yükle'),(18,'tr-TR','CSV Dosyası Aracılığıyla İçeri Aktar'),(19,'tr-TR','Oluştur'),(20,'tr-TR','Diller'),(21,'tr-TR','Dil Oluştur'),(22,'tr-TR','Özel İhtiyaçlar'),(23,'tr-TR','Önerge Etiketleri'),(24,'tr-TR','\'{tag}\' Önerge Etiketini Şununla Birleştir: ...'),(25,'tr-TR','Bir Üst Etiket Seç ...'),(26,'tr-TR','Ara'),(27,'tr-TR','Sıfırla'),(28,'tr-TR','Önerge Etiketi Oluştur'),(29,'tr-TR','Uygulama Programlama Arayüzü'),(30,'tr-TR','Yönetici'),(31,'tr-TR','Mesajlar'),(32,'tr-TR','Mesaj Oluştur'),(33,'tr-TR','{count} adet Etiket değiştirildi'),(34,'tr-TR','Dosya Söz Dizimi Hatalı'),(35,'tr-TR','Önerge Etiketi'),(36,'tr-TR','Tur'),(37,'tr-TR','Kısaltma'),(38,'tr-TR',NULL),(39,'tr-TR','Hükümet Açılış'),(40,'tr-TR','Muhalefet Açılış'),(41,'tr-TR','Hükümet Kapanış'),(42,'tr-TR','Muhalefet Kapanış'),(43,'tr-TR','Takım'),(44,'tr-TR','Aktif'),(45,'tr-TR','Turnuva'),(46,'tr-TR','Konuşmacı'),(47,'tr-TR','Topluluk'),(48,'tr-TR','Gölge Takım'),(49,'tr-TR','Dil Durumu'),(50,'tr-TR','Her şey kontrol altında'),(51,'tr-TR','Yerine bir gölge takım yerleştirildi'),(52,'tr-TR','Konuşmacı {letter} maça katılmadı'),(53,'tr-TR',NULL),(54,'tr-TR','Etiket'),(55,'tr-TR','Değer'),(56,'tr-TR','Takım pozisyonu boş bırakılamaz'),(57,'tr-TR',NULL),(58,'tr-TR','Tabmaster Kullanıcısı'),(59,'tr-TR','Kullanıcı'),(60,'tr-TR','ENL Sıralaması'),(61,'tr-TR','ESL Sıralaması'),(62,'tr-TR',''),(63,'tr-TR','Tam İsim'),(64,'tr-TR','Kısaltma'),(65,'tr-TR','Şehir'),(66,'tr-TR','Ülke'),(67,'tr-TR','HA Takımı'),(68,'tr-TR','MA Takımı'),(69,'tr-TR','HK Takımı'),(70,'tr-TR','MK Takımı'),(71,'tr-TR','Panel'),(72,'tr-TR','Salon'),(73,'tr-TR','HA Geri Bildirimi'),(74,'tr-TR','MA Geri Bildirimi'),(75,'tr-TR','HK Geri Bildirimi'),(76,'tr-TR','MK Geri Bildirimi'),(77,'tr-TR','Zaman'),(78,'tr-TR','Önerge'),(79,'tr-TR','Dil'),(80,'tr-TR','Tarih'),(81,'tr-TR','Bilgi Slaytı'),(82,'tr-TR','Link'),(83,'tr-TR','Kullanıcı Tarafından'),(84,'tr-TR','Çeviri'),(85,'tr-TR','Jüri'),(86,'tr-TR','Cevap'),(87,'tr-TR','Geri Bildirim'),(88,'tr-TR','Soru'),(89,'tr-TR','Oluşturuldu'),(90,'tr-TR','Devam Ediyor'),(91,'tr-TR','Kapandı'),(92,'tr-TR','Gizlendi'),(93,'tr-TR','Ev Sahibi:'),(94,'tr-TR','Turnuva Adı'),(95,'tr-TR','Başlangıç Tarihi'),(96,'tr-TR','Bitiş Tarihi:'),(97,'tr-TR','Zaman Dilimi'),(98,'tr-TR','Logo'),(99,'tr-TR',NULL),(100,'tr-TR','Tab Algoritması'),(101,'tr-TR','Tahmini tur sayısı'),(102,'tr-TR','ESL Sıralamasını Göster'),(103,'tr-TR','Final turu var mı'),(104,'tr-TR','Yarı final turu var mı'),(105,'tr-TR','Çeyrek final turu var m?'),(106,'tr-TR','Ön çeyrek final turu var mı'),(107,'tr-TR','Erişim İşareti'),(108,'tr-TR','Katılımcı Kimlik Kartı'),(109,'tr-TR',NULL),(110,'tr-TR',NULL),(111,'tr-TR','Bölge'),(112,'tr-TR','Dil Kodu'),(113,'tr-TR','Kapsam'),(114,'tr-TR','Son Düzenleme'),(115,'tr-TR','Kuvvet'),(116,'tr-TR','Salon Başkanlığı için uygun'),(117,'tr-TR','İzleniyor'),(118,'tr-TR','Değerlendirilmemiş'),(121,'tr-TR','Jürilik için uygun'),(122,'tr-TR',NULL),(124,'tr-TR','Yüksek Potansiyelli'),(125,'tr-TR','Salon Başkanı'),(126,'tr-TR','İyi'),(127,'tr-TR',NULL),(128,'tr-TR','Jüri Komitesi Başkanı (CA)'),(129,'tr-TR',NULL),(130,'tr-TR',NULL),(131,'tr-TR','Konuşmacı Puanları'),(132,'tr-TR','Bu e-posta adresi kullanımda.'),(133,'tr-TR','Münazır'),(134,'tr-TR','Yetkilendirme Anahtarı'),(135,'tr-TR',NULL),(136,'tr-TR','Parola Sıfırlama Anahtarı'),(137,'tr-TR','e-posta'),(138,'tr-TR','Hesap Türü'),(139,'tr-TR','Hesap Durumu'),(140,'tr-TR','Son Değişiklik'),(141,'tr-TR','İsim'),(142,'tr-TR','Soyisim'),(143,'tr-TR','Fotoğraf'),(144,'tr-TR','Vekil'),(145,'tr-TR','Tabmaster'),(146,'tr-TR','Yönetici'),(147,'tr-TR','Silindi'),(148,'tr-TR','Belirtmek istemiyor'),(149,'tr-TR','Kadın'),(150,'tr-TR','Erkek'),(151,'tr-TR','Diğer'),(152,'tr-TR','karışık'),(153,'tr-TR','Daha belirlenmedi'),(154,'tr-TR','Mülakat gerekli'),(155,'tr-TR','EPL'),(157,'tr-TR','ESL'),(158,'tr-TR','İkinci dili İngilizce olan'),(159,'tr-TR','EFL'),(160,'tr-TR','Yabancı dili İngilizce olan'),(161,'tr-TR','Belirlenmemiş'),(162,'tr-TR','{user_name} Kullanıcısı İçin Kulüp İçi İlişkiyi Kaydetmede Hata'),(163,'tr-TR','{user_name} Kullanıcısını Kaydetmede Hata'),(164,'tr-TR','{tournament_name}: {user_name} için Kullanıcı Hesabı'),(165,'tr-TR','Bu URL-Slug\'ı kullanmanız mümkün değil'),(166,'tr-TR','Yayımlandı'),(167,'tr-TR','Gösterildi'),(168,'tr-TR','Başladı'),(169,'tr-TR',NULL),(170,'tr-TR','Bitti'),(171,'tr-TR','Ana'),(172,'tr-TR','Çaylak'),(173,'tr-TR','Final'),(174,'tr-TR','Yarı Final'),(175,'tr-TR','Çeyrek Final'),(176,'tr-TR','Ön Çeyrek Final'),(177,'tr-TR','Tur #{num}'),(178,'tr-TR','Turlar'),(179,'tr-TR',NULL),(180,'tr-TR','Bilgi Slaydı'),(181,'tr-TR','Hazırlık Süresi Başladı'),(182,'tr-TR','Ölçülen Son Sıcaklık'),(183,'tr-TR','ms\'de hazırlandı'),(184,'tr-TR','Bir salonu doldurmak için yeterli takım bulunmuyor - (aktif: {teams_count})'),(185,'tr-TR','En az iki jüri gerekli - (aktif: {count_adju})'),(186,'tr-TR','Aktif takım sayısı 4\'e bölünmeli ;) - (aktif: {count_teams})'),(187,'tr-TR','Gereğinden az salon bulunuyor - (aktif: {active_rooms})'),(188,'tr-TR','Yeteri kadar jüri bulunmuyor - (aktif: {active}, minimum: {required})'),(189,'tr-TR',NULL),(190,'tr-TR','Paneli kaydederken hata: {message}'),(191,'tr-TR','Maçı kaydederken hata: {message}'),(192,'tr-TR','Maçı kaydederken hata:<br>{errors}'),(193,'tr-TR','Güncellemek istediğiniz Maç #{num} bulunamadı.'),(195,'tr-TR',NULL),(196,'tr-TR',NULL),(197,'tr-TR',NULL),(198,'tr-TR',NULL),(199,'tr-TR',NULL),(200,'tr-TR','İyi Değil'),(201,'tr-TR','Oldukça İyi'),(202,'tr-TR','Mükemmel'),(203,'tr-TR',NULL),(204,'tr-TR','Kısa Metin Kutucuğu'),(205,'tr-TR','Uzun Metin Kutucuğu'),(206,'tr-TR','Sayı Kutucuğu'),(207,'tr-TR','Onay Kutusu Listesi'),(208,'tr-TR','Maç'),(209,'tr-TR','Geri Bildirim: ID'),(210,'tr-TR',NULL),(211,'tr-TR',NULL),(212,'tr-TR','Grup'),(213,'tr-TR','Aktif Salon'),(214,'tr-TR','Yan Jüri'),(215,'tr-TR','Kullanıldı'),(216,'tr-TR','Önceden Belirlenmiş Paneldir'),(217,'tr-TR','Panel #{id}\'de {amount} Salon Başkanı var'),(218,'tr-TR','Kategori'),(219,'tr-TR','Mesaj'),(220,'tr-TR','İşlev'),(221,'tr-TR',NULL),(222,'tr-TR',NULL),(223,'tr-TR',NULL),(224,'tr-TR',NULL),(225,'tr-TR',NULL),(226,'tr-TR',NULL),(227,'tr-TR',NULL),(228,'tr-TR','HA A Konuşmacı Puanı'),(229,'tr-TR','HA B Konuşmacı Puanı'),(230,'tr-TR','HA Sıralama'),(231,'tr-TR','MA A Konuşmacı Puanı'),(232,'tr-TR','MA B Konuşmacı Puanı'),(233,'tr-TR','MA Sıralama'),(234,'tr-TR','HK A Konuşmacı Puanı'),(235,'tr-TR','HK B Konuşmacı Puanı'),(236,'tr-TR','HK Sıralama'),(237,'tr-TR','MK A Konuşmacı Puanı'),(238,'tr-TR','MK B Konuşmacı Puanı'),(239,'tr-TR','MK Sıralama'),(240,'tr-TR','Kontrol Edildi'),(241,'tr-TR','Şu Kullanıcı Tarafından Girildi: ID'),(242,'tr-TR',NULL),(243,'tr-TR',NULL),(244,'tr-TR','CA'),(245,'tr-TR','Parola sıfırlama anahtarı boş bırakılamaz.'),(246,'tr-TR','Parola sıfırlama anahtarı hatası.'),(247,'tr-TR',NULL),(248,'tr-TR',NULL),(249,'tr-TR',NULL),(250,'tr-TR','CSV Dosyası'),(251,'tr-TR',NULL),(252,'tr-TR',NULL),(253,'tr-TR','Kullanıcı Adı'),(254,'tr-TR','Profil Fotoğrafı'),(255,'tr-TR','Güncel Topluluk'),(256,'tr-TR','Kendinizi hangi cinsiyet kategorisiyle en çok bağdaştırıyorsunuz'),(257,'tr-TR','Bu URL kullanılamaz.'),(258,'tr-TR','{adju} burada!'),(259,'tr-TR','{adju} zaten yoklama vermişti'),(260,'tr-TR','{id} numarası geçersiz! Bu kişi jüri değil!'),(261,'tr-TR','{speaker} burada!'),(262,'tr-TR','{speaker} zaten yoklama vermişti'),(263,'tr-TR','{id} numarası geçersiz! Bu bir takım değil!'),(264,'tr-TR','Geçerli bir girdi değil'),(265,'tr-TR','Doğrulama Kodu'),(266,'tr-TR',NULL),(267,'tr-TR','{user} için parola sıfırlama'),(268,'tr-TR','Bu e-posta adresiyle bir kullanıcı bulunamadı'),(269,'tr-TR','{object} Ekle'),(270,'tr-TR','Topluluk Oluştur'),(271,'tr-TR',NULL),(272,'tr-TR',NULL),(273,'tr-TR','Bir ülke ara ...'),(274,'tr-TR','Yeni Topluluk Ekle'),(275,'tr-TR','Bir topluluk ara ...'),(276,'tr-TR','Başlangıç tarihini gir ...'),(277,'tr-TR','Eğer mümkünse bitiş tarihini gir ...'),(278,'tr-TR','Dil Görevlileri'),(279,'tr-TR','Görevli'),(280,'tr-TR',NULL),(281,'tr-TR','Dil Durumunu Gözden Geçirme'),(282,'tr-TR','Durum'),(283,'tr-TR','Bir mülakat iste'),(284,'tr-TR',NULL),(285,'tr-TR',NULL),(286,'tr-TR',NULL),(288,'tr-TR',NULL),(289,'tr-TR',NULL),(290,'tr-TR','Yoklama'),(291,'tr-TR',NULL),(292,'tr-TR',NULL),(293,'tr-TR',NULL),(294,'tr-TR',NULL),(295,'tr-TR',NULL),(296,'tr-TR',NULL),(297,'tr-TR',NULL),(298,'tr-TR',NULL),(299,'tr-TR',NULL),(300,'tr-TR',NULL),(301,'tr-TR',NULL),(302,'tr-TR',NULL),(303,'tr-TR',NULL),(304,'tr-TR',NULL),(305,'tr-TR',NULL),(307,'tr-TR',NULL),(308,'tr-TR',NULL),(309,'tr-TR',NULL),(310,'tr-TR',NULL),(311,'tr-TR',NULL),(312,'tr-TR',NULL),(313,'tr-TR',NULL),(314,'tr-TR',NULL),(315,'tr-TR',NULL),(316,'tr-TR',NULL),(317,'tr-TR',NULL),(318,'tr-TR',NULL),(319,'tr-TR',NULL),(320,'tr-TR',NULL),(321,'tr-TR',NULL),(322,'tr-TR',NULL),(323,'tr-TR',NULL),(325,'tr-TR',NULL),(326,'tr-TR',NULL),(327,'tr-TR',NULL),(328,'tr-TR',NULL),(329,'tr-TR',NULL),(330,'tr-TR',NULL),(331,'tr-TR',NULL),(332,'tr-TR',NULL),(333,'tr-TR',NULL),(334,'tr-TR',NULL),(335,'tr-TR',NULL),(336,'tr-TR',NULL),(337,'tr-TR',NULL),(338,'tr-TR',NULL),(339,'tr-TR',NULL),(340,'tr-TR',NULL),(341,'tr-TR',NULL),(342,'tr-TR',NULL),(343,'tr-TR',NULL),(344,'tr-TR',NULL),(345,'tr-TR',NULL),(346,'tr-TR',NULL),(347,'tr-TR',NULL),(348,'tr-TR',NULL),(349,'tr-TR',NULL),(350,'tr-TR',NULL),(351,'tr-TR',NULL),(352,'tr-TR',NULL),(353,'tr-TR',NULL),(354,'tr-TR',NULL),(355,'tr-TR',NULL),(356,'tr-TR',NULL),(357,'tr-TR',NULL),(358,'tr-TR',NULL),(359,'tr-TR',NULL),(360,'tr-TR',NULL),(361,'tr-TR',NULL),(362,'tr-TR',NULL),(363,'tr-TR',NULL),(364,'tr-TR',NULL),(365,'tr-TR',NULL),(366,'tr-TR',NULL),(367,'tr-TR',NULL),(368,'tr-TR',NULL),(369,'tr-TR',NULL),(370,'tr-TR',NULL),(371,'tr-TR',NULL),(372,'tr-TR',NULL),(373,'tr-TR',NULL),(374,'tr-TR',NULL),(375,'tr-TR',NULL),(376,'tr-TR',NULL),(377,'tr-TR',NULL),(378,'tr-TR',NULL),(379,'tr-TR',NULL),(380,'tr-TR',NULL),(381,'tr-TR',NULL),(382,'tr-TR',NULL),(383,'tr-TR',NULL),(384,'tr-TR',NULL),(385,'tr-TR',NULL),(386,'tr-TR',NULL),(387,'tr-TR',NULL),(388,'tr-TR',NULL),(389,'tr-TR',NULL),(390,'tr-TR',NULL),(391,'tr-TR',NULL),(392,'tr-TR',NULL),(393,'tr-TR',NULL),(394,'tr-TR',NULL),(395,'tr-TR',NULL),(396,'tr-TR',NULL),(397,'tr-TR',NULL),(398,'tr-TR',NULL),(399,'tr-TR',NULL),(400,'tr-TR',NULL),(401,'tr-TR',NULL),(402,'tr-TR',NULL),(403,'tr-TR',NULL),(404,'tr-TR',NULL),(405,'tr-TR',NULL),(407,'tr-TR',NULL),(408,'tr-TR',NULL),(411,'tr-TR',NULL),(412,'tr-TR',NULL),(413,'tr-TR',NULL),(414,'tr-TR',NULL),(415,'tr-TR','Cinsiyet'),(416,'tr-TR',NULL),(417,'tr-TR',NULL),(418,'tr-TR','Yükleniyor ...'),(419,'tr-TR','Geri Bildirimi Görüntüle'),(420,'tr-TR','Kullanıcıyı Görüntüle'),(421,'tr-TR','{venue} salonunu şununla değiştir:'),(422,'tr-TR','Bir Salon Seç ...'),(423,'tr-TR',NULL),(424,'tr-TR',NULL),(425,'tr-TR','Bir Takım Seç ...'),(426,'tr-TR','Bir Dil Seç ...'),(427,'tr-TR','Bir Jüri Seç ...'),(428,'tr-TR','Jürileri Değiştir'),(429,'tr-TR',NULL),(430,'tr-TR','ile'),(431,'tr-TR','bununla ...'),(432,'tr-TR','Bir Önerge Etiketi Ara ...'),(433,'tr-TR','Sıralama'),(434,'tr-TR','Toplam'),(435,'tr-TR',NULL),(436,'tr-TR','Salon'),(437,'tr-TR','Final Turları'),(438,'tr-TR',NULL),(439,'tr-TR',NULL),(440,'tr-TR',NULL),(441,'tr-TR',NULL),(442,'tr-TR',NULL),(443,'tr-TR',NULL),(444,'tr-TR',NULL),(445,'tr-TR',NULL),(446,'tr-TR',NULL),(447,'tr-TR',NULL),(448,'tr-TR',NULL),(449,'tr-TR',NULL),(450,'tr-TR',NULL),(451,'tr-TR',NULL),(452,'tr-TR',NULL),(453,'tr-TR',NULL),(454,'tr-TR',NULL),(455,'tr-TR',NULL),(456,'tr-TR',NULL),(457,'tr-TR',NULL),(458,'tr-TR',NULL),(459,'tr-TR',NULL),(460,'tr-TR',NULL),(461,'tr-TR',NULL),(462,'tr-TR','Teşekkürler'),(463,'tr-TR','Teşekkürler!'),(464,'tr-TR',NULL),(465,'tr-TR',NULL),(466,'tr-TR',NULL),(467,'tr-TR',NULL),(468,'tr-TR',NULL),(469,'tr-TR',NULL),(470,'tr-TR',NULL),(471,'tr-TR',NULL),(472,'tr-TR',NULL),(473,'tr-TR',NULL),(474,'tr-TR','Jüri Geri Bildirimi'),(475,'tr-TR','Geri Bildirim Yolla'),(476,'tr-TR',NULL),(477,'tr-TR','Dil Durumunu Gözden Geçir'),(478,'tr-TR',NULL),(479,'tr-TR',NULL),(480,'tr-TR','{tournament} - Yönetici'),(481,'tr-TR','Salonları Listele'),(482,'tr-TR','Salon Oluştur'),(483,'tr-TR','Salonları İçeri Aktar'),(484,'tr-TR','Takımları Listele'),(485,'tr-TR','Takım Oluştur'),(486,'tr-TR','Takımları İçeri Aktar'),(487,'tr-TR',NULL),(488,'tr-TR',NULL),(489,'tr-TR',NULL),(490,'tr-TR',NULL),(491,'tr-TR',NULL),(492,'tr-TR',NULL),(493,'tr-TR',NULL),(494,'tr-TR',NULL),(495,'tr-TR',NULL),(496,'tr-TR',NULL),(497,'tr-TR',NULL),(498,'tr-TR',NULL),(499,'tr-TR',NULL),(500,'tr-TR',NULL),(501,'tr-TR',NULL),(502,'tr-TR',NULL),(503,'tr-TR',NULL),(504,'tr-TR',NULL),(505,'tr-TR',NULL),(506,'tr-TR',NULL),(507,'tr-TR',NULL),(508,'tr-TR',NULL),(509,'tr-TR',NULL),(510,'tr-TR',NULL),(511,'tr-TR',NULL),(512,'tr-TR',NULL),(513,'tr-TR',NULL),(514,'tr-TR',NULL),(515,'tr-TR',NULL),(516,'tr-TR',NULL),(517,'tr-TR',NULL),(518,'tr-TR',NULL),(519,'tr-TR',NULL),(520,'tr-TR',NULL),(521,'tr-TR',NULL),(522,'tr-TR',NULL),(524,'tr-TR',NULL),(525,'tr-TR',NULL),(526,'tr-TR',NULL),(527,'tr-TR',NULL),(529,'tr-TR',NULL),(530,'tr-TR',NULL),(531,'tr-TR',NULL),(532,'tr-TR',NULL),(533,'tr-TR',NULL),(534,'tr-TR',NULL),(535,'tr-TR',NULL),(536,'tr-TR',NULL),(537,'tr-TR',NULL),(538,'tr-TR',NULL),(539,'tr-TR',NULL),(540,'tr-TR',NULL),(541,'tr-TR',NULL),(542,'tr-TR',NULL),(543,'tr-TR',NULL),(544,'tr-TR',NULL),(545,'tr-TR',NULL),(546,'tr-TR','Takım Yorumları'),(547,'tr-TR',NULL),(548,'tr-TR',NULL),(549,'tr-TR',NULL),(550,'tr-TR',NULL),(551,'tr-TR',NULL),(552,'tr-TR',NULL),(553,'tr-TR',NULL),(554,'tr-TR',NULL),(555,'tr-TR',NULL),(556,'tr-TR',NULL),(557,'tr-TR',NULL),(558,'tr-TR',NULL),(559,'tr-TR',NULL),(560,'tr-TR',NULL),(561,'tr-TR',NULL),(562,'tr-TR',NULL),(563,'tr-TR',NULL),(564,'tr-TR',NULL),(565,'tr-TR',NULL),(566,'tr-TR',NULL),(567,'tr-TR','Münazara Topluluğu Geçmişi'),(568,'tr-TR','Geçmişe yeni topluluk ekle'),(569,'tr-TR','hala aktif'),(570,'tr-TR','Topluluk Bilgisini Güncelle'),(571,'tr-TR','{name}\'i yeni parola almaya zorla'),(572,'tr-TR','İptal'),(573,'tr-TR','Bir Turnuva Ara ...'),(574,'tr-TR','Yeni Parola Belirle'),(575,'tr-TR','Kullanıcıyı Güncelle'),(576,'tr-TR','Kullanıcıyı Sil'),(577,'tr-TR','Kullanıcı Oluştur'),(578,'tr-TR',NULL),(579,'tr-TR',NULL),(580,'tr-TR',NULL),(582,'tr-TR','Uygun dosya bulunamad?'),(583,'tr-TR','E?le?en sonuç bulunamad?'),(584,'tr-TR',NULL),(585,'tr-TR',NULL),(586,'tr-TR',NULL),(587,'tr-TR',NULL),(588,'tr-TR',NULL),(589,'tr-TR',NULL),(590,'tr-TR','Kullanıcı kaydedildi! Hoşgeldin {user}'),(591,'tr-TR','Giriş başarısız!'),(592,'tr-TR','Daha fazla yönlendirme için e-posta kutunuzu ziyaret edin'),(593,'tr-TR',NULL),(594,'tr-TR',NULL),(595,'tr-TR',NULL),(596,'tr-TR',NULL),(597,'tr-TR',NULL),(598,'tr-TR',NULL),(599,'tr-TR',NULL),(600,'tr-TR',NULL),(601,'tr-TR',NULL),(602,'tr-TR',NULL),(603,'tr-TR',NULL),(604,'tr-TR',NULL),(605,'tr-TR',NULL),(606,'tr-TR',NULL),(607,'tr-TR',NULL),(608,'tr-TR',NULL),(609,'tr-TR',NULL),(610,'tr-TR',NULL),(611,'tr-TR',NULL),(613,'tr-TR',NULL),(614,'tr-TR',NULL),(615,'tr-TR',NULL),(616,'tr-TR',NULL),(617,'tr-TR',NULL),(618,'tr-TR',NULL),(619,'tr-TR',NULL),(620,'tr-TR',NULL),(622,'tr-TR',NULL),(623,'tr-TR',NULL),(624,'tr-TR',NULL),(625,'tr-TR',NULL),(626,'tr-TR',NULL),(627,'tr-TR',NULL),(628,'tr-TR',NULL),(629,'tr-TR',NULL),(630,'tr-TR',NULL),(631,'tr-TR',NULL),(632,'tr-TR',NULL),(633,'tr-TR',NULL),(634,'tr-TR',NULL),(635,'tr-TR',NULL),(636,'tr-TR',NULL),(637,'tr-TR',NULL),(638,'tr-TR',NULL),(639,'tr-TR',NULL),(640,'tr-TR',NULL),(641,'tr-TR',NULL),(642,'tr-TR',NULL),(643,'tr-TR',NULL),(644,'tr-TR',NULL),(645,'tr-TR',NULL),(646,'tr-TR',NULL),(647,'tr-TR',NULL),(648,'tr-TR',NULL),(649,'tr-TR','Dil seçenekleri kaydedildi'),(650,'tr-TR','Dil seçeneklerini kaydetmede hata'),(651,'tr-TR','Kullanıcı bulunamadı!'),(652,'tr-TR','{object} başarıyla eklendi'),(653,'tr-TR','Başarıyla silindi'),(654,'tr-TR',NULL),(656,'tr-TR','Sonuçların kaydedilmesinde hata.<br>Lütfen basılı sonuç kağıdı isteyin.'),(657,'tr-TR','Sonuçlar kaydedildi. Sıradaki!'),(658,'tr-TR',NULL),(659,'tr-TR',NULL),(660,'tr-TR',NULL),(661,'tr-TR',NULL),(662,'tr-TR',NULL),(663,'tr-TR','Yeterli sayıda salon mevcut değil'),(664,'tr-TR','Gereğinden fazla sayıda salon var'),(665,'tr-TR',NULL),(666,'tr-TR',NULL),(667,'tr-TR',NULL),(668,'tr-TR',NULL),(669,'tr-TR',NULL),(670,'tr-TR',NULL),(671,'tr-TR','Jüri daha önce bu takımı gördü'),(672,'tr-TR','Jüri daha önce bu kombinasyonu izledi'),(673,'tr-TR',NULL),(674,'tr-TR',NULL),(675,'tr-TR','Jüri {adju} ve {team} aynı topluluğa mensuplar.'),(676,'tr-TR',NULL),(677,'tr-TR',NULL),(678,'tr-TR',NULL),(679,'tr-TR',NULL),(680,'tr-TR',NULL),(681,'tr-TR',NULL),(682,'tr-TR',NULL),(686,'tr-TR','Tanımlanmamış'),(687,'tr-TR','Kuzey Avrupa'),(688,'tr-TR','Batı Avrupa'),(689,'tr-TR','Güney Avrupa'),(690,'tr-TR','Doğu Avrupa'),(691,'tr-TR','Orta Asya'),(692,'tr-TR','Doğu Asya'),(693,'tr-TR','Batı Asya'),(694,'tr-TR','Güney Asya'),(695,'tr-TR','Güneydoğu Asya'),(696,'tr-TR','Avustralya ve Yeni Zelanda'),(697,'tr-TR','Mikronezya'),(698,'tr-TR','Melanezya'),(699,'tr-TR','Polinezya'),(700,'tr-TR','Kuzey Afrika'),(701,'tr-TR','Batı Afrika'),(702,'tr-TR','Orta Afrika'),(703,'tr-TR','Doğu Afrika'),(704,'tr-TR','Güney Afrika'),(705,'tr-TR','Kuzey Amerika'),(706,'tr-TR','Orta Amerika'),(707,'tr-TR','Karayipler'),(708,'tr-TR','Güney Amerika'),(709,'tr-TR','Antarktika'),(710,'tr-TR','Cezalı Jüri Üyesi'),(711,'tr-TR','Kötü Jüri Üyesi'),(712,'tr-TR','Vasat Jüri Üyesi'),(713,'tr-TR','Ortalama Jüri Üyesi'),(714,'tr-TR','Ortalama Salon Başkanı'),(715,'tr-TR','İyi Salon Başkanı'),(716,'tr-TR',NULL),(717,'tr-TR','<b>Bu turnuvanın takımları henüz eklenmemiş.</b><br>{add_button} ya da {import_button}'),(718,'tr-TR','Takım ekle'),(719,'tr-TR','CSV dosyası aracılığıyla içeri aktar'),(720,'tr-TR','Bu turnuvanın salonları henüz girilmemiş.<br>{add} ya da {import}'),(721,'tr-TR','Salon ekle'),(722,'tr-TR','CSV dosyası aracılığıyla içeri aktar'),(723,'tr-TR','<b>Bu turnuvanın jürileri henüz eklenmemiş.</b><br>{add_button} ya da {import_button}'),(724,'tr-TR','CSV dosyası aracılığıyla içeri aktar'),(725,'tr-TR',NULL),(726,'tr-TR',NULL),(727,'tr-TR',NULL),(728,'tr-TR',NULL),(729,'tr-TR',NULL),(730,'tr-TR',NULL),(731,'tr-TR',NULL),(732,'tr-TR',NULL),(733,'tr-TR',NULL),(734,'tr-TR',NULL),(735,'tr-TR',NULL),(736,'tr-TR',NULL),(737,'tr-TR',NULL),(738,'tr-TR',NULL),(739,'tr-TR',NULL),(740,'tr-TR',NULL),(741,'tr-TR',NULL),(742,'tr-TR',NULL),(743,'tr-TR',NULL),(744,'tr-TR',NULL),(745,'tr-TR',NULL),(746,'tr-TR',NULL),(747,'tr-TR',NULL),(748,'tr-TR',NULL),(749,'tr-TR',NULL),(750,'tr-TR',NULL),(751,'tr-TR',NULL),(753,'tr-TR',NULL),(754,'tr-TR',NULL),(755,'tr-TR',NULL),(756,'tr-TR',NULL),(757,'tr-TR',NULL),(758,'tr-TR',NULL),(759,'tr-TR',NULL),(760,'tr-TR',NULL),(761,'tr-TR',NULL),(762,'tr-TR',NULL),(764,'tr-TR',NULL),(765,'tr-TR',NULL),(766,'tr-TR',NULL),(767,'tr-TR',NULL),(770,'tr-TR',NULL),(771,'tr-TR',NULL),(772,'tr-TR',NULL),(773,'tr-TR',NULL),(774,'tr-TR',NULL),(775,'tr-TR',NULL),(777,'tr-TR',NULL),(778,'tr-TR',NULL),(783,'tr-TR',NULL),(784,'tr-TR',NULL),(785,'tr-TR',NULL),(787,'tr-TR',NULL),(788,'tr-TR',NULL),(789,'tr-TR',NULL),(790,'tr-TR',NULL),(791,'tr-TR',NULL),(792,'tr-TR',NULL),(793,'tr-TR',NULL),(794,'tr-TR',NULL),(795,'tr-TR',NULL),(796,'tr-TR',NULL),(1,'{lang}',NULL),(2,'{lang}',NULL),(3,'{lang}',NULL),(4,'{lang}',NULL),(5,'{lang}',NULL),(6,'{lang}',NULL),(7,'{lang}',NULL),(8,'{lang}',NULL),(9,'{lang}',NULL),(10,'{lang}',NULL),(11,'{lang}',NULL),(12,'{lang}',NULL),(13,'{lang}',NULL),(14,'{lang}',NULL),(15,'{lang}',NULL),(16,'{lang}',NULL),(17,'{lang}',NULL),(18,'{lang}',NULL),(19,'{lang}',NULL),(20,'{lang}',NULL),(21,'{lang}',NULL),(22,'{lang}',NULL),(23,'{lang}',NULL),(24,'{lang}',NULL),(25,'{lang}',NULL),(26,'{lang}',NULL),(27,'{lang}',NULL),(28,'{lang}',NULL),(29,'{lang}',NULL),(30,'{lang}',NULL),(31,'{lang}',NULL),(32,'{lang}',NULL),(33,'{lang}',NULL),(34,'{lang}',NULL),(35,'{lang}',NULL),(36,'{lang}',NULL),(37,'{lang}',NULL),(38,'{lang}',NULL),(39,'{lang}',NULL),(40,'{lang}',NULL),(41,'{lang}',NULL),(42,'{lang}',NULL),(43,'{lang}',NULL),(44,'{lang}',NULL),(45,'{lang}',NULL),(46,'{lang}',NULL),(47,'{lang}',NULL),(48,'{lang}',NULL),(49,'{lang}',NULL),(50,'{lang}',NULL),(51,'{lang}',NULL),(52,'{lang}',NULL),(53,'{lang}',NULL),(54,'{lang}',NULL),(55,'{lang}',NULL),(56,'{lang}',NULL),(57,'{lang}',NULL),(58,'{lang}',NULL),(59,'{lang}',NULL),(60,'{lang}',NULL),(61,'{lang}',NULL),(62,'{lang}',NULL),(63,'{lang}',NULL),(64,'{lang}',NULL),(65,'{lang}',NULL),(66,'{lang}',NULL),(67,'{lang}',NULL),(68,'{lang}',NULL),(69,'{lang}',NULL),(70,'{lang}',NULL),(71,'{lang}',NULL),(72,'{lang}',NULL),(73,'{lang}',NULL),(74,'{lang}',NULL),(75,'{lang}',NULL),(76,'{lang}',NULL),(77,'{lang}',NULL),(78,'{lang}',NULL),(79,'{lang}',NULL),(80,'{lang}',NULL),(81,'{lang}',NULL),(82,'{lang}',NULL),(83,'{lang}',NULL),(84,'{lang}',NULL),(85,'{lang}',NULL),(86,'{lang}',NULL),(87,'{lang}',NULL),(88,'{lang}',NULL),(89,'{lang}',NULL),(90,'{lang}',NULL),(91,'{lang}',NULL),(92,'{lang}',NULL),(93,'{lang}',NULL),(94,'{lang}',NULL),(95,'{lang}',NULL),(96,'{lang}',NULL),(97,'{lang}',NULL),(98,'{lang}',NULL),(99,'{lang}',NULL),(100,'{lang}',NULL),(101,'{lang}',NULL),(102,'{lang}',NULL),(103,'{lang}',NULL),(104,'{lang}',NULL),(105,'{lang}',NULL),(106,'{lang}',NULL),(107,'{lang}',NULL),(108,'{lang}',NULL),(109,'{lang}',NULL),(110,'{lang}',NULL),(111,'{lang}',NULL),(112,'{lang}',NULL),(113,'{lang}',NULL),(114,'{lang}',NULL),(115,'{lang}',NULL),(116,'{lang}',NULL),(117,'{lang}',NULL),(118,'{lang}',NULL),(121,'{lang}',NULL),(122,'{lang}',NULL),(124,'{lang}',NULL),(125,'{lang}',NULL),(126,'{lang}',NULL),(127,'{lang}',NULL),(128,'{lang}',NULL),(129,'{lang}',NULL),(130,'{lang}',NULL),(131,'{lang}',NULL),(132,'{lang}',NULL),(133,'{lang}',NULL),(134,'{lang}',NULL),(135,'{lang}',NULL),(136,'{lang}',NULL),(137,'{lang}',NULL),(138,'{lang}',NULL),(139,'{lang}',NULL),(140,'{lang}',NULL),(141,'{lang}',NULL),(142,'{lang}',NULL),(143,'{lang}',NULL),(144,'{lang}',NULL),(145,'{lang}',NULL),(146,'{lang}',NULL),(147,'{lang}',NULL),(148,'{lang}',NULL),(149,'{lang}',NULL),(150,'{lang}',NULL),(151,'{lang}',NULL),(152,'{lang}',NULL),(153,'{lang}',NULL),(154,'{lang}',NULL),(155,'{lang}',NULL),(157,'{lang}',NULL),(158,'{lang}',NULL),(159,'{lang}',NULL),(160,'{lang}',NULL),(161,'{lang}',NULL),(162,'{lang}',NULL),(163,'{lang}',NULL),(164,'{lang}',NULL),(165,'{lang}',NULL),(166,'{lang}',NULL),(167,'{lang}',NULL),(168,'{lang}',NULL),(169,'{lang}',NULL),(170,'{lang}',NULL),(171,'{lang}',NULL),(172,'{lang}',NULL),(173,'{lang}',NULL),(174,'{lang}',NULL),(175,'{lang}',NULL),(176,'{lang}',NULL),(177,'{lang}',NULL),(178,'{lang}',NULL),(179,'{lang}',NULL),(180,'{lang}',NULL),(181,'{lang}',NULL),(182,'{lang}',NULL),(183,'{lang}',NULL),(184,'{lang}',NULL),(185,'{lang}',NULL),(186,'{lang}',NULL),(187,'{lang}',NULL),(188,'{lang}',NULL),(189,'{lang}',NULL),(190,'{lang}',NULL),(191,'{lang}',NULL),(192,'{lang}',NULL),(193,'{lang}',NULL),(195,'{lang}',NULL),(196,'{lang}',NULL),(197,'{lang}',NULL),(198,'{lang}',NULL),(199,'{lang}',NULL),(200,'{lang}',NULL),(201,'{lang}',NULL),(202,'{lang}',NULL),(203,'{lang}',NULL),(204,'{lang}',NULL),(205,'{lang}',NULL),(206,'{lang}',NULL),(207,'{lang}',NULL),(208,'{lang}',NULL),(209,'{lang}',NULL),(210,'{lang}',NULL),(211,'{lang}',NULL),(212,'{lang}',NULL),(213,'{lang}',NULL),(214,'{lang}',NULL),(215,'{lang}',NULL),(216,'{lang}',NULL),(217,'{lang}',NULL),(218,'{lang}',NULL),(219,'{lang}',NULL),(220,'{lang}',NULL),(221,'{lang}',NULL),(222,'{lang}',NULL),(223,'{lang}',NULL),(224,'{lang}',NULL),(225,'{lang}',NULL),(226,'{lang}',NULL),(227,'{lang}',NULL),(228,'{lang}',NULL),(229,'{lang}',NULL),(230,'{lang}',NULL),(231,'{lang}',NULL),(232,'{lang}',NULL),(233,'{lang}',NULL),(234,'{lang}',NULL),(235,'{lang}',NULL),(236,'{lang}',NULL),(237,'{lang}',NULL),(238,'{lang}',NULL),(239,'{lang}',NULL),(240,'{lang}',NULL),(241,'{lang}',NULL),(242,'{lang}',NULL),(243,'{lang}',NULL),(244,'{lang}',NULL),(245,'{lang}',NULL),(246,'{lang}',NULL),(247,'{lang}',NULL),(248,'{lang}',NULL),(249,'{lang}',NULL),(250,'{lang}',NULL),(251,'{lang}',NULL),(252,'{lang}',NULL),(253,'{lang}',NULL),(254,'{lang}',NULL),(255,'{lang}',NULL),(256,'{lang}',NULL),(257,'{lang}',NULL),(258,'{lang}',NULL),(259,'{lang}',NULL),(260,'{lang}',NULL),(261,'{lang}',NULL),(262,'{lang}',NULL),(263,'{lang}',NULL),(264,'{lang}',NULL),(265,'{lang}',NULL),(266,'{lang}',NULL),(267,'{lang}',NULL),(268,'{lang}',NULL),(269,'{lang}',NULL),(270,'{lang}',NULL),(271,'{lang}',NULL),(272,'{lang}',NULL),(273,'{lang}',NULL),(274,'{lang}',NULL),(275,'{lang}',NULL),(276,'{lang}',NULL),(277,'{lang}',NULL),(278,'{lang}',NULL),(279,'{lang}',NULL),(280,'{lang}',NULL),(281,'{lang}',NULL),(282,'{lang}',NULL),(283,'{lang}',NULL),(284,'{lang}',NULL),(285,'{lang}',NULL),(286,'{lang}',NULL),(288,'{lang}',NULL),(289,'{lang}',NULL),(290,'{lang}',NULL),(291,'{lang}',NULL),(292,'{lang}',NULL),(293,'{lang}',NULL),(294,'{lang}',NULL),(295,'{lang}',NULL),(296,'{lang}',NULL),(297,'{lang}',NULL),(298,'{lang}',NULL),(299,'{lang}',NULL),(300,'{lang}',NULL),(301,'{lang}',NULL),(302,'{lang}',NULL),(303,'{lang}',NULL),(304,'{lang}',NULL),(305,'{lang}',NULL),(307,'{lang}',NULL),(308,'{lang}',NULL),(309,'{lang}',NULL),(310,'{lang}',NULL),(311,'{lang}',NULL),(312,'{lang}',NULL),(313,'{lang}',NULL),(314,'{lang}',NULL),(315,'{lang}',NULL),(316,'{lang}',NULL),(317,'{lang}',NULL),(318,'{lang}',NULL),(319,'{lang}',NULL),(320,'{lang}',NULL),(321,'{lang}',NULL),(322,'{lang}',NULL),(323,'{lang}',NULL),(325,'{lang}',NULL),(326,'{lang}',NULL),(327,'{lang}',NULL),(328,'{lang}',NULL),(329,'{lang}',NULL),(330,'{lang}',NULL),(331,'{lang}',NULL),(332,'{lang}',NULL),(333,'{lang}',NULL),(334,'{lang}',NULL),(335,'{lang}',NULL),(336,'{lang}',NULL),(337,'{lang}',NULL),(338,'{lang}',NULL),(339,'{lang}',NULL),(340,'{lang}',NULL),(341,'{lang}',NULL),(342,'{lang}',NULL),(343,'{lang}',NULL),(344,'{lang}',NULL),(345,'{lang}',NULL),(346,'{lang}',NULL),(347,'{lang}',NULL),(348,'{lang}',NULL),(349,'{lang}',NULL),(350,'{lang}',NULL),(351,'{lang}',NULL),(352,'{lang}',NULL),(353,'{lang}',NULL),(354,'{lang}',NULL),(355,'{lang}',NULL),(356,'{lang}',NULL),(357,'{lang}',NULL),(358,'{lang}',NULL),(359,'{lang}',NULL),(360,'{lang}',NULL),(361,'{lang}',NULL),(362,'{lang}',NULL),(363,'{lang}',NULL),(364,'{lang}',NULL),(365,'{lang}',NULL),(366,'{lang}',NULL),(367,'{lang}',NULL),(368,'{lang}',NULL),(369,'{lang}',NULL),(370,'{lang}',NULL),(371,'{lang}',NULL),(372,'{lang}',NULL),(373,'{lang}',NULL),(374,'{lang}',NULL),(375,'{lang}',NULL),(376,'{lang}',NULL),(377,'{lang}',NULL),(378,'{lang}',NULL),(379,'{lang}',NULL),(380,'{lang}',NULL),(381,'{lang}',NULL),(382,'{lang}',NULL),(383,'{lang}',NULL),(384,'{lang}',NULL),(385,'{lang}',NULL),(386,'{lang}',NULL),(387,'{lang}',NULL),(388,'{lang}',NULL),(389,'{lang}',NULL),(390,'{lang}',NULL),(391,'{lang}',NULL),(392,'{lang}',NULL),(393,'{lang}',NULL),(394,'{lang}',NULL),(395,'{lang}',NULL),(396,'{lang}',NULL),(397,'{lang}',NULL),(398,'{lang}',NULL),(399,'{lang}',NULL),(400,'{lang}',NULL),(401,'{lang}',NULL),(402,'{lang}',NULL),(403,'{lang}',NULL),(404,'{lang}',NULL),(405,'{lang}',NULL),(407,'{lang}',NULL),(408,'{lang}',NULL),(411,'{lang}',NULL),(412,'{lang}',NULL),(413,'{lang}',NULL),(414,'{lang}',NULL),(415,'{lang}',NULL),(416,'{lang}',NULL),(417,'{lang}',NULL),(418,'{lang}',NULL),(419,'{lang}',NULL),(420,'{lang}',NULL),(421,'{lang}',NULL),(422,'{lang}',NULL),(423,'{lang}',NULL),(424,'{lang}',NULL),(425,'{lang}',NULL),(426,'{lang}',NULL),(427,'{lang}',NULL),(428,'{lang}',NULL),(429,'{lang}',NULL),(430,'{lang}',NULL),(431,'{lang}',NULL),(432,'{lang}',NULL),(433,'{lang}',NULL),(434,'{lang}',NULL),(435,'{lang}',NULL),(436,'{lang}',NULL),(437,'{lang}',NULL),(438,'{lang}',NULL),(439,'{lang}',NULL),(440,'{lang}',NULL),(441,'{lang}',NULL),(442,'{lang}',NULL),(443,'{lang}',NULL),(444,'{lang}',NULL),(445,'{lang}',NULL),(446,'{lang}',NULL),(447,'{lang}',NULL),(448,'{lang}',NULL),(449,'{lang}',NULL),(450,'{lang}',NULL),(451,'{lang}',NULL),(452,'{lang}',NULL),(453,'{lang}',NULL),(454,'{lang}',NULL),(455,'{lang}',NULL),(456,'{lang}',NULL),(457,'{lang}',NULL),(458,'{lang}',NULL),(459,'{lang}',NULL),(460,'{lang}',NULL),(461,'{lang}',NULL),(462,'{lang}',NULL),(463,'{lang}',NULL),(464,'{lang}',NULL),(465,'{lang}',NULL),(466,'{lang}',NULL),(467,'{lang}',NULL),(468,'{lang}',NULL),(469,'{lang}',NULL),(470,'{lang}',NULL),(471,'{lang}',NULL),(472,'{lang}',NULL),(473,'{lang}',NULL),(474,'{lang}',NULL),(475,'{lang}',NULL),(476,'{lang}',NULL),(477,'{lang}',NULL),(478,'{lang}',NULL),(479,'{lang}',NULL),(480,'{lang}',NULL),(481,'{lang}',NULL),(482,'{lang}',NULL),(483,'{lang}',NULL),(484,'{lang}',NULL),(485,'{lang}',NULL),(486,'{lang}',NULL),(487,'{lang}',NULL),(488,'{lang}',NULL),(489,'{lang}',NULL),(490,'{lang}',NULL),(491,'{lang}',NULL),(492,'{lang}',NULL),(493,'{lang}',NULL),(494,'{lang}',NULL),(495,'{lang}',NULL),(496,'{lang}',NULL),(497,'{lang}',NULL),(498,'{lang}',NULL),(499,'{lang}',NULL),(500,'{lang}',NULL),(501,'{lang}',NULL),(502,'{lang}',NULL),(503,'{lang}',NULL),(504,'{lang}',NULL),(505,'{lang}',NULL),(506,'{lang}',NULL),(507,'{lang}',NULL),(508,'{lang}',NULL),(509,'{lang}',NULL),(510,'{lang}',NULL),(511,'{lang}',NULL),(512,'{lang}',NULL),(513,'{lang}',NULL),(514,'{lang}',NULL),(515,'{lang}',NULL),(516,'{lang}',NULL),(517,'{lang}',NULL),(518,'{lang}',NULL),(519,'{lang}',NULL),(520,'{lang}',NULL),(521,'{lang}',NULL),(522,'{lang}',NULL),(524,'{lang}',NULL),(525,'{lang}',NULL),(526,'{lang}',NULL),(527,'{lang}',NULL),(529,'{lang}',NULL),(530,'{lang}',NULL),(531,'{lang}',NULL),(532,'{lang}',NULL),(533,'{lang}',NULL),(534,'{lang}',NULL),(535,'{lang}',NULL),(536,'{lang}',NULL),(537,'{lang}',NULL),(538,'{lang}',NULL),(539,'{lang}',NULL),(540,'{lang}',NULL),(541,'{lang}',NULL),(542,'{lang}',NULL),(543,'{lang}',NULL),(544,'{lang}',NULL),(545,'{lang}',NULL),(546,'{lang}',NULL),(547,'{lang}',NULL),(548,'{lang}',NULL),(549,'{lang}',NULL),(550,'{lang}',NULL),(551,'{lang}',NULL),(552,'{lang}',NULL),(553,'{lang}',NULL),(554,'{lang}',NULL),(555,'{lang}',NULL),(556,'{lang}',NULL),(557,'{lang}',NULL),(558,'{lang}',NULL),(559,'{lang}',NULL),(560,'{lang}',NULL),(561,'{lang}',NULL),(562,'{lang}',NULL),(563,'{lang}',NULL),(564,'{lang}',NULL),(565,'{lang}',NULL),(566,'{lang}',NULL),(567,'{lang}',NULL),(568,'{lang}',NULL),(569,'{lang}',NULL),(570,'{lang}',NULL),(571,'{lang}',NULL),(572,'{lang}',NULL),(573,'{lang}',NULL),(574,'{lang}',NULL),(575,'{lang}',NULL),(576,'{lang}',NULL),(577,'{lang}',NULL),(578,'{lang}',NULL),(579,'{lang}',NULL),(580,'{lang}',NULL),(582,'{lang}',NULL),(583,'{lang}',NULL),(584,'{lang}',NULL),(585,'{lang}',NULL),(586,'{lang}',NULL),(587,'{lang}',NULL),(588,'{lang}',NULL),(589,'{lang}',NULL),(590,'{lang}',NULL),(591,'{lang}',NULL),(592,'{lang}',NULL),(593,'{lang}',NULL),(594,'{lang}',NULL),(595,'{lang}',NULL),(596,'{lang}',NULL),(597,'{lang}',NULL),(598,'{lang}',NULL),(599,'{lang}',NULL),(600,'{lang}',NULL),(601,'{lang}',NULL),(602,'{lang}',NULL),(603,'{lang}',NULL),(604,'{lang}',NULL),(605,'{lang}',NULL),(606,'{lang}',NULL),(607,'{lang}',NULL),(608,'{lang}',NULL),(609,'{lang}',NULL),(610,'{lang}',NULL),(611,'{lang}',NULL),(613,'{lang}',NULL),(614,'{lang}',NULL),(615,'{lang}',NULL),(616,'{lang}',NULL),(617,'{lang}',NULL),(618,'{lang}',NULL),(619,'{lang}',NULL),(620,'{lang}',NULL),(622,'{lang}',NULL),(623,'{lang}',NULL),(624,'{lang}',NULL),(625,'{lang}',NULL),(626,'{lang}',NULL),(627,'{lang}',NULL),(628,'{lang}',NULL),(629,'{lang}',NULL),(630,'{lang}',NULL),(631,'{lang}',NULL),(632,'{lang}',NULL),(633,'{lang}',NULL),(634,'{lang}',NULL),(635,'{lang}',NULL),(636,'{lang}',NULL),(637,'{lang}',NULL),(638,'{lang}',NULL),(639,'{lang}',NULL),(640,'{lang}',NULL),(641,'{lang}',NULL),(642,'{lang}',NULL),(643,'{lang}',NULL),(644,'{lang}',NULL),(645,'{lang}',NULL),(646,'{lang}',NULL),(647,'{lang}',NULL),(648,'{lang}',NULL),(649,'{lang}',NULL),(650,'{lang}',NULL),(651,'{lang}',NULL),(652,'{lang}',NULL),(653,'{lang}',NULL),(654,'{lang}',NULL),(656,'{lang}',NULL),(657,'{lang}',NULL),(658,'{lang}',NULL),(659,'{lang}',NULL),(660,'{lang}',NULL),(661,'{lang}',NULL),(662,'{lang}',NULL),(663,'{lang}',NULL),(664,'{lang}',NULL),(665,'{lang}',NULL),(666,'{lang}',NULL),(667,'{lang}',NULL),(668,'{lang}',NULL),(669,'{lang}',NULL),(670,'{lang}',NULL),(671,'{lang}',NULL),(672,'{lang}',NULL),(673,'{lang}',NULL),(674,'{lang}',NULL),(675,'{lang}',NULL),(676,'{lang}',NULL),(677,'{lang}',NULL),(678,'{lang}',NULL),(679,'{lang}',NULL),(680,'{lang}',NULL),(681,'{lang}',NULL),(682,'{lang}',NULL),(686,'{lang}',NULL),(687,'{lang}',NULL),(688,'{lang}',NULL),(689,'{lang}',NULL),(690,'{lang}',NULL),(691,'{lang}',NULL),(692,'{lang}',NULL),(693,'{lang}',NULL),(694,'{lang}',NULL),(695,'{lang}',NULL),(696,'{lang}',NULL),(697,'{lang}',NULL),(698,'{lang}',NULL),(699,'{lang}',NULL),(700,'{lang}',NULL),(701,'{lang}',NULL),(702,'{lang}',NULL),(703,'{lang}',NULL),(704,'{lang}',NULL),(705,'{lang}',NULL),(706,'{lang}',NULL),(707,'{lang}',NULL),(708,'{lang}',NULL),(709,'{lang}',NULL),(710,'{lang}',NULL),(711,'{lang}',NULL),(712,'{lang}',NULL),(713,'{lang}',NULL),(714,'{lang}',NULL),(715,'{lang}',NULL),(716,'{lang}',NULL),(717,'{lang}',NULL),(718,'{lang}',NULL),(719,'{lang}',NULL),(720,'{lang}',NULL),(721,'{lang}',NULL),(722,'{lang}',NULL),(723,'{lang}',NULL),(724,'{lang}',NULL),(725,'{lang}',NULL),(726,'{lang}',NULL),(727,'{lang}',NULL),(728,'{lang}',NULL),(729,'{lang}',NULL),(730,'{lang}',NULL),(731,'{lang}',NULL),(732,'{lang}',NULL),(733,'{lang}',NULL),(734,'{lang}',NULL),(735,'{lang}',NULL),(736,'{lang}',NULL),(737,'{lang}',NULL),(738,'{lang}',NULL),(739,'{lang}',NULL),(740,'{lang}',NULL),(741,'{lang}',NULL),(742,'{lang}',NULL),(743,'{lang}',NULL),(744,'{lang}',NULL),(745,'{lang}',NULL),(746,'{lang}',NULL),(747,'{lang}',NULL),(748,'{lang}',NULL),(749,'{lang}',NULL),(750,'{lang}',NULL),(751,'{lang}',NULL),(753,'{lang}',NULL),(754,'{lang}',NULL),(755,'{lang}',NULL),(756,'{lang}',NULL),(757,'{lang}',NULL),(758,'{lang}',NULL),(759,'{lang}',NULL),(760,'{lang}',NULL),(761,'{lang}',NULL),(762,'{lang}',NULL),(764,'{lang}',NULL),(765,'{lang}',NULL),(766,'{lang}',NULL),(767,'{lang}',NULL),(770,'{lang}',NULL),(771,'{lang}',NULL),(772,'{lang}',NULL),(773,'{lang}',NULL),(774,'{lang}',NULL),(775,'{lang}',NULL),(777,'{lang}',NULL),(778,'{lang}',NULL),(783,'{lang}',NULL),(784,'{lang}',NULL),(785,'{lang}',NULL),(787,'{lang}',NULL),(788,'{lang}',NULL),(789,'{lang}',NULL),(790,'{lang}',NULL),(791,'{lang}',NULL),(792,'{lang}',NULL),(793,'{lang}',NULL);
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `motion_tag`
--

DROP TABLE IF EXISTS `motion_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `motion_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `abr` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=554 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `panel`
--

DROP TABLE IF EXISTS `panel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `panel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `strength` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tournament_id` int(10) unsigned NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '1',
  `is_preset` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_panel_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_panel_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8736 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publish_tab_speaker`
--

DROP TABLE IF EXISTS `publish_tab_speaker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publish_tab_speaker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(10) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `enl_place` int(11) NOT NULL,
  `esl_place` int(11) DEFAULT NULL,
  `cache_results` text NOT NULL,
  `speaks` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_publish_tab_speaker_user1_idx` (`user_id`),
  KEY `fk_publish_tab_speaker_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_publish_tab_speaker_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publish_tab_speaker_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7996 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publish_tab_team`
--

DROP TABLE IF EXISTS `publish_tab_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publish_tab_team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(10) unsigned NOT NULL,
  `team_id` int(10) unsigned NOT NULL,
  `enl_place` int(11) NOT NULL,
  `esl_place` varchar(45) DEFAULT NULL,
  `cache_results` text NOT NULL,
  `speaks` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_publish_tab_team_team1_idx` (`team_id`),
  KEY `fk_publish_tab_team_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_publish_tab_team_team1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_publish_tab_team_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4190 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `apply_T2C` tinyint(1) NOT NULL DEFAULT '0',
  `apply_C2W` tinyint(1) NOT NULL DEFAULT '0',
  `apply_W2C` tinyint(1) NOT NULL DEFAULT '0',
  `param` text,
  `help` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=280 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `result`
--

DROP TABLE IF EXISTS `result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `result` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `debate_id` int(10) unsigned NOT NULL,
  `og_A_speaks` tinyint(4) NOT NULL,
  `og_B_speaks` tinyint(4) NOT NULL,
  `og_irregular` tinyint(4) NOT NULL DEFAULT '0',
  `og_place` tinyint(4) NOT NULL,
  `oo_A_speaks` tinyint(4) NOT NULL,
  `oo_B_speaks` tinyint(4) NOT NULL,
  `oo_irregular` tinyint(4) NOT NULL DEFAULT '0',
  `oo_place` tinyint(4) NOT NULL,
  `cg_A_speaks` tinyint(4) NOT NULL,
  `cg_B_speaks` tinyint(4) NOT NULL,
  `cg_irregular` tinyint(4) NOT NULL DEFAULT '0',
  `cg_place` tinyint(4) NOT NULL,
  `co_A_speaks` tinyint(4) NOT NULL,
  `co_B_speaks` tinyint(4) NOT NULL,
  `co_irregular` tinyint(4) NOT NULL DEFAULT '0',
  `co_place` tinyint(4) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entered_by_id` int(11) unsigned DEFAULT NULL,
  `checked` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `debate_id_UNIQUE` (`debate_id`),
  KEY `fk_result_debate1_idx` (`debate_id`),
  KEY `fk_result_user1_idx` (`entered_by_id`),
  CONSTRAINT `fk_result_debate1` FOREIGN KEY (`debate_id`) REFERENCES `debate` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_result_user1` FOREIGN KEY (`entered_by_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4574 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `round`
--

DROP TABLE IF EXISTS `round`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `round` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `tournament_id` int(10) unsigned NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  `motion` text NOT NULL,
  `infoslide` text,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `displayed` tinyint(1) NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `prep_started` datetime DEFAULT NULL,
  `finished_time` datetime DEFAULT NULL,
  `lastrun_temp` float NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_round_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_round_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=542 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `society`
--

DROP TABLE IF EXISTS `society`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `society` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) DEFAULT NULL,
  `abr` varchar(45) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `adr_UNIQUE` (`abr`),
  KEY `fk_society_country1_idx` (`country_id`),
  CONSTRAINT `fk_society_country1` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2341 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `source_message`
--

DROP TABLE IF EXISTS `source_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `source_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(32) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=797 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `source_message`
--

LOCK TABLES `source_message` WRITE;
/*!40000 ALTER TABLE `source_message` DISABLE KEYS */;
INSERT INTO `source_message` VALUES (1,'app','ID'),(2,'app','Name'),(3,'app','Import {modelClass}'),(4,'app','Import'),(5,'app','Societies'),(6,'app','Update'),(7,'app','Delete'),(8,'app','Are you sure you want to delete this item?'),(9,'app','Update {modelClass}: '),(10,'app','Merge Society \'{society}\' into ...'),(11,'app','Select a Mother-Society ...'),(12,'app','Create {modelClass}'),(13,'app','View {modelClass}'),(14,'app','Update {modelClass}'),(15,'app','Delete {modelClass}'),(16,'app','Add new element'),(17,'app','Reload content'),(18,'app','Import via CSV File'),(19,'app','Create'),(20,'app','Languages'),(21,'app','Create Language'),(22,'app','Special Needs'),(23,'app','Motion Tags'),(24,'app','Merge Motion Tag \'{tag}\' into ...'),(25,'app','Select a Mother-Tag ...'),(26,'app','Search'),(27,'app','Reset'),(28,'app','Create Motion Tag'),(29,'app','API'),(30,'app','Master'),(31,'app','Messages'),(32,'app','Create Message'),(33,'app','{count} Tags switched'),(34,'app','File Syntax Wrong'),(35,'app','Motion Tag'),(36,'app','Round'),(37,'app','Abbreviation'),(38,'app','Amount'),(39,'app','Opening Government'),(40,'app','Opening Opposition'),(41,'app','Closing Government'),(42,'app','Closing Opposition'),(43,'app','Team'),(44,'app','Active'),(45,'app','Tournament'),(46,'app','Speaker'),(47,'app','Society'),(48,'app','Swing Team'),(49,'app','Language Status'),(50,'app','Everything normal'),(51,'app','Was replaced by swing team'),(52,'app','Speaker {letter} didn\'t show up'),(53,'app','Key'),(54,'app','Label'),(55,'app','Value'),(56,'app','Team position can\'t be blank'),(57,'app','Outround'),(58,'app','Tabmaster User'),(59,'app','User'),(60,'app','ENL Place'),(61,'app','ESL Place'),(62,'app','Cache Results'),(63,'app','Fullname'),(64,'app','Abbrevation'),(65,'app','City'),(66,'app','Country'),(67,'app','OG Team'),(68,'app','OO Team'),(69,'app','CG Team'),(70,'app','CO Team'),(71,'app','Panel'),(72,'app','Venue'),(73,'app','OG Feedback'),(74,'app','OO Feedback'),(75,'app','CG Feedback'),(76,'app','CO Feedback'),(77,'app','Time'),(78,'app','Motion'),(79,'app','Language'),(80,'app','Date'),(81,'app','Infoslide'),(82,'app','Link'),(83,'app','By User'),(84,'app','Translation'),(85,'app','Adjudicator'),(86,'app','Answer'),(87,'app','Feedback'),(88,'app','Question'),(89,'app','Created'),(90,'app','Running'),(91,'app','Closed'),(92,'app','Hidden'),(93,'app','Hosted by'),(94,'app','Tournament Name'),(95,'app','Start Date'),(96,'app','End Date'),(97,'app','Timezone'),(98,'app','Logo'),(99,'app','URL Slug'),(100,'app','Tab Algorithm'),(101,'app','Expected number of rounds'),(102,'app','Show ESL Ranking'),(103,'app','Is there a grand final'),(104,'app','Is there a semifinal'),(105,'app','Is there a quarterfinal'),(106,'app','Is there a octofinal'),(107,'app','Access Token'),(108,'app','Participant Badge'),(109,'app','Alpha 2'),(110,'app','Alpha 3'),(111,'app','Region'),(112,'app','Language Code'),(113,'app','Coverage'),(114,'app','Last Update'),(115,'app','Strength'),(116,'app','can Chair'),(117,'app','are Watched'),(118,'app','Not Rated'),(121,'app','Can Judge'),(122,'app','Decent'),(124,'app','High Potential'),(125,'app','Chair'),(126,'app','Good'),(127,'app','Breaking'),(128,'app','Chief Adjudicator'),(129,'app','Starting'),(130,'app','Ending'),(131,'app','Speaker Points'),(132,'app','This email address has already been taken.'),(133,'app','Debater'),(134,'app','Auth Key'),(135,'app','Password Hash'),(136,'app','Password Reset Token'),(137,'app','Email'),(138,'app','Account Role'),(139,'app','Account Status'),(140,'app','Last Change'),(141,'app','First Name'),(142,'app','Last Name'),(143,'app','Picture'),(144,'app','Placeholder'),(145,'app','Tabmaster'),(146,'app','Admin'),(147,'app','Deleted'),(148,'app','Not revealing'),(149,'app','Female'),(150,'app','Male'),(151,'app','Other'),(152,'app','mixed'),(153,'app','Not yet set'),(154,'app','Interview needed'),(155,'app','EPL'),(157,'app','ESL'),(158,'app','English as a second language'),(159,'app','EFL'),(160,'app','English as a foreign language'),(161,'app','Not set'),(162,'app','Error saving InSociety Relation for {user_name}'),(163,'app','Error Saving User {user_name}'),(164,'app','{tournament_name}: User Account for {user_name}'),(165,'app','This URL-Slug is not allowed.'),(166,'app','Published'),(167,'app','Displayed'),(168,'app','Started'),(169,'app','Judging'),(170,'app','Finished'),(171,'app','Main'),(172,'app','Novice'),(173,'app','Final'),(174,'app','Semifinal'),(175,'app','Quarterfinal'),(176,'app','Octofinal'),(177,'app','Round #{num}'),(178,'app','Inround'),(179,'app','Energy'),(180,'app','Info Slide'),(181,'app','PrepTime started'),(182,'app','Last Temperature'),(183,'app','ms to calculate'),(184,'app','Not enough Teams to fill a single room - (active: {teams_count})'),(185,'app','At least two Adjudicators are necessary - (active: {count_adju})'),(186,'app','Amount of active Teams must be divided by 4 ;) - (active: {count_teams})'),(187,'app','Not enough active Rooms (active: {active_rooms} required: {required})'),(188,'app','Not enough adjudicators (active: {active}  min-required: {required})'),(189,'app','Not enough free adjudicators with this preset panel configuration. (fillable rooms: {active}  min-required: {required})'),(190,'app','Can\'t save Panel! Error: {message}'),(191,'app','Can\'t save Debate! Error: {message}'),(192,'app','Can\'t save debate! Errors:<br>{errors}'),(193,'app','No Debate #{num} found to update'),(195,'app','Type'),(196,'app','Parameter if needed'),(197,'app','Apply to Team -> Chair'),(198,'app','Apply to Chair -> Wing'),(199,'app','Apply to Wing -> Chair'),(200,'app','Not Good'),(201,'app','Very Good'),(202,'app','Excellent'),(203,'app','Star Rating (1-5) Field'),(204,'app','Short Text Field'),(205,'app','Long Text Field'),(206,'app','Number Field'),(207,'app','Checkbox List Field'),(208,'app','Debate'),(209,'app','Feedback To ID'),(210,'app','Adjudicator Strike From ID'),(211,'app','Adjudicator Strike To ID'),(212,'app','Group'),(213,'app','Active Room'),(214,'app','Wing'),(215,'app','Used'),(216,'app','Is Preset Panel'),(217,'app','Panel #{id} has {amount} chairs'),(218,'app','Category'),(219,'app','Message'),(220,'app','Function'),(221,'app','Legacy Motion'),(222,'app','Questions'),(223,'app','Clash With'),(224,'app','Reason'),(225,'app','Team Clash'),(226,'app','Adjudicator Clash'),(227,'app','No type found'),(228,'app','OG A Speaks'),(229,'app','OG B Speaks'),(230,'app','OG Place'),(231,'app','OO A Speaks'),(232,'app','OO B Speaks'),(233,'app','OO Place'),(234,'app','CG A Speaks'),(235,'app','CG B Speaks'),(236,'app','CG Place'),(237,'app','CO A Speaks'),(238,'app','CO B Speaks'),(239,'app','CO Place'),(240,'app','Checked'),(241,'app','Entered by User ID'),(242,'app','Equal place exist'),(243,'app','Ironman by'),(244,'app','CA'),(245,'app','Password reset token cannot be blank.'),(246,'app','Wrong password reset token.'),(247,'app','Comma ( , ) separated file'),(248,'app','Semicolon ( ; ) separated file'),(249,'app','Tab ( ->| ) separated file'),(250,'app','CSV File'),(251,'app','Delimiter'),(252,'app','Mark as Test Data Import (prohibits Email sending)'),(253,'app','Username'),(254,'app','Profile Picture'),(255,'app','Current Society'),(256,'app','With which gender do you identify yourself the most'),(257,'app','This URL is not allowed.'),(258,'app','{adju} checked in!'),(259,'app','{adju} already checked in!'),(260,'app','{id} number not valid! Not an Adjudicator!'),(261,'app','{speaker} checked in!'),(262,'app','{speaker} already checked in!'),(263,'app','{id} number not valid! Not a Team!'),(264,'app','Not a valid input'),(265,'app','Verification Code'),(266,'app','DebReg'),(267,'app','Password reset for {user}'),(268,'app','User not found with this Email'),(269,'app','Add {object}'),(270,'app','Create Society'),(271,'app','Hey cool! You entered an unknown Society!'),(272,'app','Before we can link you, can you please complete the information about your Society:'),(273,'app','Search for a country ...'),(274,'app','Add new Society'),(275,'app','Search for a society ...'),(276,'app','Enter start date ...'),(277,'app','Enter ending date if applicable ...'),(278,'app','Language Officers'),(279,'app','Officer'),(280,'app','Any {object} ...'),(281,'app','Language Status Review'),(282,'app','Status'),(283,'app','Request an interview'),(284,'app','Set ENL'),(285,'app','Set ESL'),(286,'app','Language Officer'),(288,'app','Add'),(289,'app','Search for a User ...'),(290,'app','Checkin'),(291,'app','Submit'),(292,'app','Generate Badges'),(293,'app','Only do for User ...'),(294,'app','Print Badges'),(295,'app','Generate Barcodes'),(296,'app','Search for a user ... or leave blank'),(297,'app','Print Barcodes'),(298,'app','Teams'),(299,'app','Team Name'),(300,'app','Speaker A'),(301,'app','Speaker B'),(302,'app','Make it so'),(303,'app','Motion:'),(304,'app','Panel:'),(305,'app','Toogle Active'),(307,'app','Search for a user ...'),(308,'app','Tournaments'),(309,'app','Overview'),(310,'app','Motions'),(311,'app','Team Tab'),(312,'app','Speaker Tab'),(313,'app','Out-Rounds'),(314,'app','Breaking Adjudicators'),(315,'app','Make it so!'),(316,'app','DebReg Tournament'),(317,'app','Show old tournaments'),(318,'app','Adjudicators'),(319,'app','Result'),(320,'app','together with {teammate}'),(321,'app','as ironman'),(322,'app','You are registered as team <br> \'{team}\' {with} for {society}'),(323,'app','You are registered as adjudicator for {society}'),(325,'app','Registration Information'),(326,'app','Enter Information'),(327,'app','Round #{num} Info'),(328,'app','You are <b>{pos}</b> in room <b>{room}</b>.'),(329,'app','Round starts at: <b>{time}</b>'),(330,'app','InfoSlide'),(331,'app','Round #{num} Teams'),(332,'app','My super awesome IV ... e.g. Vienna IV'),(333,'app','Select the Convenors ...'),(334,'app','Enter start date / time ...'),(335,'app','Enter the end date / time ...'),(336,'app','Chief Adjudicators'),(337,'app','Choose your CAs ...'),(338,'app','Choose your Tabmaster ...'),(339,'app','Tournament Archive'),(340,'app','The above error occurred while the Web server was processing your request.'),(341,'app','Please contact us if you think this is a server error. Thank you.'),(342,'app','Reset password'),(343,'app','Please choose your new password:'),(344,'app','Save'),(345,'app','Signup'),(346,'app','Please fill out the following fields to signup:'),(347,'app','Most tournament allocation algorithm in this system try also to take panel diversity into account.\n					For this to work at all, we would politely ask to choose an option from this list.\n					We are aware that not every personal preference can be matched by our choises and apologise for missing options.\n					If you feel that none of the options is in any applicable please choose <Not Revealing>.\n					This option will never be shown to any user and is only for calculation purposes only!'),(348,'app','Login'),(349,'app','Please fill out the following fields to login:'),(350,'app','If you forgot your password you can {resetIt}'),(351,'app','reset it'),(352,'app','Request password reset'),(353,'app','Please fill out your email. A link to reset password will be sent there.'),(354,'app','Send'),(355,'app','Venue CSV'),(356,'app','Adjudicator CSV'),(357,'app','Team CSV'),(358,'app','created'),(359,'app','Sample Venue CSV'),(360,'app','Sample Team CSV'),(361,'app','Sample Adjudicator CSV'),(362,'app','Current BP Debate {count, plural, =0{Tournament} =1{Tournament} other{Tournaments}}'),(363,'app','Welcome to {appName}!'),(364,'app','View Tournaments'),(365,'app','Create Tournament'),(366,'app','Before we can register you can you please complete the information about your Society:'),(367,'app','Contact'),(368,'app','Preset Panel #'),(369,'app','Panels'),(370,'app','Average Panel Strength'),(371,'app','Create Panel'),(372,'app','Preset Panels for next round'),(373,'app','Add {object} ...'),(374,'app','Place'),(375,'app','Team Points'),(376,'app','#{number}'),(377,'app','No Breaking Adjudicators defined'),(378,'app','Speaker Points Distribution'),(379,'app','Run'),(380,'app','Opening Gov'),(381,'app','Opening Opp'),(382,'app','Closing Gov'),(383,'app','Closing Opp'),(384,'app','Show Info Slide'),(385,'app','Show Motion'),(386,'app','Missing User'),(387,'app','Mark missing as inactive'),(388,'app','Results'),(389,'app','Runner View for Round #{number}'),(390,'app','Auto Update <i id=\'pjax-status\' class=\'\'></i>'),(391,'app','Update individual clash'),(392,'app','Individual Clash'),(393,'app','Not every debater is yet in the system. :)'),(394,'app','Create clash'),(395,'app','Update clash'),(396,'app','Energy Configs'),(397,'app','Update Energy Value'),(398,'app','Round #{number}'),(399,'app','Rounds'),(400,'app','Actions'),(401,'app','Publish Tab'),(402,'app','Retry to generate Draw'),(403,'app','Update Round'),(404,'app','Toggle Dropdown'),(405,'app','Continue Improving by'),(407,'app','Are you sure you want to re-draw the round? All information will be lost!'),(408,'app','Print Ballots'),(411,'app','Round Status'),(412,'app','Average Energy'),(413,'app','Creation Time'),(414,'app','Color Palette'),(415,'app','Gender'),(416,'app','Regions'),(417,'app','Points'),(418,'app','Loading ...'),(419,'app','View Feedback'),(420,'app','View User'),(421,'app','Switch venue {venue} with'),(422,'app','Select a Venue ...'),(423,'app','Update {modelClass} #{number}'),(424,'app','Energy Level'),(425,'app','Select a Team ...'),(426,'app','Select a Language ...'),(427,'app','Select an Adjudicator ...'),(428,'app','Switch Adjudicators'),(429,'app','Switch this Adjudicator ...'),(430,'app','with'),(431,'app','with this one ...'),(432,'app','Search for a Motion tag ...'),(433,'app','Rank'),(434,'app','Total'),(435,'app','Debate ID'),(436,'app','Room'),(437,'app','Outrounds'),(438,'app','Motion Archive'),(439,'app','Third-Party\n			Motion'),(440,'app','Your amazing IV'),(441,'app','Enter date ...'),(442,'app','Round #1 or Final'),(443,'app','THW ...'),(444,'app','http://give.credit.where.credit.is.due.com'),(445,'app','Enter {modelClass} Manual'),(446,'app','Options'),(447,'app','Continue'),(448,'app','No results yet!'),(449,'app','Results in Room: {venue}'),(450,'app','Results for {venue}'),(451,'app','Table View'),(452,'app','Results for {label}'),(453,'app','Switch to Venue View'),(454,'app','Swing Team Score'),(455,'app','View Result Details'),(456,'app','Correct Result'),(457,'app','Venue View'),(458,'app','Switch to Tableview'),(459,'app','Confirm Data for {venue}'),(460,'app','start over'),(461,'app','Round {number}'),(462,'app','Thank you'),(463,'app','Thank you!'),(464,'app','Results successfully saved'),(465,'app','Speeeed Bonus!'),(466,'app','Hurry up! Chop Chop!'),(467,'app','Bummer! Last one!'),(468,'app','You are <b>#{place}</b> from {max}'),(469,'app','Enter Feedback'),(470,'app','Return to Tournament'),(471,'app','Feedbacks'),(472,'app','Target Adjudicator'),(473,'app','Adjudicator name ...'),(474,'app','Adjudicator Feedback'),(475,'app','Submit Feedback'),(476,'app','{tournament} - Language Officer'),(477,'app','Review Language Status'),(478,'app','made with secret alien technology'),(479,'app','Report a Bug'),(480,'app','{tournament} - Manager'),(481,'app','List Venues'),(482,'app','Create Venue'),(483,'app','Import Venue'),(484,'app','List Teams'),(485,'app','Create Team'),(486,'app','Import Team'),(487,'app','Strike Team'),(488,'app','List Adjudicators'),(489,'app','Create Adjudicator'),(490,'app','Import Adjudicator'),(491,'app','View Preset Panels'),(492,'app','Create Preset Panel'),(493,'app','Strike Adjudicator'),(494,'app','Update Tournament'),(495,'app','Display Team Tab'),(496,'app','Display Speaker Tab'),(497,'app','Display Outrounds'),(498,'app','Publishing the Tab will close and archive the tournament!! Are you sure you want to continue?'),(499,'app','Missing Users'),(500,'app','Checkin Form'),(501,'app','Print Badgets'),(502,'app','Reset Checkin'),(503,'app','Are you sure you want to reset the checkin?'),(504,'app','Sync with DebReg'),(505,'app','Migrate to Tabbie 1'),(506,'app','Extreme caution young padawan!'),(507,'app','List Rounds'),(508,'app','Create Round'),(509,'app','Energy Options'),(510,'app','List Results'),(511,'app','Insert Ballot'),(512,'app','Correct Cache'),(513,'app','Setup Questions'),(514,'app','Every Feedback'),(515,'app','Feedback on Adjudicator'),(516,'app','About'),(517,'app','How-To'),(518,'app','Users'),(519,'app','Register'),(520,'app','{user}\'s Profile'),(521,'app','{user}\'s History'),(522,'app','Logout'),(524,'app','Update {label}'),(525,'app','Next Step'),(526,'app','Room {number}'),(527,'app','Venues'),(529,'app','Strikes'),(530,'app','Import Strikes'),(531,'app','Accept'),(532,'app','Deny'),(533,'app','Search for a Team ...'),(534,'app','Search for an Adjudicator ...'),(535,'app','Strike Adjudicators'),(536,'app','Create Additional {modelClass}'),(537,'app','Update team'),(538,'app','Delete team'),(539,'app','Search for an From Adjudicator ...'),(540,'app','Search for an To Adjudicator ...'),(541,'app','Strike Team with Adjudicator'),(542,'app','Update Team'),(543,'app','Delete Team'),(544,'app','{modelClass}\'s History'),(545,'app','History'),(546,'app','Team Review'),(547,'app','EPL Place'),(548,'app','Team Speaker Points'),(549,'app','No published tab available at the moment'),(550,'app','Can chair'),(551,'app','Should not chair'),(552,'app','Break'),(553,'app','Not breaking'),(554,'app','Watched'),(555,'app','Unwatched'),(556,'app','Toogle Watch'),(557,'app','Toogle Breaking'),(558,'app','Reset watcher flag'),(559,'app','Search for a {object} ...'),(560,'app','Chaired'),(561,'app','Pointer'),(562,'app','Update User profile'),(563,'app','Individual Clashes'),(564,'app','Update Clash Info'),(565,'app','Delete Clash'),(566,'app','No clash known to the system.'),(567,'app','Debate Society History'),(568,'app','Add new society to history'),(569,'app','still active'),(570,'app','Update Society Info'),(571,'app','Force new password for {name}'),(572,'app','Cancel'),(573,'app','Search for a tournament ...'),(574,'app','Set new Password'),(575,'app','Update User'),(576,'app','Delete User'),(577,'app','Create User'),(578,'app','No condition matched'),(579,'app','Did not pass panel check old: {old} / new: {new}'),(580,'app','Can\'t save {object}! Error: {message}'),(582,'app','No File available'),(583,'app','No matching records found'),(584,'app','Thank you for your submission.'),(585,'app','Error saving Panel:'),(586,'app','Panel deleted'),(587,'app','Welcome! This is your first login, please check that your information are correct'),(588,'app','A new society has been saved'),(589,'app','There has been an error receiving your previous input. Please enter them again.'),(590,'app','User registered! Welcome {user}'),(591,'app','Login failed'),(592,'app','Check your email for further instructions.'),(593,'app','Sorry, we are unable to reset password for email provided.<br>{message}'),(594,'app','New password was saved.'),(595,'app','New Passwort set'),(596,'app','Error saving new password'),(597,'app','Society connection not saved'),(598,'app','User successfully saved!'),(599,'app','User not saved!'),(600,'app','Society Connection not saved!'),(601,'app','User successfully updated!'),(602,'app','Please enter a new password!'),(603,'app','User deleted'),(604,'app','Cant\'t delete because of {error}'),(605,'app','Cound\'t delete because already in use. <br> {ex}'),(606,'app','Checking Flags reset'),(607,'app','There was no need for a reset'),(608,'app','Please set breaking adjudicators first - use the star icon in the action column.'),(609,'app','Couldn\'t create Team.'),(610,'app','Error saving Society Relation for {society}'),(611,'app','Error saving team {name}!'),(613,'app','Can\'t save Tournament connection'),(614,'app','Can\'t delete Question'),(615,'app','Society connection successfully created'),(616,'app','Society could not be saved'),(617,'app','Error in wakeup'),(618,'app','Society Info updated'),(619,'app','Tab published and tournament closed. Go have a drink!'),(620,'app','Chair in Panel not found - type wrong?'),(622,'app','No valid type'),(623,'app','{object} successfully submitted'),(624,'app','{object} created'),(625,'app','Individual clash'),(626,'app','Individual clash could not be saved'),(627,'app','{object} updated'),(628,'app','{object} could not be saved'),(629,'app','{object} deleted'),(630,'app','{tournament} on Tabbie2'),(631,'app','{tournament} is taking place from {start} to {end} hosted by {host} in {country}'),(632,'app','Tournament successfully created'),(633,'app','Tournament created but Energy config failed!'),(634,'app','Can\'t save Tournament!'),(635,'app','DebReg Syncing successful'),(636,'app','Venues switched'),(637,'app','Error while switching'),(638,'app','New Venues set'),(639,'app','Error while setting new venue'),(640,'app','Can\'t create Round: Amount of Teams is not dividable by 4'),(641,'app','Successfully redrawn in {secs}s'),(642,'app','Improved Energy by {diff} points in {secs}s'),(643,'app','Adjudicator {n1} and {n2} switched'),(644,'app','Could not switch because: {a_panel}<br>and<br>{b_panel}'),(645,'app','Show Round {number}'),(646,'app','No debates found in that round'),(647,'app','Not a valid language options in params'),(648,'app','Team upgraded to {status}'),(649,'app','Language Settings saved'),(650,'app','Error saving Language Settings'),(651,'app','User not found!'),(652,'app','{object} successfully added'),(653,'app','Successfully deleted'),(654,'app','File Syntax Wrong! Expecting 3 columns'),(656,'app','Error saving Results.<br>Please request a paper ballot!'),(657,'app','Result saved. Next one!'),(658,'app','Debate #{id} does not exist'),(659,'app','Correct Team Points for {team} from {old_points} to {new_points}'),(660,'app','Correct Speaker {pos} speaks for {team} from {old_points} to {new_points}'),(661,'app','Cache in perfect shape. No change needed!'),(662,'app','Can\'t save clash decision. {reason}'),(663,'app','Not enough venues'),(664,'app','Too many venues'),(665,'app','Max Iterations to improve the Adjudicator Allocation'),(666,'app','Team and adjudicator in same society penalty'),(667,'app','Both Adjudicators are clashed'),(668,'app','Team with Adjudicator is clashed'),(669,'app','Adjudicator is not allowed to chair'),(670,'app','Chair is not perfect at the current situation'),(671,'app','Adjudicator has seen the team already'),(672,'app','Adjudicator has already judged in this combination'),(673,'app','Panel is wrong strength for room'),(674,'app','Richard\'s special ingredient'),(675,'app','Adjudicator {adju} and {team} in same society.'),(676,'app','Adjudicator {adju1} and {adju2} are manually clashed.'),(677,'app','Adjudicator {adju} and Team {team} are manually clashed.'),(678,'app','Adjudicator {adju} has been labelled a non-chair.'),(679,'app','Chair not perfect by {points}.'),(680,'app','Adjudicator {adju1} and {adju2} have judged together x{occ} before'),(681,'app','Adjudicator {adju} has judged Team {team} x {occ} before.'),(682,'app','Steepness Comparison: {comparison_factor}, Difference: {roomDifference}, Steepness Penalty: {steepnessPenalty}'),(686,'app.country','Undefined'),(687,'app.country','Northern Europe'),(688,'app.country','Western Europe'),(689,'app.country','Southern Europe'),(690,'app.country','Eastern Europe'),(691,'app.country','Central Asia'),(692,'app.country','Eastern Asia'),(693,'app.country','Western Asia'),(694,'app.country','Southern Asia'),(695,'app.country','South-Eastern Asia'),(696,'app.country','Australia & New Zealand'),(697,'app.country','Micronesia'),(698,'app.country','Melanesia'),(699,'app.country','Polynesia'),(700,'app.country','Northern Africa'),(701,'app.country','Western Africa'),(702,'app.country','Central Africa'),(703,'app.country','Eastern Africa'),(704,'app.country','Southern Africa'),(705,'app.country','Northern America'),(706,'app.country','Central America'),(707,'app.country','Caribbean'),(708,'app.country','South America'),(709,'app.country','Antarctic'),(710,'app','Punished Adjudicator'),(711,'app','Bad Adjudicator'),(712,'app','Decent Adjudicator'),(713,'app','Average Adjudicator'),(714,'app','Average Chair'),(715,'app','Good Chair'),(716,'app','Breaking Chair'),(717,'app','<b>This tournament has no teams yet.</b><br>{add_button} or {import_button}'),(718,'app','Add a team'),(719,'app','Import them via CSV File.'),(720,'app','This tournament has no venues yet.<br>{add} or {import}'),(721,'app','Add a venue'),(722,'app','Import them via csv File'),(723,'app','<b>This tournament has no adjudicators yet.</b><br>{add_button} or {import_button}.'),(724,'app','Import them via CSV File'),(725,'app','Already Results entered for this round. Can\'t redraw!'),(726,'app','Already Results entered for this round. Can\'t improve!'),(727,'app','Feedback #{num}'),(728,'app','User ID'),(729,'app','Language Maintainer'),(730,'app','Language Maintainers'),(731,'app','Create Language Maintainer'),(732,'app','Show EFL Ranking'),(733,'app','Show Novice Ranking'),(734,'app','English as proficient language'),(735,'app','NOV'),(736,'app','Set Novice'),(737,'app','ENL'),(738,'app','Create new Language'),(739,'app','Export Draw as JSON'),(740,'app','Can\'t delete Team {name} because it is already in use'),(741,'app','Can\'t delete Adjudicator {name} because he/she is already in use'),(742,'app','Can\'t delete Venue {name} because it is already in use'),(743,'app','Tell us in 3-4 general keywords what the motion is about. Reuse tags ...'),(744,'app','Error Saving Custom Attribute: {name}'),(745,'app','Error Saving Custom Value \'{key}\': {value}'),(746,'app','User Attr ID'),(747,'app','Tournament ID'),(748,'app','Required'),(749,'app','Help'),(750,'app','Custom Values for {tournament}'),(751,'app','File Syntax not matching. Minimal 5 columns required.'),(753,'app','Trainee'),(754,'app','Import {modelClass} #{number}'),(755,'app','Publish approved Draw'),(756,'app','Import Draw from JSON'),(757,'app','This will override the current draw! All information will be lost!'),(758,'app','Re-draw Round'),(759,'app','Delete Round'),(760,'app','Are you sure you want to DELETE the round? All information will be lost!'),(761,'app','Round is already active! Can\'t override with input.'),(762,'app','Uploaded file was empty. Please select a file.'),(764,'app','Speakers'),(765,'app','File Syntax Wrong! At least {min} columns expected; {num} provided in line {line}'),(766,'app','Question Text'),(767,'app','Help Text'),(770,'app','Adjudicator to Adjudicator Clashes'),(771,'app','Accept all'),(772,'app','Deny all'),(773,'app','Team to Adjudicator Clashes'),(774,'app','Not a valid decision'),(775,'app','Import Score for {modelClass}'),(777,'app','Public Access URLs'),(778,'app','Debate not found - type wrong?'),(783,'app','Motion Balance'),(784,'app','Round information'),(785,'app','There is currently no active round. Refresh this page later.'),(787,'app','PD-Octofinal'),(788,'app','Replace adjudicator {adjudicator} with'),(789,'app','Replace'),(790,'app','View'),(791,'app','Switch Team {team} with'),(792,'app','Retry to set Draw'),(793,'app','AVG'),(794,'app','Only authorised Tabmasters can access this function'),(795,'app','Tournament successfully updated'),(796,'app','Tournament updated but Energy config updated failed!');
/*!40000 ALTER TABLE `source_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `special_needs`
--

DROP TABLE IF EXISTS `special_needs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `special_needs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tabmaster`
--

DROP TABLE IF EXISTS `tabmaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tabmaster` (
  `user_id` int(11) unsigned NOT NULL,
  `tournament_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`tournament_id`),
  KEY `fk_user_has_tournament_tournament3_idx` (`tournament_id`),
  KEY `fk_user_has_tournament_user3_idx` (`user_id`),
  CONSTRAINT `fk_user_has_tournament_tournament3` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_tournament_user3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `motion_tag_id` int(11) NOT NULL,
  `round_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`motion_tag_id`,`round_id`),
  KEY `fk_motion_tag_has_round_round1_idx` (`round_id`),
  KEY `fk_motion_tag_has_round_motion_tag1_idx` (`motion_tag_id`),
  CONSTRAINT `fk_motion_tag_has_round_motion_tag1` FOREIGN KEY (`motion_tag_id`) REFERENCES `motion_tag` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_motion_tag_has_round_round1` FOREIGN KEY (`round_id`) REFERENCES `round` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `tournament_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `speakerA_id` int(10) unsigned DEFAULT NULL,
  `speakerB_id` int(10) unsigned DEFAULT NULL,
  `society_id` int(10) unsigned NOT NULL,
  `isSwing` tinyint(1) NOT NULL DEFAULT '0',
  `language_status` tinyint(4) NOT NULL DEFAULT '0',
  `points` int(11) NOT NULL DEFAULT '0',
  `speakerA_speaks` int(11) NOT NULL DEFAULT '0',
  `speakerB_speaks` int(11) NOT NULL DEFAULT '0',
  `speakerA_checkedin` tinyint(1) NOT NULL DEFAULT '0',
  `speakerB_checkedin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_team_username_idx` (`speakerA_id`),
  KEY `fk_team_username1_idx` (`speakerB_id`),
  KEY `fk_team_tournament1_idx` (`tournament_id`),
  KEY `fk_team_society1_idx` (`society_id`),
  CONSTRAINT `fk_team_society1` FOREIGN KEY (`society_id`) REFERENCES `society` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_username` FOREIGN KEY (`speakerA_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_username1` FOREIGN KEY (`speakerB_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6181 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team_strike`
--

DROP TABLE IF EXISTS `team_strike`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_strike` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(10) unsigned NOT NULL,
  `adjudicator_id` int(10) unsigned NOT NULL,
  `tournament_id` int(10) unsigned NOT NULL,
  `user_clash_id` int(11) DEFAULT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_team_strike_team1_idx` (`team_id`),
  KEY `fk_team_strike_adjudicator1_idx` (`adjudicator_id`),
  KEY `fk_team_strike_tournament1_idx` (`tournament_id`),
  KEY `fk_team_strike_user_clash1_idx` (`user_clash_id`),
  CONSTRAINT `fk_team_strike_adjudicator1` FOREIGN KEY (`adjudicator_id`) REFERENCES `adjudicator` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_strike_team1` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_strike_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_team_strike_user_clash1` FOREIGN KEY (`user_clash_id`) REFERENCES `user_clash` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=734 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tournament`
--

DROP TABLE IF EXISTS `tournament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tournament` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url_slug` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `hosted_by_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `timezone` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tabAlgorithmClass` varchar(100) NOT NULL DEFAULT 'StrictWUDCRules',
  `expected_rounds` int(11) NOT NULL DEFAULT '6',
  `has_esl` tinyint(1) NOT NULL DEFAULT '0',
  `has_final` tinyint(1) NOT NULL DEFAULT '1',
  `has_semifinal` tinyint(1) NOT NULL DEFAULT '1',
  `has_quarterfinal` tinyint(1) NOT NULL DEFAULT '0',
  `has_octofinal` tinyint(1) NOT NULL DEFAULT '0',
  `accessToken` varchar(255) DEFAULT NULL,
  `badge` varchar(255) DEFAULT NULL,
  `has_efl` tinyint(1) DEFAULT '0',
  `has_novice` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_UNIQUE` (`url_slug`),
  KEY `fk_tournament_society1_idx` (`hosted_by_id`),
  CONSTRAINT `fk_tournament_society1` FOREIGN KEY (`hosted_by_id`) REFERENCES `society` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tournament_has_question`
--

DROP TABLE IF EXISTS `tournament_has_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tournament_has_question` (
  `tournament_id` int(10) unsigned NOT NULL,
  `questions_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tournament_id`,`questions_id`),
  KEY `fk_tournament_has_questions_questions1_idx` (`questions_id`),
  KEY `fk_tournament_has_questions_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_tournament_has_questions_questions1` FOREIGN KEY (`questions_id`) REFERENCES `question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tournament_has_questions_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `role` smallint(6) NOT NULL DEFAULT '10',
  `status` smallint(6) NOT NULL DEFAULT '10',
  `givenname` varchar(255) DEFAULT NULL,
  `surename` varchar(255) DEFAULT NULL,
  `gender` int(11) NOT NULL DEFAULT '0',
  `language_status` int(11) NOT NULL DEFAULT '0',
  `language_status_by_id` int(11) unsigned DEFAULT NULL,
  `language_status_update` datetime DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time` datetime NOT NULL,
  `language` varchar(10) NOT NULL DEFAULT 'en-UK',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `fk_user_user1_idx` (`language_status_by_id`),
  CONSTRAINT `fk_user_user1` FOREIGN KEY (`language_status_by_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8042 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_attr`
--

DROP TABLE IF EXISTS `user_attr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `help` text,
  PRIMARY KEY (`id`),
  KEY `fk_user_attr_tournament1_idx` (`tournament_id`),
  CONSTRAINT `fk_user_attr_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_clash`
--

DROP TABLE IF EXISTS `user_clash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_clash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `clash_with` int(11) unsigned NOT NULL,
  `reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user_has_user_user2_idx` (`clash_with`),
  KEY `fk_user_has_user_user1_idx` (`user_id`),
  CONSTRAINT `fk_user_has_user_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_user_user2` FOREIGN KEY (`clash_with`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1593 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_value`
--

DROP TABLE IF EXISTS `user_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_attr_id` int(11) NOT NULL,
  `value` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_value_user_attr1_idx` (`user_attr_id`),
  KEY `fk_user_value_user1_idx` (`user_id`),
  CONSTRAINT `fk_user_value_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_value_user_attr1` FOREIGN KEY (`user_attr_id`) REFERENCES `user_attr` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1115 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `username_has_special_needs`
--

DROP TABLE IF EXISTS `username_has_special_needs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `username_has_special_needs` (
  `username_id` int(10) unsigned NOT NULL,
  `special_needs_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`username_id`,`special_needs_id`),
  KEY `fk_username_has_special_needs_special_needs1_idx` (`special_needs_id`),
  KEY `fk_username_has_special_needs_username1_idx` (`username_id`),
  CONSTRAINT `fk_username_has_special_needs_special_needs1` FOREIGN KEY (`special_needs_id`) REFERENCES `special_needs` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_username_has_special_needs_username1` FOREIGN KEY (`username_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `venue`
--

DROP TABLE IF EXISTS `venue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `venue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tournament_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `group` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_venue_tournament1_idx` (`tournament_id`),
  KEY `order` (`group`),
  CONSTRAINT `fk_venue_tournament1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2102 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `venue_provides_special_needs`
--

DROP TABLE IF EXISTS `venue_provides_special_needs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `venue_provides_special_needs` (
  `venue_id` int(10) unsigned NOT NULL,
  `special_needs_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`venue_id`,`special_needs_id`),
  KEY `fk_venue_has_special_needs_special_needs1_idx` (`special_needs_id`),
  KEY `fk_venue_has_special_needs_venue1_idx` (`venue_id`),
  CONSTRAINT `fk_venue_has_special_needs_special_needs1` FOREIGN KEY (`special_needs_id`) REFERENCES `special_needs` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_venue_has_special_needs_venue1` FOREIGN KEY (`venue_id`) REFERENCES `venue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'tabbie'
--

--
-- Dumping routines for database 'tabbie'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-05-15 11:15:34