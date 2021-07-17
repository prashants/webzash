INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (1, NULL, 'Assets', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (2, NULL, 'Liabilities and Owners Equity', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (3, NULL, 'Incomes', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (4, NULL, 'Expenses', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (5, 1, 'Fixed Assets', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (6, 1, 'Current Assets', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (7, 1, 'Investments', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (8, 2, 'Capital Account', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (9, 2, 'Current Liabilities', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (10, 2, 'Loans (Liabilities)', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (11, 3, 'Direct Incomes', NULL, 1);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (12, 4, 'Direct Expenses', NULL, 1);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (13, 3, 'Indirect Incomes', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (14, 4, 'Indirect Expenses', NULL, 0);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (15, 3, 'Sales', NULL, 1);
INSERT INTO %_SCHEMA_%groups (id, parent_id, name, code, affects_gross) VALUES (16, 4, 'Purchases', NULL, 1);

INSERT INTO %_SCHEMA_%entrytypes (id, label, name, description, base_type, numbering, prefix, suffix, zero_padding, restriction_bankcash) VALUES (1, 'receipt', 'Receipt', 'Received in Bank account or Cash account', 1, 1, '', '', 0, 2);
INSERT INTO %_SCHEMA_%entrytypes (id, label, name, description, base_type, numbering, prefix, suffix, zero_padding, restriction_bankcash) VALUES (2, 'payment', 'Payment', 'Payment made from Bank account or Cash account', 1, 1, '', '', 0, 3);
INSERT INTO %_SCHEMA_%entrytypes (id, label, name, description, base_type, numbering, prefix, suffix, zero_padding, restriction_bankcash) VALUES (3, 'contra', 'Contra', 'Transfer between Bank account and Cash account', 1, 1, '', '', 0, 4);
INSERT INTO %_SCHEMA_%entrytypes (id, label, name, description, base_type, numbering, prefix, suffix, zero_padding, restriction_bankcash) VALUES (4, 'journal', 'Journal', 'Transaction that does not involve a Bank account or Cash account', 1, 1, '', '', 0, 5);
