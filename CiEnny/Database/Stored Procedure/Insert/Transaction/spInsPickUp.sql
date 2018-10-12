/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert sale return
Created Date: 23 February 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsPickUp;

DELIMITER $$
CREATE PROCEDURE spInsPickUp (
	pID 				BIGINT, 
	pBookingID 			BIGINT,
	pTransactionDate 	DATETIME,
	pPickUpData 		TEXT,
	pIsEdit				INT,
    pCurrentUser		VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE PassValidate INT;
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spInsPickUp', pCurrentUser);
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
		IF(pIsEdit = 0)	THEN /*Tambah baru*/
			INSERT INTO transaction_pick
			(
				BookingID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES (
				pBookingID,
				pTransactionDate,
				NOW(),
				pCurrentUser
			);
			
SET State = 2;			               
			SELECT
				LAST_INSERT_ID()
			INTO 
				pID;
			
		ELSE
			
SET State = 3;
			UPDATE
				transaction_pick
			SET
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				PickID = pID;
	
		END IF;
	
SET State = 4;

		DELETE 
		FROM 
			transaction_pickdetails
		WHERE
			PickID = pID;
					
SET State = 5;
		IF(pPickUpData <> "" ) THEN
			SET @query = CONCAT("INSERT INTO transaction_pickdetails
								(
									PickID,
									ItemID,
                                    ItemDetailsID,
									BranchID,
									Quantity,
									BuyPrice,
									SalePrice,
                                    Discount,
									BookingDetailsID,
									CreatedDate,
									CreatedBy
								)
								VALUES", REPLACE(REPLACE(pPickUpData, ', UserLogin)', CONCAT(', "', pCurrentUser, '")')), '(0,', CONCAT('(', pID, ','))
								);
								
			PREPARE stmt FROM @query;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
			
		END IF;

SET State = 6;

		IF(pIsEdit = 0) THEN
			SELECT
				pID AS 'ID',
				'Pengambilan Berhasil Ditambahkan' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
		ELSE
	
SET State = 7;

			SELECT
				pID AS 'ID',
				'Pengambilan Berhasil Diubah' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
		END IF;
    COMMIT;
END;
$$
DELIMITER ;
