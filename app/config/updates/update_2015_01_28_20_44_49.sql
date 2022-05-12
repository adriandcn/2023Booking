
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblDuplicatedUniqueID', 'backend', 'Label / There is another reservation with such ID.', 'script', '2015-01-28 20:40:22');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There is another reservation with such ID.', 'script');

COMMIT;