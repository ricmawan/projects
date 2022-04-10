/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete Payment details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPaymentDetails;

DELIMITER $$
CREATE PROCEDURE spDelPaymentDetails (
	pPaymentDetailsID	BIGINT,
	pCurrentUser		VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT,
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spDelPaymentDetails', pCurrentUser);
        SELECT
			pPaymentDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_paymentdetails
		WHERE
			PaymentDetailsID = pPaymentDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPaymentDetailsID AS 'ID',
			'Pembayaran berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
