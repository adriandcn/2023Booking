
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblAvailableTokens', 'backend', 'Label / Available tokens', 'script', '2014-11-05 06:21:38');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td width="50%" valign="top">{Name} - The customer''s name;<br/> {Email} - The customer''s e-mail;<br/> {Phone} - The provided phone number;<br/> {Adults} - Number of adults;<br/> {Children} - Number of children;<br/> {Notes} - Any additional notes;<br/> {Address} - The provided address;<br/> {City} - The provided city;<br/> {Country} - The provided country;<br/> {State} - The provided state;<br/> {Zip} - The provided zip code;<br/> {CCType} - The provided CC type;<br/> {CCNum} - The provided CC number;<br/>{CCExpMonth} - The provided CC exp.month;<br/> {CCExpYear} - The provided CC exp.year;<br/> {CCSec} - The provided CC sec. code;<br/> {PaymentMethod} - The payment method;</td><td width="50%" valign="top">{StartDate} - Reservation''s start date;<br/> {EndDate} - Reservation''s end date;<br/> {Deposit} - Deposit;<br/> {Security} - Security amount;<br/> {Tax} - Tax;<br/> {Price} - Price;<br/> {TotalPrice} - Total Price;<br/> {CalendarID} - Calendar ID;<br/> {ReservationID} - Reservation''s ID;<br/> {ReservationUUID} - Reservation''s UUID;<br/> {CancelURL} - Cancel URL</td</tr></tbody></table>', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_body_new_reservation");

UPDATE `multi_lang` SET `content` = 'Body' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_booking_status_ARRAY_1");

UPDATE `multi_lang` SET `content` = 'Your reservation has been received.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;