
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblInstallInfoDesc");
UPDATE `multi_lang` SET `content` = 'Copy the code below and put it on your web page. It will show the front end calendar.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;