/*
Removed: hook-pickup.php
Added: hook-inventory.php

Date: 8/5/2009 12:02:40 pm
*/

-- ----------------------------
-- Update interrupts
-- ----------------------------
UPDATE `system_hooks` SET `filename`='hook-inventory.php' WHERE (`filename`='hook-pickup.php')  

-- ----------------------------
-- Update database revision
-- ----------------------------
UPDATE `db_version` SET `revision` = 153;
