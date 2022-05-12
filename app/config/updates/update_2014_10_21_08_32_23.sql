
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblChangeDates', 'frontend', 'change dates', 'script', '2014-10-21 03:50:40');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'change dates', 'script');

COMMIT;