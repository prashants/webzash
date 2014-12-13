CREATE TABLE "wzaccounts" (
        "id" INTEGER PRIMARY KEY NOT NULL,
        "label" VARCHAR NOT NULL,
        "db_datasource" VARCHAR DEFAULT (null),
        "db_database" VARCHAR DEFAULT (null),
        "db_host" VARCHAR DEFAULT (null),
        "db_port" INTEGER DEFAULT (null),
        "db_login" VARCHAR DEFAULT (null),
        "db_password" VARCHAR DEFAULT (null),
        "db_prefix" VARCHAR DEFAULT (null),
        "db_persistent" VARCHAR DEFAULT (null),
        "db_schema" VARCHAR DEFAULT (null),
        "db_unixsocket" VARCHAR DEFAULT (null),
        "db_settings" VARCHAR DEFAULT (null),
        "ssl_key" VARCHAR DEFAULT (null),
        "ssl_cert" VARCHAR DEFAULT (null),
        "ssl_ca" VARCHAR DEFAULT (null)
);

CREATE TABLE "wzusers" (
        "id" INTEGER PRIMARY KEY NOT NULL,
        "username" VARCHAR NOT NULL,
        "password" VARCHAR NOT NULL,
        "fullname" VARCHAR DEFAULT (null),
        "email" VARCHAR DEFAULT (null),
        "timezone" VARCHAR DEFAULT (null),
        "role" VARCHAR DEFAULT (null),
        "status" INTEGER NOT NULL DEFAULT (0),
        "verification_key" VARCHAR DEFAULT (null),
        "email_verified" INTEGER NOT NULL DEFAULT (0),
        "admin_verified" INTEGER NOT NULL DEFAULT (0),
        "retry_count" INTEGER NOT NULL DEFAULT (0),
        "all_accounts" INTEGER NOT NULL DEFAULT (0)
);

CREATE TABLE "wzsettings" (
        "id" INTEGER NOT NULL,
        "sitename" VARCHAR DEFAULT (null),
        "drcr_toby" VARCHAR DEFAULT (null),
        "enable_logging" INTEGER NOT NULL DEFAULT (0),
        "row_count" INTEGER NOT NULL DEFAULT (10),
        "user_registration" INTEGER NOT NULL DEFAULT (0),
        "admin_verification" INTEGER NOT NULL DEFAULT (0),
        "email_verification" INTEGER NOT NULL DEFAULT (0),
        "email_protocol" VARCHAR DEFAULT (null),
        "email_host" VARCHAR DEFAULT (null),
        "email_port" INTEGER DEFAULT (0),
        "email_tls" INTEGER DEFAULT (0),
        "email_username" VARCHAR DEFAULT (null),
        "email_password" VARCHAR DEFAULT (null),
        "email_from" VARCHAR DEFAULT (null)
);

CREATE TABLE "wzuseraccounts" (
        "id" INTEGER PRIMARY KEY NOT NULL,
        "wzuser_id" INTEGER NOT NULL,
        "wzaccount_id" INTEGER NOT NULL,
        "role" VARCHAR DEFAULT (null)
);

INSERT INTO "wzusers" (`id`, `username`, `password`, `fullname`, `email`, `timezone`, `role`, `status`, `verification_key`, `email_verified`, `admin_verified`, `retry_count`, `all_accounts`) VALUES ('1','admin','','Administrator', '','UTC','admin','1','','1','1','0','1');
