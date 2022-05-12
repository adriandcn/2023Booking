
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'opt_o_allow_cash', 'backend', 'Options / Allow payment with cash', 'script', '2014-11-05 03:18:52');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow payment with cash', 'script');

INSERT INTO `fields` VALUES (NULL, 'payment_methods_ARRAY_cash', 'arrays', 'payment_methods_ARRAY_cash', 'script', '2014-11-05 03:32:01');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblReservationPrice', 'backend', 'Label / Price', 'script', '2014-11-05 04:06:11');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblReservationTotal', 'backend', 'Label / Total', 'script', '2014-11-05 04:06:37');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblReservationSecurity");

UPDATE `multi_lang` SET `content` = 'Security deposit' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblReservationDeposit");

UPDATE `multi_lang` SET `content` = 'Reservation deposit' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;