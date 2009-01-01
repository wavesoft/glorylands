/*
Added: Price and stackable fields on item instance table
Date: 30/12/2008 0:53:40
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure alteration
-- ----------------------------
ALTER TABLE `item_template` ADD `stackable` INT NOT NULL DEFAULT '0';
ALTER TABLE `item_template` ADD `sell_price` INT NOT NULL DEFAULT '0';
ALTER TABLE `item_template` ADD `buy_price` INT NOT NULL DEFAULT '0';
