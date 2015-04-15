CREATE TABLE "entries" (
"id" int8 NOT NULL,
"tag_id" int8 DEFAULT NULL,
"entrytype_id" int8 NOT NULL,
"number" int8 DEFAULT NULL,
"date" date NOT NULL,
"cheque_date" date DEFAULT NULL,
"cheque_number" varchar(255) DEFAULT NULL,
"dr_total" varchar(255) NOT NULL DEFAULT '0.00',
"cr_total" varchar(255) NOT NULL DEFAULT '0.00',
"narration" text NOT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "unique_id" ON "entries" ("id");
CREATE INDEX "id" ON "entries" ("id");
CREATE INDEX "tag_id" ON "entries" ("tag_id");
CREATE INDEX "entrytype_id" ON "entries" ("entrytype_id");

CREATE TABLE "entryitems" (
"id" int8 NOT NULL,
"entry_id" int8 NOT NULL,
"ledger_id" int8 NOT NULL,
"amount" varchar(255) NOT NULL DEFAULT '0.00',
"dc" char(1) NOT NULL,
"reconciliation_date" date DEFAULT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "unique_id" ON "entryitems" ("id");
CREATE INDEX "id" ON "entryitems" ("id");
CREATE INDEX "entry_id" ON "entryitems" ("entry_id");
CREATE INDEX "ledger_id" ON "entryitems" ("ledger_id");

CREATE TABLE "entrytypes" (
"id" int8 NOT NULL,
"label" varchar(255) NOT NULL,
"name" varchar(255) NOT NULL,
"description" varchar(255) NOT NULL,
"base_type" int4 NOT NULL DEFAULT '0',
"numbering" int4 NOT NULL DEFAULT '1',
"prefix" varchar(255) NOT NULL,
"suffix" varchar(255) NOT NULL,
"zero_padding" int4 NOT NULL DEFAULT '0',
"restriction_bankcash" int4 NOT NULL DEFAULT '1',
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "unique_id" ON "entrytypes" ("id");
CREATE UNIQUE INDEX "label" ON "entrytypes" ("label");
CREATE INDEX "id" ON "entrytypes" ("id");

CREATE TABLE "groups" (
"id" int8 NOT NULL,
"parent_id" int8 DEFAULT '0',
"name" varchar(255) NOT NULL,
"code" varchar(255) DEFAULT NULL,
"affects_gross" int4 NOT NULL DEFAULT '0',
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "unique_id" ON "groups" ("id");
CREATE UNIQUE INDEX "name" ON "groups" ("name");
CREATE INDEX "id" ON "groups" ("id");
CREATE INDEX "parent_id" ON "groups" ("parent_id");

CREATE TABLE "ledgers" (
"id" int8 NOT NULL,
"group_id" int8 NOT NULL,
"name" varchar(255) NOT NULL,
"code" varchar(255) DEFAULT NULL,
"op_balance" varchar(255) NOT NULL DEFAULT '0.00',
"op_balance_dc" char(1) NOT NULL,
"type" int4 NOT NULL DEFAULT '0',
"reconciliation" int4 NOT NULL DEFAULT '0',
"notes" text NOT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "unique_id" ON "ledgers" ("id");
CREATE UNIQUE INDEX "name" ON "ledgers" ("name");
CREATE INDEX "id" ON "ledgers" ("id");
CREATE INDEX "group_id" ON "ledgers" ("group_id");

CREATE TABLE "logs" (
"id" int8 NOT NULL,
"date" timestamp NOT NULL,
"level" int4 NOT NULL,
"host_ip" varchar(25) NOT NULL,
"user" varchar(25) NOT NULL,
"url" varchar(255) NOT NULL,
"user_agent" varchar(100) NOT NULL,
"message" varchar(255) NOT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "unique_id" ON "logs" ("id");
CREATE INDEX "id" ON "logs" ("id");

CREATE TABLE "settings" (
"id" int4 NOT NULL,
"name" varchar(255) NOT NULL,
"address" varchar(255) NOT NULL,
"email" varchar(255) NOT NULL,
"fy_start" date NOT NULL,
"fy_end" date NOT NULL,
"currency_symbol" varchar(100) NOT NULL,
"date_format" varchar(100) NOT NULL,
"timezone" varchar(100) NOT NULL,
"manage_inventory" int4 NOT NULL DEFAULT '0',
"account_locked" int4 NOT NULL DEFAULT '0',
"email_use_default" int4 NOT NULL DEFAULT '0',
"email_protocol" varchar(10) NOT NULL,
"email_host" varchar(255) NOT NULL,
"email_port" int4 NOT NULL,
"email_tls" int4 NOT NULL DEFAULT '0',
"email_username" varchar(255) NOT NULL,
"email_password" varchar(255) NOT NULL,
"email_from" varchar(255) NOT NULL,
"print_paper_height" varchar(255) NOT NULL DEFAULT '0.000',
"print_paper_width" varchar(255) NOT NULL DEFAULT '0.000',
"print_margin_top" varchar(255) NOT NULL DEFAULT '0.000',
"print_margin_bottom" varchar(255) NOT NULL DEFAULT '0.000',
"print_margin_left" varchar(255) NOT NULL DEFAULT '0.000',
"print_margin_right" varchar(255) NOT NULL DEFAULT '0.000',
"print_orientation" char(1) NOT NULL,
"print_page_format" char(1) NOT NULL,
"database_version" int4 NOT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "unique_id" ON "settings" ("id");
CREATE INDEX "id" ON "settings" ("id");

CREATE TABLE "tags" (
"id" int8 NOT NULL,
"title" varchar(255) NOT NULL,
"color" char(6) NOT NULL,
"background" char(6) NOT NULL,
PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "unique_id" ON "tags" ("id");
CREATE UNIQUE INDEX "title" ON "tags" ("title");
CREATE INDEX "id" ON "tags" ("id");


ALTER TABLE "entries" ADD CONSTRAINT "entries_fk_check_entrytype_id" FOREIGN KEY ("entrytype_id") REFERENCES "entrytypes" ("id");
ALTER TABLE "entries" ADD CONSTRAINT "entries_fk_check_tag_id" FOREIGN KEY ("tag_id") REFERENCES "tags" ("id");
ALTER TABLE "entryitems" ADD CONSTRAINT "entryitems_fk_check_entry_id" FOREIGN KEY ("entry_id") REFERENCES "entries" ("id");
ALTER TABLE "entryitems" ADD CONSTRAINT "entryitems_fk_check_ledger_id" FOREIGN KEY ("ledger_id") REFERENCES "ledgers" ("id");
ALTER TABLE "groups" ADD CONSTRAINT "groups_fk_check_parent_id" FOREIGN KEY ("parent_id") REFERENCES "groups" ("id");
ALTER TABLE "ledgers" ADD CONSTRAINT "ledgers_fk_check_group_id" FOREIGN KEY ("group_id") REFERENCES "groups" ("id");
