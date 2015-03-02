/* UPDATE DATABASE TABLES */
ALTER TABLE `groups` ADD `code` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `name`;
ALTER TABLE `groups` ADD UNIQUE (`code`);
ALTER TABLE `ledgers` ADD `code` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `name` ;
ALTER TABLE `ledgers` ADD UNIQUE (`code`);
