-- MySQL dump 10.13  Distrib 5.6.13, for osx10.6 (i386)
--
-- Host: 127.0.0.1    Database: tabbie
-- ------------------------------------------------------
-- Server version	5.6.12

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE = @@TIME_ZONE */;
/*!40103 SET TIME_ZONE = '+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;


--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country`
VALUES (1, 'Afghanistan', 'af', 'afg', 24), (2, 'Aland Islands', 'ax', 'ala', 11),
  (3, 'Albania', 'al', 'alb', 13),
  (4, 'Algeria', 'dz', 'dza', 41), (5, 'American Samoa', 'as', 'asm', 34), (6, 'Andorra', 'ad', 'and', 13),
  (7, 'Angola', 'ao', 'ago', 43), (8, 'Anguilla', 'ai', 'aia', 53), (9, 'Antarctica', 'aq', NULL, 71),
  (10, 'Antigua and Barbuda', 'ag', 'atg', 53), (11, 'Argentina', 'ar', 'arg', 61), (12, 'Armenia', 'am', 'arm', 23),
  (13, 'Aruba', 'aw', 'abw', 53), (14, 'Australia', 'au', 'aus', 31), (15, 'Austria', 'at', 'aut', 12),
  (16, 'Azerbaijan', 'az', 'aze', 23), (17, 'Bahamas', 'bs', 'bhs', 53), (18, 'Bahrain', 'bh', 'bhr', 23),
  (19, 'Bangladesh', 'bd', 'bgd', 24), (20, 'Barbados', 'bb', 'brb', 53), (21, 'Belarus', 'by', 'blr', 14),
  (22, 'Belgium', 'be', 'bel', 12), (23, 'Belize', 'bz', 'blz', 52), (24, 'Benin', 'bj', 'ben', 42),
  (25, 'Bermuda', 'bm', 'bmu', 51), (26, 'Bhutan', 'bt', 'btn', 24),
  (27, 'Bolivia, Plurinational State of', 'bo', 'bol', 61), (28, 'Bonaire, Sint Eustatius and Saba', 'bq', 'bes', 53),
  (29, 'Bosnia and Herzegovina', 'ba', 'bih', 13), (30, 'Botswana', 'bw', 'bwa', 45),
  (31, 'Bouvet Island', 'bv', NULL, 11), (32, 'Brazil', 'br', 'bra', 61),
  (33, 'British Indian Ocean Territory', 'io', NULL, 25), (34, 'Brunei Darussalam', 'bn', 'brn', 25),
  (35, 'Bulgaria', 'bg', 'bgr', 14), (36, 'Burkina Faso', 'bf', 'bfa', 42), (37, 'Burundi', 'bi', 'bdi', 44),
  (38, 'Cambodia', 'kh', 'khm', 25), (39, 'Cameroon', 'cm', 'cmr', 43), (40, 'Canada', 'ca', 'can', 51),
  (41, 'Cape Verde', 'cv', 'cpv', 42), (42, 'Cayman Islands', 'ky', 'cym', 53),
  (43, 'Central African Republic', 'cf', 'caf', 43), (44, 'Chad', 'td', 'tcd', 43), (45, 'Chile', 'cl', 'chl', 61),
  (46, 'China', 'cn', 'chn', 22), (47, 'Christmas Island', 'cx', NULL, 31),
  (48, 'Cocos (Keeling) Islands', 'cc', NULL, 31), (49, 'Colombia', 'co', 'col', 61), (50, 'Comoros', 'km', 'com', 44),
  (51, 'Congo', 'cg', 'cog', 43), (52, 'Congo, The Democratic Republic of the', 'cd', 'cod', 43),
  (53, 'Cook Islands', 'ck', 'cok', 34), (54, 'Costa Rica', 'cr', 'cri', 52), (55, 'Cote d\'Ivoire', 'ci', 'civ', 42),
  (56, 'Croatia', 'hr', 'hrv', 13), (57, 'Cuba', 'cu', 'cub', 53), (58, 'Curacao', 'cw', 'cuw', 53),
  (59, 'Cyprus', 'cy', 'cyp', 13), (60, 'Czech Republic', 'cz', 'cze', 14), (61, 'Denmark', 'dk', 'dnk', 11),
  (62, 'Djibouti', 'dj', 'dji', 44), (63, 'Dominica', 'dm', 'dma', 53), (64, 'Dominican Republic', 'do', 'dom', 53),
  (65, 'Ecuador', 'ec', 'ecu', 61), (66, 'Egypt', 'eg', 'egy', 41), (67, 'El Salvador', 'sv', 'slv', 52),
  (68, 'Equatorial Guinea', 'gq', 'gnq', 43), (69, 'Eritrea', 'er', 'eri', 44), (70, 'Estonia', 'ee', 'est', 11),
  (71, 'Ethiopia', 'et', 'eth', 44), (72, 'Falkland Islands (Malvinas)', 'fk', 'flk', 61),
  (73, 'Faroe Islands', 'fo', 'fro', 11), (74, 'Fiji', 'fj', 'fji', 33), (75, 'Finland', 'fi', 'fin', 11),
  (76, 'France', 'fr', 'fra', 12), (77, 'French Guiana', 'gf', 'guf', 61), (78, 'French Polynesia', 'pf', 'pyf', 34),
  (79, 'French Southern Territories', 'tf', NULL, 71), (80, 'Gabon', 'ga', 'gab', 43), (81, 'Gambia', 'gm', 'gmb', 42),
  (82, 'Georgia', 'ge', 'geo', 23), (83, 'Germany', 'de', 'deu', 12), (84, 'Ghana', 'gh', 'gha', 42),
  (85, 'Gibraltar', 'gi', 'gib', 13), (86, 'Greece', 'gr', 'grc', 13), (87, 'Greenland', 'gl', 'grl', 51),
  (88, 'Grenada', 'gd', 'grd', 53), (89, 'Guadeloupe', 'gp', 'glp', 53), (90, 'Guam', 'gu', 'gum', 32),
  (91, 'Guatemala', 'gt', 'gtm', 52), (92, 'Guernsey', 'gg', 'ggy', 11), (93, 'Guinea', 'gn', 'gin', 42),
  (94, 'Guinea-Bissau', 'gw', 'gnb', 42), (95, 'Guyana', 'gy', 'guy', 61), (96, 'Haiti', 'ht', 'hti', 53),
  (97, 'Heard Island and McDonald Islands', 'hm', NULL, 71), (98, 'Holy See (Vatican City State)', 'va', 'vat', 13),
  (99, 'Honduras', 'hn', 'hnd', 52), (100, 'China, Hong Kong Special Administrative Region', 'hk', 'hkg', 22),
  (101, 'Hungary', 'hu', 'hun', 14), (102, 'Iceland', 'is', 'isl', 11), (103, 'India', 'in', 'ind', 24),
  (104, 'Indonesia', 'id', 'idn', 25), (105, 'Iran, Islamic Republic of', 'ir', 'irn', 24),
  (106, 'Iraq', 'iq', 'irq', 23), (107, 'Ireland', 'ie', 'irl', 11), (108, 'Isle of Man', 'im', 'imn', 11),
  (109, 'Israel', 'il', 'isr', 23), (110, 'Italy', 'it', 'ita', 13), (111, 'Jamaica', 'jm', 'jam', 53),
  (112, 'Japan', 'jp', 'jpn', 22), (113, 'Jersey', 'je', 'jey', 11), (114, 'Jordan', 'jo', 'jor', 23),
  (115, 'Kazakhstan', 'kz', 'kaz', 21), (116, 'Kenya', 'ke', 'ken', 44), (117, 'Kiribati', 'ki', 'kir', 32),
  (118, 'Korea, Democratic People\'s Republic of', 'kp', 'prk', 22), (119, 'Korea, Republic of', 'kr', 'kor', 22),
  (120, 'Kuwait', 'kw', 'kwt', 23), (121, 'Kyrgyzstan', 'kg', 'kgz', 21),
  (122, 'Lao People\'s Democratic Republic', 'la', 'lao', 25), (123, 'Latvia', 'lv', 'lva', 11),
  (124, 'Lebanon', 'lb', 'lbn', 23), (125, 'Lesotho', 'ls', 'lso', 45), (126, 'Liberia', 'lr', 'lbr', 42),
  (127, 'Libyan Arab Jamahiriya', 'ly', 'lby', 41), (128, 'Liechtenstein', 'li', 'lie', 12),
  (129, 'Lithuania', 'lt', 'ltu', 11), (130, 'Luxembourg', 'lu', 'lux', 12),
  (131, 'China, Macau Special Administrative Region', 'mo', 'mac', 22),
  (132, 'Macedonia, The former Yugoslav Republic of', 'mk', 'mkd', 13), (133, 'Madagascar', 'mg', 'mdg', 44),
  (134, 'Malawi', 'mw', 'mwi', 44), (135, 'Malaysia', 'my', 'mys', 25), (136, 'Maldives', 'mv', 'mdv', 24),
  (137, 'Mali', 'ml', 'mli', 42), (138, 'Malta', 'mt', 'mlt', 13), (139, 'Marshall Islands', 'mh', 'mhl', 32),
  (140, 'Martinique', 'mq', 'mtq', 53), (141, 'Mauritania', 'mr', 'mrt', 42), (142, 'Mauritius', 'mu', 'mus', 44),
  (143, 'Mayotte', 'yt', 'myt', 44), (144, 'Mexico', 'mx', 'mex', 52),
  (145, 'Micronesia, Federated States of', 'fm', 'fsm', 32), (146, 'Moldova, Republic of', 'md', 'mda', 14),
  (147, 'Monaco', 'mc', 'mco', 12), (148, 'Mongolia', 'mn', 'mng', 22), (149, 'Montenegro', 'me', 'mne', 13),
  (150, 'Montserrat', 'ms', 'msr', 53), (151, 'Morocco', 'ma', 'mar', 41), (152, 'Mozambique', 'mz', 'moz', 44),
  (153, 'Myanmar', 'mm', 'mmr', 25), (154, 'Namibia', 'na', 'nam', 45), (155, 'Nauru', 'nr', 'nru', 32),
  (156, 'Nepal', 'np', 'npl', 24), (157, 'Netherlands', 'nl', 'nld', 12), (158, 'New Caledonia', 'nc', 'ncl', 33),
  (159, 'New Zealand', 'nz', 'nzl', 31), (160, 'Nicaragua', 'ni', 'nic', 52), (161, 'Niger', 'ne', 'ner', 42),
  (162, 'Nigeria', 'ng', 'nga', 42), (163, 'Niue', 'nu', 'niu', 34), (164, 'Norfolk Island', 'nf', 'nfk', 31),
  (165, 'Northern Mariana Islands', 'mp', 'mnp', 32), (166, 'Norway', 'no', 'nor', 11), (167, 'Oman', 'om', 'omn', 23),
  (168, 'Pakistan', 'pk', 'pak', 24), (169, 'Palau', 'pw', 'plw', 32),
  (170, 'State of Palestine, \"non-state entity\"', 'ps', 'pse', 23), (171, 'Panama', 'pa', 'pan', 52),
  (172, 'Papua New Guinea', 'pg', 'png', 33), (173, 'Paraguay', 'py', 'pry', 61), (174, 'Peru', 'pe', 'per', 61),
  (175, 'Philippines', 'ph', 'phl', 25), (176, 'Pitcairn', 'pn', 'pcn', 34), (177, 'Poland', 'pl', 'pol', 14),
  (178, 'Portugal', 'pt', 'prt', 13), (179, 'Puerto Rico', 'pr', 'pri', 53), (180, 'Qatar', 'qa', 'qat', 23),
  (181, 'Reunion', 're', 'reu', 44), (182, 'Romania', 'ro', 'rou', 14), (183, 'Russian Federation', 'ru', 'rus', 14),
  (184, 'Rwanda', 'rw', 'rwa', 44), (185, 'Saint Barthelemy', 'bl', 'blm', 53),
  (186, 'Saint Helena, Ascension and Tristan Da Cunha', 'sh', 'shn', 42),
  (187, 'Saint Kitts and Nevis', 'kn', 'kna', 53), (188, 'Saint Lucia', 'lc', 'lca', 53),
  (189, 'Saint Martin (French Part)', 'mf', 'maf', 53), (190, 'Saint Pierre and Miquelon', 'pm', 'spm', 51),
  (191, 'Saint Vincent and The Grenadines', 'vc', 'vct', 53), (192, 'Samoa', 'ws', 'wsm', 34),
  (193, 'San Marino', 'sm', 'smr', 13), (194, 'Sao Tome and Principe', 'st', 'stp', 43),
  (195, 'Saudi Arabia', 'sa', 'sau', 23), (196, 'Senegal', 'sn', 'sen', 42), (197, 'Serbia', 'rs', 'srb', 13),
  (198, 'Seychelles', 'sc', 'syc', 44), (199, 'Sierra Leone', 'sl', 'sle', 42), (200, 'Singapore', 'sg', 'sgp', 25),
  (201, 'Sint Maarten (Dutch Part)', 'sx', 'sxm', 53), (202, 'Slovakia', 'sk', 'svk', 14),
  (203, 'Slovenia', 'si', 'svn', 13), (204, 'Solomon Islands', 'sb', 'slb', 33), (205, 'Somalia', 'so', 'som', 44),
  (206, 'South Africa', 'za', 'zaf', 45), (207, 'South Georgia and The South Sandwich Islands', 'gs', NULL, 71),
  (208, 'South Sudan', 'ss', 'ssd', 41), (209, 'Spain', 'es', 'esp', 13), (210, 'Sri Lanka', 'lk', 'lka', 24),
  (211, 'Sudan', 'sd', 'sdn', 41), (212, 'Suriname', 'sr', 'sur', 61), (213, 'Svalbard and Jan Mayen', 'sj', 'sjm', 11),
  (214, 'Swaziland', 'sz', 'swz', 45), (215, 'Sweden', 'se', 'swe', 11), (216, 'Switzerland', 'ch', 'che', 12),
  (217, 'Syrian Arab Republic', 'sy', 'syr', 23), (218, 'Taiwan, Province of China', 'tw', NULL, 22),
  (219, 'Tajikistan', 'tj', 'tjk', 21), (220, 'Tanzania, United Republic of', 'tz', 'tza', 44),
  (221, 'Thailand', 'th', 'tha', 25), (222, 'Timor-Leste', 'tl', 'tls', 25), (223, 'Togo', 'tg', 'tgo', 42),
  (224, 'Tokelau', 'tk', 'tkl', 34), (225, 'Tonga', 'to', 'ton', 34), (226, 'Trinidad and Tobago', 'tt', 'tto', 53),
  (227, 'Tunisia', 'tn', 'tun', 41), (228, 'Turkey', 'tr', 'tur', 23), (229, 'Turkmenistan', 'tm', 'tkm', 21),
  (230, 'Turks and Caicos Islands', 'tc', 'tca', 53), (231, 'Tuvalu', 'tv', 'tuv', 34),
  (232, 'Uganda', 'ug', 'uga', 44), (233, 'Ukraine', 'ua', 'ukr', 14), (234, 'United Arab Emirates', 'ae', 'are', 23),
  (235, 'United Kingdom of Great Britain and Northern Irela', 'gb', 'gbr', 11), (236, 'United States', 'us', 'usa', 51),
  (237, 'United States Minor Outlying Islands', 'um', NULL, 51), (238, 'Uruguay', 'uy', 'ury', 61),
  (239, 'Uzbekistan', 'uz', 'uzb', 21), (240, 'Vanuatu', 'vu', 'vut', 33),
  (241, 'Venezuela, Bolivarian Republic of', 've', 'ven', 61), (242, 'Viet Nam', 'vn', 'vnm', 25),
  (243, 'Virgin Islands, British', 'vg', 'vgb', 53), (244, 'Virgin Islands, U.S.', 'vi', 'vir', 53),
  (245, 'Wallis and Futuna', 'wf', 'wlf', 34), (246, 'Western Sahara', 'eh', 'esh', 41),
  (247, 'Yemen', 'ye', 'yem', 23), (248, 'Zambia', 'zm', 'zmb', 44), (249, 'Zimbabwe', 'zw', 'zwe', 44),
  (250, 'Unknown', 'un', 'unk', 0);
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE = @OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;

-- Dump completed on 2015-03-15 17:11:28
