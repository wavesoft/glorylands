/*
MySQL Data Transfer
Source Host: localhost
Source Database: test
Target Host: localhost
Target Database: test
Date: 31/8/2008 3:03:48 ��
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for char_instance
-- ----------------------------
CREATE TABLE `char_instance` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `data` text collate latin1_general_ci,
  `template` int(11) default NULL,
  `account` int(11) default NULL,
  `name` varchar(40) collate latin1_general_ci default NULL,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `map` int(11) default NULL,
  `level` int(11) default NULL,
  `inventory_bag` int(11) default NULL,
  `model` varchar(40) collate latin1_general_ci default NULL,
  `online` tinyint(1) default NULL,
  `visible` tinyint(1) default NULL,
  `state` enum('NORMAL','GHOST','INVISIBLE') collate latin1_general_ci default NULL,
  `HP` int(11) default NULL,
  `MP` int(11) default NULL,
  `STR` int(11) default NULL,
  `DEX` int(11) default NULL,
  `CON` int(11) default NULL,
  `INT` int(11) default NULL,
  `WIS` int(11) default NULL,
  `CHA` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for char_template
-- ----------------------------
CREATE TABLE `char_template` (
  `template` int(11) NOT NULL auto_increment COMMENT 'The template ID',
  `schema` longtext collate latin1_general_ci COMMENT 'Schema data copied to data field on object creation',
  `race` varchar(30) collate latin1_general_ci default NULL,
  `icon` varchar(40) collate latin1_general_ci default NULL,
  `flags` set('ADMIN') collate latin1_general_ci default NULL,
  `description` varchar(250) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for char_vardesc
-- ----------------------------
CREATE TABLE `char_vardesc` (
  `variable` varchar(60) collate latin1_general_ci NOT NULL,
  `name` varchar(120) collate latin1_general_ci default NULL,
  `level` int(1) default NULL,
  `showmissing` tinyint(1) default NULL,
  `default` varchar(120) collate latin1_general_ci default NULL,
  `mode` enum('RAW','ALIAS','SCRIPT','GUID','MONEY','QUERY','IMAGE') collate latin1_general_ci default NULL,
  `translation` mediumtext collate latin1_general_ci,
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_maps
-- ----------------------------
CREATE TABLE `data_maps` (
  `index` int(11) NOT NULL auto_increment,
  `name` varchar(120) collate latin1_general_ci default NULL,
  `desc` mediumtext collate latin1_general_ci,
  `history` longtext collate latin1_general_ci,
  `background` varchar(80) collate latin1_general_ci default NULL,
  `filename` varchar(80) collate latin1_general_ci default NULL,
  `z-base` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_maps_teleports
-- ----------------------------
CREATE TABLE `data_maps_teleports` (
  `index` int(11) NOT NULL auto_increment,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `map` int(11) default NULL,
  `to_x` int(11) default NULL,
  `to_y` int(11) default NULL,
  `to_map` int(11) default NULL,
  `message` varchar(120) collate latin1_general_ci default NULL,
  `locks` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_mix_defaults
-- ----------------------------
CREATE TABLE `data_mix_defaults` (
  `index` int(11) NOT NULL auto_increment,
  `linkguid` int(11) default NULL,
  `type` enum('MODIFIER','TIMEOUT','CLASS','GROUP','DAMAGE','TRIGGER','SCRIPT') collate latin1_general_ci default NULL,
  `typeparm` varchar(20) collate latin1_general_ci default NULL,
  `offset` int(11) default NULL,
  `gravity` int(11) default NULL,
  `dropchance` int(4) default NULL,
  `attennuation` float(11,2) default '-1.00',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_mix_iconrules
-- ----------------------------
CREATE TABLE `data_mix_iconrules` (
  `index` int(11) NOT NULL auto_increment,
  `icon` int(11) default NULL,
  `type` varchar(20) collate latin1_general_ci default NULL,
  `subtype` varchar(20) collate latin1_general_ci default NULL,
  `offset` int(11) default NULL,
  `check` enum('EXISTS','GREATER','LESS','EQUAL') collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_mix_icons
-- ----------------------------
CREATE TABLE `data_mix_icons` (
  `index` int(11) NOT NULL auto_increment,
  `icon` varchar(50) collate latin1_general_ci default NULL,
  `suggestname` varchar(120) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_mix_mixgroups
-- ----------------------------
CREATE TABLE `data_mix_mixgroups` (
  `index` int(11) NOT NULL auto_increment,
  `group` int(11) default NULL COMMENT 'Source Group',
  `mixgroup` int(11) default NULL COMMENT 'Mixing Group',
  `skillguid` int(11) default NULL COMMENT 'Required Skill GUID',
  `deftype` varchar(20) collate latin1_general_ci default NULL COMMENT 'The Default result type if both objects are dropped',
  `defgroup` int(11) default NULL,
  `droprate` int(11) default '0',
  `skill_min` int(11) default NULL,
  `skill_max` int(11) default NULL,
  `drop_min` int(11) default '100',
  `drop_max` int(11) default '0',
  `attennuate_min` int(11) default '50',
  `attennuate_max` int(11) default '100',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_religions
-- ----------------------------
CREATE TABLE `data_religions` (
  `index` int(11) NOT NULL auto_increment,
  `name` varchar(250) collate latin1_general_ci default NULL,
  `description` mediumtext collate latin1_general_ci,
  `believes` longtext collate latin1_general_ci,
  `gods` mediumtext collate latin1_general_ci,
  `maintemple_guid` int(11) default NULL,
  `founder_guid` int(11) default NULL,
  `image` varchar(120) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for gameobject_instance
-- ----------------------------
CREATE TABLE `gameobject_instance` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `data` text collate latin1_general_ci,
  `template` int(11) default NULL,
  `name` varchar(120) collate latin1_general_ci default NULL,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `z` int(11) default NULL,
  `map` int(11) default NULL,
  `level` int(11) default NULL,
  `visible` tinyint(1) default NULL,
  `model` varchar(40) collate latin1_general_ci default NULL,
  `mixhash` varchar(40) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for gameobject_template
-- ----------------------------
CREATE TABLE `gameobject_template` (
  `template` int(11) NOT NULL auto_increment COMMENT 'The template ID',
  `schema` longtext collate latin1_general_ci COMMENT 'Schema data copied to data field on object creation',
  `templatename` varchar(250) collate latin1_general_ci default NULL COMMENT 'Unit name',
  `subname` varchar(250) collate latin1_general_ci default NULL COMMENT 'Unit subname',
  `icon` varchar(250) collate latin1_general_ci default NULL COMMENT 'Unit Icon',
  `description` mediumtext collate latin1_general_ci COMMENT 'Unit Description',
  `flags` set('OPENABLE','TRIGGER') collate latin1_general_ci default NULL,
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for gameobject_vardesc
-- ----------------------------
CREATE TABLE `gameobject_vardesc` (
  `variable` varchar(60) collate latin1_general_ci NOT NULL,
  `name` varchar(120) collate latin1_general_ci default NULL,
  `level` int(1) default NULL,
  `showmissing` tinyint(1) default NULL,
  `default` varchar(120) collate latin1_general_ci default NULL,
  `mode` enum('RAW','ALIAS','SCRIPT','GUID','MONEY','QUERY','IMAGE') collate latin1_general_ci default NULL,
  `translation` mediumtext collate latin1_general_ci,
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for interface_module_assign
-- ----------------------------
CREATE TABLE `interface_module_assign` (
  `index` int(11) NOT NULL auto_increment,
  `action` varchar(40) collate latin1_general_ci default NULL,
  `module` varchar(10) collate latin1_general_ci default NULL,
  `position` int(11) default NULL,
  `weight` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for interface_module_resources
-- ----------------------------
CREATE TABLE `interface_module_resources` (
  `index` int(11) NOT NULL auto_increment,
  `module` varchar(10) collate latin1_general_ci default NULL,
  `mode` enum('CSS','JS','HEADER','FOOTER') collate latin1_general_ci default NULL,
  `filename` varchar(120) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for interface_modules
-- ----------------------------
CREATE TABLE `interface_modules` (
  `index` varchar(10) collate latin1_general_ci NOT NULL,
  `name` varchar(30) collate latin1_general_ci default NULL,
  `description` text collate latin1_general_ci,
  `filename` varchar(120) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for item_instance
-- ----------------------------
CREATE TABLE `item_instance` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `data` text collate latin1_general_ci,
  `template` int(11) default NULL,
  `parent` int(11) default NULL,
  `item_type` varchar(20) collate latin1_general_ci default NULL,
  `item_template` int(11) default NULL,
  `item_count` int(11) default NULL,
  `item_variables` longtext collate latin1_general_ci,
  `mixhash` varchar(40) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Contains all the initialization instances that must be done';

-- ----------------------------
-- Table structure for item_template
-- ----------------------------
CREATE TABLE `item_template` (
  `template` int(11) NOT NULL auto_increment,
  `schema` mediumtext collate latin1_general_ci,
  `name` varchar(250) collate latin1_general_ci default NULL,
  `description` mediumtext collate latin1_general_ci,
  `class` enum('CONSUMABLE','CONTAINER','WEAPON','ARMOR','REAGENT','PROJECTILE','TRADEGOOD','RECIPE','QUIVER','QUEST','KEY','MISC','BOOK') collate latin1_general_ci default NULL,
  `subclass` enum('GENERIC','BAG','SOULBAG','HERBBAG','ENCHBAG','GEMBAG','MININGBAG','ONEHAND-AXE','TWOHAND-AXE','BOW','GUN','ONEHAND-MACE','TWOHAND-MACE','POLEARMS','ONEHAND-SWORD','TWOHAND-SWORD','STAFF','OHEHAND-EXOTIC','TWOHAND-EXOTIC','FIST','DAGGER','THROWN','SPEAR','CROSSBOW','WAND','FISHINGPOLE','CLOTH','LEATHER','MAIL','PLATE','SHIELD','LIBRAM','IDOL','TOTEM','ARROW','BULLET','THROWN','TRADEGOODS','PARTS','EXPLOSIVE','DEVICE','GEM','BOOK','LEATHERWORKING','TAILORING','ENGINEERING','BLACKSMITHING','COOKING','ALCHEMY','FIRSTAID','ENCHANTING','FISHING','JEWELCRAFTING','AMMOPOUCH','KEY','LOCKPICK','JUNK','MISC') collate latin1_general_ci default NULL,
  `icon` varchar(250) collate latin1_general_ci default NULL,
  `quality` int(11) default NULL,
  `item_level` int(11) default NULL,
  `require_level` int(11) default NULL,
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for item_vardesc
-- ----------------------------
CREATE TABLE `item_vardesc` (
  `variable` varchar(60) collate latin1_general_ci NOT NULL,
  `name` varchar(120) collate latin1_general_ci default NULL,
  `level` int(1) default NULL,
  `showmissing` tinyint(1) default NULL,
  `default` varchar(120) collate latin1_general_ci default NULL,
  `mode` enum('RAW','ALIAS','SCRIPT','GUID','MONEY','QUERY','IMAGE') collate latin1_general_ci default NULL,
  `translation` mediumtext collate latin1_general_ci,
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for locks_global
-- ----------------------------
CREATE TABLE `locks_global` (
  `index` int(11) NOT NULL auto_increment,
  `type` varchar(5) collate latin1_general_ci default NULL,
  `template` int(11) default NULL,
  `timeout` int(11) default NULL,
  `description` varchar(250) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for locks_private
-- ----------------------------
CREATE TABLE `locks_private` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `timeout` int(11) default NULL,
  `description` varchar(250) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for mod_chat_channel_registrations
-- ----------------------------
CREATE TABLE `mod_chat_channel_registrations` (
  `index` int(11) NOT NULL auto_increment,
  `user` int(11) default NULL,
  `channel` varchar(30) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=281 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for npc_instance
-- ----------------------------
CREATE TABLE `npc_instance` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `data` text collate latin1_general_ci,
  `template` int(11) default NULL,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `map` int(11) default NULL,
  `level` int(11) default NULL,
  `model` varchar(40) collate latin1_general_ci default NULL,
  `visible` tinyint(1) default NULL,
  `state` enum('NORMAL','GHOST','INVISIBLE') collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for npc_template
-- ----------------------------
CREATE TABLE `npc_template` (
  `template` int(11) NOT NULL auto_increment,
  `schema` mediumtext collate latin1_general_ci,
  `name` varchar(250) collate latin1_general_ci default NULL,
  `race` varchar(60) collate latin1_general_ci default NULL,
  `class` varchar(60) collate latin1_general_ci default NULL,
  `icon` varchar(120) collate latin1_general_ci default NULL,
  `flags` set('CHAT','QUEST','VENDOR','BANKER','TAXI','TABARD','TRAINER','BATTLEFIELD','HEALER','AUCTIONEER','GUARD','STABLEMASTER','INNKEEPER','ARMORER') collate latin1_general_ci default NULL,
  `type` enum('BEAST','DRAGON','DAEMON','ELEMENTAL','GIANT','UNDEAD','HUMANOID','CRITTER','TOTEM') collate latin1_general_ci default NULL,
  `description` text collate latin1_general_ci,
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for npc_vardesc
-- ----------------------------
CREATE TABLE `npc_vardesc` (
  `variable` varchar(60) collate latin1_general_ci NOT NULL,
  `name` varchar(120) collate latin1_general_ci default NULL,
  `level` int(1) default NULL,
  `showmissing` tinyint(1) default NULL,
  `default` varchar(120) collate latin1_general_ci default NULL,
  `mode` enum('RAW','ALIAS','SCRIPT','GUID','MONEY','QUERY','IMAGE') collate latin1_general_ci default NULL,
  `translation` mediumtext collate latin1_general_ci,
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_events
-- ----------------------------
CREATE TABLE `system_events` (
  `event` varchar(30) collate latin1_general_ci NOT NULL default '',
  `package` int(11) default '0',
  `file` varchar(30) collate latin1_general_ci default NULL,
  `function` varchar(30) collate latin1_general_ci default NULL,
  `enabled` enum('YES','NO') collate latin1_general_ci default 'YES',
  PRIMARY KEY  (`event`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_files
-- ----------------------------
CREATE TABLE `system_files` (
  `index` int(11) NOT NULL auto_increment,
  `type` enum('ACTION','ACTION.MANIFEST','ACTION.LIBRARY','SYSTEM.MANAGER','SYSTEM.INCLUDE','SYSTEM.ENGINE','OUTPUT.PROCESSOR','OUTPUT.FILE','IMAGE.CHARS','IMAGE.ELEMENTS','IMAGE.INVENTORY','IMAGE.PLAYERS','IMAGE.PORTRAITS','IMAGE.SIGHTSEENS','IMAGE.UI','IMAGE.TILES','INTERFACE.INCLUDE','INTERFACE.THEME','DATABASE.TABLE','DATABASE.PATCH','DOCUMENTATION') collate latin1_general_ci default NULL,
  `package` int(11) default NULL,
  `filename` varchar(255) collate latin1_general_ci default NULL,
  `version` int(11) default NULL,
  `hash` varchar(32) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=45332 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_group_dictionary
-- ----------------------------
CREATE TABLE `system_group_dictionary` (
  `index` int(11) NOT NULL,
  `name` varchar(120) collate latin1_general_ci default NULL,
  `package` varchar(32) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_messages
-- ----------------------------
CREATE TABLE `system_messages` (
  `index` int(11) NOT NULL auto_increment,
  `time` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  `type` int(8) default NULL,
  `user` int(11) default NULL,
  `data` text collate latin1_general_ci,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=9309 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_packages
-- ----------------------------
CREATE TABLE `system_packages` (
  `index` int(11) NOT NULL auto_increment,
  `guid` varchar(32) collate latin1_general_ci default NULL,
  `type` enum('FILES','PATCH','DOCUMENTATION') collate latin1_general_ci default NULL,
  `version` int(11) default NULL,
  `name` varchar(120) collate latin1_general_ci default NULL,
  `description` mediumtext collate latin1_general_ci,
  `author` varchar(200) collate latin1_general_ci default NULL,
  `copyright` varchar(200) collate latin1_general_ci default NULL,
  `website` varchar(200) collate latin1_general_ci default NULL,
  `installdate` int(11) default NULL,
  `require` mediumtext collate latin1_general_ci,
  `status` enum('ACTIVE','INACTIVE','INCOMPLETED','BUGGY','UNINSTALLINIG') collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_packages_uninstall
-- ----------------------------
CREATE TABLE `system_packages_uninstall` (
  `index` int(11) NOT NULL auto_increment,
  `package` int(11) default NULL,
  `umode` varchar(120) collate latin1_general_ci default NULL,
  `data` text collate latin1_general_ci,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_scheduler
-- ----------------------------
CREATE TABLE `system_scheduler` (
  `index` int(11) NOT NULL auto_increment,
  `timestamp` int(11) default NULL,
  `user` int(11) default NULL,
  `eventid` varchar(120) collate latin1_general_ci default NULL,
  `description` varchar(250) collate latin1_general_ci default NULL,
  `data` text collate latin1_general_ci,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=140 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for unit_instance
-- ----------------------------
CREATE TABLE `unit_instance` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `data` text collate latin1_general_ci,
  `template` int(11) default NULL,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `map` int(11) default NULL,
  `level` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for unit_template
-- ----------------------------
CREATE TABLE `unit_template` (
  `template` int(11) NOT NULL auto_increment COMMENT 'The template ID',
  `schema` longtext collate latin1_general_ci COMMENT 'Schema data copied to data field on object creation',
  `name` varchar(250) collate latin1_general_ci default NULL COMMENT 'Unit name',
  `subname` varchar(250) collate latin1_general_ci default NULL COMMENT 'Unit subname',
  `icon` varchar(250) collate latin1_general_ci default NULL COMMENT 'Unit Icon',
  `description` mediumtext collate latin1_general_ci COMMENT 'Unit Description',
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for unit_vardesc
-- ----------------------------
CREATE TABLE `unit_vardesc` (
  `variable` varchar(60) collate latin1_general_ci NOT NULL,
  `name` varchar(120) collate latin1_general_ci default NULL,
  `level` int(1) default NULL,
  `showmissing` tinyint(1) default NULL,
  `default` varchar(120) collate latin1_general_ci default NULL,
  `mode` enum('RAW','ALIAS','SCRIPT','GUID','MONEY','QUERY','IMAGE') collate latin1_general_ci default NULL,
  `translation` mediumtext collate latin1_general_ci,
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for users_accounts
-- ----------------------------
CREATE TABLE `users_accounts` (
  `index` int(11) NOT NULL auto_increment,
  `name` varchar(30) collate latin1_general_ci default NULL,
  `password` varchar(32) collate latin1_general_ci default NULL,
  `lastip` varchar(15) collate latin1_general_ci default NULL,
  `lastlogin` timestamp NULL default NULL,
  `lastaction` int(11) default NULL,
  `online` tinyint(1) default NULL,
  `level` enum('BANNED','USER','MODERATOR','ADMIN') collate latin1_general_ci default 'USER',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `char_template` VALUES ('1', 'map=1&x=5&y=5&visible=1&state=NORMAL', 'Human', 'portraits/clans_alchemist.gif', null, 'This is a human player. It is said that human players tend to have better abilities on trade operations that any other race.');
INSERT INTO `char_template` VALUES ('2', 'map=4&x=5&y=5&visible=1&state=NORMAL', 'Elf', 'portraits/neutral_elfranger.gif', null, 'This is an elf player.');
INSERT INTO `char_vardesc` VALUES ('race', 'Race', '0', '0', '(Unknown)', 'RAW', null);
INSERT INTO `char_vardesc` VALUES ('money', 'Money', '0', '1', '42', 'MONEY', null);
INSERT INTO `char_vardesc` VALUES ('itelligence', 'Itelligence', '0', '1', '10', 'SCRIPT', 'if ($var > 90) {\r\n	$color=\'red\';\r\n} elseif ($var > 50) {\r\n	$color=\'blue\';\r\n} elseif ($var > 30) {\r\n	$color=\'green\';\r\n} else {\r\n	$color=\'grey\';\r\n}\r\nreturn \"<font color=\\\"$color\\\">$var %</font>\";');
INSERT INTO `data_maps` VALUES ('1', 'Luskan Village', 'Luskan is a small village on the eastern side of mount Solomir. It is an old and very small village that is mostly consisted of old people and visitors.\r\nIt is said that the village was founded because of a sacred spring that was located nearby. That spring was said that had healing powers and could cure almost any diseace! Nevertheless, no one has ever head of it again for many, many years. Only the legend and some ancient ruins remains nowadays to remind the people the past days...', 'Luskan is a small village on the eastern side of mount Solomir. It is an old and very small village that is mostly consisted of old people and visitors.\r\nIt is said that the village was founded because of a sacred spring that was located nearby. That spring was said that had healing powers and could cure almost any diseace! Nevertheless, no one has ever head of it again for many, many years. Only the legend and some ancient ruins remains nowadays to remind the people the past days...', 'z-field-ext-1-2.gif', 'luskan-vlg', '50');
INSERT INTO `data_maps` VALUES ('2', 'Well', 'A hidden area under the central well of Luskan Village!', null, 'z-dungeon-caves-1-5.gif', 'luskan-well', '-10');
INSERT INTO `data_maps` VALUES ('3', 'Well Room', 'A secret room by the well', null, 'z-dungeon-caves-1-5.gif', 'luskan-well-2', '-10');
INSERT INTO `data_maps` VALUES ('4', 'Unknown Area', 'You fell from a hole on a room above your head. You are now standing on a dark cave with a water stream.', null, 'z-field-ext-1-2.gif', 'luskan-well-3', '-20');
INSERT INTO `data_maps` VALUES ('5', 'Luskan Calste Dungeon', 'This is the secret dungeon of the Luskan Castle. It is said that many precious treasures are hidden down there!', null, 'z-castle-int-1-3.gif', 'luskan-castle-1', '-20');
INSERT INTO `data_maps` VALUES ('6', 'Temporary Test Map', null, null, 'z-field-ext-1-2.gif', 'test', '0');
INSERT INTO `data_maps` VALUES ('7', 'Cuhlkah Village', null, null, 'z-snow-0-0.gif', 'snow2', '0');
INSERT INTO `data_maps_teleports` VALUES ('1', '16', '8', '1', '6', '4', '2', 'You have found a secret room under the well!', '0');
INSERT INTO `data_maps_teleports` VALUES ('2', '8', '3', '2', '16', '9', '1', 'You are up on the surface again!', '0');
INSERT INTO `data_maps_teleports` VALUES ('3', '3', '3', '2', '3', '19', '3', 'A Secret room under the well', '0');
INSERT INTO `data_maps_teleports` VALUES ('4', '3', '19', '3', '3', '4', '2', 'You are back to the well', '0');
INSERT INTO `data_maps_teleports` VALUES ('5', '8', '16', '3', '8', '8', '4', 'You fell into the hole!', '0');
INSERT INTO `data_maps_teleports` VALUES ('6', '8', '13', '3', '8', '8', '4', 'You fell into the hole!', '0');
INSERT INTO `data_maps_teleports` VALUES ('7', '44', '5', '4', '6', '9', '5', 'You found a secret entrance', '0');
INSERT INTO `data_maps_teleports` VALUES ('8', '45', '5', '4', '7', '9', '5', 'You found a secret entrance', '0');
INSERT INTO `data_maps_teleports` VALUES ('9', '6', '9', '5', '44', '5', '4', 'You are back to the underground cave', '0');
INSERT INTO `data_maps_teleports` VALUES ('10', '7', '9', '5', '45', '5', '4', 'You are back to the underground cave', '0');
INSERT INTO `data_mix_defaults` VALUES ('1', '264', 'MODIFIER', 'STR', '30', '30', '70', '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('2', '264', 'MODIFIER', 'DEX', '10', '80', '0', '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('3', '264', 'TIMEOUT', 'STR', '40', '100', '0', '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('4', '264', 'TIMEOUT', 'DEX', '40', '100', '0', '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('5', '264', 'CLASS', 'CONSUMABLE', null, '50', '50', '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('6', '256', 'TIMEOUT', '*', '86400', '150', '50', '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('7', '256', 'CLASS', 'RAGENT', null, '50', '50', '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('9', '264', 'GROUP', '1', null, null, null, '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('10', '256', 'GROUP', '2', null, null, null, '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('11', '260', 'GROUP', '1', null, null, null, '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('12', '260', 'MODIFIER', 'STR', '500', '10', '90', '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('13', '260', 'MODIFIER', 'DEX', '500', '10', '90', '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('14', '260', 'TIMEOUT', '*', '30', '5', '10', '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('16', '260', 'CLASS', 'CONSUMABLE', null, null, null, '-1.00');
INSERT INTO `data_mix_defaults` VALUES ('17', '260', 'MODIFIER', 'HP', '-100', '20', '5', '-1.50');
INSERT INTO `data_mix_iconrules` VALUES ('1', '1', 'CLASS', 'CONSUMABLE', null, 'EXISTS');
INSERT INTO `data_mix_iconrules` VALUES ('2', '1', 'MODIFIER', 'HP', '20', 'LESS');
INSERT INTO `data_mix_iconrules` VALUES ('3', '2', 'CLASS', 'CONSUMABLE', null, 'EXISTS');
INSERT INTO `data_mix_iconrules` VALUES ('4', '2', 'MODIFIER', 'HP', '20', 'GREATER');
INSERT INTO `data_mix_iconrules` VALUES ('5', '3', 'MODIFIER', 'STR', '0', 'GREATER');
INSERT INTO `data_mix_iconrules` VALUES ('6', '2', 'MODIFIER', 'MP', null, 'EXISTS');
INSERT INTO `data_mix_icons` VALUES ('1', 'inventory/INV_Potion_50.jpg', 'Minor healthy potion');
INSERT INTO `data_mix_icons` VALUES ('2', 'inventory/INV_Potion_14.jpg', 'Great healthy potion');
INSERT INTO `data_mix_icons` VALUES ('3', 'inventory/INV_Potion_97.jpg', 'Strength potion');
INSERT INTO `data_mix_mixgroups` VALUES ('2', '1', '2', '0', 'RAGENT', '1', '0', '0', '100', '10', '0', '1', '100');
INSERT INTO `data_religions` VALUES ('1', 'Deites', 'ÓõæçôÞóåéò', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('2', 'Jaccob', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('3', 'Corellon Larethian', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('4', 'Ehlonna', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('5', 'Erythnul', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('6', 'Fharlanghn', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('7', 'Garl Glittergold', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('8', 'Gruumsh', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('9', 'Heironeous', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('10', 'Hextor', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('11', 'Kord', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('12', 'Moradin', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('13', 'Nerull', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('14', 'Obad-Hai', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('15', 'Olidammara', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('16', 'Perlor', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('17', 'St. Cuthbert', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('18', 'Vecna', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('19', 'Wee Jas', '', null, null, null, null, null);
INSERT INTO `data_religions` VALUES ('20', 'Yondalla', '', null, null, null, null, null);
INSERT INTO `gameobject_instance` VALUES ('49', '12553', 'a:2:{s:11:\"displayname\";s:6:\"Object\";s:11:\"description\";s:70:\"This is a bag found somewere on the Luskan Village. It contains skata.\";}', '1', 'Ena saki', '40', '11', '0', '1', '1', '1', 'b_saki.o', null);
INSERT INTO `gameobject_instance` VALUES ('38', '9737', 'a:1:{s:0:\"\";N;}', '1', 'Object', '76', '13', '0', '7', '1', '1', 'aebre-bouche.o', null);
INSERT INTO `gameobject_instance` VALUES ('39', '9993', 'a:1:{s:0:\"\";N;}', '1', 'Object', '79', '15', '0', '7', '1', '1', 'blondinnet-bracelet.o', null);
INSERT INTO `gameobject_instance` VALUES ('43', '11017', 'a:3:{s:11:\"displayname\";s:6:\"Object\";s:4:\"icon\";s:37:\"inventory/Trash-Empty-128x128 (2).png\";s:11:\"description\";s:48:\"An open barrel found by a well on Luskan Village\";}', '1', 'Round Barrel', '48', '16', '0', '1', '1', '1', 'b_obj-barrel2.o', null);
INSERT INTO `gameobject_instance` VALUES ('48', '12297', 'a:2:{s:11:\"displayname\";s:6:\"Object\";s:11:\"description\";s:70:\"This is a bag found somewere on the Luskan Village. It contains skata.\";}', '1', 'Ena saki', '40', '11', '0', '1', '1', '1', 'b_saki.o', null);
INSERT INTO `gameobject_instance` VALUES ('67', '17161', 'a:3:{s:11:\"displayname\";s:6:\"Object\";s:4:\"icon\";s:30:\"inventory/Spell_Holy_Power.jpg\";s:11:\"description\";s:172:\"This is a picture of a strange old man with a helmet. A text below the picture says:Toti is one of the gratest mayors ever in Liaque town. Noone has ever beat him in a war!\";}', '1', 'Picture of Toti the Great', '32', '10', '1', '6', '1', '1', 'Map Object-Note.o', null);
INSERT INTO `gameobject_instance` VALUES ('73', '18697', 'a:3:{s:11:\"displayname\";s:6:\"Object\";s:11:\"description\";s:7:\"A boat!\";s:4:\"icon\";s:34:\"inventory/Ellas-Greece-128x128.png\";}', '1', 'Boat', '10', '-1', '1', '6', '1', '1', 'Field-Boat 1.o', null);
INSERT INTO `gameobject_instance` VALUES ('74', '18953', 'a:6:{s:11:\"displayname\";s:6:\"Object\";s:11:\"description\";s:7:\"A boat!\";s:4:\"icon\";s:27:\"UI/Ellas-Greece-128x128.png\";s:12:\"templatename\";s:14:\"Testing Object\";s:7:\"subname\";s:18:\"(Developing Usage)\";s:7:\"mixinfo\";a:2:{s:5:\"group\";s:1:\"1\";s:4:\"data\";a:5:{i:0;a:6:{s:3:\"typ\";s:8:\"MODIFIER\";s:3:\"mod\";s:3:\"STR\";s:3:\"ofs\";s:2:\"30\";s:3:\"grv\";s:2:\"30\";s:3:\"drp\";s:2:\"70\";s:3:\"att\";s:5:\"-1.00\";}i:1;a:6:{s:3:\"typ\";s:8:\"MODIFIER\";s:3:\"mod\";s:3:\"DEX\";s:3:\"ofs\";s:2:\"10\";s:3:\"grv\";s:2:\"80\";s:3:\"drp\";s:1:\"0\";s:3:\"att\";s:5:\"-1.00\";}i:2;a:6:{s:3:\"typ\";s:7:\"TIMEOUT\";s:3:\"mod\";s:3:\"STR\";s:3:\"ofs\";s:2:\"40\";s:3:\"grv\";s:3:\"100\";s:3:\"drp\";s:1:\"0\";s:3:\"att\";s:5:\"-1.00\";}i:3;a:6:{s:3:\"typ\";s:7:\"TIMEOUT\";s:3:\"mod\";s:3:\"DEX\";s:3:\"ofs\";s:2:\"40\";s:3:\"grv\";s:3:\"100\";s:3:\"drp\";s:1:\"0\";s:3:\"att\";s:5:\"-1.00\";}i:4;a:6:{s:3:\"typ\";s:5:\"CLASS\";s:3:\"mod\";s:10:\"CONSUMABLE\";s:3:\"ofs\";N;s:3:\"grv\";s:2:\"50\";s:3:\"drp\";s:2:\"50\";s:3:\"att\";s:5:\"-1.00\";}}}}', '1', 'Boat', '10', '7', '1', '6', '1', '1', 'Field-Boat 1.o', null);
INSERT INTO `gameobject_instance` VALUES ('75', '19209', 'a:2:{s:11:\"displayname\";s:6:\"Object\";s:0:\"\";N;}', '1', 'Left Statue', '3', '15', '1', '6', '1', '1', 'Castle General-Statue1.o', null);
INSERT INTO `gameobject_template` VALUES ('1', 'z=0&visible=1&model=&level=1&displayname=Object', 'Testing Object', '(Developing Usage)', 'inventory/INV_Misc_Bag_08.jpg', null, null);
INSERT INTO `gameobject_vardesc` VALUES ('mixinfo', 'Mixing', '0', '0', null, 'SCRIPT', 'return \"<font size=\\\"-1\\\">\".gl_mix_visualize($var,true, true).\"</font>\";');
INSERT INTO `interface_module_assign` VALUES ('1', 'interface.main', 'CHATWIN', '2', '10');
INSERT INTO `interface_module_assign` VALUES ('4', 'interface.main', 'SIDEBAR', '0', '1');
INSERT INTO `interface_module_resources` VALUES ('1', 'CHATWIN', 'JS', '{DATA.MODULE}/mod_chat/chatapi.js');
INSERT INTO `interface_module_resources` VALUES ('2', 'CHATWIN', 'CSS', '{DATA.MODULE}/mod_chat/styles/style.css');
INSERT INTO `interface_module_resources` VALUES ('3', 'SIDEBAR', 'JS', '{DATA.MODULE}/mod_sidebar/sidebar-feed.js');
INSERT INTO `interface_module_resources` VALUES ('4', 'SIDEBAR', 'CSS', '{DATA.MODULE}/mod_sidebar/style.css');
INSERT INTO `interface_modules` VALUES ('CHATWIN', 'Chat Window', 'Cross-player chat window and system message receiver for any User Interface', 'chat');
INSERT INTO `interface_modules` VALUES ('SIDEBAR', 'Side Bar', 'A sidebar that displays the user\'s current statistics', 'sidebar');
INSERT INTO `item_instance` VALUES ('13', '3333', 'a:0:{}', '1', '3589', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('14', '3589', 'a:1:{s:5:\"slots\";s:2:\"10\";}', '3', '3585', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('15', '3845', 'a:0:{}', '2', '3589', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('16', '4101', 'a:0:{}', '4', '3589', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('19', '4869', 'a:0:{}', '4', '12297', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('18', '4613', 'a:9:{s:4:\"name\";s:15:\"Aggressive Shit\";s:11:\"description\";s:29:\"This is shit. Purely shit :-P\";s:5:\"class\";s:5:\"QUEST\";s:8:\"subclass\";s:3:\"GEM\";s:4:\"icon\";s:34:\"inventory/Spell_Fire_LavaSpawn.jpg\";s:7:\"quality\";s:1:\"4\";s:10:\"item_level\";s:3:\"100\";s:13:\"require_level\";s:2:\"10\";s:7:\"mixinfo\";a:2:{s:5:\"group\";s:1:\"1\";s:4:\"data\";a:5:{i:0;a:6:{s:3:\"typ\";s:8:\"MODIFIER\";s:3:\"mod\";s:3:\"STR\";s:3:\"ofs\";s:3:\"500\";s:3:\"grv\";s:2:\"10\";s:3:\"drp\";s:2:\"90\";s:3:\"att\";s:5:\"-1.00\";}i:1;a:6:{s:3:\"typ\";s:8:\"MODIFIER\";s:3:\"mod\";s:3:\"DEX\";s:3:\"ofs\";s:3:\"500\";s:3:\"grv\";s:2:\"10\";s:3:\"drp\";s:2:\"90\";s:3:\"att\";s:5:\"-1.00\";}i:2;a:6:{s:3:\"typ\";s:7:\"TIMEOUT\";s:3:\"mod\";s:1:\"*\";s:3:\"ofs\";s:2:\"30\";s:3:\"grv\";s:1:\"5\";s:3:\"drp\";s:2:\"10\";s:3:\"att\";s:5:\"-1.00\";}i:3;a:6:{s:3:\"typ\";s:5:\"CLASS\";s:3:\"mod\";s:10:\"CONSUMABLE\";s:3:\"ofs\";N;s:3:\"grv\";N;s:3:\"drp\";N;s:3:\"att\";s:5:\"-1.00\";}i:4;a:6:{s:3:\"typ\";s:8:\"MODIFIER\";s:3:\"mod\";s:2:\"HP\";s:3:\"ofs\";s:4:\"-100\";s:3:\"grv\";s:2:\"20\";s:3:\"drp\";s:1:\"5\";s:3:\"att\";s:5:\"-1.50\";}}}}', '5', '12297', '', '0', '0', '', null);
INSERT INTO `item_instance` VALUES ('20', '5125', 'a:0:{}', '4', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('21', '5381', 'a:0:{}', '1', '17417', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('22', '5637', 'a:0:{}', '4', '17673', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('23', '5893', 'a:0:{}', '1', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('24', '6149', 'a:0:{}', '2', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('25', '6405', 'a:1:{s:5:\"slots\";s:2:\"10\";}', '3', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('26', '6661', 'a:0:{}', '4', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('27', '6917', 'a:0:{}', '5', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('28', '7173', 'a:0:{}', '1', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('29', '7429', 'a:0:{}', '2', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('30', '7685', 'a:1:{s:5:\"slots\";s:2:\"10\";}', '3', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('31', '7941', 'a:0:{}', '4', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('32', '8197', 'a:0:{}', '5', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('33', '8453', 'a:0:{}', '1', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('34', '8709', 'a:0:{}', '2', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('35', '8965', 'a:1:{s:5:\"slots\";s:2:\"10\";}', '3', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('36', '9221', 'a:0:{}', '4', '4609', null, null, null, null, null);
INSERT INTO `item_instance` VALUES ('37', '9477', 'a:0:{}', '5', '4609', null, null, null, null, null);
INSERT INTO `item_template` VALUES ('1', null, 'Light Sword', 'A quite light sword, mostly used by dwarfs', 'WEAPON', 'ONEHAND-SWORD', 'inventory/INV_Sword_04.jpg', '1', '2', '1');
INSERT INTO `item_template` VALUES ('2', null, 'Woodsman Sword', 'A Wooden excercising sword', 'WEAPON', 'TWOHAND-SWORD', 'inventory/INV_Sword_05.jpg', '2', '20', '0');
INSERT INTO `item_template` VALUES ('3', 'slots=10', 'Woolen Bag', 'A bag made of wool. This bag can carry up to 10 items', 'CONTAINER', 'GENERIC', 'inventory/INV_Misc_Bag_08.jpg', '1', '5', '0');
INSERT INTO `item_template` VALUES ('4', null, 'Dragon Book', 'One of the sacrest books of the Wulz\'ar Kingdom!', 'BOOK', null, 'inventory/INV_Misc_Book_10.jpg', '3', '20', '20');
INSERT INTO `item_template` VALUES ('5', null, 'Aggressive Shit', 'This is shit. Purely shit :-P', 'QUEST', 'GEM', 'inventory/Spell_Fire_LavaSpawn.jpg', '4', '100', '10');
INSERT INTO `item_vardesc` VALUES ('quality', 'Quality', '0', '1', 'Common', 'ALIAS', '0=%3Cfont+color%3D%23666666%3EJunk%3C%2Ffont%3E&1=%3Cfont+color%3D%23009900%3ENormal%3C%2Ffont%3E&2=%3Cfont+color%3D%230033CC%3ERare%3C%2Ffont%3E&3=%3Cfont+color%3D%23663399%3ELegendary%3C%2Ffont%3E&4=%3Cfont+color%3D%23FF9900%3EEpic%3C%2Ffont%3E&5=%3Cfont+color%3D%23FF0000%3EHeroic%3C%2Ffont%3E');
INSERT INTO `item_vardesc` VALUES ('class', 'Class', '0', '1', 'Unknown', 'SCRIPT', '$name = ucfirst(strtolower($var));\r\nif ($var==\'CONTAINER\') {\r\n  $name .= \" <a href=\\\"javascript:gloryIO(\'?a=interface.container&guid=\".$guid.\"\');\\\"><small><em>(Open)<em></small></a>\";\r\n}\r\nreturn $name;\r\n');
INSERT INTO `item_vardesc` VALUES ('parent', 'Is On', '0', '0', null, 'GUID', null);
INSERT INTO `item_vardesc` VALUES ('subclass', 'Subclass', '0', '0', 'Unknown', 'SCRIPT', 'return ucfirst(strtolower($var));');
INSERT INTO `item_vardesc` VALUES ('mixinfo', 'Mixing', '0', '0', null, 'SCRIPT', 'return \"<font size=\\\"-1\\\">\".gl_mix_visualize($var,true, true).\"</font>\";');
INSERT INTO `locks_global` VALUES ('1', null, null, null, null);
INSERT INTO `npc_template` VALUES ('1', 'level=3&model=zombie-vert.o&state=NORMAL&visible=1', 'Wuz Grub', 'ORC', 'WARRIOR', 'portraits/lod_cultist.gif', 'CHAT,QUEST', 'HUMANOID', 'Wuz Grub is one of the finest warriors of the stratholme kingdom');
INSERT INTO `npc_template` VALUES ('2', 'level=3&model=samurai-maitre.o&state=NORMAL&visible=1', 'Joe Amaroth', 'HUMAN', 'PALADIN', 'portraits/clans_kingsguard.gif', 'CHAT', 'HUMANOID', 'Joe Amaroth is a kingsguard!');
INSERT INTO `npc_vardesc` VALUES ('name', 'Name', '1', '0', null, 'RAW', null);
INSERT INTO `npc_vardesc` VALUES ('race', 'Race', '1', '0', null, 'SCRIPT', 'return ucfirst(strtolower($var));');
INSERT INTO `npc_vardesc` VALUES ('class', 'Class', '1', '0', null, 'SCRIPT', 'return ucfirst(strtolower($var));');
INSERT INTO `npc_vardesc` VALUES ('type', 'Type', '1', '0', null, 'SCRIPT', 'return ucfirst(strtolower($var));');
INSERT INTO `npc_vardesc` VALUES ('born_city', 'Born in city', '1', '0', null, 'GUID', null);
INSERT INTO `system_group_dictionary` VALUES ('0', 'char', '0');
INSERT INTO `system_group_dictionary` VALUES ('1', 'unit', '0');
INSERT INTO `system_group_dictionary` VALUES ('2', 'item', '0');
INSERT INTO `system_group_dictionary` VALUES ('3', 'npc', '0');
INSERT INTO `system_group_dictionary` VALUES ('4', 'gameobject', '0');
INSERT INTO `unit_instance` VALUES ('9', '2305', 'a:4:{s:5:\"owner\";s:4:\"2307\";s:8:\"religion\";s:1:\"1\";s:8:\"citizens\";s:2:\"20\";s:8:\"soldiers\";s:1:\"0\";}', '2', '1', '1', '1', '54');
INSERT INTO `unit_template` VALUES ('1', 'owner=0&religion=1&citizens=20&soldiers=0&x=12', 'Luskan Village', null, 'elements/mini/city.gif', 'Luskan village is a small, quite village oriented on the north side of the dark cliff.');
INSERT INTO `unit_template` VALUES ('2', 'owner=1292&religion=1&citizens=20&soldiers=0', 'Mormon City', 'custom', 'elements/mini/capital.gif', 'Mormon is the capital of the strongholme kingdom. The king Melaton VI currently rules this area...');
INSERT INTO `unit_vardesc` VALUES ('owner', 'Unit owner', '1', '0', '0', 'GUID', null);
INSERT INTO `unit_vardesc` VALUES ('religion', 'City Religion', '1', '0', 'NEUTRAL', 'QUERY', 'SELECT `name` FROM `data_religions` WHERE `index` = $var');
INSERT INTO `unit_vardesc` VALUES ('citizens', 'Number of citizens', '1', '0', '0', 'RAW', null);
INSERT INTO `unit_vardesc` VALUES ('soldiers', 'Number of soldiers', '1', '0', '0', 'RAW', null);
INSERT INTO `users_accounts` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '127.0.0.1', '2008-08-31 15:03:08', null, '0', 'ADMIN');
