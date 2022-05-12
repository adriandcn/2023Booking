
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_err_ARRAY_calendars', 'arrays', 'Calendar / Loading calendars ...', 'script', '2014-10-28 07:46:16');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Loading calendars ...', 'script');

COMMIT;