
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_err_ARRAY_limit', 'arrays', 'Calendar / Limit not match', 'script', '2015-05-28 06:56:19');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Limits not match. \r\nMin: {MIN}, Max: {MAX}, Your choise: {YOUR}', 'script');

COMMIT;