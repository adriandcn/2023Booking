
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "error_titles_ARRAY_AR11");
UPDATE `multi_lang` SET `content` = 'Reservation not updated!' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "error_bodies_ARRAY_AR11");
UPDATE `multi_lang` SET `content` = 'The period is already booked and reservation cannot be proceeded. Please change the date range.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;