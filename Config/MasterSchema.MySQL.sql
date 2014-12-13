CREATE TABLE IF NOT EXISTS `wzaccounts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `label` varchar(255) NOT NULL,
        `db_datasource` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `db_database` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `db_host` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `db_port` int(11) DEFAULT NULL,
        `db_login` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `db_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `db_prefix` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `db_persistent` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `db_schema` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `db_unixsocket` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `db_settings` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `ssl_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `ssl_cert` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `ssl_ca` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `wzusers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `fullname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `timezone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `role` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `status` int(1) NOT NULL DEFAULT '0',
        `verification_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `email_verified` int(1) NOT NULL DEFAULT '0',
        `admin_verified` int(1) NOT NULL DEFAULT '0',
        `retry_count` int(1) NOT NULL DEFAULT '0',
        `all_accounts` int(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2;


CREATE TABLE IF NOT EXISTS `wzsettings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `sitename` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `drcr_toby` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `enable_logging` int(1) NOT NULL DEFAULT '0',
        `row_count` int(11) NOT NULL DEFAULT '10',
        `user_registration` int(1) NOT NULL DEFAULT '0',
        `admin_verification` int(1) NOT NULL DEFAULT '0',
        `email_verification` int(1) NOT NULL DEFAULT '0',
        `email_protocol` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `email_host` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `email_port` int(11) DEFAULT '0',
        `email_tls` int(1) DEFAULT '0',
        `email_username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `email_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        `email_from` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `wzuseraccounts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `wzuser_id` int(11) NOT NULL,
        `wzaccount_id` int(11) NOT NULL,
        `role` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT INTO `wzusers` (`id`, `username`, `password`, `fullname`, `email`, `timezone`, `role`, `status`, `verification_key`, `email_verified`, `admin_verified`, `retry_count`, `all_accounts`) VALUES
(1, 'admin', '', 'Administrator', '', 'UTC', 'admin', 1, '', 1, 1, 0, 1);
