
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'export_periods_ARRAY_all', 'arrays', 'export_periods_ARRAY_all', 'script', '2015-12-29 07:31:37');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All', 'script');

INSERT INTO `fields` VALUES (NULL, 'export_periods_ARRAY_range', 'arrays', 'export_periods_ARRAY_range', 'script', '2015-12-29 07:32:36');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Specific period', 'script');

COMMIT;