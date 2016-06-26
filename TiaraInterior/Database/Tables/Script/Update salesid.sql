UPDATE 
	transaction_salereturn SR
	JOIN master_customer MC
		ON MC.CustomerID = SR.CustomerID
SET
	SR.SalesID = MC.SalesID