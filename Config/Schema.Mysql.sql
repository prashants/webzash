CREATE TABLE `%_PREFIX_%groups` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`parent_id` int(11) NOT NULL,
	`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`affects_gross` int(1) DEFAULT 0 NOT NULL,
        UNIQUE KEY `id`(`id`), PRIMARY KEY(`id`)) DEFAULT CHARSET=utf8,
	COLLATE=utf8_unicode_ci,
	ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%ledgers` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`group_id` int(11) NOT NULL,
	`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`op_balance` float(25,2) DEFAULT '0.00' NOT NULL,
	`op_balance_dc` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`type` int(2) DEFAULT 0 NOT NULL,
	`reconciliation` int(1) NOT NULL, PRIMARY KEY(`id`)) DEFAULT CHARSET=utf8,
	COLLATE=utf8_unicode_ci,
	ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%entries` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`tag_id` int(11) DEFAULT NULL,
	`entrytype_id` int(11) NOT NULL,
	`number` int(11) DEFAULT NULL,
	`date` datetime NOT NULL,
	`dr_total` float(25,2) DEFAULT '0.00' NOT NULL,
	`cr_total` float(25,2) DEFAULT '0.00' NOT NULL,
	`narration` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        UNIQUE KEY `id`(`id`), PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8,
	COLLATE=utf8_unicode_ci,
	ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%entryitems` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`entry_id` int(11) NOT NULL,
	`ledger_id` int(11) NOT NULL,
	`amount` float(25,2) DEFAULT '0.00' NOT NULL,
	`dc` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`reconciliation_date` datetime DEFAULT NULL,
        UNIQUE KEY `id`(`id`), PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8,
	COLLATE=utf8_unicode_ci,
	ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%settings` (
	`id` int(1) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`fy_start` datetime NOT NULL,
	`fy_end` datetime NOT NULL,
	`currency_symbol` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`date_format` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`timezone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`manage_inventory` int(1) NOT NULL,
	`account_locked` int(1) NOT NULL,
	`email_use_default` int(1) NOT NULL,
	`email_protocol` varchar(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`email_host` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`email_port` int(5) NOT NULL,
	`email_username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`email_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`email_from` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`print_paper_height` float NOT NULL,
	`print_paper_width` float NOT NULL,
	`print_margin_top` float NOT NULL,
	`print_margin_bottom` float NOT NULL,
	`print_margin_left` float NOT NULL,
	`print_margin_right` float NOT NULL,
	`print_orientation` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`print_page_format` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`database_version` int(10) NOT NULL,
        UNIQUE KEY `id`(`id`), PRIMARY KEY(`id`)) DEFAULT CHARSET=utf8,
	COLLATE=utf8_unicode_ci,
	ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%tags` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`color` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`background` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        UNIQUE KEY `id`(`id`), PRIMARY KEY(`id`)) DEFAULT CHARSET=utf8,
	COLLATE=utf8_unicode_ci,
	ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%entrytypes` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`label` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`base_type` int(2) NOT NULL,
	`numbering` int(2) NOT NULL,
	`prefix` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`suffix` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`zero_padding` int(2) DEFAULT 0 NOT NULL,
	`restriction_bankcash` int(2) DEFAULT 1 NOT NULL,
        UNIQUE KEY `id`(`id`), PRIMARY KEY(`id`)) DEFAULT CHARSET=utf8,
	COLLATE=utf8_unicode_ci,
	ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%logs` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date` datetime NOT NULL,
	`level` int(1) NOT NULL,
	`host_ip` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_agent` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`message_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`message_desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        UNIQUE KEY `id`(`id`), PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (1, 0, 'Assets', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (2, 0, 'Liabilities and Owners Equity', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (3, 0, 'Incomes', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (4, 0, 'Expenses', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (5, 1, 'Fixed Assets', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (6, 1, 'Current Assets', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (7, 1, 'Investments', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (8, 2, 'Capital Account', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (9, 2, 'Current Liabilities', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (10, 2, 'Loans (Liabilities)', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (11, 3, 'Direct Incomes', 1);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (12, 4, 'Direct Expenses', 1);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (13, 3, 'Indirect Incomes', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (14, 4, 'Indirect Expenses', 0);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (15, 3, 'Sales', 1);
INSERT INTO `%_PREFIX_%groups` (`id`, `parent_id`, `name`, `affects_gross`) VALUES (16, 4, 'Purchases', 1);

INSERT INTO `%_PREFIX_%entrytypes` (`id`, `label`, `name`, `description`, `base_type`, `numbering`, `prefix`, `suffix`, `zero_padding`, `restriction_bankcash`) VALUES (1, 'receipt', 'Receipt', 'Received in Bank account or Cash account', 1, 1, '', '', 0, 2);
INSERT INTO `%_PREFIX_%entrytypes` (`id`, `label`, `name`, `description`, `base_type`, `numbering`, `prefix`, `suffix`, `zero_padding`, `restriction_bankcash`) VALUES (2, 'payment', 'Payment', 'Payment made from Bank account or Cash account', 1, 1, '', '', 0, 3);
INSERT INTO `%_PREFIX_%entrytypes` (`id`, `label`, `name`, `description`, `base_type`, `numbering`, `prefix`, `suffix`, `zero_padding`, `restriction_bankcash`) VALUES (3, 'contra', 'Contra', 'Transfer between Bank account and Cash account', 1, 1, '', '', 0, 4);
INSERT INTO `%_PREFIX_%entrytypes` (`id`, `label`, `name`, `description`, `base_type`, `numbering`, `prefix`, `suffix`, `zero_padding`, `restriction_bankcash`) VALUES (4, 'journal', 'Journal', 'Transfer between Non Bank account and Cash account', 1, 1, '', '', 0, 5);
