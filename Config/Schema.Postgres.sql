CREATE TABLE "%_PREFIX_%groups" (
	"id" bigserial NOT NULL PRIMARY KEY,
	"parent_id" bigint DEFAULT '0',
	"name" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"code" varchar(255) COLLATE "en_US.utf8" DEFAULT NULL,
	"affects_gross" int NOT NULL DEFAULT '0',
	UNIQUE(id),
	UNIQUE(name),
	UNIQUE(code)
);
CREATE INDEX "%_PREFIX_%groups_id" ON %_PREFIX_%groups ("id");
CREATE INDEX "%_PREFIX_%groups_parent_id" ON %_PREFIX_%groups ("parent_id");

CREATE TABLE "%_PREFIX_%ledgers" (
	"id" bigserial NOT NULL PRIMARY KEY,
	"group_id" bigint NOT NULL,
	"name" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"code" varchar(255) COLLATE "en_US.utf8" DEFAULT NULL,
	"op_balance" numeric(25,2) NOT NULL DEFAULT '0.00',
	"op_balance_dc" char(1) COLLATE "en_US.utf8" NOT NULL,
	"type" int NOT NULL DEFAULT '0',
	"reconciliation" int NOT NULL DEFAULT '0',
	"notes" varchar(500) COLLATE "en_US.utf8" NOT NULL,
	UNIQUE("id"),
	UNIQUE("name"),
	UNIQUE("code")
);
CREATE INDEX "%_PREFIX_%ledgers_id" ON %_PREFIX_%ledgers ("id");
CREATE INDEX "%_PREFIX_%ledgers_group_id" ON %_PREFIX_%ledgers ("group_id");

CREATE TABLE "%_PREFIX_%entrytypes" (
	"id" bigserial NOT NULL PRIMARY KEY,
	"label" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"name" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"description" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"base_type" int NOT NULL DEFAULT '0',
	"numbering" int NOT NULL DEFAULT '1',
	"prefix" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"suffix" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"zero_padding" int NOT NULL DEFAULT '0',
	"restriction_bankcash" int NOT NULL DEFAULT '1',
	UNIQUE("id"),
	UNIQUE("label")
);
CREATE INDEX "%_PREFIX_%entrytypes_id" ON %_PREFIX_%entrytypes ("id");

CREATE TABLE "%_PREFIX_%tags" (
	"id" bigserial NOT NULL PRIMARY KEY,
	"title" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"color" char(6) COLLATE "en_US.utf8" NOT NULL,
	"background" char(6) COLLATE "en_US.utf8" NOT NULL,
	UNIQUE("id"),
	UNIQUE("title")
);
CREATE INDEX "%_PREFIX_%tags_id" ON %_PREFIX_%tags ("id");

CREATE TABLE "%_PREFIX_%entries" (
	"id" bigserial NOT NULL PRIMARY KEY,
	"tag_id" bigint DEFAULT NULL,
	"entrytype_id" bigint NOT NULL,
	"number" bigint DEFAULT NULL,
	"date" date NOT NULL,
	"dr_total" numeric(25,%_DECIMAL_%) NOT NULL DEFAULT '0.00',
	"cr_total" numeric(25,%_DECIMAL_%) NOT NULL DEFAULT '0.00',
	"narration" varchar(500) COLLATE "en_US.utf8" NOT NULL,
	UNIQUE("id")
);
CREATE INDEX "%_PREFIX_%entries_id" ON %_PREFIX_%entries ("id");
CREATE INDEX "%_PREFIX_%entries_tag_id" ON %_PREFIX_%entries ("tag_id");
CREATE INDEX "%_PREFIX_%entries_entrytype_id" ON %_PREFIX_%entries ("entrytype_id");

CREATE TABLE "%_PREFIX_%entryitems" (
	"id" bigserial NOT NULL PRIMARY KEY,
	"entry_id" bigint NOT NULL,
	"ledger_id" bigint NOT NULL,
	"amount" numeric(25,%_DECIMAL_%) NOT NULL DEFAULT '0.00',
	"dc" char(1) COLLATE "en_US.utf8" NOT NULL,
	"reconciliation_date" date DEFAULT NULL,
	UNIQUE("id")
);
CREATE INDEX "%_PREFIX_%entryitems_id" ON %_PREFIX_%entryitems ("id");
CREATE INDEX "%_PREFIX_%entryitems_entry_id" ON %_PREFIX_%entryitems ("entry_id");
CREATE INDEX "%_PREFIX_%entryitems_ledger_id" ON %_PREFIX_%entryitems ("ledger_id");

