ALTER TABLE `transaction_onlineschedule` ADD `CustomerSelfRegFlag` BIT(1) NOT NULL AFTER `DeliveredDate`;
ALTER TABLE `transaction_onlineschedule` ADD `CustomerConfirmation` CHAR(1) NOT NULL AFTER `DeliveredDate`;
ALTER TABLE `transaction_onlineschedule` ADD `ConfirmedDate` DATETIME NOT NULL AFTER `DeliveredDate`;
ALTER TABLE `master_patient` ADD `NIK` VARCHAR(100) NOT NULL AFTER `PatientID`;