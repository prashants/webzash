DROP TRIGGER IF EXISTS `bfins_%_PREFIX_%ledgers`;
CREATE TRIGGER `bfins_%_PREFIX_%ledgers` BEFORE INSERT ON `%_PREFIX_%ledgers`
FOR EACH ROW BEGIN
	SET NEW.op_balance_dc = UPPER(NEW.op_balance_dc);
	IF !(NEW.op_balance_dc <=> 'D' OR NEW.op_balance_dc <=> 'C') THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'op_balance_dc restricted to char D or C.';
	END IF;
	IF (NEW.op_balance < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'op_balance cannot be less than 0.00.';
	END IF;
END;

DROP TRIGGER IF EXISTS `bfup_%_PREFIX_%ledgers`;
CREATE TRIGGER `bfup_%_PREFIX_%ledgers` BEFORE UPDATE ON `%_PREFIX_%ledgers`
FOR EACH ROW BEGIN
	IF (NEW.op_balance_dc IS NOT NULL) THEN
		SET NEW.op_balance_dc = UPPER(NEW.op_balance_dc);
		IF !(NEW.op_balance_dc <=> 'D' OR NEW.op_balance_dc <=> 'C') THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'op_balance_dc restricted to char D or C.';
		END IF;
	END IF;
	IF (NEW.op_balance IS NOT NULL) THEN
		IF (NEW.op_balance < 0.0) THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'op_balance cannot be less than 0.00.';
		END IF;
	END IF;
END;

DROP TRIGGER IF EXISTS `bfins_%_PREFIX_%entries`;
CREATE TRIGGER `bfins_%_PREFIX_%entries` BEFORE INSERT ON `%_PREFIX_%entries`
FOR EACH ROW BEGIN
	IF (NEW.dr_total < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dr_total cannot be less than 0.00.';
	END IF;
	IF (NEW.cr_total < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'cr_total cannot be less than 0.00.';
	END IF;
	IF !(NEW.dr_total <=> NEW.cr_total) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dr_total is not equal to cr_total.';
	END IF;

	SELECT fy_start, fy_end FROM `%_PREFIX_%settings` WHERE id = 1 INTO @fy_start, @fy_end;
	IF (NEW.date < @fy_start) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'date before fy_start.';
	END IF;
	IF (NEW.date > @fy_end) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'date after fy_end.';
	END IF;
END;

DROP TRIGGER IF EXISTS `bfup_%_PREFIX_%entries`;
CREATE TRIGGER `bfup_%_PREFIX_%entries` BEFORE UPDATE ON `%_PREFIX_%entries`
FOR EACH ROW BEGIN
	DECLARE dr_total decimal(25,2);
	DECLARE cr_total decimal(25,2);

	IF (NEW.dr_total IS NOT NULL) THEN
		SET dr_total = NEW.dr_total;
	ELSE
		SET dr_total = OLD.dr_total;
	END IF;
	IF (NEW.cr_total IS NOT NULL) THEN
		SET cr_total = NEW.cr_total;
	ELSE
		SET cr_total = OLD.cr_total;
	END IF;

	IF (dr_total < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dr_total cannot be less than 0.00.';
	END IF;
	IF (cr_total < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'cr_total cannot be less than 0.00.';
	END IF;
	IF !(dr_total <=> cr_total) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dr_total is not equal to cr_total.';
	END IF;

	IF (NEW.date IS NOT NULL) THEN
		SELECT fy_start, fy_end FROM `%_PREFIX_%settings` WHERE id = 1 INTO @fy_start, @fy_end;
		IF (NEW.date < @fy_start) THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'date before fy_start.';
		END IF;
		IF (NEW.date > @fy_end) THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'date after fy_end.';
		END IF;
	END IF;
END;

DROP TRIGGER IF EXISTS `bfins_%_PREFIX_%entryitems`;
CREATE TRIGGER `bfins_%_PREFIX_%entryitems` BEFORE INSERT ON `%_PREFIX_%entryitems`
FOR EACH ROW BEGIN
	SET NEW.dc = UPPER(NEW.dc);
	IF !(NEW.dc <=> 'D' OR NEW.dc <=> 'C') THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dc restricted to char D or C.';
	END IF;
	IF (NEW.amount < 0.0) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'amount cannot be less than 0.00.';
	END IF;
END;

DROP TRIGGER IF EXISTS `bfup_%_PREFIX_%entryitems`;
CREATE TRIGGER `bfup_%_PREFIX_%entryitems` BEFORE UPDATE ON `%_PREFIX_%entryitems`
FOR EACH ROW BEGIN
	IF (NEW.dc IS NOT NULL) THEN
		SET NEW.dc = UPPER(NEW.dc);
		IF !(NEW.dc <=> 'D' OR NEW.dc <=> 'C') THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'dc restricted to char D or C.';
		END IF;
	END IF;
	IF (NEW.amount IS NOT NULL) THEN
		IF (NEW.amount < 0.0) THEN
			SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'amount cannot be less than 0.00.';
		END IF;
	END IF;
END;

DROP TRIGGER IF EXISTS `bfins_%_PREFIX_%settings`;
CREATE TRIGGER `bfins_%_PREFIX_%settings` BEFORE INSERT ON `%_PREFIX_%settings`
FOR EACH ROW BEGIN
	SET NEW.id = 1;

	IF EXISTS (SELECT id FROM `%_PREFIX_%entries` WHERE date < NEW.fy_start) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'entries present before fy_start.';
	END IF;

	IF EXISTS (SELECT id FROM `%_PREFIX_%entries` WHERE date > NEW.fy_end) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'entries present after fy_end.';
	END IF;

	IF (NEW.fy_start >= NEW.fy_end) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'fy_start cannot be after fy_end.';
	END IF;
END;

DROP TRIGGER IF EXISTS `bfup_%_PREFIX_%settings`;
CREATE TRIGGER `bfup_%_PREFIX_%settings` BEFORE UPDATE ON `%_PREFIX_%settings`
FOR EACH ROW BEGIN
	DECLARE fy_start date;
	DECLARE fy_end date;

	SET NEW.id = 1;

	IF (NEW.fy_start IS NULL) THEN
		SET fy_start = OLD.fy_start;
	ELSE
		SET fy_start = NEW.fy_start;
	END IF;

	IF (NEW.fy_end IS NULL) THEN
		SET fy_end = OLD.fy_end;
	ELSE
		SET fy_end = NEW.fy_end;
	END IF;

	IF EXISTS (SELECT id FROM `%_PREFIX_%entries` WHERE date < fy_start) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'entries present before fy_start.';
	END IF;

	IF EXISTS (SELECT id FROM `%_PREFIX_%entries` WHERE date > fy_end) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'entries present after fy_end.';
	END IF;

	IF (fy_start >= fy_end) THEN
		SIGNAL SQLSTATE '12345' SET MESSAGE_TEXT = 'fy_start cannot be after fy_end.';
	END IF;
END;
