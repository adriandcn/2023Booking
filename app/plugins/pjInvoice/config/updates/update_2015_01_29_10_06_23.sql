
START TRANSACTION;

ALTER TABLE `plugin_invoice` ADD COLUMN `payment_method` enum('paypal','authorize','creditcard','bank','cash') DEFAULT NULL AFTER `status`;

ALTER TABLE `plugin_invoice` ADD COLUMN `cc_type` blob AFTER `payment_method`;

ALTER TABLE `plugin_invoice` ADD COLUMN `cc_num` blob AFTER `cc_type`;

ALTER TABLE `plugin_invoice` ADD COLUMN `cc_exp_month` blob AFTER `cc_num`;

ALTER TABLE `plugin_invoice` ADD COLUMN `cc_exp_year` blob AFTER `cc_exp_month`;

ALTER TABLE `plugin_invoice` ADD COLUMN `cc_code` blob AFTER `cc_exp_year`;

ALTER TABLE `plugin_invoice` ADD COLUMN `y_country` int(10) DEFAULT NULL AFTER `y_street_address`;

ALTER TABLE `plugin_invoice` ADD COLUMN `b_country` int(10) DEFAULT NULL AFTER `b_street_address`;

ALTER TABLE `plugin_invoice` ADD COLUMN `s_country` int(10) DEFAULT NULL AFTER `s_street_address`;

ALTER TABLE `plugin_invoice_config` ADD COLUMN `y_country` int(10) DEFAULT NULL AFTER `y_street_address`;

ALTER TABLE `plugin_invoice_config` ADD COLUMN `p_accept_cash` tinyint(1) unsigned DEFAULT '0' AFTER `p_accept_creditcard`;

ALTER TABLE `plugin_invoice_config` ADD COLUMN `o_use_qty_unit_price` tinyint(1) unsigned DEFAULT '1' AFTER `o_qty_is_int`;

UPDATE `plugin_invoice_config` SET `y_country`='236', `o_qty_is_int`='0', `o_use_qty_unit_price`='1' WHERE `id`='1';

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_accept_cash', 'backend', 'Invoice plugin / Allow cash payments', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Allow cash payments', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_use_shipping_details', 'backend', 'Invoice plugin / Use Shipping Details', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use Shipping Details', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_view_invoice', 'backend', 'Invoice plugin / View invoice', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'View invoice', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_print_invoice', 'backend', 'Invoice plugin / Print invoice', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Print invoice', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_company_information', 'backend', 'Invoice plugin / Company information', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Company Details', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_invoice_config', 'backend', 'Invoice plugin / Invoice config', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_invoice_template', 'backend', 'Invoice plugin / Invoice template', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Template', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_details', 'backend', 'Invoice plugin / Details', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Details', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_client', 'backend', 'Invoice plugin / Client', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_invoice', 'backend', 'Invoice plugin / Invoice', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Invoice', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_payment_method', 'backend', 'Invoice plugin / Payment method', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment method', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_payment_methods_ARRAY_authorize', 'arrays', 'Invoice plugin / Payment method: Authorize.net', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.net', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_payment_methods_ARRAY_bank', 'arrays', 'Invoice plugin / Payment method: Bank account', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bank account', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_payment_methods_ARRAY_creditcard', 'arrays', 'Invoice plugin / Payment method: Credit card', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_payment_methods_ARRAY_paypal', 'arrays', 'Invoice plugin / Payment method: PayPal', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PayPal', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_payment_methods_ARRAY_cash', 'arrays', 'Invoice plugin / Payment method: Cash', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'plugin');


INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_option', 'backend', 'Invoice plugin / Option', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Option', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_value', 'backend', 'Invoice plugin / Value', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Value', 'plugin');


INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_pay_with_cash', 'backend', 'Invoice plugin / Pay with Cash', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pay with Cash', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_cc_type', 'backend', 'Invoice plugin / CC Type', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CC Type', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_cc_num', 'backend', 'Invoice plugin / CC Number', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CC Number', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_cc_code', 'backend', 'Invoice plugin / CC Code', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CC Code', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_cc_exp', 'backend', 'Invoice plugin / CC Expiration', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CC Expiration', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_uuid_exists', 'backend', 'Invoice plugin / Invoice ID already exists.', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Invoice ID already exists.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_cc_types_ARRAY_Maestro', 'arrays', 'Invoice plugin / CC Types: Maestro', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maestro', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_cc_types_ARRAY_AmericanExpress', 'arrays', 'Invoice plugin / CC Types: AmericanExpress', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'AmericanExpress', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_cc_types_ARRAY_MasterCard', 'arrays', 'Invoice plugin / CC Types: MasterCard', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'MasterCard', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_cc_types_ARRAY_Visa', 'arrays', 'Invoice plugin / CC Types: Visa', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Visa', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_cc_details', 'backend', 'Invoice plugin / CC Details', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'CC Details', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_country', 'backend', 'Invoice plugin / Country', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Country', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_use_qty_unit_price', 'backend', 'Invoice plugin / Use quantity and Unit price', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use quantity and Unit price', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_payment_confirm', 'backend', 'Invoice plugin / Payment confirm', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thank you for your payment. We will contact you as soon as possible.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_i_send_invoice_link', 'backend', 'Invoice plugin / Send invoice link', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Click on the link below to view the invoice.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_yesno_ARRAY_1', 'arrays', 'Invoice plugin / Yes', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'plugin_invoice_yesno_ARRAY_0', 'arrays', 'Invoice plugin / No', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_PIN12', 'arrays', 'Invoice plugin / Invoice billing title', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'List of invoices', 'plugin');


INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_PIN12', 'arrays', 'Invoice plugin / Invoice billing body', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can see the list of generated invoices.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_PIN13', 'arrays', 'Invoice plugin / Invoice billing title', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Company Details', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_PIN13', 'arrays', 'Invoice plugin / Invoice billing body', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use the form below to set up your company details. These details will be used for all the invoices that you create. To view all invoices <a href="index.php?controller=pjInvoice&action=pjActionInvoices">click here</a>', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_PIN14', 'arrays', 'Invoice plugin / Invoice config', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_PIN14', 'arrays', 'Invoice plugin / Invoice billing body', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Set payment methods to be accepted when invoices are paid. You can also specify if Shipping details will be used in the invoices you create or not.', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_PIN15', 'arrays', 'Invoice plugin / Invoice template', 'plugin', '2015-01-20 13:30:56');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Template', 'plugin');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_PIN15', 'arrays', 'Invoice plugin / Invoice billing body', 'plugin', '2015-01-20 13:42:25');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use the editor below to create a template for your invoices. Include different tokens which will be replaced by invoice data.<br /><br /><br /> <table width="100%" border="0" cellpadding="5">   <tr>     <td align="left" valign="top" width="50%">{uuid} - invoice unique ID<br />      	{order_id} - order ID          {issue_date} - invoice issue date<br />         {due_date} - payment due date<br />         {created} - created date and time<br />         {modified} - update date and time<br />         {status} - invoice status<br />         {subtotal} - sub total amount<br />         {discount} - discount amount<br />         {tax} - tax amount<br />         {shipping} - shipping amount<br />         {total} - total amount<br />         {paid_deposit} - paid deposit amount<br />         {amount_due} - amount due<br />         {currency} - currency<br />         {notes} - notes<br /> 	    {items} - items         <strong>Company details</strong><br />         {y_logo} - logo<br />         {y_company} - company name<br />         {y_name} - name<br />         {y_street_address} - address<br />         {y_country} - country<br />         {y_city} - city<br />         {y_state} - state<br />         {y_zip} - zip<br />         {y_phone} - phone<br />         {y_fax} - fax<br />         {y_email} - email<br />         {y_url} - url</td>     <td align="left" valign="top" width="50%">     <strong>Client billing details</strong><br />     {b_billing_address} - address<br />     {b_company} - company<br />     {b_name} - name<br />     {b_address} - address<br />     {b_street_address} - street address<br />     {b_country} - country<br />     {b_city} - city<br />     {b_state} - state<br />     {b_zip} - zip<br />     {b_phone} - phone<br />     {b_fax} - fax<br />     {b_email} - email<br />     {b_url} - url<br />     <strong>Client shipping details</strong><br />     {s_shipping_address} - address<br />     {s_company} - company<br />     {s_name} - name<br />     {s_address} - address<br />     {s_street_address} - street address<br />     {s_country} - country<br />     {s_city} - city<br />     {s_state} - state<br />     {s_zip} - zip<br />     {s_phone} - phone<br />     {s_fax} - fax<br />     {s_email} - email<br />     {s_url} - url<br />     {s_date} - shipping date<br />     {s_terms} - shipping terms<br />     {s_is_shipped} - is shipped status<br />     </td>   </tr> </table>', 'plugin');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "plugin_invoice_template");
UPDATE `multi_lang` SET `content` = 'Template' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "plugin_invoice_company_info");
UPDATE `multi_lang` SET `content` = 'Company Details' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "plugin_invoice_statuses_ARRAY_not_paid");
UPDATE `multi_lang` SET `content` = 'Not Paid' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "plugin_invoice_statuses_ARRAY_paid");
UPDATE `multi_lang` SET `content` = 'Paid' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "plugin_invoice_statuses_ARRAY_cancelled");
UPDATE `multi_lang` SET `content` = 'Cancelled' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "error_bodies_ARRAY_PIN01");
UPDATE `multi_lang` SET `content` = 'Use the form below to set up your company details. These details will be used for all the invoices that you create. To view all invoices <a href="index.php?controller=pjInvoice&action=pjActionInvoices">click here</a>' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "error_bodies_ARRAY_PIN09");
UPDATE `multi_lang` SET `content` = 'Invoice will be sent to the following email address(es). You can change or delete any of them.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;