CREATE TABLE "%_PREFIX_%settings" (
	"id" int NOT NULL PRIMARY KEY,
	"name" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"address" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"email" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"fy_start" date NOT NULL,
	"fy_end" date NOT NULL,
	"currency_symbol" varchar(100) COLLATE "en_US.utf8" NOT NULL,
	"currency_format" varchar(100) COLLATE "en_US.utf8" NOT NULL,
	"decimal_places" int NOT NULL DEFAULT '2',
	"date_format" varchar(100) COLLATE "en_US.utf8" NOT NULL,
	"timezone" varchar(100) COLLATE "en_US.utf8" NOT NULL,
	"manage_inventory" int NOT NULL DEFAULT '0',
	"account_locked" int NOT NULL DEFAULT '0',
	"email_use_default" int NOT NULL DEFAULT '0',
	"email_protocol" varchar(10) COLLATE "en_US.utf8" NOT NULL,
	"email_host" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"email_port" int NOT NULL,
	"email_tls" int NOT NULL DEFAULT '0',
	"email_username" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"email_password" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"email_from" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"print_paper_height" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_paper_width" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_margin_top" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_margin_bottom" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_margin_left" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_margin_right" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_orientation" char(1) COLLATE "en_US.utf8" NOT NULL,
	"print_page_format" char(1) COLLATE "en_US.utf8" NOT NULL,
	"database_version" int NOT NULL,
	"settings" bytea NULL DEFAULT NULL,
	UNIQUE("id")
);
CREATE INDEX "%_PREFIX_%settings_id" ON %_PREFIX_%settings ("id");

CREATE TABLE "%_PREFIX_%logs" (
	"id" bigserial NOT NULL PRIMARY KEY,
	"date" timestamp NOT NULL,
	"level" int NOT NULL,
	"host_ip" varchar(25) COLLATE "en_US.utf8" NOT NULL,
	"user" varchar(25) COLLATE "en_US.utf8" NOT NULL,
	"url" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	"user_agent" varchar(100) COLLATE "en_US.utf8" NOT NULL,
	"message" varchar(255) COLLATE "en_US.utf8" NOT NULL,
	UNIQUE("id")
);
CREATE INDEX "%_PREFIX_%logs_id" ON %_PREFIX_%logs ("id");

ALTER TABLE "%_PREFIX_%groups" ADD CONSTRAINT "%_PREFIX_%groups_fk_check_parent_id" FOREIGN KEY ("parent_id") REFERENCES "%_PREFIX_%groups" ("id");
ALTER TABLE "%_PREFIX_%ledgers" ADD CONSTRAINT "%_PREFIX_%ledgers_fk_check_group_id" FOREIGN KEY ("group_id") REFERENCES "%_PREFIX_%groups" ("id");
ALTER TABLE "%_PREFIX_%entries" ADD CONSTRAINT "%_PREFIX_%entries_fk_check_entrytype_id" FOREIGN KEY ("entrytype_id") REFERENCES "%_PREFIX_%entrytypes" ("id");
ALTER TABLE "%_PREFIX_%entries" ADD CONSTRAINT "%_PREFIX_%entries_fk_check_tag_id" FOREIGN KEY ("tag_id") REFERENCES "%_PREFIX_%tags" ("id");
ALTER TABLE "%_PREFIX_%entryitems" ADD CONSTRAINT "%_PREFIX_%entryitems_fk_check_entry_id" FOREIGN KEY ("entry_id") REFERENCES "%_PREFIX_%entries" ("id");
ALTER TABLE "%_PREFIX_%entryitems" ADD CONSTRAINT "%_PREFIX_%entryitems_fk_check_ledger_id" FOREIGN KEY ("ledger_id") REFERENCES "%_PREFIX_%ledgers" ("id");
