DROP PROCEDURE IF EXISTS spInsPayment;

DELIMITER $$
CREATE PROCEDURE spInsPayment (
	pID 				BIGINT,
	pPaymentDate 		DATETIME,
    pTransactionType 	VARCHAR(1),
	pPaymentDetailsID	BIGINT,
    pAmount				DOUBLE,
	pRemarks			TEXT,
    pCurrentUser		VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spInsPayment', pCurrentUser);
		SELECT
			pID AS 'ID',
            pPaymentDetailsID AS 'PaymentDetailsID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		IF(pPaymentDetailsID = 0) THEN
			INSERT INTO transaction_paymentdetails
			(
				PaymentDetailsID,
                TransactionID,
                TransactionType,
				PaymentDate,
                Amount,
				Remarks,
				CreatedDate,
				CreatedBy
			)
			VALUES
			(
				pPaymentDetailsID,
				pID,
				pTransactionType,
				pPaymentDate,
				pAmount,
				pRemarks,
				NOW(),
				pCurrentUser
			);
			
SET State = 6;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pPaymentDetailsID;
		
		ELSE
				
SET State = 7;
			
			UPDATE 
				transaction_paymentdetails
			SET
				PaymentDate = pPaymentDate,
				Amount = pAmount,
				Remarks = pRemarks,
				ModifiedBy = pCurrentUser
			WHERE
				PaymentDetailsID = pPaymentDetailsID;
			
		END IF;
		
SET State = 8;

		SELECT
			pID AS 'ID',
			pPaymentDetailsID AS 'PaymentDetailsID',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
                
	COMMIT;
END;
$$
DELIMITER ;
