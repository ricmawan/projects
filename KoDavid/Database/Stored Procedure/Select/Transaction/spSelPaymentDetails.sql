/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPaymentDetails;

DELIMITER $$
CREATE PROCEDURE spSelPaymentDetails (
	pTransactionID		BIGINT,
    pTransactionType	VARCHAR(1),
    pCurrentUser		VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelPaymentDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		PD.PaymentDetailsID,
        DATE_FORMAT(PD.PaymentDate, '%Y-%m-%d') PlainTransactionDate,
        DATE_FORMAT(PD.PaymentDate, '%d-%m-%Y') TransactionDate,
        PD.Amount,
        PD.Remarks
	FROM
		transaction_paymentdetails PD
	WHERE
		PD.TransactionID = pTransactionID
        AND TRIM(PD.TransactionType) = TRIM(pTransactionType)
	ORDER BY
		PD.PaymentDetailsID;
        
END;
$$
DELIMITER ;
