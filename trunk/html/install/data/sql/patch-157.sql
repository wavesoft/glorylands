/*
Added: Equip field on item_template

Date: 8/5/2009 12:02:40 pm
*/

-- ----------------------------
-- Alter table
-- ----------------------------
ALTER TABLE `item_template` ADD `equip` enum('NONE','HEAD','BACK','NECK','AMMO','HAND1','CHEST','HAND2','LEGS','HANDS','FEET','POUCHE') NOT NULL DEFAULT 'NONE';

-- ----------------------------
-- Update default items
-- ----------------------------
UPDATE `item_template` SET `equip` = 'HAND1' WHERE `template` = 1;
UPDATE `item_template` SET `equip` = 'BACK' WHERE `template` = 2;
UPDATE `item_template` SET `equip` = 'POUCHE' WHERE `template` = 3;
UPDATE `item_template` SET `equip` = 'NONE' WHERE `template` = 4;
UPDATE `item_template` SET `equip` = 'NONE' WHERE `template` = 5;

-- ----------------------------
-- Update database revision
-- ----------------------------
UPDATE `db_version` SET `revision` = 157;
