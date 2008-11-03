/*
Added: Intoduction Tips System
Date: 3/11/2008 2:10:42 μμ
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for data_tips
-- ----------------------------
CREATE TABLE `data_tips` (
  `index` int(11) NOT NULL auto_increment,
  `trigger_action` varchar(60) collate latin1_general_ci default NULL,
  `trigger_request` mediumtext collate latin1_general_ci,
  `importance` enum('HIGH','NORMAL','LOW') collate latin1_general_ci default NULL,
  `title` varchar(120) collate latin1_general_ci default NULL,
  `tip` longtext collate latin1_general_ci,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `data_tips` VALUES ('1', 'interface.main', '', 'NORMAL', 'Game interface', '<div style=\"padding: 5px;\">\r\n<b>Welcome to GloryLands!</b><br />\r\n<p>This is the main game interface. From here you can do all the game actions.</p>\r\n<p>On your left, you can see the map. Moving your cursor to the middle-bottom of your character shows a highlighted green area. This area shows the maximum range you can move. You can click on a green rectange to move to the specified position.</p>\r\n<p>On your right, you can see the status bar. The status bar contains your character status, the main menu buttons and the chat window</p>\r\n<p>On the bottom, you can see the quick access bar. You can drag items here from your bag, spellbooks or information windows, and use them with a single click</p>\r\n</div>');
INSERT INTO `data_tips` VALUES ('2', 'admin.addobj', null, 'NORMAL', 'Administration', '<p style=\"margin: 5px;\">Using this button you can place new buildings in the active map.</p>');

-- ----------------------------
-- Hook registration
-- ----------------------------
INSERT INTO system_hooks (hook, filename, function, active, package) VALUES ("system.complete_operation", "hook-tips.php", "tipshook_complete_operation", "YES", 0);
