/*
Added: Contributor field on each table the user can edit
Added: MODERATOR Level on user
Date: 3/11/2008 2:20:40 μμ
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure alteration
-- ----------------------------
-- ===================
--  Account Entries
-- ===================
ALTER TABLE `users_accounts` CHANGE `level` `level` ENUM( 'BANNED', 'USER', 'EDITOR', 'MODERATOR', 'ADMIN' ) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT 'USER';

-- ===================
--  GUID Contribution
-- ===================
ALTER TABLE `char_template` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `char_vardesc` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `char_instance` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `gameobject_template` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `gameobject_vardesc` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `gameobject_instance` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `item_template` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `item_vardesc` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `item_instance` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `npc_template` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `npc_vardesc` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `npc_instance` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `unit_template` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `unit_vardesc` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `unit_instance` ADD `contributor` INT NOT NULL DEFAULT '0';

-- ===================
--  DATA Contribution
-- ===================
ALTER TABLE `data_maps` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `data_mix_defaults` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `data_mix_iconrules` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `data_mix_icons` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `data_mix_mixgroups` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `data_religions` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `data_spawn` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `data_tips` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `data_regquiz_answers` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `data_regquiz_data` ADD `contributor` INT NOT NULL DEFAULT '0';
ALTER TABLE `data_regquiz_questions` ADD `contributor` INT NOT NULL DEFAULT '0';
