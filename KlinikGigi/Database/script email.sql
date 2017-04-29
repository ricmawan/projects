ALTER TABLE `transaction_checkschedule` 
ADD `EmailStatus` TEXT NOT NULL AFTER `ScheduledDate`, 
ADD `EmailMessage` TEXT NOT NULL AFTER `EmailStatus`, 
ADD `DeliveredDate` DATETIME NOT NULL AFTER `EmailMessage`;

ALTER TABLE `master_patient` ADD `Email` VARCHAR(255) NOT NULL AFTER `Telephone`;