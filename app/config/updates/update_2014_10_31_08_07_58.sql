
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblCalendar', 'backend', 'Label / Calendar', 'script', '2014-10-31 07:49:56');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Calendar', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoReservationFeedTitle', 'backend', 'Infobox / Reservations Feed URL', 'script', '2014-10-31 08:03:52');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Reservations Feed URL', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoReservationFeedDesc', 'backend', 'Infobox / Reservations Feed URL', 'script', '2014-10-31 08:04:24');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use the URL below to have access to all reservations. Please, note that if you change the password the URL will change too as password is used in the URL itself so no one else can open it.', 'script');

ALTER TABLE `reservations` ADD `modified` datetime DEFAULT NULL AFTER `c_zip`;

COMMIT;