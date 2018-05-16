/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert the supplier
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsSupplier;

DELIMITER $$
CREATE PROCEDURE spInsSupplier (
	pID 				BIGINT, 
    pSupplierCode		VARCHAR(100),
	pSupplierName 		VARCHAR(255),
    pTelephone			VARCHAR(100),
	pAddress			TEXT,
    pCity				VARCHAR(100),
	pRemarks			TEXT,
    pIsEdit				INT,
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
		CALL spInsEventLog(@full_error, 'spInsSupplier', pCurrentUser);
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

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_supplier
		WHERE
			TRIM(SupplierCode) = TRIM(pSupplierCode)
			AND SupplierID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;

			SELECT
				pID AS 'ID',
				CONCAT('Kode Supplier ', pSupplierCode, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		END IF;
        
SET State = 1;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_supplier
		WHERE
			(TRIM(SupplierName) = TRIM(pSupplierName)
            AND TRIM(Address) = TRIM(pAddress))
			AND SupplierID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 3;

			SELECT
				pID AS 'ID',
				CONCAT('Nama Supplier ', pSupplierName, ' dengan alamat ', pAddress, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 4;

			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_supplier
				(
                    SupplierCode,
                    SupplierName,
					Telephone,
					Address,
					City,
					Remarks,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pSupplierCode,
					pSupplierName,
					pTelephone,
					pAddress,
					pCity,
					pRemarks,
					NOW(),
					pCurrentUser
				);
			
SET State = 5;			               

				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;

SET State = 6;

				SELECT
					pID AS 'ID',
					'Supplier Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 7;

				UPDATE
					master_supplier
				SET
					SupplierCode = pSupplierCode,
                    SupplierName = pSupplierName,
					Telephone = pTelephone,
					Address = pAddress,
					City = pCity,
					Remarks = pRemarks,
					ModifiedBy = pCurrentUser
				WHERE
					SupplierID = pID;

SET State = 8;

				SELECT
					pID AS 'ID',
					'Supplier Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
