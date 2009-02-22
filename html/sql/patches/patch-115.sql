/*
Added: International translations for GUIDs
Date: 23/2/2009 12:04:55 am
*/


SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for char_international
-- ----------------------------
CREATE TABLE `char_international` (
  `template` int(11) NOT NULL default '0',
  `lang` varchar(4) collate utf8_unicode_ci default 'en',
  `variables` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for gameobject_international
-- ----------------------------
CREATE TABLE `gameobject_international` (
  `template` int(11) NOT NULL default '0',
  `lang` varchar(4) collate utf8_unicode_ci default 'en',
  `variables` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for item_international
-- ----------------------------
CREATE TABLE `item_international` (
  `template` int(11) NOT NULL default '0',
  `lang` varchar(4) collate utf8_unicode_ci default 'en',
  `variables` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for npc_international
-- ----------------------------
CREATE TABLE `npc_international` (
  `template` int(11) NOT NULL default '0',
  `lang` varchar(4) collate utf8_unicode_ci default 'en',
  `variables` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for unit_international
-- ----------------------------
CREATE TABLE `unit_international` (
  `template` int(11) NOT NULL default '0',
  `lang` varchar(4) collate utf8_unicode_ci default 'en',
  `variables` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `item_international` VALUES ('1', 'el', 'a:2:{s:4:\"name\";s:67:\"Ελαφρύ Σπαθί\";s:11:\"description\";s:280:\"Ένα ελαφρύ σπαθί το οποίο συνήθως έχει η φυλή των Νάνων\";}');
INSERT INTO `item_international` VALUES ('2', 'el', 'a:2:{s:4:\"name\";s:67:\"Ξύλινο Σπαθί\";s:11:\"description\";s:264:\"Ενα ξύλινο σπαθί που χρησιμοποιείται για εξάσκιση\";}');
