
START TRANSACTION;

INSERT INTO `multi_lang` (`id`, `model`, `foreign_id`, `locale`, `field`, `content`, `source`) 
SELECT NULL, 'pjInvoiceConfig', '1', '::LOCALE::', 'y_company', `y_company`, 'plugin'
FROM `plugin_invoice_config`
WHERE `id` = '1'
LIMIT 1;

INSERT INTO `multi_lang` (`id`, `model`, `foreign_id`, `locale`, `field`, `content`, `source`) 
SELECT NULL, 'pjInvoiceConfig', '1', '::LOCALE::', 'y_name', `y_name`, 'plugin'
FROM `plugin_invoice_config`
WHERE `id` = '1'
LIMIT 1;

INSERT INTO `multi_lang` (`id`, `model`, `foreign_id`, `locale`, `field`, `content`, `source`) 
SELECT NULL, 'pjInvoiceConfig', '1', '::LOCALE::', 'y_street_address', `y_street_address`, 'plugin'
FROM `plugin_invoice_config`
WHERE `id` = '1'
LIMIT 1;

INSERT INTO `multi_lang` (`id`, `model`, `foreign_id`, `locale`, `field`, `content`, `source`) 
SELECT NULL, 'pjInvoiceConfig', '1', '::LOCALE::', 'y_city', `y_city`, 'plugin'
FROM `plugin_invoice_config`
WHERE `id` = '1'
LIMIT 1;

INSERT INTO `multi_lang` (`id`, `model`, `foreign_id`, `locale`, `field`, `content`, `source`) 
SELECT NULL, 'pjInvoiceConfig', '1', '::LOCALE::', 'y_state', `y_state`, 'plugin'
FROM `plugin_invoice_config`
WHERE `id` = '1'
LIMIT 1;

INSERT INTO `multi_lang` (`id`, `model`, `foreign_id`, `locale`, `field`, `content`, `source`) 
SELECT NULL, 'pjInvoiceConfig', '1', '::LOCALE::', 'y_template', `y_template`, 'plugin'
FROM `plugin_invoice_config`
WHERE `id` = '1'
LIMIT 1;

INSERT INTO `multi_lang` (`id`, `model`, `foreign_id`, `locale`, `field`, `content`, `source`) 
SELECT NULL, 'pjInvoiceConfig', '1', '::LOCALE::', 'p_paypal_address', `p_paypal_address`, 'plugin'
FROM `plugin_invoice_config`
WHERE `id` = '1'
LIMIT 1;

INSERT INTO `multi_lang` (`id`, `model`, `foreign_id`, `locale`, `field`, `content`, `source`) 
SELECT NULL, 'pjInvoiceConfig', '1', '::LOCALE::', 'p_bank_account', `p_bank_account`, 'plugin'
FROM `plugin_invoice_config`
WHERE `id` = '1'
LIMIT 1;

ALTER TABLE `plugin_invoice_config` DROP `y_company`,
DROP `y_name`,
DROP `y_street_address`,
DROP `y_city`,
DROP `y_state`,
DROP `y_template`,
DROP `p_paypal_address`,
DROP `p_bank_account`;

ALTER TABLE `plugin_invoice` ADD `locale_id` INT(10) UNSIGNED NULL AFTER `foreign_id`;

COMMIT;