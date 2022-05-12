
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblEdit', 'backend', 'Label / Edit', 'script', '2014-11-04 02:12:03');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Edit', 'script');

COMMIT;