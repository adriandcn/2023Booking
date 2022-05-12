
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_err_ARRAY_limit");
UPDATE `multi_lang` SET `content` = 'Limits not match. Min: {MIN}, Max: {MAX}, Your choise: {YOUR}' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;