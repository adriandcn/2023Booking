
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_email_invoice', 'backend', 'Plugin / Email invoice', 'script', '2016-01-04 06:47:39');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email invoice', 'script');

COMMIT;