DROP PROCEDURE IF EXISTS spInsFirstBalance;

DELIMITER $$
CREATE PROCEDURE spInsFirstBalance (
	pID 				BIGINT,
    pFirstBalanceAmount	DOUBLE,
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
		CALL spInsEventLog(@full_error, 'spInsFirstBalance', pCurrentUser);
		SELECT
			pID AS 'ID',
           'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;
		INSERT INTO transaction_firstbalance
		(
            UserID,
            TransactionDate,
            FirstBalanceAmount,
			CreatedDate,
			CreatedBy
		)
		VALUES 
        (
			pID,
			NOW(),
			pFirstBalanceAmount,
			NOW(),
			pCurrentUser
		);


		SELECT
			pID AS 'ID',
           'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

	COMMIT;
END;
$$
DELIMITER ;
