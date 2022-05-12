
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "error_titles_ARRAY_AO26");
UPDATE `multi_lang` SET `content` = 'Terms and Conditions' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;