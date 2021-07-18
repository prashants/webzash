CREATE TABLE %_SCHEMA_%%_PREFIX_%wzaccounts (
        id serial PRIMARY KEY,
        label varchar(255) NOT NULL,
        db_datasource varchar(255) DEFAULT NULL,
        db_database varchar(255) DEFAULT NULL,
        db_host varchar(255) DEFAULT NULL,
        db_port integer DEFAULT NULL,
        db_login varchar(255) DEFAULT NULL,
        db_password varchar(255) DEFAULT NULL,
        db_prefix varchar(255) DEFAULT NULL,
        db_persistent varchar(255) DEFAULT NULL,
        db_schema varchar(255) DEFAULT NULL,
        db_unixsocket varchar(255) DEFAULT NULL,
        db_settings varchar(255) DEFAULT NULL,
        ssl_key varchar(255) DEFAULT NULL,
        ssl_cert varchar(255) DEFAULT NULL,
        ssl_ca varchar(255) DEFAULT NULL,
        hidden integer NOT NULL DEFAULT '0',
        others varchar(255) DEFAULT NULL
);

CREATE TABLE %_SCHEMA_%%_PREFIX_%wzusers (
        id serial PRIMARY KEY,
        username varchar(255) NOT NULL,
        password varchar(255) NOT NULL,
        fullname varchar(255) DEFAULT NULL,
        email varchar(255) DEFAULT NULL,
        timezone varchar(255) DEFAULT NULL,
        role varchar(255) DEFAULT NULL,
        status integer NOT NULL DEFAULT '0',
        verification_key varchar(255) DEFAULT NULL,
        email_verified integer NOT NULL DEFAULT '0',
        admin_verified integer NOT NULL DEFAULT '0',
        retry_count integer NOT NULL DEFAULT '0',
        all_accounts integer NOT NULL DEFAULT '0',
        default_account integer NOT NULL DEFAULT '0',
        others varchar(255) DEFAULT NULL
);

CREATE TABLE %_SCHEMA_%%_PREFIX_%wzsettings (
        id serial PRIMARY KEY,
        sitename varchar(255)  DEFAULT NULL,
        drcr_toby varchar(255)  DEFAULT NULL,
        enable_logging integer NOT NULL DEFAULT '0',
        row_count integer NOT NULL DEFAULT '10',
        user_registration integer NOT NULL DEFAULT '0',
        admin_verification integer NOT NULL DEFAULT '0',
        email_verification integer NOT NULL DEFAULT '0',
        email_protocol varchar(255)  DEFAULT NULL,
        email_host varchar(255)  DEFAULT NULL,
        email_port integer DEFAULT '0',
        email_tls integer DEFAULT '0',
        email_username varchar(255)  DEFAULT NULL,
        email_password varchar(255)  DEFAULT NULL,
        email_from varchar(255)  DEFAULT NULL,
        others varchar(255)  DEFAULT NULL
);

CREATE TABLE %_SCHEMA_%%_PREFIX_%wzuseraccounts (
        id serial PRIMARY KEY,
        wzuser_id int NOT NULL REFERENCES %_SCHEMA_%%_PREFIX_%wzusers (id),
        wzaccount_id int NOT NULL REFERENCES %_SCHEMA_%%_PREFIX_%wzaccounts (id),
        role varchar(255)  DEFAULT NULL
);
