DELETE FROM `interface_modules` WHERE `index` = 'CHATWIN';
DELETE FROM `interface_module_resources` WHERE `module` = 'CHATWIN';
DROP TABLE `mod_chat_channel_registrations`;
DELETE FROM `interface_module_assign` WHERE `module` = 'CHATWIN';
