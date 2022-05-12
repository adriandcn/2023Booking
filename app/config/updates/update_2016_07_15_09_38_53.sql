
START TRANSACTION;

ALTER TABLE `reservations` ADD COLUMN `price_based_on` enum('nights','days') DEFAULT NULL AFTER `date_to`;

COMMIT;