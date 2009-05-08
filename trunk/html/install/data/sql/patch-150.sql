/*
Added: Parent field on npc_instance
Added: PathWalk for NPC
Added: Sample pathwaln NPC (Felicia Marcan)
Added: Translation fields on variable description
Updated: Module interface position IDs
Updated: All models are now sprited

Date: 8/5/2009 12:02:40 pm
*/

-- ----------------------------
-- Update translation fields
-- ----------------------------
UPDATE `char_vardesc` SET `name` = '{#RACE#}' WHERE `variable` = 'race';
UPDATE `char_vardesc` SET `name` = '{#MONEY#}' WHERE `variable` = 'money';
UPDATE `char_vardesc` SET `name` = '{#ITELLIGENCE#}' WHERE `variable` = 'itelligence';
UPDATE `item_vardesc` SET `name` = '{#QUALITY#}' WHERE `variable` = 'quality';
UPDATE `item_vardesc` SET `name` = '{#CLASS#}' WHERE `variable` = 'class';
UPDATE `item_vardesc` SET `name` = '{#IS_ON#}' WHERE `variable` = 'parent';
UPDATE `item_vardesc` SET `name` = '{#SUBCLASS#}' WHERE `variable` = 'subclass';
UPDATE `item_vardesc` SET `name` = '{#MIXING#}' WHERE `variable` = 'mixinfo';
UPDATE `item_vardesc` SET `name` = '{#SLOTS#}' WHERE `variable` = 'slots';
UPDATE `gameobject_vardesc` SET `name` = '{#MIXING#}' WHERE `variable` = 'mixinfo';

-- ----------------------------
-- Update interface_module_assign
-- ----------------------------
UPDATE `interface_module_assign` SET `position` = 103 WHERE `module` = 'CHATWIN' AND `action` = 'interface.main';
UPDATE `interface_module_assign` SET `position` = 101 WHERE `module` = 'SIDEBAR' AND `action` = 'interface.main';
UPDATE `interface_module_assign` SET `position` = 201 WHERE `module` = 'QUICKBAR' AND `action` = 'interface.main';
UPDATE `interface_module_assign` SET `position` = 500 WHERE `module` = 'AUDIO' AND `action` = 'interface.main';

-- -------------------------------------------
-- Insert the first aplha of inventory module
-- -------------------------------------------
INSERT INTO `interface_module_resources` VALUES ('8', 'INVENTORY', 'JS', '{DATA.MODULE}/mod_inventory/inventory.js');
INSERT INTO `interface_module_resources` VALUES ('9', 'INVENTORY', 'CSS', '{DATA.MODULE}/mod_inventory/style.css');
INSERT INTO `interface_modules` VALUES ('INVENTORY', 'Character Inventory', 'Provides the character inventory and equipment slots inside the main window', 'inventory', 'ENABLED');
INSERT INTO `interface_module_assign` VALUES ('5', 'interface.main', 'INVENTORY', '102', '1');

-- ----------------------------
-- Update system_messages
-- ----------------------------
ALTER TABLE `npc_instance` ADD `parent` int(11) default '0' AFTER `guid`;

-- ----------------------------
-- Insert the new NPC template
-- ----------------------------
INSERT INTO `npc_template` VALUES ('5', 'level=5&model=sprites/woman_npc01_walk.png&state=NORMAL&visible=1', 'Felicia Marcan', 'HUMAN', 'MERCHANT', 'portraits/clans_kingsguard.gif', 'VENDOR', 'UNDEAD', 'Felicia Marcan is a merchant. She can be found around the Luskan Village.', '0');

-- ----------------------------
-- Update NPC instance tables
-- ----------------------------
UPDATE `npc_instance` SET `model` = 'sprites/man_npc02_walk.png' WHERE `index` = 1;
UPDATE `npc_instance` SET `model` = 'sprites/vanica_walk.png' WHERE `index` = 2;
UPDATE `npc_template` SET `icon` = 'portraits/empire_marksman.gif' WHERE `template` = 3;
UPDATE `npc_template` SET `icon` = 'portraits/clans_tenderfoot.gif' WHERE `template` = 4;
INSERT INTO `npc_instance` VALUES ('3', '775', null, 'a:0:{}', '5', '40', '20', '1', '5', 'sprites/woman_npc01_walk.png', '1', 'NORMAL', '0');

