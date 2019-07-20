ALTER master_patient AUTO_INCREMENT = 1000000;
ALTER transaction_medication AUTO_INCREMENT = 2000000;
ALTER transaction_medicationdetails AUTO_INCREMENT = 3000000;
ALTER TABLE `master_patient` ADD `Synchronized` BIT(1) NOT NULL AFTER `Info`;
ALTER TABLE `transaction_medicationdetails` ADD `Synchronized` BIT(1) NOT NULL AFTER `Quantity`;