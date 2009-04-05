/*
Added: Multi-frame sprite characters
Date: 5/4/2009 2:48:40 am
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Update character templates
-- ----------------------------

UPDATE `char_template` SET `schema` = 'map=1&x=25&y=25&visible=1&state=NORMAL&model=sprites_map_claudius.png&INT=10&STR=50&DEX=10&CON=3&WIS=3&CHA=20&money=2000' WHERE `template` = 1;
UPDATE `char_template` SET `schema` = 'map=1&x=25&y=25&visible=1&state=NORMAL&model=sprites_map_laila.png&INT=40&STR=10&DEX=10&CON=50&WIS=50&CHA=10&money=1000' WHERE `template` = 2;
UPDATE `char_instance` SET `model` = 'sprites_map_claudius.png' WHERE `template` = 1;
UPDATE `char_instance` SET `model` = 'sprites_map_laila.png' WHERE `template` = 2;

-- ----------------------------
-- Update database revision
-- ----------------------------

UPDATE `db_version` SET `revision` = 136;
