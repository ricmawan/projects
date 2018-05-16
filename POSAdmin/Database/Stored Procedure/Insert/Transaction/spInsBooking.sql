DROP PROCEDURE IF EXISTS spInsBooking;

DELIMITER $$
CREATE PROCEDURE spInsBooking (
	pID 				BIGINT,
	pBookingNumber			VARCHAR(100),
	pRetailFlag			BIT,
    pCustomerID			BIGINT,
	pTransactionDate 	DATETIME,
	pBookingDetailsID		BIGINT,
    pBranchID			INT,
    pItemID				BIGINT,
	pItemDetailsID		BIGINT,
	pQuantity			DOUBLE,
    pBuyPrice			DOUBLE,
    pBookingPrice			DOUBLE,
	pDiscount			DOUBLE,
	pUserID				BIGINT,
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
		CALL spInsEventLog(@full_error, 'spInsBooking', pCurrentUser);
		SELECT
			pID AS 'ID',
            pBookingDetailsID AS 'BookingDetailsID',
			pBookingNumber AS 'BookingNumber',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		IF(pID = 0)	THEN /*Tambah baru*/
			SELECT
				CONCAT('DO', RIGHT(CONCAT('00', pUserID), 2), DATE_FORMAT(NOW(), '%Y%m'), RIGHT(CONCAT('00000', (IFNULL(MAX(CAST(RIGHT(BookingNumber, 5) AS UNSIGNED)), 0) + 1)), 5))
			FROM
				transaction_booking TS
			WHERE
				MONTH(TS.TransactionDate) = MONTH(NOW())
				AND YEAR(TS.TransactionDate) = YEAR(NOW())
			INTO 
				pBookingNumber;
				
SET State = 2;
			INSERT INTO transaction_booking
			(
				BookingNumber,
				RetailFlag,
				CustomerID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
				pBookingNumber,
				pRetailFlag,
				pCustomerID,
				pTransactionDate,
				NOW(),
				pCurrentUser
			);
		
SET State = 3;	               
			SELECT
				LAST_INSERT_ID()
			INTO 
				pID;
				
		ELSE
		
SET State = 4;
			UPDATE
				transaction_booking
			SET
				customerID = pCustomerID,
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				BookingID = pID;
				
		END IF;
		
SET State = 5;
		
		IF(pBookingDetailsID = 0) THEN
			INSERT INTO transaction_bookingdetails
			(
				BookingID,
				ItemID,
                ItemDetailsID,
				BranchID,
				Quantity,
				BuyPrice,
				BookingPrice,
				Discount,
				CreatedDate,
				CreatedBy
			)
			VALUES
			(
				pID,
				pItemID,
				pItemDetailsID,
				pBranchID,
				pQuantity,
				pBuyPrice,
				pBookingPrice,
				pDiscount,
				NOW(),
				pCurrentUser
			);
			
SET State = 6;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pBookingDetailsID;
		
		ELSE
				
SET State = 7;
			
			UPDATE 
				transaction_bookingdetails
			SET
				ItemID = pItemID,
                ItemDetailsID = pItemDetailsID,
				BranchID = pBranchID,
				Quantity = pQuantity,
				BuyPrice = pBuyPrice,
				BookingPrice = pBookingPrice,
				Discount = pDiscount,
				ModifiedBy = pCurrentUser
			WHERE
				BookingDetailsID = pBookingDetailsID;
			
		END IF;
		
SET State = 8;

		SELECT
			pID AS 'ID',
			pBookingDetailsID AS 'BookingDetailsID',
			pBookingNumber AS 'BookingNumber',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
                
	COMMIT;
END;
$$
DELIMITER ;
