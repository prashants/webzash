CREATE TABLE %_SCHEMA_%%_PREFIX_%groups (
	"id" bigserial NOT NULL PRIMARY KEY,
	"parent_id" bigint DEFAULT '0',
	"name" varchar(255) NOT NULL,
	"code" varchar(255) DEFAULT NULL,
	"affects_gross" integer NOT NULL DEFAULT '0',
	UNIQUE(id),
	UNIQUE(name),
	UNIQUE(code)
);

CREATE TABLE %_SCHEMA_%%_PREFIX_%ledgers (
	"id" bigserial NOT NULL PRIMARY KEY,
	"group_id" bigint NOT NULL,
	"name" varchar(255) NOT NULL,
	"code" varchar(255) DEFAULT NULL,
	"op_balance" numeric(25,2) NOT NULL DEFAULT '0.00',
	"op_balance_dc" char(1) NOT NULL,
	"type" integer NOT NULL DEFAULT '0',
	"reconciliation" integer NOT NULL DEFAULT '0',
	"notes" varchar(500) NOT NULL,
	UNIQUE("id"),
	UNIQUE("name"),
	UNIQUE("code")
);

CREATE TABLE %_SCHEMA_%%_PREFIX_%entrytypes (
	"id" bigserial NOT NULL PRIMARY KEY,
	"label" varchar(255) NOT NULL,
	"name" varchar(255) NOT NULL,
	"description" varchar(255) NOT NULL,
	"base_type" integer NOT NULL DEFAULT '0',
	"numbering" integer NOT NULL DEFAULT '1',
	"prefix" varchar(255) NOT NULL,
	"suffix" varchar(255) NOT NULL,
	"zero_padding" integer NOT NULL DEFAULT '0',
	"restriction_bankcash" integer NOT NULL DEFAULT '1',
	UNIQUE("id"),
	UNIQUE("label")
);

CREATE TABLE %_SCHEMA_%%_PREFIX_%tags (
	"id" bigserial NOT NULL PRIMARY KEY,
	"title" varchar(255) NOT NULL,
	"color" char(6) NOT NULL,
	"background" char(6) NOT NULL,
	UNIQUE("id"),
	UNIQUE("title")
);

CREATE TABLE %_SCHEMA_%%_PREFIX_%entries (
	"id" bigserial NOT NULL PRIMARY KEY,
	"tag_id" bigint DEFAULT NULL,
	"entrytype_id" bigint NOT NULL,
	"number" bigint DEFAULT NULL,
	"date" date NOT NULL,
	"dr_total" numeric(25,%_DECIMAL_%) NOT NULL DEFAULT '0.00',
	"cr_total" numeric(25,%_DECIMAL_%) NOT NULL DEFAULT '0.00',
	"narration" varchar(500) NOT NULL,
	UNIQUE("id")
);

CREATE TABLE %_SCHEMA_%%_PREFIX_%entryitems (
	"id" bigserial NOT NULL PRIMARY KEY,
	"entry_id" bigint NOT NULL,
	"ledger_id" bigint NOT NULL,
	"amount" numeric(25,%_DECIMAL_%) NOT NULL DEFAULT '0.00',
	"dc" char(1) NOT NULL,
	"reconciliation_date" date DEFAULT NULL,
	UNIQUE("id")
);

CREATE TABLE %_SCHEMA_%%_PREFIX_%settings (
	"id" serial NOT NULL PRIMARY KEY,
	"name" varchar(255) NOT NULL,
	"address" varchar(255) NOT NULL,
	"email" varchar(255) NOT NULL,
	"fy_start" date NOT NULL,
	"fy_end" date NOT NULL,
	"currency_symbol" varchar(100) NOT NULL,
	"currency_format" varchar(100) NOT NULL,
	"decimal_places" integer NOT NULL DEFAULT '2',
	"date_format" varchar(100) NOT NULL,
	"timezone" varchar(100) NOT NULL,
	"manage_inventory" integer NOT NULL DEFAULT '0',
	"account_locked" integer NOT NULL DEFAULT '0',
	"email_use_default" integer NOT NULL DEFAULT '0',
	"email_protocol" varchar(10) NOT NULL,
	"email_host" varchar(255) NOT NULL,
	"email_port" integer NOT NULL,
	"email_tls" integer NOT NULL DEFAULT '0',
	"email_username" varchar(255) NOT NULL,
	"email_password" varchar(255) NOT NULL,
	"email_from" varchar(255) NOT NULL,
	"print_paper_height" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_paper_width" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_margin_top" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_margin_bottom" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_margin_left" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_margin_right" numeric(10,3) NOT NULL DEFAULT '0.0',
	"print_orientation" char(1) NOT NULL,
	"print_page_format" char(1) NOT NULL,
	"database_version" integer NOT NULL,
	"settings" varchar(2048) NULL DEFAULT NULL,
	UNIQUE("id")
);

CREATE TABLE %_SCHEMA_%%_PREFIX_%logs (
	"id" bigserial NOT NULL PRIMARY KEY,
	"date" timestamp NOT NULL,
	"level" integer NOT NULL,
	"host_ip" varchar(25) NOT NULL,
	"user" varchar(25) NOT NULL,
	"url" varchar(255) NOT NULL,
	"user_agent" varchar(100) NOT NULL,
	"message" varchar(255) NOT NULL,
	UNIQUE("id")
);

ALTER TABLE %_SCHEMA_%%_PREFIX_%groups ADD CONSTRAINT groups_fk_check_parent_id FOREIGN KEY ("parent_id") REFERENCES %_SCHEMA_%%_PREFIX_%groups ("id");
ALTER TABLE %_SCHEMA_%%_PREFIX_%ledgers ADD CONSTRAINT ledgers_fk_check_group_id FOREIGN KEY ("group_id") REFERENCES %_SCHEMA_%%_PREFIX_%groups ("id");
ALTER TABLE %_SCHEMA_%%_PREFIX_%entries ADD CONSTRAINT entries_fk_check_entrytype_id FOREIGN KEY ("entrytype_id") REFERENCES %_SCHEMA_%%_PREFIX_%entrytypes ("id");
ALTER TABLE %_SCHEMA_%%_PREFIX_%entries ADD CONSTRAINT entries_fk_check_tag_id FOREIGN KEY ("tag_id") REFERENCES %_SCHEMA_%%_PREFIX_%tags ("id");
ALTER TABLE %_SCHEMA_%%_PREFIX_%entryitems ADD CONSTRAINT entryitems_fk_check_entry_id FOREIGN KEY ("entry_id") REFERENCES %_SCHEMA_%%_PREFIX_%entries ("id");
ALTER TABLE %_SCHEMA_%%_PREFIX_%entryitems ADD CONSTRAINT entryitems_fk_check_ledger_id FOREIGN KEY ("ledger_id") REFERENCES %_SCHEMA_%%_PREFIX_%ledgers ("id");
