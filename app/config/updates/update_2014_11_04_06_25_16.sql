
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblOptionCopyTip");

UPDATE `multi_lang` SET `content` = 'You can copy all the options below from any of your other calendars.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

DROP TABLE IF EXISTS `password`;
CREATE TABLE IF NOT EXISTS `password` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;