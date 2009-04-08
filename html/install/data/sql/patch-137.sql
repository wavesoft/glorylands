/*
Added: International translations for GUIDs
Date: 5/4/2009 3:03:55 pm
*/

-- ----------------------------
-- Update system_messages
-- ----------------------------
ALTER TABLE `char_instance` ADD `speed` INT NOT NULL DEFAULT '0';

-- ----------------------------
-- Update system_messages
-- ----------------------------

DROP TABLE `system_hooks`;
CREATE TABLE `system_hooks` (
  `index` int(11) NOT NULL auto_increment,
  `filename` varchar(250) collate utf8_unicode_ci default NULL,
  `active` enum('YES','NO') collate utf8_unicode_ci default 'YES',
  `package` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `system_hooks` VALUES ('1', 'hook-admin.php', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('2', 'hook-base.php', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('3', 'hook-chatcommands.php', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('4', 'hook-distance.php', 'NO', '0');
INSERT INTO `system_hooks` VALUES ('5', 'hook-itemuse.php', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('6', 'hook-pickup.php', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('7', 'hook-portal.php', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('8', 'hook-sidebar.php', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('9', 'hook-tips.php', 'YES', '0');
INSERT INTO `system_hooks` VALUES ('10', 'hook-clicktogo.php', 'YES', '0');


-- ----------------------------
-- Update database revision
-- ----------------------------

UPDATE `db_version` SET `revision` = 137;
