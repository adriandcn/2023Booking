
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblBackToCalendar', 'frontend', 'Back to calendar', 'script', '2014-10-20 07:48:41');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Back to calendar', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_err_ARRAY_calendar', 'arrays', 'Calendar / Loading calendar ...', 'script', '2014-10-20 09:19:08');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Loading calendar ...', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_err_ARRAY_form', 'arrays', 'Calendar / Loading booking form ...', 'script', '2014-10-20 09:19:36');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Loading booking form ...', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_err_ARRAY_summary', 'arrays', 'Calendar / Loading confirmation ...', 'script', '2014-10-20 09:20:11');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Loading confirmation ...', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_err_ARRAY_save', 'arrays', 'Calendar / Saving reservation ...', 'script', '2014-10-20 09:20:41');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Saving reservation ...', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_err_ARRAY_paypal', 'arrays', 'Calendar / Redirecting to PayPal.com ...', 'script', '2014-10-20 09:21:41');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Saving reservation and redirecting to PayPal.com ...', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_err_ARRAY_authorize', 'arrays', 'Calendar / Redirecting to Authorize.net ...', 'script', '2014-10-20 09:23:28');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Saving reservation and redirecting to Authorize.net ...', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblCalendarMessage', 'frontend', 'Calendar message', 'script', '2014-10-20 09:43:03');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Click on available arrival date and then on departure date to make a reservation.', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_background_nav_hover', 'backend', 'Options / Month Nav Hover Background', 'script', '2014-10-20 10:32:11');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Month Nav Hover Background', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblWeekTitle', 'backend', 'week', 'script', '2014-10-20 10:45:59');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'week', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "reservation_calc_body");
UPDATE `multi_lang` SET `content` = 'Are you sure you want to recalculate the price? Current price will be lost!' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;