-- ----------------------------
-- Create new tables
-- ----------------------------
CREATE TABLE `data_npc_walk` (
  `guid` int(11) NOT NULL default '0',
  `path` longtext collate latin1_general_ci COMMENT 'a:0:{}',
  `status` enum('STOP','WALK','PAUSE') collate latin1_general_ci default 'STOP',
  `step_delay` int(11) default '1',
  `speed` int(11) default '5',
  `current_pos` int(11) default '0',
  PRIMARY KEY  (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
INSERT INTO `data_npc_walk` VALUES ('775', 'a:4:{i:0;a:14:{i:0;a:2:{i:0;i:27;i:1;i:20;}i:1;a:2:{i:0;i:28;i:1;i:20;}i:2;a:2:{i:0;i:29;i:1;i:20;}i:3;a:2:{i:0;i:30;i:1;i:20;}i:4;a:2:{i:0;i:31;i:1;i:20;}i:5;a:2:{i:0;i:32;i:1;i:20;}i:6;a:2:{i:0;i:33;i:1;i:20;}i:7;a:2:{i:0;i:34;i:1;i:20;}i:8;a:2:{i:0;i:35;i:1;i:20;}i:9;a:2:{i:0;i:36;i:1;i:20;}i:10;a:2:{i:0;i:37;i:1;i:20;}i:11;a:2:{i:0;i:38;i:1;i:20;}i:12;a:2:{i:0;i:39;i:1;i:20;}i:13;a:2:{i:0;i:40;i:1;i:20;}}i:1;a:10:{i:0;a:2:{i:0;i:40;i:1;i:20;}i:1;a:2:{i:0;i:40;i:1;i:21;}i:2;a:2:{i:0;i:40;i:1;i:22;}i:3;a:2:{i:0;i:40;i:1;i:23;}i:4;a:2:{i:0;i:40;i:1;i:24;}i:5;a:2:{i:0;i:40;i:1;i:25;}i:6;a:2:{i:0;i:40;i:1;i:26;}i:7;a:2:{i:0;i:40;i:1;i:27;}i:8;a:2:{i:0;i:40;i:1;i:28;}i:9;a:2:{i:0;i:40;i:1;i:29;}}i:2;a:14:{i:0;a:2:{i:0;i:40;i:1;i:29;}i:1;a:2:{i:0;i:39;i:1;i:29;}i:2;a:2:{i:0;i:38;i:1;i:29;}i:3;a:2:{i:0;i:37;i:1;i:29;}i:4;a:2:{i:0;i:36;i:1;i:29;}i:5;a:2:{i:0;i:35;i:1;i:29;}i:6;a:2:{i:0;i:34;i:1;i:29;}i:7;a:2:{i:0;i:33;i:1;i:29;}i:8;a:2:{i:0;i:32;i:1;i:29;}i:9;a:2:{i:0;i:31;i:1;i:29;}i:10;a:2:{i:0;i:30;i:1;i:29;}i:11;a:2:{i:0;i:29;i:1;i:29;}i:12;a:2:{i:0;i:28;i:1;i:29;}i:13;a:2:{i:0;i:27;i:1;i:29;}}i:3;a:10:{i:0;a:2:{i:0;i:27;i:1;i:29;}i:1;a:2:{i:0;i:27;i:1;i:28;}i:2;a:2:{i:0;i:27;i:1;i:27;}i:3;a:2:{i:0;i:27;i:1;i:26;}i:4;a:2:{i:0;i:27;i:1;i:25;}i:5;a:2:{i:0;i:27;i:1;i:24;}i:6;a:2:{i:0;i:27;i:1;i:23;}i:7;a:2:{i:0;i:27;i:1;i:22;}i:8;a:2:{i:0;i:27;i:1;i:21;}i:9;a:2:{i:0;i:27;i:1;i:20;}}}\r\n', 'WALK', '10', '2', '0');

-- ----------------------------
-- Register the new hook
-- ----------------------------
INSERT INTO `system_hooks` VALUES ('11', 'hook-npcpath.php', 'YES', '0');


-- ----------------------------
-- Update database revision
-- ----------------------------
UPDATE `db_version` SET `revision` = 150;
