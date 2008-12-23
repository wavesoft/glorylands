/*
Updated: Char template to use new models
Date: 3/11/2008 2:20:40 μμ
*/

SET FOREIGN_KEY_CHECKS=0;

UPDATE `char_template` SET `schema` = 'map=1&x=5&y=5&visible=1&state=NORMAL&model=char-pillard.png&INT=10&STR=50&DEX=10&CON=3&WIS=3&CHA=20' WHERE `template` = 1;
UPDATE `char_template` SET `schema` = 'map=4&x=5&y=5&visible=1&state=NORMAL&model=char-cape-grise.png&INT=40&STR=10&DEX=10&CON=50&WIS=50&CHA=10' WHERE `template` = 2;