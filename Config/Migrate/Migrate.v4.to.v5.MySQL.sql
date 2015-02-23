/* UPDATE DATABASE TABLES */
ALTER TABLE `groups` CHANGE `id` `id` BIGINT(18) NOT NULL AUTO_INCREMENT;
ALTER TABLE `groups` CHANGE `parent_id` `parent_id` BIGINT(18) NULL DEFAULT '0';
UPDATE `groups` SET `parent_id` = NULL WHERE `parent_id` = 0;
ALTER TABLE `groups` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `groups` ADD UNIQUE KEY `unique_id` (`id`);
ALTER TABLE `groups` ADD UNIQUE KEY `name` (`name`);
ALTER TABLE `groups` ADD KEY `id` (`id`);
ALTER TABLE `groups` ADD KEY `parent_id` (`parent_id`);
ALTER TABLE `groups` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `ledgers` CHANGE `id` `id` BIGINT(18) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ledgers` CHANGE `group_id` `group_id` BIGINT(18) NOT NULL;
ALTER TABLE `ledgers` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `ledgers` CHANGE `op_balance` `op_balance` DECIMAL(25,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `ledgers` CHANGE `op_balance_dc` `op_balance_dc` CHAR(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `ledgers` CHANGE `reconciliation` `reconciliation` INT(1) NOT NULL DEFAULT '0';
ALTER TABLE `ledgers` ADD `notes` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `ledgers` ADD UNIQUE KEY `unique_id` (`id`);
ALTER TABLE `ledgers` ADD UNIQUE KEY `name` (`name`);
ALTER TABLE `ledgers` ADD KEY `id` (`id`);
ALTER TABLE `ledgers` ADD KEY `group_id` (`group_id`);
ALTER TABLE `ledgers` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

RENAME TABLE `entry_types` TO `entrytypes`;
ALTER TABLE `entrytypes` CHANGE `id` `id` BIGINT(18) NOT NULL AUTO_INCREMENT;
ALTER TABLE `entrytypes` CHANGE `label` `label` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `entrytypes` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `entrytypes` CHANGE `description` `description` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `entrytypes` CHANGE `base_type` `base_type` INT(2) NOT NULL DEFAULT '0';
ALTER TABLE `entrytypes` CHANGE `numbering` `numbering` INT(2) NOT NULL DEFAULT '1';
ALTER TABLE `entrytypes` CHANGE `prefix` `prefix` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `entrytypes` CHANGE `suffix` `suffix` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `entrytypes` CHANGE `zero_padding` `zero_padding` INT(2) NOT NULL DEFAULT '0';
ALTER TABLE `entrytypes` CHANGE `bank_cash_ledger_restriction` `restriction_bankcash` INT(2) NOT NULL DEFAULT '1';
ALTER TABLE `entrytypes` ADD UNIQUE KEY `unique_id` (`id`);
ALTER TABLE `entrytypes` ADD UNIQUE KEY `label` (`label`);
ALTER TABLE `entrytypes` ADD KEY `id` (`id`);
ALTER TABLE `entrytypes` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `tags` CHANGE `id` `id` BIGINT(18) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tags` CHANGE `title` `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tags` CHANGE `color` `color` CHAR(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tags` CHANGE `background` `background` CHAR(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tags` ADD UNIQUE KEY `unique_id` (`id`);
ALTER TABLE `tags` ADD UNIQUE KEY `title` (`title`);
ALTER TABLE `tags` ADD KEY `id` (`id`);
ALTER TABLE `tags` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `entries` CHANGE `id` `id` BIGINT(18) NOT NULL AUTO_INCREMENT;
ALTER TABLE `entries` CHANGE `tag_id` `tag_id` BIGINT(18) DEFAULT NULL;
ALTER TABLE `entries` CHANGE `entry_type` `entrytype_id` BIGINT(18) NOT NULL;
ALTER TABLE `entries` CHANGE `number` `number` BIGINT(18) DEFAULT NULL;
ALTER TABLE `entries` CHANGE `date` `date` DATE NOT NULL;
ALTER TABLE `entries` CHANGE `dr_total` `dr_total` DECIMAL(25,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `entries` CHANGE `cr_total` `cr_total` DECIMAL(25,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `entries` CHANGE `narration` `narration` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `entries` ADD UNIQUE KEY `unique_id` (`id`);
ALTER TABLE `entries` ADD KEY `id` (`id`);
ALTER TABLE `entries` ADD KEY `tag_id` (`tag_id`);
ALTER TABLE `entries` ADD KEY `entrytype_id` (`entrytype_id`);
ALTER TABLE `entries` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

RENAME TABLE `entry_items` TO `entryitems`;
ALTER TABLE `entryitems` CHANGE `id` `id` BIGINT(18) NOT NULL AUTO_INCREMENT;
ALTER TABLE `entryitems` CHANGE `entry_id` `entry_id` BIGINT(18) NOT NULL;
ALTER TABLE `entryitems` CHANGE `ledger_id` `ledger_id` BIGINT(18) NOT NULL;
ALTER TABLE `entryitems` CHANGE `amount` `amount` DECIMAL(25,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `entryitems` CHANGE `dc` `dc` CHAR(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `entryitems` CHANGE `reconciliation_date` `reconciliation_date` DATE DEFAULT NULL;
ALTER TABLE `entryitems` ADD UNIQUE KEY `unique_id` (`id`);
ALTER TABLE `entryitems` ADD KEY `id` (`id`);
ALTER TABLE `entryitems` ADD KEY `entry_id` (`entry_id`);
ALTER TABLE `entryitems` ADD KEY `ledger_id` (`ledger_id`);
ALTER TABLE `entryitems` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `settings` CHANGE `id` `id` INT(1) NOT NULL;
ALTER TABLE `settings` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` CHANGE `address` `address` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` CHANGE `email` `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` CHANGE `fy_start` `fy_start` DATE NOT NULL;
ALTER TABLE `settings` CHANGE `fy_end` `fy_end` DATE NOT NULL;
ALTER TABLE `settings` CHANGE `currency_symbol` `currency_symbol` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` CHANGE `date_format` `date_format` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` CHANGE `timezone` `timezone` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` CHANGE `manage_inventory` `manage_inventory` INT(1) NOT NULL DEFAULT '0';
ALTER TABLE `settings` CHANGE `account_locked` `account_locked` INT(1) NOT NULL DEFAULT '0';
ALTER TABLE `settings` ADD `email_use_default` INT(1) NOT NULL DEFAULT '0' AFTER `account_locked`;
ALTER TABLE `settings` CHANGE `email_protocol` `email_protocol` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` CHANGE `email_host` `email_host` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` ADD `email_tls` INT(1) NOT NULL DEFAULT '0' AFTER `email_port`;
ALTER TABLE `settings` CHANGE `email_username` `email_username` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` CHANGE `email_password` `email_password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` ADD `email_from` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `email_password`;
ALTER TABLE `settings` CHANGE `print_paper_height` `print_paper_height` DECIMAL(10,3) NOT NULL DEFAULT '0.00';
ALTER TABLE `settings` CHANGE `print_paper_width` `print_paper_width` DECIMAL(10,3) NOT NULL DEFAULT '0.00';
ALTER TABLE `settings` CHANGE `print_margin_top` `print_margin_top` DECIMAL(10,3) NOT NULL DEFAULT '0.00';
ALTER TABLE `settings` CHANGE `print_margin_bottom` `print_margin_bottom` DECIMAL(10,3) NOT NULL DEFAULT '0.00';
ALTER TABLE `settings` CHANGE `print_margin_left` `print_margin_left` DECIMAL(10,3) NOT NULL DEFAULT '0.00';
ALTER TABLE `settings` CHANGE `print_margin_right` `print_margin_right` DECIMAL(10,3) NOT NULL DEFAULT '0.00';
ALTER TABLE `settings` CHANGE `print_orientation` `print_orientation` CHAR(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` CHANGE `print_page_format` `print_page_format` CHAR(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `settings` ADD PRIMARY KEY (`id`);
ALTER TABLE `settings` ADD UNIQUE KEY `unique_id` (`id`);
ALTER TABLE `settings` ADD KEY `id` (`id`);
ALTER TABLE `settings` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `logs` CHANGE `id` `id` BIGINT(18) NOT NULL AUTO_INCREMENT;
ALTER TABLE `logs` CHANGE `host_ip` `host_ip` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `logs` CHANGE `user` `user` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `logs` CHANGE `url` `url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `logs` CHANGE `user_agent` `user_agent` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `logs` DROP `message_title`;
ALTER TABLE `logs` CHANGE `message_desc` `message` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `logs` ADD UNIQUE KEY `unique_id` (`id`);
ALTER TABLE `logs` ADD KEY `id` (`id`);
ALTER TABLE `logs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

UPDATE `settings` SET `database_version` = '5' WHERE `id` = 1;
UPDATE `settings` SET `date_format` = 'd-M-Y|dd-M-yy' WHERE `id` = 1;
UPDATE `settings` SET `timezone` = 'UTC' WHERE `id` = 1;

/* ADD FOREIGN KEY CONSTRAINT */
ALTER TABLE `groups`
	ADD CONSTRAINT `groups_fk_check_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `groups` (`id`);

ALTER TABLE `ledgers`
	ADD CONSTRAINT `ledgers_fk_check_group_id` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`);

ALTER TABLE `entries`
	ADD CONSTRAINT `entries_fk_check_entrytype_id` FOREIGN KEY (`entrytype_id`) REFERENCES `entrytypes` (`id`),
	ADD CONSTRAINT `entries_fk_check_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);

ALTER TABLE `entryitems`
	ADD CONSTRAINT `entryitems_fk_check_ledger_id` FOREIGN KEY (`ledger_id`) REFERENCES `ledgers` (`id`),
	ADD CONSTRAINT `entryitems_fk_check_entry_id` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`);

/* ADD TRIGGERS */

DROP TRIGGER IF EXISTS `bfins_ledgers`;
DELIMITER //
CREATE TRIGGER `bfins_ledgers` BEFORE INSERT ON `ledgers`
FOR EACH ROW BEGIN
	SET NEW.op_balance_dc = UPPER(NEW.op_balance_dc);
	IF !(NEW.op_balance_dc <=> 'D' OR NEW.op_balance_dc <=> 'C') THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'op_balance_dc restricted to char D or C.';
	END IF;
	IF (NEW.op_balance < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'op_balance cannot be less than 0.00.';
	END IF;
END
//
DELIMITER ;

DROP TRIGGER IF EXISTS `bfup_ledgers`;
DELIMITER //
CREATE TRIGGER `bfup_ledgers` BEFORE UPDATE ON `ledgers`
FOR EACH ROW BEGIN
	IF (NEW.op_balance_dc IS NOT NULL) THEN
		SET NEW.op_balance_dc = UPPER(NEW.op_balance_dc);
		IF !(NEW.op_balance_dc <=> 'D' OR NEW.op_balance_dc <=> 'C') THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'op_balance_dc restricted to char D or C.';
		END IF;
	END IF;
	IF (NEW.op_balance IS NOT NULL) THEN
		IF (NEW.op_balance < 0.0) THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'op_balance cannot be less than 0.00.';
		END IF;
	END IF;
END
//
DELIMITER ;

DROP TRIGGER IF EXISTS `bfins_entries`;
DELIMITER //
CREATE TRIGGER `bfins_entries` BEFORE INSERT ON `entries`
FOR EACH ROW BEGIN
	IF (NEW.dr_total < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dr_total cannot be less than 0.00.';
	END IF;
	IF (NEW.cr_total < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'cr_total cannot be less than 0.00.';
	END IF;
	IF !(NEW.dr_total <=> NEW.cr_total) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dr_total is not equal to cr_total.';
	END IF;

	SELECT fy_start, fy_end FROM `settings` WHERE id = 1 INTO @fy_start, @fy_end;
	IF (NEW.date < @fy_start) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'date before fy_start.';
	END IF;
	IF (NEW.date > @fy_end) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'date after fy_end.';
	END IF;
END
//
DELIMITER ;

DROP TRIGGER IF EXISTS `bfup_entries`;
DELIMITER //
CREATE TRIGGER `bfup_entries` BEFORE UPDATE ON `entries`
FOR EACH ROW BEGIN
	DECLARE dr_total decimal(25,2);
	DECLARE cr_total decimal(25,2);

	IF (NEW.dr_total IS NOT NULL) THEN
		SET dr_total = NEW.dr_total;
	ELSE
		SET dr_total = OLD.dr_total;
	END IF;
	IF (NEW.cr_total IS NOT NULL) THEN
		SET cr_total = NEW.cr_total;
	ELSE
		SET cr_total = OLD.cr_total;
	END IF;

	IF (dr_total < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dr_total cannot be less than 0.00.';
	END IF;
	IF (cr_total < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'cr_total cannot be less than 0.00.';
	END IF;
	IF !(dr_total <=> cr_total) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dr_total is not equal to cr_total.';
	END IF;

	IF (NEW.date IS NOT NULL) THEN
		SELECT fy_start, fy_end FROM `settings` WHERE id = 1 INTO @fy_start, @fy_end;
		IF (NEW.date < @fy_start) THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'date before fy_start.';
		END IF;
		IF (NEW.date > @fy_end) THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'date after fy_end.';
		END IF;
	END IF;
END
//
DELIMITER ;

DROP TRIGGER IF EXISTS `bfins_entryitems`;
DELIMITER //
CREATE TRIGGER `bfins_entryitems` BEFORE INSERT ON `entryitems`
FOR EACH ROW BEGIN
	SET NEW.dc = UPPER(NEW.dc);
	IF !(NEW.dc <=> 'D' OR NEW.dc <=> 'C') THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dc restricted to char D or C.';
	END IF;
	IF (NEW.amount < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'amount cannot be less than 0.00.';
	END IF;
END
//
DELIMITER ;

DROP TRIGGER IF EXISTS `bfup_entryitems`;
DELIMITER //
CREATE TRIGGER `bfup_entryitems` BEFORE UPDATE ON `entryitems`
FOR EACH ROW BEGIN
	IF (NEW.dc IS NOT NULL) THEN
		SET NEW.dc = UPPER(NEW.dc);
		IF !(NEW.dc <=> 'D' OR NEW.dc <=> 'C') THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dc restricted to char D or C.';
		END IF;
	END IF;
	IF (NEW.amount IS NOT NULL) THEN
		IF (NEW.amount < 0.0) THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'amount cannot be less than 0.00.';
		END IF;
	END IF;
END
//
DELIMITER ;

DROP TRIGGER IF EXISTS `bfins_settings`;
DELIMITER //
CREATE TRIGGER `bfins_settings` BEFORE INSERT ON `settings`
FOR EACH ROW BEGIN
	SET NEW.id = 1;

	IF EXISTS (SELECT id FROM `entries` WHERE date < NEW.fy_start) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'entries present before fy_start.';
	END IF;

	IF EXISTS (SELECT id FROM `entries` WHERE date > NEW.fy_end) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'entries present after fy_end.';
	END IF;

	IF (NEW.fy_start >= NEW.fy_end) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'fy_start cannot be after fy_end.';
	END IF;
END
//
DELIMITER ;

DROP TRIGGER IF EXISTS `bfup_settings`;
DELIMITER //
CREATE TRIGGER `bfup_settings` BEFORE UPDATE ON `settings`
FOR EACH ROW BEGIN
	DECLARE fy_start date;
	DECLARE fy_end date;

	SET NEW.id = 1;

	IF (NEW.fy_start IS NULL) THEN
		SET fy_start = OLD.fy_start;
	ELSE
		SET fy_start = NEW.fy_start;
	END IF;

	IF (NEW.fy_end IS NULL) THEN
		SET fy_end = OLD.fy_end;
	ELSE
		SET fy_end = NEW.fy_end;
	END IF;

	IF EXISTS (SELECT id FROM `entries` WHERE date < fy_start) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'entries present before fy_start.';
	END IF;

	IF EXISTS (SELECT id FROM `entries` WHERE date > fy_end) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'entries present after fy_end.';
	END IF;

	IF (fy_start >= fy_end) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'fy_start cannot be after fy_end.';
	END IF;
END
//
DELIMITER ;
