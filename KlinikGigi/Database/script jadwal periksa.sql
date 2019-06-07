ALTER TABLE `transaction_checkschedule` DROP FOREIGN KEY `transaction_checkschedule_ibfk_1`;

ALTER TABLE `transaction_checkschedule` ADD `PatientID` BIGINT NOT NULL AFTER `MedicationID`;

ALTER TABLE `transaction_checkschedule` ADD INDEX(`PatientID`);

ALTER TABLE `transaction_checkschedule` ADD CONSTRAINT `fk_patient` FOREIGN KEY (`PatientID`) REFERENCES `klinik_gigi`.`master_patient`(`PatientID`) ON DELETE CASCADE ON UPDATE CASCADE;

UPDATE `klinik_gigi`.`master_menu` SET `MenuName` = 'Jadwal Periksa' WHERE `master_menu`.`MenuID` = 12;