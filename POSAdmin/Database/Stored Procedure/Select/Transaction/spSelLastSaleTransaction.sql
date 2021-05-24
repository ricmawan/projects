/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure to select sale transaction for print last transaction
Created Date: 24 May 2021
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelLastSaleTransaction;

DELIMITER $$
CREATE PROCEDURE spSelLastSaleTransaction (
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelLastSaleTransaction', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TS.SaleID,
		TS.SaleNumber,
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
		TS.TransactionDate PlainTransactionDate,
		TS.CreatedBy,
		MC.CustomerName,
		MC.Address,
		MC.City,
		MC.Telephone,
		MP.PaymentTypeName,
		TS.Payment,
		TS.Discount,
		TS.CreatedDate
	FROM
		transaction_sale TS
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_paymenttype MP
			ON MP.PaymentTypeID = TS.PaymentTypeID
	WHERE
		TS.CreatedBy = pCurrentUser
		AND FinishFlag = 1
	ORDER BY
		TS.SaleID DESC
	LIMIT 0,1;

END;
$$
DELIMITER ;
