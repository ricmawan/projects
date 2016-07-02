ALTER TABLE `transaction_salereturn` ADD `SalesID` BIGINT NOT NULL AFTER `CustomerID`;

ALTER TABLE `transaction_buyreturn` ADD `IsCancelled` BIT(1) NOT NULL AFTER `Remarks`;

ALTER TABLE `transaction_salereturn` ADD `IsCancelled` BIT(1) NOT NULL AFTER `Remarks`;

ALTER TABLE `transaction_incoming` ADD `IsCancelled` BIT(1) NOT NULL AFTER `Remarks`;

ALTER TABLE `transaction_cancellation` ADD `IncomingID` BIGINT NOT NULL AFTER `OutgoingID`, ADD `SaleReturnID` BIGINT NOT NULL AFTER `IncomingID`, ADD `BuyReturnID` BIGINT NOT NULL AFTER `SaleReturnID`;

ALTER TABLE `transaction_incoming` ADD `DeliveryCost` DOUBLE NOT NULL AFTER `TransactionDate`;

INSERT INTO master_menu
(
	MenuID,
	GroupMenuID,
	MenuName,
	URL,
	Icon,
	IsReport,
	OrderNo    
)
VALUES
(
	26,
	4,
	'Retur Jual',
	'Report/SaleReturn/',
	NULL,
	1,
	7
),
(
	27,
	4,
	'Retur Beli',
	'Report/BuyReturn/',
	NULL,
	1,
	8
);

INSERT INTO master_role
VALUES
(
	0,
	1,
	26,
	1,
	1
),
(
	0,
	1,
	27,
	1,
	1
);

UPDATE 
	transaction_salereturn SR
	JOIN master_customer MC
		ON MC.CustomerID = SR.CustomerID
SET
	SR.SalesID = MC.SalesID
	
ALTER TABLE `transaction_cancellation` DROP FOREIGN KEY `transaction_cancellation_ibfk_2`;