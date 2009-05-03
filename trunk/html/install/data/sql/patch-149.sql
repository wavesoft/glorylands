/*
Added: Speed field on char_instance
Updated: Hooking system table
Date: 5/4/2009 3:03:55 pm
*/

-- ----------------------------
-- Update system_messages
-- ----------------------------
ALTER TABLE `interface_modules` ADD `status` enum('ENABLED','DISABLED') collate latin1_general_ci default 'ENABLED';

-- ----------------------------
-- Update database revision
-- ----------------------------

UPDATE `db_version` SET `revision` = 149;
