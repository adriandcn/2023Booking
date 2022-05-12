
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'jquery_validation_ARRAY_required', 'arrays', 'jquery_validation_ARRAY_required', 'script', '2015-05-05 09:30:54');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This field is required.', 'script');

INSERT INTO `fields` VALUES (NULL, 'jquery_validation_ARRAY_email', 'arrays', 'jquery_validation_ARRAY_email', 'script', '2015-05-05 09:19:20');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please enter a valid email.', 'script');

COMMIT;