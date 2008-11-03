/*
Added: Registration Quiz
Date: 14/10/2008 12:57:44 рм
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for data_regquiz_answers
-- ----------------------------
CREATE TABLE `data_regquiz_answers` (
  `index` int(11) NOT NULL auto_increment,
  `question` int(11) default NULL,
  `answer` varchar(250) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_regquiz_data
-- ----------------------------
CREATE TABLE `data_regquiz_data` (
  `index` int(11) NOT NULL auto_increment,
  `answer` int(11) default NULL,
  `v_modifier` varchar(120) collate latin1_general_ci default NULL,
  `v_value` varchar(120) collate latin1_general_ci default NULL,
  `i_guid` int(11) default NULL,
  `i_parent` int(11) default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- ----------------------------
-- Table structure for data_regquiz_questions
-- ----------------------------
CREATE TABLE `data_regquiz_questions` (
  `index` int(11) NOT NULL auto_increment,
  `question` mediumtext collate latin1_general_ci,
  `title` varchar(250) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`index`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
