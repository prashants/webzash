CREATE TABLE `%_PREFIX_%groups` (
	`id` bigint(18) NOT NULL AUTO_INCREMENT,
	`parent_id` bigint(18) DEFAULT '0',
	`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
	`affects_gross` int(1) NOT NULL DEFAULT '0',
    PRIMARY KEY(`id`),
	UNIQUE KEY `unique_id` (`id`),
	UNIQUE KEY `name` (`name`),
	UNIQUE KEY `code` (`code`),
	KEY `id` (`id`),
	KEY `parent_id` (`parent_id`)
) DEFAULT CHARSET=utf8,
COLLATE=utf8_unicode_ci,
AUTO_INCREMENT=1,
ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%ledgers` (
	`id` bigint(18) NOT NULL AUTO_INCREMENT,
	`group_id` bigint(18) NOT NULL,
	`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
	`op_balance` decimal(25,%_DECIMAL_%) NOT NULL DEFAULT '0.00',
	`op_balance_dc` char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`type` int(2) NOT NULL DEFAULT '0',
	`reconciliation` int(1) NOT NULL DEFAULT '0',
	`notes` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY(`id`),
	UNIQUE KEY `unique_id` (`id`),
	UNIQUE KEY `name` (`name`),
	UNIQUE KEY `code` (`code`),
	KEY `id` (`id`),
	KEY `group_id` (`group_id`)
) DEFAULT CHARSET=utf8,
COLLATE=utf8_unicode_ci,
AUTO_INCREMENT=1,
ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%entrytypes` (
	`id` bigint(18) NOT NULL AUTO_INCREMENT,
	`label` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`base_type` int(2) NOT NULL DEFAULT '0',
	`numbering` int(2) NOT NULL DEFAULT '1',
	`prefix` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`suffix` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`zero_padding` int(2) NOT NULL DEFAULT '0',
	`restriction_bankcash` int(2) NOT NULL DEFAULT '1',
    PRIMARY KEY(`id`),
	UNIQUE KEY `unique_id` (`id`),
	UNIQUE KEY `label` (`label`),
	KEY `id` (`id`)
) DEFAULT CHARSET=utf8,
COLLATE=utf8_unicode_ci,
AUTO_INCREMENT=1,
ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%tags` (
	`id` bigint(18) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`color` char(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`background` char(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY(`id`),
	UNIQUE KEY `unique_id` (`id`),
	UNIQUE KEY `title` (`title`),
	KEY `id` (`id`)
) DEFAULT CHARSET=utf8,
COLLATE=utf8_unicode_ci,
AUTO_INCREMENT=1,
ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%entries` (
	`id` bigint(18) NOT NULL AUTO_INCREMENT,
	`tag_id` bigint(18) DEFAULT NULL,
	`entrytype_id` bigint(18) NOT NULL,
	`number` bigint(18) DEFAULT NULL,
	`date` date NOT NULL,
	`dr_total` decimal(25,%_DECIMAL_%) NOT NULL DEFAULT '0.00',
	`cr_total` decimal(25,%_DECIMAL_%) NOT NULL DEFAULT '0.00',
	`narration` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `unique_id` (`id`),
	KEY `id` (`id`),
	KEY `tag_id` (`tag_id`),
	KEY `entrytype_id` (`entrytype_id`)
) DEFAULT CHARSET=utf8,
COLLATE=utf8_unicode_ci,
AUTO_INCREMENT=1,
ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%entryitems` (
	`id` bigint(18) NOT NULL AUTO_INCREMENT,
	`entry_id` bigint(18) NOT NULL,
	`ledger_id` bigint(18) NOT NULL,
	`amount` decimal(25,%_DECIMAL_%) NOT NULL DEFAULT '0.00',
	`dc` char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`reconciliation_date` date DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `unique_id` (`id`),
	KEY `id` (`id`),
	KEY `entry_id` (`entry_id`),
	KEY `ledger_id` (`ledger_id`)
) DEFAULT CHARSET=utf8,
COLLATE=utf8_unicode_ci,
AUTO_INCREMENT=1,
ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%settings` (
	`id` int(1) NOT NULL,
	`name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`fy_start` date NOT NULL,
	`fy_end` date NOT NULL,
	`currency_symbol` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`currency_format` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`decimal_places` int(2) NOT NULL DEFAULT '2',
	`date_format` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`timezone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`manage_inventory` int(1) NOT NULL DEFAULT '0',
	`account_locked` int(1) NOT NULL DEFAULT '0',
	`email_use_default` int(1) NOT NULL DEFAULT '0',
	`email_protocol` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`email_host` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`email_port` int(5) NOT NULL,
	`email_tls` int(1) NOT NULL DEFAULT '0',
	`email_username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`email_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`email_from` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`print_paper_height` decimal(10,3) NOT NULL DEFAULT '0.0',
	`print_paper_width` decimal(10,3) NOT NULL DEFAULT '0.0',
	`print_margin_top` decimal(10,3) NOT NULL DEFAULT '0.0',
	`print_margin_bottom` decimal(10,3) NOT NULL DEFAULT '0.0',
	`print_margin_left` decimal(10,3) NOT NULL DEFAULT '0.0',
	`print_margin_right` decimal(10,3) NOT NULL DEFAULT '0.0',
	`print_orientation` char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`print_page_format` char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`database_version` int(10) NOT NULL,
	`settings` BLOB NULL DEFAULT NULL,
	PRIMARY KEY(`id`),
	UNIQUE KEY `unique_id` (`id`),
	KEY `id` (`id`)
) DEFAULT CHARSET=utf8,
COLLATE=utf8_unicode_ci,
ENGINE=InnoDB;

CREATE TABLE `%_PREFIX_%logs` (
	`id` bigint(18) NOT NULL AUTO_INCREMENT,
	`date` datetime NOT NULL,
	`level` int(1) NOT NULL,
	`host_ip` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`user_agent` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`message` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `unique_id` (`id`),
	KEY `id` (`id`)
) DEFAULT CHARSET=utf8,
COLLATE=utf8_unicode_ci,
AUTO_INCREMENT=1,
ENGINE=InnoDB;

ALTER TABLE `%_PREFIX_%groups`
	ADD CONSTRAINT `%_PREFIX_%groups_fk_check_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `%_PREFIX_%groups` (`id`);

ALTER TABLE `%_PREFIX_%ledgers`
	ADD CONSTRAINT `%_PREFIX_%ledgers_fk_check_group_id` FOREIGN KEY (`group_id`) REFERENCES `%_PREFIX_%groups` (`id`);

ALTER TABLE `%_PREFIX_%entries`
	ADD CONSTRAINT `%_PREFIX_%entries_fk_check_entrytype_id` FOREIGN KEY (`entrytype_id`) REFERENCES `%_PREFIX_%entrytypes` (`id`),
	ADD CONSTRAINT `%_PREFIX_%entries_fk_check_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `%_PREFIX_%tags` (`id`);

ALTER TABLE `%_PREFIX_%entryitems`
	ADD CONSTRAINT `%_PREFIX_%entryitems_fk_check_ledger_id` FOREIGN KEY (`ledger_id`) REFERENCES `%_PREFIX_%ledgers` (`id`),
	ADD CONSTRAINT `%_PREFIX_%entryitems_fk_check_entry_id` FOREIGN KEY (`entry_id`) REFERENCES `%_PREFIX_%entries` (`id`);
