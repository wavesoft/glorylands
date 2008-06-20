SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for mod_chat_channel_registrations
-- ----------------------------
CREATE TABLE `mod_chat_channel_registrations` (
  `index` int(11) NOT NULL auto_increment,
  `user` int(11) default NULL,
  `channel` varchar(30) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

INSERT INTO `interface_module_resources` (`module`, `mode`, `filename`) VALUES ('CHATWIN', 'JS', '{DATA.MODULE}/mod_chat/chatapi.js');
INSERT INTO `interface_module_resources` (`module`, `mode`, `filename`) VALUES ('CHATWIN', 'CSS', '{DATA.MODULE}/mod_chat/styles/style.css');
INSERT INTO `interface_modules` (`index`, `name`, `description`, `filename`) VALUES ('CHATWIN' ,'Chat Window', 'Cross-player chat window and system message receiver for any User Interface', 'chat');
INSERT INTO `interface_module_assign` (`action`, `module`, `position`, `weight`) VALUES ('interface.main', 'CHATWIN', 2, 1);
