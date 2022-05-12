
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_status_required', 'backend', 'Invoice plugin / Status required', 'plugin', '2016-01-08 14:33:31');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status is required', 'plugin');

COMMIT;