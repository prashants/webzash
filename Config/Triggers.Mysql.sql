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
