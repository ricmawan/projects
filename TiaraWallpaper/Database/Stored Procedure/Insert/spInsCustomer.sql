DROP PROCEDURE IF EXISTS spInsCustomer;

DELIMITER $$
CREATE PROCEDURE spInsCustomer (
	pID 			BIGINT,
	pSalesID		BIGINT,
	pCustomerName 	VARCHAR(255),
	pAddress1 		TEXT,
	pAddress2 		TEXT,
	pCity			VARCHAR(100),
	pTelephone		VARCHAR(255),
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	/*DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR ", IFNULL(@ErrNo, ''), " (", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		SELECT 
			pId AS 'ID', 
			'Terjadi Kesalahan Sistem' AS 'Message', 
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag', 
			State AS 'State';
	END;*/
	
	SET PassValidate = 1;
	
	START TRANSACTION;	

SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_customer
		WHERE
			TRIM(CustomerName) = TRIM(pCustomerName)
			AND TRIM(Address1) = TRIM(pAddress1)
			AND TRIM(Address2) = TRIM(pAddress2)
			AND TRIM(City) = TRIM(pCity)
			AND CustomerID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Customer sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_customer
				(
					CustomerName,
					SalesID,
					Address1,
					Address2,
					City,
					Telephone,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pCustomerName,
					pSalesID,
					pAddress1,
					pAddress2,
					pCity,
					pTelephone,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Pelanggan Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';		
			ELSE
SET State = 5;
				UPDATE
					master_customer
				SET
					CustomerName = pCustomerName,
					SalesID = pSalesID,
					Address1 = pAddress1,
					Address2 = pAddress2,
					City = pCity,
					Telephone = pTelephone,
					ModifiedBy = pCurrentUser
				WHERE
					CustomerID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Pelanggan Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
		
			END IF;	
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
