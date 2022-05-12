
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'login_err_ARRAY_4', 'arrays', 'login_err_ARRAY_4', 'script', '2016-07-14 15:10:12');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email is not valid.', 'script');

COMMIT;