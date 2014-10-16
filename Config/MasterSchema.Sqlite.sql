CREATE TABLE "wzaccounts" ("id" INTEGER PRIMARY KEY NOT NULL, "label" VARCHAR NOT NULL, "db_datasource" VARCHAR, "db_database" VARCHAR, "db_host" VARCHAR, "db_port" INTEGER, "db_login" VARCHAR, "db_password" VARCHAR, "db_prefix" VARCHAR, "db_persistent" VARCHAR, "db_schema" VARCHAR, "db_unixsocket" VARCHAR, "db_settings" VARCHAR, "ssl_key" VARCHAR, "ssl_cert" VARCHAR, "ssl_ca" VARCHAR);

CREATE TABLE "wzusers" ("id" INTEGER PRIMARY KEY NOT NULL, "username" VARCHAR NOT NULL, "password" VARCHAR NOT NULL, "fullname" VARCHAR, "email" VARCHAR NOT NULL, "timezone" VARCHAR NOT NULL, "role" VARCHAR NOT NULL, "status" INTEGER NOT NULL, "verification_key" VARCHAR, "email_verified" INTEGER NOT NULL, "admin_verified" INTEGER NOT NULL, "retry_count" INTEGER NOT NULL, "all_accounts" INTEGER NOT NULL);

CREATE TABLE "wzsettings" ("id" INTEGER NOT NULL, "sitename" VARCHAR, "drcr_toby" VARCHAR, "enable_logging" INTEGER, "row_count" INTEGER, "user_registration" INTEGER, "admin_verification" INTEGER, "email_verification" INTEGER, "email_protocol" VARCHAR, "email_host" VARCHAR, "email_port" INTEGER, "email_tls" INTEGER, "email_username" VARCHAR, "email_password" VARCHAR, "email_from" VARCHAR);

CREATE TABLE "wzuseraccounts" ("id" INTEGER PRIMARY KEY NOT NULL, "wzuser_id" INTEGER NOT NULL, "wzaccount_id" INTEGER NOT NULL, "role" VARCHAR NOT NULL);

INSERT INTO "wzusers" VALUES ("1","admin","77f9101246a0ca5e24a20ef8d06d19c83bbb69e4","Administrator","","UTC","admin","1","","1","1","0","1");
