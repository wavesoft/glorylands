/*
MySQL Data Transfer
Source Host: localhost
Source Database: gl_for_release
Target Host: localhost
Target Database: gl_for_release
Date: 1/1/2009 4:04:32 ��
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for char_instance
-- ----------------------------
CREATE TABLE `char_instance` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `data` text collate utf8_unicode_ci,
  `parent` int(11) default '0',
  `template` int(11) default NULL,
  `account` int(11) default NULL,
  `name` varchar(40) collate utf8_unicode_ci default NULL,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `map` int(11) default NULL,
  `level` int(11) default NULL,
  `inventory_bag` int(11) default NULL,
  `model` varchar(40) collate utf8_unicode_ci default NULL,
  `online` tinyint(1) default NULL,
  `visible` tinyint(1) default NULL,
  `state` enum('NORMAL','GHOST','INVISIBLE') collate utf8_unicode_ci default NULL,
  `HP` int(11) default NULL,
  `MP` int(11) default NULL,
  `STR` int(11) default NULL,
  `DEX` int(11) default NULL,
  `CON` int(11) default NULL,
  `INT` int(11) default NULL,
  `WIS` int(11) default NULL,
  `CHA` int(11) default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for char_template
-- ----------------------------
CREATE TABLE `char_template` (
  `template` int(11) NOT NULL auto_increment COMMENT 'The template ID',
  `schema` longtext collate utf8_unicode_ci COMMENT 'Schema data copied to data field on object creation',
  `race` varchar(30) collate utf8_unicode_ci default NULL,
  `icon` varchar(40) collate utf8_unicode_ci default NULL,
  `flags` set('ADMIN') collate utf8_unicode_ci default NULL,
  `description` varchar(250) collate utf8_unicode_ci default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for char_vardesc
-- ----------------------------
CREATE TABLE `char_vardesc` (
  `variable` varchar(60) collate utf8_unicode_ci NOT NULL,
  `name` varchar(120) collate utf8_unicode_ci default NULL,
  `level` int(1) default NULL,
  `showmissing` tinyint(1) default NULL,
  `default` varchar(120) collate utf8_unicode_ci default NULL,
  `mode` enum('RAW','ALIAS','SCRIPT','GUID','MONEY','QUERY','IMAGE') collate utf8_unicode_ci default NULL,
  `translation` mediumtext collate utf8_unicode_ci,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_maps
-- ----------------------------
CREATE TABLE `data_maps` (
  `index` int(11) NOT NULL auto_increment,
  `name` varchar(120) collate utf8_unicode_ci default NULL,
  `desc` mediumtext collate utf8_unicode_ci,
  `history` longtext collate utf8_unicode_ci,
  `background` varchar(80) collate utf8_unicode_ci default NULL,
  `filename` varchar(80) collate utf8_unicode_ci default NULL,
  `z-base` int(11) default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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
  `message` varchar(120) collate utf8_unicode_ci default NULL,
  `locks` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_mix_defaults
-- ----------------------------
CREATE TABLE `data_mix_defaults` (
  `index` int(11) NOT NULL auto_increment,
  `linkguid` int(11) default NULL,
  `type` enum('MODIFIER','TIMEOUT','CLASS','GROUP','DAMAGE','TRIGGER','SCRIPT') collate utf8_unicode_ci default NULL,
  `typeparm` varchar(20) collate utf8_unicode_ci default NULL,
  `offset` int(11) default NULL,
  `gravity` int(11) default NULL,
  `dropchance` int(4) default NULL,
  `attennuation` float(11,2) default '-1.00',
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_mix_iconrules
-- ----------------------------
CREATE TABLE `data_mix_iconrules` (
  `index` int(11) NOT NULL auto_increment,
  `icon` int(11) default NULL,
  `type` varchar(20) collate utf8_unicode_ci default NULL,
  `subtype` varchar(20) collate utf8_unicode_ci default NULL,
  `offset` int(11) default NULL,
  `check` enum('EXISTS','GREATER','LESS','EQUAL') collate utf8_unicode_ci default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_mix_icons
-- ----------------------------
CREATE TABLE `data_mix_icons` (
  `index` int(11) NOT NULL auto_increment,
  `icon` varchar(50) collate utf8_unicode_ci default NULL,
  `suggestname` varchar(120) collate utf8_unicode_ci default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_mix_mixgroups
-- ----------------------------
CREATE TABLE `data_mix_mixgroups` (
  `index` int(11) NOT NULL auto_increment,
  `group` int(11) default NULL COMMENT 'Source Group',
  `mixgroup` int(11) default NULL COMMENT 'Mixing Group',
  `skillguid` int(11) default NULL COMMENT 'Required Skill GUID',
  `deftype` varchar(20) collate utf8_unicode_ci default NULL COMMENT 'The Default result type if both objects are dropped',
  `defgroup` int(11) default NULL,
  `droprate` int(11) default '0',
  `skill_min` int(11) default NULL,
  `skill_max` int(11) default NULL,
  `drop_min` int(11) default '100',
  `drop_max` int(11) default '0',
  `attennuate_min` int(11) default '50',
  `attennuate_max` int(11) default '100',
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_regquiz_answers
-- ----------------------------
CREATE TABLE `data_regquiz_answers` (
  `index` int(11) NOT NULL auto_increment,
  `question` int(11) default NULL,
  `answer` varchar(250) collate utf8_unicode_ci default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_regquiz_data
-- ----------------------------
CREATE TABLE `data_regquiz_data` (
  `index` int(11) NOT NULL auto_increment,
  `answer` int(11) default NULL,
  `v_modifier` varchar(120) collate utf8_unicode_ci default NULL,
  `v_value` varchar(120) collate utf8_unicode_ci default NULL,
  `i_guid` int(11) default NULL,
  `i_parent` int(11) default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_regquiz_questions
-- ----------------------------
CREATE TABLE `data_regquiz_questions` (
  `index` int(11) NOT NULL auto_increment,
  `question` mediumtext collate utf8_unicode_ci,
  `title` varchar(250) collate utf8_unicode_ci default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_religions
-- ----------------------------
CREATE TABLE `data_religions` (
  `index` int(11) NOT NULL auto_increment,
  `name` varchar(250) collate utf8_unicode_ci default NULL,
  `description` mediumtext collate utf8_unicode_ci,
  `believes` longtext collate utf8_unicode_ci,
  `gods` mediumtext collate utf8_unicode_ci,
  `maintemple_guid` int(11) default NULL,
  `founder_guid` int(11) default NULL,
  `image` varchar(120) collate utf8_unicode_ci default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_spawn
-- ----------------------------
CREATE TABLE `data_spawn` (
  `index` int(11) NOT NULL auto_increment,
  `container` int(11) default NULL,
  `guid` int(11) default NULL,
  `delay` int(11) default '60',
  `maxitems` int(11) default '1',
  `spawncount` int(11) default '1',
  `successrate` int(1) default '100',
  `variables` text collate utf8_unicode_ci,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_spawn_times
-- ----------------------------
CREATE TABLE `data_spawn_times` (
  `index` int(11) NOT NULL auto_increment,
  `spawn_id` int(11) default NULL,
  `last_spawn` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_tips
-- ----------------------------
CREATE TABLE `data_tips` (
  `index` int(11) NOT NULL auto_increment,
  `trigger_action` varchar(60) collate utf8_unicode_ci default NULL,
  `trigger_request` mediumtext collate utf8_unicode_ci,
  `importance` enum('HIGH','NORMAL','LOW') collate utf8_unicode_ci default NULL,
  `title` varchar(120) collate utf8_unicode_ci default NULL,
  `tip` longtext collate utf8_unicode_ci,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for gameobject_instance
-- ----------------------------
CREATE TABLE `gameobject_instance` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `data` text collate utf8_unicode_ci,
  `template` int(11) default NULL,
  `parent` int(11) default '0',
  `name` varchar(120) collate utf8_unicode_ci default NULL,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `z` int(11) default NULL,
  `map` int(11) default NULL,
  `level` int(11) default NULL,
  `visible` tinyint(1) default NULL,
  `model` varchar(40) collate utf8_unicode_ci default NULL,
  `mixhash` varchar(40) collate utf8_unicode_ci default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for gameobject_template
-- ----------------------------
CREATE TABLE `gameobject_template` (
  `template` int(11) NOT NULL auto_increment COMMENT 'The template ID',
  `schema` longtext collate utf8_unicode_ci COMMENT 'Schema data copied to data field on object creation',
  `templatename` varchar(250) collate utf8_unicode_ci default NULL COMMENT 'Unit name',
  `subname` varchar(250) collate utf8_unicode_ci default NULL COMMENT 'Unit subname',
  `icon` varchar(250) collate utf8_unicode_ci default NULL COMMENT 'Unit Icon',
  `description` mediumtext collate utf8_unicode_ci COMMENT 'Unit Description',
  `flags` set('OPENABLE','TRIGGER') collate utf8_unicode_ci default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for gameobject_vardesc
-- ----------------------------
CREATE TABLE `gameobject_vardesc` (
  `variable` varchar(60) collate utf8_unicode_ci NOT NULL,
  `name` varchar(120) collate utf8_unicode_ci default NULL,
  `level` int(1) default NULL,
  `showmissing` tinyint(1) default NULL,
  `default` varchar(120) collate utf8_unicode_ci default NULL,
  `mode` enum('RAW','ALIAS','SCRIPT','GUID','MONEY','QUERY','IMAGE') collate utf8_unicode_ci default NULL,
  `translation` mediumtext collate utf8_unicode_ci,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for interface_module_assign
-- ----------------------------
CREATE TABLE `interface_module_assign` (
  `index` int(11) NOT NULL auto_increment,
  `action` varchar(40) collate utf8_unicode_ci default NULL,
  `module` varchar(10) collate utf8_unicode_ci default NULL,
  `position` int(11) default NULL,
  `weight` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for interface_module_resources
-- ----------------------------
CREATE TABLE `interface_module_resources` (
  `index` int(11) NOT NULL auto_increment,
  `module` varchar(10) collate utf8_unicode_ci default NULL,
  `mode` enum('CSS','JS','HEADER','FOOTER') collate utf8_unicode_ci default NULL,
  `filename` varchar(120) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for interface_modules
-- ----------------------------
CREATE TABLE `interface_modules` (
  `index` varchar(10) collate utf8_unicode_ci NOT NULL,
  `name` varchar(30) collate utf8_unicode_ci default NULL,
  `description` text collate utf8_unicode_ci,
  `filename` varchar(120) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for interface_openwin
-- ----------------------------
CREATE TABLE `interface_openwin` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) NOT NULL default '0' COMMENT 'The GUID the window is associated with',
  `player` int(11) default '0',
  `updateurl` varchar(250) collate utf8_unicode_ci default NULL COMMENT 'The reply message to update the content',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Contains the player''s open pop-up windows.Used for DynUpdate';

-- ----------------------------
-- Table structure for item_instance
-- ----------------------------
CREATE TABLE `item_instance` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `data` text collate utf8_unicode_ci,
  `template` int(11) default NULL,
  `parent` int(11) default NULL,
  `item_type` varchar(20) collate utf8_unicode_ci default NULL,
  `item_template` int(11) default NULL,
  `item_count` int(11) default NULL,
  `item_variables` longtext collate utf8_unicode_ci,
  `mixhash` varchar(40) collate utf8_unicode_ci default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Contains all the initialization instances that must be done';

-- ----------------------------
-- Table structure for item_template
-- ----------------------------
CREATE TABLE `item_template` (
  `template` int(11) NOT NULL auto_increment,
  `schema` mediumtext collate utf8_unicode_ci,
  `name` varchar(250) collate utf8_unicode_ci default NULL,
  `description` mediumtext collate utf8_unicode_ci,
  `class` enum('CONSUMABLE','CONTAINER','WEAPON','ARMOR','REAGENT','PROJECTILE','TRADEGOOD','RECIPE','QUIVER','QUEST','KEY','MISC','BOOK') collate utf8_unicode_ci default NULL,
  `subclass` enum('GENERIC','BAG','SOULBAG','HERBBAG','ENCHBAG','GEMBAG','MININGBAG','ONEHAND-AXE','TWOHAND-AXE','BOW','GUN','ONEHAND-MACE','TWOHAND-MACE','POLEARMS','ONEHAND-SWORD','TWOHAND-SWORD','STAFF','OHEHAND-EXOTIC','TWOHAND-EXOTIC','FIST','DAGGER','THROWN','SPEAR','CROSSBOW','WAND','FISHINGPOLE','CLOTH','LEATHER','MAIL','PLATE','SHIELD','LIBRAM','IDOL','TOTEM','ARROW','BULLET','THROWN','TRADEGOODS','PARTS','EXPLOSIVE','DEVICE','GEM','BOOK','LEATHERWORKING','TAILORING','ENGINEERING','BLACKSMITHING','COOKING','ALCHEMY','FIRSTAID','ENCHANTING','FISHING','JEWELCRAFTING','AMMOPOUCH','KEY','LOCKPICK','JUNK','MISC') collate utf8_unicode_ci default NULL,
  `icon` varchar(250) collate utf8_unicode_ci default NULL,
  `quality` int(11) default NULL,
  `item_level` int(11) default NULL,
  `require_level` int(11) default NULL,
  `contributor` int(11) NOT NULL default '0',
  `stackable` int(11) NOT NULL default '0',
  `sell_price` int(11) NOT NULL default '0',
  `buy_price` int(11) NOT NULL default '0',
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for item_vardesc
-- ----------------------------
CREATE TABLE `item_vardesc` (
  `variable` varchar(60) collate utf8_unicode_ci NOT NULL,
  `name` varchar(120) collate utf8_unicode_ci default NULL,
  `level` int(1) default NULL,
  `showmissing` tinyint(1) default NULL,
  `default` varchar(120) collate utf8_unicode_ci default NULL,
  `mode` enum('RAW','ALIAS','SCRIPT','GUID','MONEY','QUERY','IMAGE') collate utf8_unicode_ci default NULL,
  `translation` mediumtext collate utf8_unicode_ci,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for locks_global
-- ----------------------------
CREATE TABLE `locks_global` (
  `index` int(11) NOT NULL auto_increment,
  `type` varchar(5) collate utf8_unicode_ci default NULL,
  `template` int(11) default NULL,
  `timeout` int(11) default NULL,
  `description` varchar(250) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for locks_private
-- ----------------------------
CREATE TABLE `locks_private` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `timeout` int(11) default NULL,
  `description` varchar(250) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for mod_chat_channel_registrations
-- ----------------------------
CREATE TABLE `mod_chat_channel_registrations` (
  `index` int(11) NOT NULL auto_increment,
  `user` int(11) default NULL,
  `channel` varchar(30) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for mod_quickbar_slots
-- ----------------------------
CREATE TABLE `mod_quickbar_slots` (
  `index` int(11) NOT NULL auto_increment,
  `player` int(11) default NULL,
  `slot` int(11) default NULL,
  `guid` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for npc_instance
-- ----------------------------
CREATE TABLE `npc_instance` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `data` text collate utf8_unicode_ci,
  `template` int(11) default NULL,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `map` int(11) default NULL,
  `level` int(11) default NULL,
  `model` varchar(40) collate utf8_unicode_ci default NULL,
  `visible` tinyint(1) default NULL,
  `state` enum('NORMAL','GHOST','INVISIBLE') collate utf8_unicode_ci default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for npc_template
-- ----------------------------
CREATE TABLE `npc_template` (
  `template` int(11) NOT NULL auto_increment,
  `schema` mediumtext collate utf8_unicode_ci,
  `name` varchar(250) collate utf8_unicode_ci default NULL,
  `race` varchar(60) collate utf8_unicode_ci default NULL,
  `class` varchar(60) collate utf8_unicode_ci default NULL,
  `icon` varchar(120) collate utf8_unicode_ci default NULL,
  `flags` set('CHAT','QUEST','VENDOR','BANKER','TAXI','TABARD','TRAINER','BATTLEFIELD','HEALER','AUCTIONEER','GUARD','STABLEMASTER','INNKEEPER','ARMORER') collate utf8_unicode_ci default NULL,
  `type` enum('BEAST','DRAGON','DAEMON','ELEMENTAL','GIANT','UNDEAD','HUMANOID','CRITTER','TOTEM') collate utf8_unicode_ci default NULL,
  `description` text collate utf8_unicode_ci,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for npc_vardesc
-- ----------------------------
CREATE TABLE `npc_vardesc` (
  `variable` varchar(60) collate utf8_unicode_ci NOT NULL,
  `name` varchar(120) collate utf8_unicode_ci default NULL,
  `level` int(1) default NULL,
  `showmissing` tinyint(1) default NULL,
  `default` varchar(120) collate utf8_unicode_ci default NULL,
  `mode` enum('RAW','ALIAS','SCRIPT','GUID','MONEY','QUERY','IMAGE') collate utf8_unicode_ci default NULL,
  `translation` mediumtext collate utf8_unicode_ci,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_dictionaries
-- ----------------------------
CREATE TABLE `system_dictionaries` (
  `index` int(11) NOT NULL auto_increment,
  `group` varchar(30) collate utf8_unicode_ci default NULL,
  `name` varchar(120) collate utf8_unicode_ci default NULL,
  `value` int(120) default NULL,
  `mode` enum('FIXED','DYNAMIC') collate utf8_unicode_ci default NULL,
  `package` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_files
-- ----------------------------
CREATE TABLE `system_files` (
  `index` int(11) NOT NULL auto_increment,
  `type` varchar(40) collate utf8_unicode_ci default NULL,
  `package` int(11) default NULL,
  `filename` varchar(255) collate utf8_unicode_ci default NULL,
  `version` int(11) default NULL,
  `hash` varchar(32) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_hooks
-- ----------------------------
CREATE TABLE `system_hooks` (
  `index` int(11) NOT NULL auto_increment,
  `hook` varchar(128) collate utf8_unicode_ci default NULL,
  `filename` varchar(128) collate utf8_unicode_ci default NULL,
  `function` varchar(128) collate utf8_unicode_ci default NULL,
  `active` enum('YES','NO') collate utf8_unicode_ci default 'YES',
  `package` int(11) default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_messages
-- ----------------------------
CREATE TABLE `system_messages` (
  `index` int(11) NOT NULL auto_increment,
  `time` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  `type` int(8) default NULL,
  `user` int(11) default NULL,
  `data` text collate utf8_unicode_ci,
  `onceid` varchar(20) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_packages
-- ----------------------------
CREATE TABLE `system_packages` (
  `index` int(11) NOT NULL auto_increment,
  `guid` varchar(32) collate utf8_unicode_ci default NULL,
  `type` varchar(30) collate utf8_unicode_ci default 'MIXED',
  `version` int(11) default NULL,
  `name` varchar(120) collate utf8_unicode_ci default NULL,
  `description` mediumtext collate utf8_unicode_ci,
  `author` varchar(200) collate utf8_unicode_ci default NULL,
  `copyright` varchar(200) collate utf8_unicode_ci default NULL,
  `website` varchar(200) collate utf8_unicode_ci default NULL,
  `installdate` int(11) default NULL,
  `require` mediumtext collate utf8_unicode_ci,
  `status` enum('ACTIVE','INACTIVE','INCOMPLETED','BUGGY','UNINSTALLINIG') collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_packages_install
-- ----------------------------
CREATE TABLE `system_packages_install` (
  `index` int(11) NOT NULL auto_increment,
  `package` int(11) default NULL,
  `imode` enum('SCRIPT','SQL') collate utf8_unicode_ci default NULL,
  `use` enum('INSTALL','ENABLE') collate utf8_unicode_ci default 'INSTALL',
  `data` varchar(120) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_packages_uninstall
-- ----------------------------
CREATE TABLE `system_packages_uninstall` (
  `index` int(11) NOT NULL auto_increment,
  `package` int(11) default NULL,
  `umode` varchar(120) collate utf8_unicode_ci default NULL,
  `use` enum('UNINSTALL','DISABLE') collate utf8_unicode_ci default 'UNINSTALL',
  `data` text collate utf8_unicode_ci,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for system_scheduler
-- ----------------------------
CREATE TABLE `system_scheduler` (
  `index` int(11) NOT NULL auto_increment,
  `timestamp` int(11) default NULL,
  `user` int(11) default NULL,
  `eventid` varchar(120) collate utf8_unicode_ci default NULL,
  `description` varchar(250) collate utf8_unicode_ci default NULL,
  `data` text collate utf8_unicode_ci,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for unit_instance
-- ----------------------------
CREATE TABLE `unit_instance` (
  `index` int(11) NOT NULL auto_increment,
  `guid` int(11) default NULL,
  `data` text collate utf8_unicode_ci,
  `template` int(11) default NULL,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `map` int(11) default NULL,
  `level` int(11) default NULL,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for unit_template
-- ----------------------------
CREATE TABLE `unit_template` (
  `template` int(11) NOT NULL auto_increment COMMENT 'The template ID',
  `schema` longtext collate utf8_unicode_ci COMMENT 'Schema data copied to data field on object creation',
  `name` varchar(250) collate utf8_unicode_ci default NULL COMMENT 'Unit name',
  `subname` varchar(250) collate utf8_unicode_ci default NULL COMMENT 'Unit subname',
  `icon` varchar(250) collate utf8_unicode_ci default NULL COMMENT 'Unit Icon',
  `description` mediumtext collate utf8_unicode_ci COMMENT 'Unit Description',
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`template`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for unit_vardesc
-- ----------------------------
CREATE TABLE `unit_vardesc` (
  `variable` varchar(60) collate utf8_unicode_ci NOT NULL,
  `name` varchar(120) collate utf8_unicode_ci default NULL,
  `level` int(1) default NULL,
  `showmissing` tinyint(1) default NULL,
  `default` varchar(120) collate utf8_unicode_ci default NULL,
  `mode` enum('RAW','ALIAS','SCRIPT','GUID','MONEY','QUERY','IMAGE') collate utf8_unicode_ci default NULL,
  `translation` mediumtext collate utf8_unicode_ci,
  `contributor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for users_accounts
-- ----------------------------
CREATE TABLE `users_accounts` (
  `index` int(11) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci default NULL,
  `password` varchar(32) collate utf8_unicode_ci default NULL,
  `email` varchar(80) collate utf8_unicode_ci default NULL,
  `lastip` varchar(15) collate utf8_unicode_ci default NULL,
  `lastlogin` timestamp NULL default NULL,
  `lastaction` int(11) default NULL,
  `online` tinyint(1) default NULL,
  `level` enum('BANNED','USER','EDITOR','MODERATOR','ADMIN') collate utf8_unicode_ci default 'USER',
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `char_template` VALUES ('1', 'map=1&x=25&y=25&visible=1&state=NORMAL&model=char-pillard.png&INT=10&STR=50&DEX=10&CON=3&WIS=3&CHA=20&money=2000', 'Human', 'portraits/clans_alchemist.gif', null, 'This is a human player. It is said that human players tend to have better abilities on trade operations that any other race.', '0');
INSERT INTO `char_template` VALUES ('2', 'map=1&x=25&y=25&visible=1&state=NORMAL&model=char-cape-grise.png&INT=40&STR=10&DEX=10&CON=50&WIS=50&CHA=10&money=1000', 'Elf', 'portraits/neutral_elfranger.gif', null, 'This is an elf player.', '0');
INSERT INTO `char_vardesc` VALUES ('race', 'Race', '0', '0', '(Unknown)', 'RAW', null, '0');
INSERT INTO `char_vardesc` VALUES ('money', 'Money', '0', '1', '42', 'MONEY', null, '0');
INSERT INTO `char_vardesc` VALUES ('itelligence', 'Itelligence', '0', '1', '10', 'SCRIPT', 'if ($var > 90) {\r\n	$color=\'red\';\r\n} elseif ($var > 50) {\r\n	$color=\'blue\';\r\n} elseif ($var > 30) {\r\n	$color=\'green\';\r\n} else {\r\n	$color=\'grey\';\r\n}\r\nreturn \"<font color=\\\"$color\\\">$var %</font>\";', '0');
INSERT INTO `data_maps` VALUES ('1', 'Luskan Village', 'Luskan is a small village on the eastern side of mount Solomir. It is an old and very small village that is mostly consisted of old people and visitors.\r\nIt is said that the village was founded because of a sacred spring that was located nearby. That spring was said that had healing powers and could cure almost any diseace! Nevertheless, no one has ever head of it again for many, many years. Only the legend and some ancient ruins remains nowadays to remind the people the past days...', 'Luskan is a small village on the eastern side of mount Solomir. It is an old and very small village that is mostly consisted of old people and visitors.\r\nIt is said that the village was founded because of a sacred spring that was located nearby. That spring was said that had healing powers and could cure almost any diseace! Nevertheless, no one has ever head of it again for many, many years. Only the legend and some ancient ruins remains nowadays to remind the people the past days...', 'z-field-ext-1-2.png', 'luskan-vlg', '50', '0');
INSERT INTO `data_religions` VALUES ('1', 'Deites', 'ÓõæçôÞóåéò', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('2', 'Jaccob', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('3', 'Corellon Larethian', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('4', 'Ehlonna', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('5', 'Erythnul', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('6', 'Fharlanghn', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('7', 'Garl Glittergold', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('8', 'Gruumsh', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('9', 'Heironeous', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('10', 'Hextor', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('11', 'Kord', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('12', 'Moradin', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('13', 'Nerull', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('14', 'Obad-Hai', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('15', 'Olidammara', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('16', 'Perlor', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('17', 'St. Cuthbert', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('18', 'Vecna', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('19', 'Wee Jas', '', null, null, null, null, null, '0');
INSERT INTO `data_religions` VALUES ('20', 'Yondalla', '', null, null, null, null, null, '0');
INSERT INTO `data_spawn` VALUES ('1', '263', '260', '1', '5', '1', '100', null, '0');
INSERT INTO `data_spawn` VALUES ('2', '263', '516', '1', '5', '1', '100', null, '0');
INSERT INTO `data_spawn_times` VALUES ('1', '1', '0');
INSERT INTO `data_spawn_times` VALUES ('2', '2', '0');
INSERT INTO `data_tips` VALUES ('1', 'interface.main', '', 'NORMAL', 'Game interface', '<div style=\"padding: 5px;\">\r\n<b>Welcome to GloryLands!</b><br />\r\n<p>This is the main game interface. From here you can do all the game actions.</p>\r\n<p>On your left, you can see the map. Moving your cursor to the middle-bottom of your character shows a highlighted green area. This area shows the maximum range you can move. You can click on a green rectange to move to the specified position.</p>\r\n<p>On your right, you can see the status bar. The status bar contains your character status, the main menu buttons and the chat window</p>\r\n<p>On the bottom, you can see the quick access bar. You can drag items here from your bag, spellbooks or information windows, and use them with a single click</p>\r\n</div>', '2');
INSERT INTO `data_tips` VALUES ('2', 'map.grid.get', '%map% ! 1', 'NORMAL', 'Discovery', '<p>You have just discovered a new area!<br />Always have your eyes open for places that you can enter!</p>', '3');
INSERT INTO `data_tips` VALUES ('3', 'interface.dropdown', '', 'NORMAL', 'Dropdown Menu', '<p><strong>Dropdown Menu</strong></p>\r\n<p>You can perform various actions by right-clicking on a map object:</p>\r\n<ul>\r\n<li><img style=\"vertical-align: middle;\" src=\"images/UI/piemenu/help.gif\" alt=\"\" align=\"absmiddle\" />Clicking on this icon will show more details about the object</li>\r\n<li><img style=\"vertical-align: middle;\" src=\"images/UI/piemenu/take.gif\" alt=\"\" align=\"absmiddle\" />Pick up this item (if able)</li>\r\n<li><img style=\"vertical-align: middle;\" src=\"images/UI/piemenu/find.gif\" alt=\"\" align=\"absmiddle\" />Search this item</li>\r\n</ul>', '1');
INSERT INTO `gameobject_template` VALUES ('1', 'z=0&visible=1&model=&level=1&displayname=Object', 'Testing Object', '(Developing Usage)', 'inventory/box128.png', null, null, '0');
INSERT INTO `gameobject_vardesc` VALUES ('mixinfo', 'Mixing', '0', '0', null, 'SCRIPT', 'return \"<font size=\\\"-1\\\">\".gl_mix_visualize($var,true, true).\"</font>\";', '0');
INSERT INTO `interface_module_assign` VALUES ('1', 'interface.main', 'CHATWIN', '2', '10');
INSERT INTO `interface_module_assign` VALUES ('2', 'interface.main', 'SIDEBAR', '0', '1');
INSERT INTO `interface_module_assign` VALUES ('3', 'interface.main', 'QUICKBAR', '4', '10');
INSERT INTO `interface_module_assign` VALUES ('4', 'interface.main', 'AUDIO', '5', '10');
INSERT INTO `interface_module_resources` VALUES ('1', 'CHATWIN', 'JS', '{DATA.MODULE}/mod_chat/chatapi.js');
INSERT INTO `interface_module_resources` VALUES ('2', 'CHATWIN', 'CSS', '{DATA.MODULE}/mod_chat/styles/style.css');
INSERT INTO `interface_module_resources` VALUES ('3', 'SIDEBAR', 'JS', '{DATA.MODULE}/mod_sidebar/sidebar-feed.js');
INSERT INTO `interface_module_resources` VALUES ('4', 'SIDEBAR', 'CSS', '{DATA.MODULE}/mod_sidebar/style.css');
INSERT INTO `interface_module_resources` VALUES ('5', 'QUICKBAR', 'CSS', '{DATA.MODULE}/mod_quickbar/quickbar.css');
INSERT INTO `interface_module_resources` VALUES ('6', 'QUICKBAR', 'JS', '{DATA.MODULE}/mod_quickbar/quickbar.js');
INSERT INTO `interface_module_resources` VALUES ('7', 'AUDIO', 'JS', '{DATA.MODULE}/mod_audio/soundapi.js');
INSERT INTO `interface_modules` VALUES ('CHATWIN', 'Chat Window', 'Cross-player chat window and system message receiver for any User Interface', 'chat');
INSERT INTO `interface_modules` VALUES ('SIDEBAR', 'Side Bar', 'A sidebar that displays the user\'s current statistics', 'sidebar');
INSERT INTO `interface_modules` VALUES ('QUICKBAR', 'Qucik Access Bar', 'A bar with 12 buttons with droppable/customizable ability that allows user to hold there items and actions', 'quickbar');
INSERT INTO `interface_modules` VALUES ('AUDIO', 'Audio Provider', 'This module provides the sound to the game', 'audio');
INSERT INTO `item_template` VALUES ('1', null, 'Light Sword', 'A quite light sword, mostly used by dwarfs', 'WEAPON', 'ONEHAND-SWORD', 'inventory/Kunai-128x128.png', '1', '2', '1', '0', '1', '5', '10');
INSERT INTO `item_template` VALUES ('2', null, 'Woodsman Sword', 'A Wooden excercising sword', 'WEAPON', 'TWOHAND-SWORD', 'inventory/Kunai-128x128.png', '2', '20', '0', '0', '1', '10', '20');
INSERT INTO `item_template` VALUES ('3', 'slots=10', 'Woolen Bag', 'A bag made of wool. This bag can carry up to 10 items', 'CONTAINER', 'GENERIC', 'inventory/box128.png', '1', '5', '0', '0', '1', '400', '500');
INSERT INTO `item_template` VALUES ('4', null, 'Dragon Book', 'One of the sacrest books of the Wulz\'ar Kingdom!', 'BOOK', null, 'inventory/Address-Book-128x128.png', '3', '20', '20', '0', '10', '800', '1000');
INSERT INTO `item_template` VALUES ('5', null, 'Fire orb', 'This is a small, soft orb with a fire rune carved on it', 'CONSUMABLE', 'GEM', 'inventory/orbz-fire-128x128.png', '4', '100', '10', '0', '20', '50', '150');
INSERT INTO `item_vardesc` VALUES ('quality', 'Quality', '0', '1', 'Common', 'ALIAS', '0=%3Cfont+color%3D%23666666%3EJunk%3C%2Ffont%3E&1=%3Cfont+color%3D%23009900%3ENormal%3C%2Ffont%3E&2=%3Cfont+color%3D%230033CC%3ERare%3C%2Ffont%3E&3=%3Cfont+color%3D%23663399%3ELegendary%3C%2Ffont%3E&4=%3Cfont+color%3D%23FF9900%3EEpic%3C%2Ffont%3E&5=%3Cfont+color%3D%23FF0000%3EHeroic%3C%2Ffont%3E', '0');
INSERT INTO `item_vardesc` VALUES ('class', 'Class', '0', '1', 'Unknown', 'SCRIPT', '$name = ucfirst(strtolower($var));\r\nif ($var==\'CONTAINER\') {\r\n  $name .= \" <a href=\\\"javascript:gloryIO(\'?a=interface.container&guid=\".$guid.\"\');\\\"><small><em>(Open)<em></small></a>\";\r\n}\r\nreturn $name;\r\n', '0');
INSERT INTO `item_vardesc` VALUES ('parent', 'Is On', '0', '0', null, 'GUID', null, '0');
INSERT INTO `item_vardesc` VALUES ('subclass', 'Subclass', '0', '0', 'Unknown', 'SCRIPT', 'return ucfirst(strtolower($var));', '0');
INSERT INTO `item_vardesc` VALUES ('mixinfo', 'Mixing', '0', '0', null, 'SCRIPT', 'return \"<font size=\\\"-1\\\">\".gl_mix_visualize($var,true, true).\"</font>\";', '0');
INSERT INTO `npc_instance` VALUES ('1', '263', 'a:0:{}', '3', '29', '27', '1', '8', 'marchande.png', '1', 'NORMAL', '0');
INSERT INTO `npc_instance` VALUES ('2', '519', 'a:0:{}', '4', '37', '27', '1', '5', 'ryu.png', '1', 'NORMAL', '0');
INSERT INTO `npc_template` VALUES ('1', 'level=3&model=zombie-vert.o&state=NORMAL&visible=1', 'Wuz Grub', 'ORC', 'WARRIOR', 'portraits/lod_cultist.gif', 'CHAT,QUEST', 'HUMANOID', 'Wuz Grub is one of the finest warriors of the stratholme kingdom', '0');
INSERT INTO `npc_template` VALUES ('2', 'level=3&model=samurai-maitre.o&state=NORMAL&visible=1', 'Joe Amaroth', 'HUMAN', 'PALADIN', 'portraits/clans_kingsguard.gif', 'CHAT', 'HUMANOID', 'Joe Amaroth is a kingsguard!', '0');
INSERT INTO `npc_template` VALUES ('3', 'level=8&model=marchande.png&state=NORMAL&visible=1', 'Riran Hod', 'HUMAN', 'MERCHANT', 'portraits/clans_tenderfoot.gif', 'CHAT,VENDOR', 'HUMANOID', 'Riran Hod is a merchant. He is known to be wherever you want him!', '0');
INSERT INTO `npc_template` VALUES ('4', 'level=5&model=ryu.png&state=NORMAL&visible=1', 'Hinmhon Josyh', 'HUMAN', 'MERCHANT', 'portraits/empire_marksman.gif', 'VENDOR', 'HUMANOID', 'Hinmhon Josyh is a merchant. His speciality is armor.\r\n', '0');
INSERT INTO `npc_vardesc` VALUES ('name', 'Name', '1', '0', null, 'RAW', null, '0');
INSERT INTO `npc_vardesc` VALUES ('race', 'Race', '1', '0', null, 'SCRIPT', 'return ucfirst(strtolower($var));', '0');
INSERT INTO `npc_vardesc` VALUES ('class', 'Class', '1', '0', null, 'SCRIPT', 'return ucfirst(strtolower($var));', '0');
INSERT INTO `npc_vardesc` VALUES ('type', 'Type', '1', '0', null, 'SCRIPT', 'return ucfirst(strtolower($var));', '0');
INSERT INTO `npc_vardesc` VALUES ('born_city', 'Born in city', '1', '0', null, 'GUID', null, '0');
INSERT INTO `system_dictionaries` VALUES ('1', 'GUID', 'CHAR', '0', 'FIXED', '0');
INSERT INTO `system_dictionaries` VALUES ('2', 'GUID', 'UNIT', '1', 'FIXED', '0');
INSERT INTO `system_dictionaries` VALUES ('3', 'GUID', 'ITEM', '2', 'FIXED', '0');
INSERT INTO `system_dictionaries` VALUES ('4', 'GUID', 'NPC', '3', 'FIXED', '0');
INSERT INTO `system_dictionaries` VALUES ('5', 'GUID', 'GAMEOBJECT', '4', 'FIXED', '0');
INSERT INTO `system_dictionaries` VALUES ('6', 'GUID', 'SPELL', '5', 'FIXED', '0');
INSERT INTO `system_hooks` VALUES ('1', 'map.move', 'hook-portal.php', 'portal_map_move', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('2', 'chat.command', 'hook-chatcommands.php', 'chat_admin', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('3', 'map.updategrid', 'hook-chatcommands.php', 'chat_notify_zidchange', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('4', 'interface.dropdown', 'hook-admin.php', 'admin_hook_dropdown', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('5', 'grid.alter', 'hook-base.php', 'hb_dynamic_grid_alter', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('6', 'system.init_operation', 'hook-distance.php', 'opinitTranslateID', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('7', 'system.init_operation', 'hook-chatcommands.php', 'chat_module_initialize', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('8', 'system.clientpoll', 'hook-sidebar.php', 'sidebar_data_feed', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('9', 'system.schedule', 'hook-itemuse.php', 'itemuse_schedule_hook', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('10', 'system.guid.update_end', 'hook-base.php', 'hb_update_user_session', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('11', 'map.render', 'hook-distance.php', 'renderrange', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('12', 'system.init_operation', 'hook-sidebar.php', 'sidebar_data_initialize', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('13', 'interface.dropdown', 'hook-itemuse.php', 'itemuse_dropdown', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('14', 'map.render', 'hook-itemuse.php', 'itemuse_quickbar_init', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('15', 'system.guid.deleted', 'hook-itemuse.php', 'itemuse_guid_deleted', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('16', 'item.pickup', 'hook-pickup.php', 'pickuphook_check_compatibility', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('17', 'system.guid.deleted', 'hook-base.php', 'hb_guid_deleted', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('18', 'system.complete_operation', 'hook-sidebar.php', 'sidebar_data_feed', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('19', 'system.complete_operation', 'hook-tips.php', 'tipshook_complete_operation', 'YES', '0');
INSERT INTO `unit_template` VALUES ('1', 'owner=0&religion=1&citizens=20&soldiers=0&x=12', 'Luskan Village', null, 'elements/mini/city.gif', 'Luskan village is a small, quite village oriented on the north side of the dark cliff.', '0');
INSERT INTO `unit_template` VALUES ('2', 'owner=1292&religion=1&citizens=20&soldiers=0', 'Mormon City', 'custom', 'elements/mini/capital.gif', 'Mormon is the capital of the strongholme kingdom. The king Melaton VI currently rules this area...', '0');
INSERT INTO `unit_vardesc` VALUES ('owner', 'Unit owner', '1', '0', '0', 'GUID', null, '0');
INSERT INTO `unit_vardesc` VALUES ('religion', 'City Religion', '1', '0', 'NEUTRAL', 'QUERY', 'SELECT `name` FROM `data_religions` WHERE `index` = $var', '0');
INSERT INTO `unit_vardesc` VALUES ('citizens', 'Number of citizens', '1', '0', '0', 'RAW', null, '0');
INSERT INTO `unit_vardesc` VALUES ('soldiers', 'Number of soldiers', '1', '0', '0', 'RAW', null, '0');
INSERT INTO `users_accounts` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '', null, null, null, null, 'ADMIN');
INSERT INTO `users_accounts` VALUES ('2', 'player', '912af0dff974604f1321254ca8ff38b6', '', null, null, null, null, 'USER');
