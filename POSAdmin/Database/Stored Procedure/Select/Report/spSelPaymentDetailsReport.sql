/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPaymentDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelPaymentDetailsReport (
	pSaleID				BIGINT,
    pFromDate			DATE,
	pTransactionType 	VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelPaymentDetailsReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') PaymentDate,
        IFNULL(TS.Payment, 0) Amount,
        'Pembayaran Awal' Remarks
	FROM
		transaction_sale TS
	WHERE
		TS.SaleID = pSaleID
        AND pTransactionType = 'Penjualan'
        AND IFNULL(TS.Payment, 0) > 0
    UNION ALL
    SELECT
		DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') PaymentDate,
        IFNULL(TB.Payment, 0) Amount,
        'Pembayaran Awal' Remarks
	FROM
		transaction_booking TB
	WHERE
		TB.BookingID = pSaleID
        AND pTransactionType = 'Pemesanan'
        AND IFNULL(TB.Payment, 0) > 0
	UNION ALL
	SELECT
		DATE_FORMAT(PD.PaymentDate, '%d-%m-%Y') PaymentDate,
        IFNULL(PD.Amount, 0) Amount,
        PD.Remarks
	FROM
		transaction_paymentdetails PD
	WHERE
		PD.TransactionID = pSaleID
        AND CASE
				WHEN pTransactionType = 'Penjualan'
                THEN 'S'
                WHEN pTransactionType = 'Pemesanan'
                THEN 'B'
                ELSE 'P'
			END = PD.TransactionType
		AND PD.PaymentDate <= pFromDate;        
END;
$$
DELIMITER ;
