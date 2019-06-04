DROP PROCEDURE IF EXISTS spInsSale;

DELIMITER $$
CREATE PROCEDURE spInsSale (
	pID 				BIGINT,
	pSaleNumber			VARCHAR(100),
	pRetailFlag			BIT,
    pFinishFlag			BIT,
    pCustomerID			BIGINT,
	pTransactionDate 	DATETIME,
	pSaleDetailsID		BIGINT,
    pBranchID			INT,
    pItemID				BIGINT,
	pItemDetailsID		BIGINT,
	pQuantity			DOUBLE,
    pBuyPrice			DOUBLE,
    pSalePrice			DOUBLE,
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
		CALL spInsEventLog(@full_error, 'spInsSale', pCurrentUser);
		SELECT
			pID AS 'ID',
            pSaleDetailsID AS 'SaleDetailsID',
			pSaleNumber AS 'SaleNumber',
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
				CONCAT(RIGHT(CONCAT('00', pUserID), 2), DATE_FORMAT(NOW(), '%Y%m'), RIGHT(CONCAT('00000', (IFNULL(MAX(CAST(RIGHT(SaleNumber, 5) AS UNSIGNED)), 0) + 1)), 5))
			FROM
				transaction_sale TS
			WHERE
				MONTH(TS.TransactionDate) = MONTH(NOW())
				AND YEAR(TS.TransactionDate) = YEAR(NOW())
			INTO 
				pSaleNumber;
				
SET State = 2;
			INSERT INTO transaction_sale
			(
				SaleNumber,
				RetailFlag,
                FinishFlag,
				CustomerID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
				pSaleNumber,
				pRetailFlag,
                pFinishFlag,
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
				transaction_sale
			SET
				customerID = pCustomerID,
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				SaleID = pID;
				
		END IF;
		
SET State = 5;
		
		IF(pSaleDetailsID = 0) THEN
			INSERT INTO transaction_saledetails
			(
				SaleID,
				ItemID,
                ItemDetailsID,
				BranchID,
				Quantity,
				BuyPrice,
				SalePrice,
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
				pSalePrice,
				pDiscount,
				NOW(),
				pCurrentUser
			);
			
SET State = 6;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pSaleDetailsID;
		
		ELSE
				
SET State = 7;
			
			UPDATE 
				transaction_saledetails
			SET
				ItemID = pItemID,
                ItemDetailsID = pItemDetailsID,
				BranchID = pBranchID,
				Quantity = pQuantity,
				BuyPrice = pBuyPrice,
				SalePrice = pSalePrice,
				Discount = pDiscount,
				ModifiedBy = pCurrentUser
			WHERE
				SaleDetailsID = pSaleDetailsID;
			
		END IF;
		
SET State = 8;

		SELECT
			pID AS 'ID',
			pSaleDetailsID AS 'SaleDetailsID',
			pSaleNumber AS 'SaleNumber',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
                
	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert sale return
Created Date: 23 February 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsSaleReturn;

DELIMITER $$
CREATE PROCEDURE spInsSaleReturn (
	pID 				BIGINT, 
	pSaleID 			BIGINT,
	pTransactionDate 	DATETIME,
	pSaleReturnData 	TEXT,
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
		CALL spInsEventLog(@full_error, 'spInsSaleReturn', pCurrentUser);
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
			INSERT INTO transaction_salereturn
			(
				SaleID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES (
				pSaleID,
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
				transaction_salereturn
			SET
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				SaleReturnID = pID;
	
		END IF;
	
SET State = 4;

		DELETE 
		FROM 
			transaction_salereturndetails
		WHERE
			SaleReturnID = pID;
					
SET State = 5;
		IF(pSaleReturnData <> "" ) THEN
			SET @query = CONCAT("INSERT INTO transaction_salereturndetails
								(
									SaleReturnID,
									ItemID,
									BranchID,
									Quantity,
									BuyPrice,
									SalePrice,
									SaleDetailsID,
									CreatedDate,
									CreatedBy
								)
								VALUES", REPLACE(REPLACE(pSaleReturnData, ', UserLogin)', CONCAT(', "', pCurrentUser, '")')), '(0,', CONCAT('(', pID, ','))
								);
								
			PREPARE stmt FROM @query;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
			
		END IF;

SET State = 6;

		IF(pIsEdit = 0) THEN
			SELECT
				pID AS 'ID',
				'Retur Berhasil Ditambahkan' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
		ELSE
	
SET State = 7;

			SELECT
				pID AS 'ID',
				'Retur Berhasil Diubah' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
		END IF;
    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsStockAdjust;

DELIMITER $$
CREATE PROCEDURE spInsStockAdjust (
	pID 						BIGINT,
	pBranchID					INT,
	pTransactionDate 			DATETIME,
	pStockAdjustDetailsID		BIGINT,
    pItemID						BIGINT,
    pItemDetailsID				BIGINT,
	pQuantity					DOUBLE,
	pAdjustedQuantity			DOUBLE,
    pUserID						BIGINT,
    pCurrentUser				VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spInsStockAdjust', pCurrentUser);
		SELECT
			pID AS 'ID',
            pStockAdjustDetailsID AS 'StockAdjustDetailsID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		IF(pID = 0)	THEN /*Tambah baru*/
			INSERT INTO transaction_stockadjust
			(
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
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
				transaction_stockadjust
			SET
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				StockAdjustID = pID;
				
		END IF;
		
SET State = 5;
		
		IF(pStockAdjustDetailsID = 0) THEN
			INSERT INTO transaction_stockadjustdetails
			(
				StockAdjustID,
				BranchID,
				ItemID,
                ItemDetailsID,
				Quantity,
				AdjustedQuantity,
				CreatedDate,
				CreatedBy
			)
			VALUES
			(
				pID,
				pBranchID,
				pItemID,
                pItemDetailsID,
				pQuantity,
				pAdjustedQuantity,
				NOW(),
				pCurrentUser
			);
			
SET State = 6;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pStockAdjustDetailsID;
		
		ELSE
				
SET State = 7;
			
			UPDATE 
				transaction_stockadjustdetails
			SET
				ItemID = pItemID,
				ItemDetailsID = pItemDetailsID,
				BranchID = pBranchID,
				Quantity = pQuantity,
				AdjustedQuantity = pAdjustedQuantity,
				ModifiedBy = pCurrentUser
			WHERE
				StockAdjustDetailsID = pStockAdjustDetailsID;
			
		END IF;
		
SET State = 8;

		SELECT
			pID AS 'ID',
			pStockAdjustDetailsID AS 'StockAdjustDetailsID',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
                
	COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsStockMutation;

DELIMITER $$
CREATE PROCEDURE spInsStockMutation (
	pID 						BIGINT,
	pSourceID					INT,
	pDestinationID				INT,
	pTransactionDate 			DATETIME,
	pStockMutationDetailsID		BIGINT,
    pItemID						BIGINT,
    pItemDetailsID				BIGINT,
	pQuantity					DOUBLE,
    pUserID						BIGINT,
    pCurrentUser				VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spInsStockMutation', pCurrentUser);
		SELECT
			pID AS 'ID',
            pStockMutationDetailsID AS 'StockMutationDetailsID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		IF(pID = 0)	THEN /*Tambah baru*/
			INSERT INTO transaction_stockmutation
			(
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
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
				transaction_stockmutation
			SET
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				StockMutationID = pID;
				
		END IF;
		
SET State = 5;
		
		IF(pStockMutationDetailsID = 0) THEN
			INSERT INTO transaction_stockmutationdetails
			(
				StockMutationID,
				SourceID,
				DestinationID,
				ItemID,
                ItemDetailsID,
				Quantity,
				CreatedDate,
				CreatedBy
			)
			VALUES
			(
				pID,
				pSourceID,
				pDestinationID,
				pItemID,
                pItemDetailsID,
				pQuantity,
				NOW(),
				pCurrentUser
			);
			
SET State = 6;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pStockMutationDetailsID;
		
		ELSE
				
SET State = 7;
			
			UPDATE 
				transaction_stockmutationdetails
			SET
				ItemID = pItemID,
                ItemDetailsID = pItemDetailsID,
				SourceID = pSourceID,
				DestinationID = pDestinationID,
				Quantity = pQuantity,
				ModifiedBy = pCurrentUser
			WHERE
				StockMutationDetailsID = pStockMutationDetailsID;
			
		END IF;
		
SET State = 8;

		SELECT
			pID AS 'ID',
			pStockMutationDetailsID AS 'StockMutationDetailsID',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
                
	COMMIT;
END;
$$
DELIMITER ;
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
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert the supplier
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsUnit;

DELIMITER $$
CREATE PROCEDURE spInsUnit (
	pID 			INT, 
	pUnitName 		VARCHAR(255),
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
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spInsUnit', pCurrentUser);
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
			master_unit
		WHERE
			TRIM(UnitName) = TRIM(pUnitName)
			AND UnitID <> pID
		LIMIT 1;
        
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				CONCAT('Nama satuan ', pUnitName, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_unit
				(
					UnitName,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pUnitName,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Satuan Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_unit
				SET
					UnitName= pUnitName,
					ModifiedBy = pCurrentUser
				WHERE
					UnitID = pID;

SET State = 6;
				SELECT
					pID AS 'ID',
					'Satuan Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert the user
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsUser;

DELIMITER $$
CREATE PROCEDURE spInsUser (
	pID 			BIGINT, 
	pUserName 		VARCHAR(255),
	pUserTypeID		SMALLINT,
	pUserLogin 		VARCHAR(100),
	pPassword 		VARCHAR(255),
	pIsActive		BIT,
	pRoleValues		TEXT,
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spInsUser', pCurrentUser);
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
			master_user 
		WHERE
			UserLogin = pUserLogin
			AND UserID <> pID
		LIMIT 1;
			
SET State = 2;

		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Username ', pUserLogin, ' sudah dipakai') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
		
SET State = 3;

			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_user
				(
					UserName,
					UserLogin,
					UserTypeID,
					UserPassword,
					IsActive,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pUserName,
					pUserLogin,
					pUserTypeID,
					pPassword,
					pIsActive,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;
					
			ELSE
			
SET State = 5;
				UPDATE
					master_user
				SET
					UserName = pUserName,
					UserLogin = pUserLogin,
					UserTypeID = pUserTypeID,
					UserPassword = pPassword,
					IsActive = pIsActive,
					ModifiedBy = pCurrentUser
				WHERE
					UserID = pID;
		
			END IF;
	
SET State = 6;

				DELETE 
				FROM 
					master_role
				WHERE
					UserID = pID;
					
SET State = 7;
			IF(pRoleValues <> "" ) THEN
				SET @query = CONCAT("INSERT INTO master_role
									(
										UserID,
										MenuID,
										EditFlag,
										DeleteFlag
									)
									VALUES", REPLACE(pRoleValues, '(0,', CONCAT('(', pID, ',')));
									
				PREPARE stmt FROM @query;
				EXECUTE stmt;
				DEALLOCATE PREPARE stmt;
				
			END IF;

		END IF;

SET State = 8;

	IF(pIsEdit = 0) THEN
		SELECT
			pID AS 'ID',
			'User Berhasil Ditambahkan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	ELSE
	
SET State = 9;

		SELECT
			pID AS 'ID',
			'User Berhasil Diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	END IF;
	
    COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select category
Created Date: 30 Desember 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelCategory;

DELIMITER $$
CREATE PROCEDURE spSelCategory (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUser', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_category MC
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MC.CategoryID,
						MC.CategoryCode,
						MC.CategoryName
					FROM
						master_category MC
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item details by itemCode
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelCategoryByName;

DELIMITER $$
CREATE PROCEDURE spSelCategoryByName (
	pCategoryName	VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelCategoryByName', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MC.CategoryID,
		MC.CategoryName
	FROM
		master_category MC
	WHERE
		TRIM(MC.CategoryName) = TRIM(pCategoryName);
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select Customer
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelCustomer;

DELIMITER $$
CREATE PROCEDURE spSelCustomer (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelCustomer', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_customer MC
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MC.CustomerID,
                        MC.CustomerCode,
                        MC.CustomerName,
                        MC.Telephone,
                        MC.Address,
                        MC.City,
                        MC.Remarks
					FROM
						master_customer MC
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select branch from dropdown list
Created Date: 11 january 2018
Modified Date: 
===============================================================*/


DROP PROCEDURE IF EXISTS spSelDDLBranch;
DELIMITER $$
CREATE PROCEDURE spSelDDLBranch (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLBranch', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		MB.BranchID,
		MB.BranchCode,
		MB.BranchName
	FROM 
		master_branch MB
	ORDER BY 
		MB.BranchID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select category from dropdown list
Created Date: 3 january 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDDLCategory;

DELIMITER $$
CREATE PROCEDURE spSelDDLCategory (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLCategory', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		MC.CategoryID,
		MC.CategoryCode,
		MC.CategoryName
	FROM 
		master_category MC
	ORDER BY 
		MC.CategoryCode;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select customer from dropdown list
Created Date: 9 january 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDDLCustomer;

DELIMITER $$
CREATE PROCEDURE spSelDDLCustomer (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLCustomer', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		MC.CustomerID,
		MC.CustomerCode,
		MC.CustomerName
	FROM 
		master_customer MC
	ORDER BY 
		MC.CustomerCode;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select supplier from dropdown list
Created Date: 9 january 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDDLSupplier;

DELIMITER $$
CREATE PROCEDURE spSelDDLSupplier (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLSupplier', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		MS.SupplierID,
		MS.SupplierCode,
		MS.SupplierName
	FROM 
		master_supplier MS
	ORDER BY 
		MS.SupplierCode;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select category from dropdown list
Created Date: 3 january 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDDLUnit;

DELIMITER $$
CREATE PROCEDURE spSelDDLUnit (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLUnit', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		MU.UnitID,
		MU.UnitName
	FROM 
		master_unit MU
	ORDER BY 
		MU.UnitName;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select user type from dropdown list
Created Date: 7 january 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDDLUserType;

DELIMITER $$
CREATE PROCEDURE spSelDDLUserType (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLUserType', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		UT.UserTypeID,
        UT.UserTypeName
	FROM 
		master_usertype UT
	ORDER BY 
		UT.UserTypeID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportItem;

DELIMITER $$
CREATE PROCEDURE spSelExportItem (
	pCategoryID		BIGINT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelExportItem', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MI.ItemID,
		MI.ItemCode,
		MI.ItemName,
		MC.CategoryID,
		MC.CategoryName,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
		MI.MinimumStock,
        MU.UnitName
	FROM
		master_item MI
		JOIN master_category MC
			ON MC.CategoryID = MI.CategoryID
		JOIN master_unit MU
			ON MU.UnitID = MI.UnitID
	WHERE 
		MC.CategoryID = pCategoryID;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItem;

DELIMITER $$
CREATE PROCEDURE spSelItem (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelItem', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_item MI
                        JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						JOIN master_unit MU
							ON MU.UnitID = MI.UnitID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MI.ItemID,
                        MI.ItemCode,
						MI.ItemName,
                        MC.CategoryID,
						MC.CategoryName,
                        MI.BuyPrice,
						MI.RetailPrice,
                        MI.Price1,
                        MI.Qty1,
                        MI.Price2,
                        MI.Qty2,
                        MI.Weight,
                        MI.MinimumStock,
                        MU.UnitID,
                        MU.UnitName
					FROM
						master_item MI
                        JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						JOIN master_unit MU
							ON MU.UnitID = MI.UnitID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemUnitDetails;

DELIMITER $$
CREATE PROCEDURE spSelItemUnitDetails (
	pItemID			BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelItemUnitDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		ItemDetailsID,
		ItemID,
		ItemDetailsCode,
		UnitID,
		ConversionQuantity
		/*BuyPrice,
		RetailPrice,
		Price1,
		Qty1,
		Price2,
		Qty2,
		Weight,
		MinimumStock*/
	FROM
		master_itemdetails MID
	WHERE
		MID.ItemID = pItemID
	ORDER BY
		MID.ItemDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Get all menu
Created Date: 24 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelMenu;

DELIMITER $$
CREATE PROCEDURE spSelMenu (
	 pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN
	
    DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelMenu', pCurrentUser);
	END;

SET State = 1;

	SELECT 
		MG.GroupMenuID,
		MG.GroupMenuName,
		MM.MenuID,
		MM.MenuName
	FROM
		master_groupmenu MG
		JOIN master_menu MM 
			ON MG.GroupMenuID = MM.GroupMenuID
	GROUP BY
		MM.MenuID
	ORDER BY 
		MG.OrderNo ASC , 
		MM.OrderNo ASC;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for return all parameter
Created Date: 16 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelParameter;

DELIMITER $$
CREATE PROCEDURE spSelParameter (
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN
	
    DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelParameter', pCurrentUser);
	END;

SET State = 1;

	SELECT
		ParameterName,
		ParameterValue
	FROM
		master_parameter;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select supplier
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSupplier;

DELIMITER $$
CREATE PROCEDURE spSelSupplier (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelSupplier', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_supplier MS
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MS.SupplierID,
                        MS.SupplierCode,
                        MS.SupplierName,
                        MS.Telephone,
                        MS.Address,
                        MS.City,
                        MS.Remarks
					FROM
						master_supplier MS
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select category
Created Date: 30 Desember 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUnit;

DELIMITER $$
CREATE PROCEDURE spSelUnit (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUnit', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_unit MU
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MU.UnitID,
						MU.UnitName
					FROM
						master_unit MU
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item details by itemCode
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUnitByName;

DELIMITER $$
CREATE PROCEDURE spSelUnitByName (
	pUnitName		VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelUnitByName', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MU.UnitID,
        MU.UnitName
	FROM
		master_unit MU
	WHERE
		TRIM(MU.UnitName) = TRIM(pUnitName);
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select user login
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUser;

DELIMITER $$
CREATE PROCEDURE spSelUser (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUser', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_user MU
						JOIN master_usertype MUT
							ON MU.UserTypeID = MUT.UserTypeID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MU.UserID,
						MU.UserName,
						MU.UserLogin,
						CASE
							WHEN MU.IsActive = 0
							THEN 'Tidak Aktif'
							ELSE 'Aktif'
						END AS Status,
						MU.IsActive,
                        MUT.UserTypeID,
						MUT.UserTypeName,
                        IFNULL(GC.MenuID, '') MenuID,
                        IFNULL(GC.EditFlag, '') EditFlag,
                        IFNULL(GC.DeleteFlag, '') DeleteFlag
					FROM
						master_user MU
						JOIN master_usertype MUT
							ON MU.UserTypeID = MUT.UserTypeID
						LEFT JOIN 
                        (
							SELECT
								GC.UserID,
                                GROUP_CONCAT(MenuID SEPARATOR ', ') MenuID,
                                GROUP_CONCAT(EditFlag SEPARATOR ', ') EditFlag,
                                GROUP_CONCAT(DeleteFlag SEPARATOR ', ') DeleteFlag
							FROM
								master_role GC
							GROUP BY
								GC.UserID
                        )GC
							ON MU.UserID = GC.UserID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select user login
Created Date: 1 Desember 2017to show user details
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUserDetails;

DELIMITER $$
CREATE PROCEDURE spSelUserDetails (
	pUserID 		BIGINT,
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUserDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MR.RoleID,
		MR.UserID,
		MR.MenuID,
		MR.EditFlag,
		MR.DeleteFlag,
		MU.UserName,
		MU.UserLogin,
		MU.IsActive
	FROM
		master_user MU
		LEFT JOIN master_role MR
			ON MU.UserID = MR.UserID
	WHERE
		MU.UserID = pUserID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select user login
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUserLogin;

DELIMITER $$
CREATE PROCEDURE spSelUserLogin (
	pUserLogin 		VARCHAR(100),
	pPassword 		VARCHAR(255),
	pIsActive		BIT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUserLogin', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		UserID,
		UserName,
		UserLogin,
		UserPassword,
		UserTypeID
	FROM
		master_user 
	WHERE 
		UserLogin = pUserLogin
		AND IsActive = pIsActive
		AND UserPassword = pPassword
	LIMIT 1;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Get user menu permission
Created Date: 24 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUserMenuNavigation;

DELIMITER $$
CREATE PROCEDURE spSelUserMenuNavigation (
	pUserID			BIGINT,
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN
	
    DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUserMenuNavigation', pCurrentUser);
	END;

SET State = 1;

	SELECT 
		MG.GroupMenuID,
		MG.GroupMenuName,
		MM.MenuID,
		MM.MenuName,
		MM.Url,
		MG.Icon
	FROM
		master_groupmenu MG
		JOIN master_menu MM 
			ON MG.GroupMenuID = MM.GroupMenuID
		JOIN master_role MR 
			ON MR.MenuID = MM.MenuID
	WHERE
		MR.UserID = pUserID
	GROUP BY
		MM.MenuID
	ORDER BY 
		MG.OrderNo ASC , 
		MM.OrderNo ASC;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Get user menu permission
Created Date: 16 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUserMenuPermission;

DELIMITER $$
CREATE PROCEDURE spSelUserMenuPermission (
	pApplicationPath	VARCHAR(255),
    pRequestedPath		VARCHAR(255),
	pCurrentUser		VARCHAR(255)
)
StoredProcedure:BEGIN
	
    DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUserMenuPermission', pCurrentUser);
	END;

SET State = 1;

	SELECT
		MR.EditFlag,
		MR.DeleteFlag
	FROM
		master_role MR
		JOIN master_menu MM
			ON MM.MenuID = MR.MenuID
	WHERE
		CONCAT(pApplicationPath, MM.Url) = pRequestedPath
		AND MR.UserID = pCurrentUser;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete category
Created Date: 1 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelCategory;

DELIMITER $$
CREATE PROCEDURE spDelCategory (
	pCategoryID		INT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelCategory', pCurrentUser);
        SELECT
			pCategoryID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_category
		WHERE
			CategoryID = pCategoryID;

    COMMIT;
    
SET State = 2;

		 SELECT
			pCategoryID AS 'ID',
			'Kategori berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete Customer
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelCustomer;

DELIMITER $$
CREATE PROCEDURE spDelCustomer (
	pCustomerID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelCustomer', pCurrentUser);
        SELECT
			pCustomerID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_customer
		WHERE
			CustomerID = pCustomerID;

    COMMIT;
    
SET State = 2;

		SELECT
			pCustomerID AS 'ID',
			'Pelanggan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelItem;

DELIMITER $$
CREATE PROCEDURE spDelItem (
	pItemID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelItem', pCurrentUser);
        SELECT
			pItemID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_item
		WHERE
			ItemID = pItemID;

    COMMIT;
    
SET State = 2;

		SELECT
			pItemID AS 'ID',
			'Barang berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete supplier
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelSupplier;

DELIMITER $$
CREATE PROCEDURE spDelSupplier (
	pSupplierID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelSupplier', pCurrentUser);
        SELECT
			pSupplierID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_supplier
		WHERE
			SupplierID = pSupplierID;

    COMMIT;
    
SET State = 2;

		SELECT
			pSupplierID AS 'ID',
			'Supplier berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete category
Created Date: 1 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelUnit;

DELIMITER $$
CREATE PROCEDURE spDelUnit (
	pUnitID			INT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelUnit', pCurrentUser);
        SELECT
			pUnitID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_unit
		WHERE
			UnitID = pUnitID;

    COMMIT;
    
SET State = 2;

		 SELECT
			pUnitID AS 'ID',
			'Satuan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete user
Created Date: 27 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelUser;

DELIMITER $$
CREATE PROCEDURE spDelUser (
	pUserID			BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelUser', pCurrentUser);
        SELECT
			pUserID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_role
		WHERE
			UserID = pUserID;
			
SET State = 2;

		DELETE FROM
			master_user
		WHERE
			UserID = pUserID;

    COMMIT;
    
SET State = 3;

		SELECT
			pUserID AS 'ID',
			'User berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportCustomerPurchaseReport;

DELIMITER $$
CREATE PROCEDURE spSelExportCustomerPurchaseReport (
	pCustomerID		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelExportCustomerPurchaseReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TS.SaleID,
		TS.SaleNumber,
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
		SUM(SD.Quantity * SD.SalePrice) - SD.Discount Total
	FROM
		transaction_sale TS
		JOIN transaction_saledetails SD
			ON SD.SaleID = TS.SaleID
	WHERE 
		TS.CustomerID = pCustomerID
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	GROUP BY
		TS.SaleID,
		TS.SaleNumber,
		TS.TransactionDate
	UNION ALL
	SELECT
		TSR.SaleReturnID,
		CONCAT('R', TS.SaleNumber),
		DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
		SUM(SRD.Quantity * SRD.SalePrice) Total
	FROM
		transaction_salereturn TSR
		JOIN transaction_sale TS
			ON TS.SaleID = TSR.SaleID
		JOIN transaction_salereturndetails SRD
			ON SRD.SaleReturnID = TSR.SaleReturnID
	WHERE 
		TS.CustomerID = pCustomerID
		AND CAST(TSR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TSR.TransactionDate AS DATE) <= pToDate
	GROUP BY
		TSR.SaleReturnID,
		TS.SaleNumber,
		TSR.TransactionDate
	ORDER BY
		SaleNumber,
        SaleID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelCustomerPurchaseReport;

DELIMITER $$
CREATE PROCEDURE spSelCustomerPurchaseReport (
	pCustomerID		INT,
	pFromDate		DATE,
	pToDate			DATE,
	pWhere 			TEXT,
    pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								1
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
							WHERE 
								TS.CustomerID = ", pCustomerID ,"
								AND CAST(TS.TransactionDate AS DATE) >= '", pFromDate, "'
								AND CAST(TS.TransactionDate AS DATE) <= '", pToDate, "'
								AND ", pWhere, "
							GROUP BY
								TS.SaleID
							UNION ALL
		                    SELECT
								1
							FROM
								transaction_salereturn TSR
								JOIN transaction_sale TS
									ON TS.SaleID = TSR.SaleID
		                        JOIN transaction_salereturndetails SRD
									ON SRD.SaleReturnID = TSR.SaleReturnID
							WHERE 
								TS.CustomerID = ", pCustomerID ,"
								AND CAST(TSR.TransactionDate AS DATE) >= '", pFromDate, "'
								AND CAST(TSR.TransactionDate AS DATE) <= '", pToDate, "'
								AND ", pWhere2, "
		                    GROUP BY
								TSR.SaleReturnID
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
						'Penjualan' TransactionType,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        SUM(SD.Quantity * SD.SalePrice) - SD.Discount Total
					FROM
						transaction_sale TS
                        JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
					WHERE 
						TS.CustomerID = ", pCustomerID ,"
						AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TS.SaleID,
                        TS.SaleNumber,
                        TS.TransactionDate
                    UNION ALL
                    SELECT
						TSR.SaleReturnID,
						'Retur' TransactionType,
                        CONCAT('R', TS.SaleNumber),
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        SUM(SRD.Quantity * SRD.SalePrice) Total
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN transaction_salereturndetails SRD
							ON SRD.SaleReturnID = TSR.SaleReturnID
					WHERE 
						TS.CustomerID = ", pCustomerID ,"
						AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere2, "
                    GROUP BY
						TSR.SaleReturnID,
                        TS.SaleNumber,
                        TSR.TransactionDate
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportIncomeReport;

DELIMITER $$
CREATE PROCEDURE spSelExportIncomeReport (
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelExportIncomeReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TS.SaleID,
		TS.SaleNumber,
        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        SD.Quantity,
        SD.BuyPrice,
        (SD.Quantity * SD.BuyPrice) TotalBuy,
        SD.SalePrice,
        SD.Discount,
        ((SD.Quantity * SD.SalePrice) - SD.Discount) TotalSale,
		(SD.Quantity * SD.SalePrice) - (SD.Quantity * SD.BuyPrice) Income
    FROM
		transaction_sale TS
        JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
	WHERE
		SD.BranchID = pBranchID
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		TSR.SaleReturnID,
		CONCAT('R', TS.SaleNumber) SaleNumber,
        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        SRD.Quantity,
        SRD.BuyPrice,
		(SRD.Quantity * SRD.BuyPrice) TotalBuy,
        SRD.SalePrice,
        0 Discount,
        (SRD.Quantity * SRD.SalePrice) TotalSale,
        -((SRD.Quantity * SRD.SalePrice) - (SRD.Quantity * SRD.BuyPrice)) Income
    FROM
		transaction_salereturn TSR
		JOIN transaction_sale TS
			ON TSR.SaleID = TS.SaleID
        JOIN transaction_salereturndetails SRD
			ON TSR.SaleReturnID = SRD.SaleReturnID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SRD.ItemID
	WHERE
		SRD.BranchID = pBranchID
		AND CAST(TSR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TSR.TransactionDate AS DATE) <= pToDate
	ORDER BY
		SaleNumber,
        SaleID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelIncomeDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelIncomeDetailsReport (
	pSaleID				BIGINT,
	pBranchID			INT,
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
		CALL spInsEventLog(@full_error, 'spSelIncomeDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	IF(pTransactionType = 'Penjualan')
	THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        SD.Quantity,
            SD.BuyPrice,
            (SD.Quantity * SD.BuyPrice) TotalBuy,
	        SD.SalePrice,
			SD.Discount,
			((SD.Quantity * SD.SalePrice) - SD.Discount) TotalSale,
            ((SD.Quantity * SD.SalePrice) - SD.Discount) - (SD.Quantity * SD.BuyPrice) Income
		FROM
			transaction_saledetails SD
	        JOIN master_item MI
				ON MI.ItemID = SD.ItemID
		WHERE
			SD.SaleID = pSaleID
            AND SD.BranchID = pBranchID
		ORDER BY
			SD.SaleDetailsID;
	ELSE
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        SRD.Quantity,
            SRD.BuyPrice,
            (SRD.Quantity * SRD.BuyPrice) TotalBuy,
	        SRD.SalePrice,
            0 Discount,
			(SRD.Quantity * SRD.SalePrice) TotalSale,
            -((SRD.Quantity * SRD.SalePrice) - (SRD.Quantity * SRD.BuyPrice)) Income
		FROM
			transaction_salereturndetails SRD
	        JOIN master_item MI
				ON MI.ItemID = SRD.ItemID
		WHERE
			SRD.SaleReturnID = pSaleID
            AND SRD.BranchID = pBranchID
		ORDER BY
			SRD.SaleReturnDetailsID;
            
	END IF;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelIncomeReport;

DELIMITER $$
CREATE PROCEDURE spSelIncomeReport(
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
	pWhere 			TEXT,
    pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelIncomeReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								1
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
							WHERE 
								SD.BranchID = ", pBranchID ,"
								AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere, "
							GROUP BY
								TS.SaleID
							UNION ALL
		                    SELECT
								1
							FROM
								transaction_salereturn TSR
								JOIN transaction_sale TS
									ON TS.SaleID = TSR.SaleID
		                        JOIN transaction_salereturndetails SRD
									ON SRD.SaleReturnID = TSR.SaleReturnID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
							WHERE 
								SRD.BranchID = ", pBranchID ,"
								AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere2, "
		                    GROUP BY
								TSR.SaleReturnID
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
						'Penjualan' TransactionType,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        MC.CustomerName,
                        SUM(SD.Quantity * SD.SalePrice) - SD.Discount Total
					FROM
						transaction_sale TS
                        JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE 
						SD.BranchID = ", pBranchID ,"
						AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TS.SaleID,
                        TS.SaleNumber,
                        TS.TransactionDate,
                        MC.CustomerName
                    UNION ALL
                    SELECT
						TSR.SaleReturnID,
						'Retur' TransactionType,
                        CONCAT('R', TS.SaleNumber),
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        MC.CustomerName,
                        SUM(SRD.Quantity * SRD.SalePrice) Total
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN transaction_salereturndetails SRD
							ON SRD.SaleReturnID = TSR.SaleReturnID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE 
						SRD.BranchID = ", pBranchID ,"
						AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere2, "
                    GROUP BY
						TSR.SaleReturnID,
                        TS.SaleNumber,
                        TSR.TransactionDate,
                        MC.CustomerName
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportPurchaseReport;

DELIMITER $$
CREATE PROCEDURE spSelExportPurchaseReport (
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelExportPurchaseReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TP.PurchaseID,
		TP.PurchaseNumber,
		DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
		MS.SupplierName,
        MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        PD.Quantity,
        PD.BuyPrice,
		(PD.Quantity * PD.BuyPrice) SubTotal
	FROM
		transaction_purchase TP
		JOIN transaction_purchasedetails PD
			ON TP.PurchaseID = PD.PurchaseID
		JOIN master_supplier MS
			ON MS.SupplierID = TP.SupplierID
		JOIN master_item MI
			ON MI.ItemID = PD.ItemID
	WHERE
		PD.BranchID = pBranchID
		AND CAST(TP.TransactionDate AS DATE) >= pFromDate
		AND CAST(TP.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		TPR.PurchaseReturnID,
		TPR.PurchaseReturnNumber,
		DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
		MS.SupplierName,
        MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        PRD.Quantity,
        PRD.BuyPrice,
		-(PRD.Quantity * PRD.BuyPrice) SubTotal
	FROM
		transaction_purchasereturn TPR
		JOIN transaction_purchasereturndetails PRD
			ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
		JOIN master_supplier MS
			ON MS.SupplierID = TPR.SupplierID
		JOIN master_item MI
			ON MI.ItemID = PRD.ItemID
	WHERE
		PRD.BranchID = pBranchID
		AND CAST(TPR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TPR.TransactionDate AS DATE) <= pToDate
	ORDER BY
		PurchaseNumber,
        PurchaseID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportSaleReport;

DELIMITER $$
CREATE PROCEDURE spSelExportSaleReport (
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelExportSaleReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TS.SaleID,
		TS.SaleNumber,
        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        SD.Quantity,
        SD.SalePrice,
        SD.Discount,
        ((SD.Quantity * SD.SalePrice) - SD.Discount) SubTotal
    FROM
		transaction_sale TS
        JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
	WHERE
		SD.BranchID = pBranchID
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		TSR.SaleReturnID,
		CONCAT('R', TS.SaleNumber) SaleNumber,
        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        SRD.Quantity,
        SRD.SalePrice,
        0 Discount,
        -(SRD.Quantity * SRD.SalePrice) SubTotal
    FROM
		transaction_salereturn TSR
		JOIN transaction_sale TS
			ON TSR.SaleID = TS.SaleID
        JOIN transaction_salereturndetails SRD
			ON TSR.SaleReturnID = SRD.SaleReturnID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SRD.ItemID
	WHERE
		SRD.BranchID = pBranchID
		AND CAST(TSR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TSR.TransactionDate AS DATE) <= pToDate
	ORDER BY
		SaleNumber,
        SaleID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportStockReport;

DELIMITER $$
CREATE PROCEDURE spSelExportStockReport (
	pCategoryID		BIGINT,
	pBranchID 		INT,
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
		CALL spInsEventLog(@full_error, 'spSelExportStockReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		MI.ItemCode,
		MI.ItemName,
		MC.CategoryName,
		MB.BranchName,
		(IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0)) Stock
	FROM
		master_item MI
		CROSS JOIN master_branch MB
		JOIN master_category MC
			ON MC.CategoryID = MI.CategoryID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				TPD.BranchID,
				SUM(TPD.Quantity) Quantity
			FROM
				transaction_purchasedetails TPD
				JOIN master_item MI
					ON TPD.ItemID = MI.ItemID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN TPD.BranchID
						ELSE pBranchID
					END = TPD.BranchID
			GROUP BY
				MI.ItemID,
				TPD.BranchID
		)TP
			ON TP.ItemID = MI.ItemID
			AND MB.BranchID = TP.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SRD.BranchID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_salereturndetails SRD
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SRD.BranchID
						ELSE pBranchID
					END = SRD.BranchID
			GROUP BY
				MI.ItemID,
				SRD.BranchID
		)SR
			ON SR.ItemID = MI.ItemID
			AND SR.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SD.BranchID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_saledetails SD
				JOIN master_item MI
					ON SD.ItemID = MI.ItemID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SD.BranchID
						ELSE pBranchID
					END = SD.BranchID
			GROUP BY
				MI.ItemID,
				SD.BranchID
		)S
			ON S.ItemID = MI.ItemID
			AND S.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				PRD.BranchID,
				SUM(PRD.Quantity) Quantity
			FROM
				transaction_purchasereturndetails PRD
				JOIN master_item MI
					ON MI.ItemID = PRD.ItemID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN PRD.BranchID
						ELSE pBranchID
					END = PRD.BranchID
			GROUP BY
				MI.ItemID,
				PRD.BranchID
		)PR
			ON MI.ItemID = PR.ItemID
			AND PR.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SMD.DestinationID,
				SUM(SMD.Quantity) Quantity
			FROM
				transaction_stockmutationdetails SMD
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SMD.DestinationID
						ELSE pBranchID
					END = SMD.DestinationID
			GROUP BY
				MI.ItemID,
				SMD.DestinationID
		)SM
			ON MI.ItemID = SM.ItemID
			AND SM.DestinationID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SAD.BranchID,
				SUM(SAD.AdjustedQuantity - SAD.Quantity) Quantity
			FROM
				transaction_stockadjustdetails SAD
				JOIN master_item MI
					ON MI.ItemID = SAD.ItemID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SAD.BranchID
						ELSE pBranchID
					END = SAD.BranchID
			GROUP BY
				MI.ItemID,
				SAD.BranchID
		)SA
			ON MI.ItemID = SA.ItemID
			AND SA.BranchID = MB.BranchID
	WHERE
		CASE
			WHEN pCategoryID = 0
			THEN MC.CategoryID
			ELSE pCategoryID
		END = MC.CategoryID
		AND CASE
				WHEN pBranchID = 0
				THEN MB.BranchID
				ELSE pBranchID
			END = MB.BranchID
	ORDER BY
		MC.CategoryID ASC,
        MI.ItemCode ASC;
                    
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsPurchaseReturn;

DELIMITER $$
CREATE PROCEDURE spInsPurchaseReturn (
	pID 						BIGINT,
	pPurchaseReturnNumber		VARCHAR(100),
	pPurchaseReturnDetailsID	BIGINT,
    pSupplierID					BIGINT,
	pTransactionDate 			DATETIME,
    pBranchID					INT,
    pItemID						BIGINT,
	pQuantity					DOUBLE,
    pBuyPrice					DOUBLE,
	pUserID						BIGINT,
    pCurrentUser				VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spInsPurchaseReturn', pCurrentUser);
		SELECT
			pID AS 'ID',
            pPurchaseReturnDetailsID AS 'PurchaseReturnDetailsID',
            pPurchaseReturnNumber AS 'PurchaseReturnNumber',
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
				CONCAT('RB', RIGHT(CONCAT('00', pUserID), 2), DATE_FORMAT(NOW(), '%Y%m'), RIGHT(CONCAT('00000', (IFNULL(MAX(CAST(RIGHT(PurchaseReturnNumber, 5) AS UNSIGNED)), 0) + 1)), 5))
			FROM
				transaction_purchasereturn PR
			WHERE
				MONTH(PR.TransactionDate) = MONTH(NOW())
				AND YEAR(PR.TransactionDate) = YEAR(NOW())
			INTO 
				pPurchaseReturnNumber;

			INSERT INTO transaction_purchasereturn
			(
				PurchaseReturnNumber,
				SupplierID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
				pPurchaseReturnNumber,
				pSupplierID,
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
				transaction_purchasereturn
			SET
				SupplierID = pSupplierID,
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				PurchaseReturnID = pID;
				
		END IF;
            
SET State = 4;
			
		IF(pPurchaseReturnDetailsID = 0) THEN
			INSERT INTO transaction_purchasereturndetails
			(
				PurchaseReturnID,
				ItemID,
				BranchID,
				Quantity,
				BuyPrice,
				CreatedDate,
				CreatedBy
			)
			VALUES
			(
				pID,
				pItemID,
				pBranchID,
				pQuantity,
				pBuyPrice,
				NOW(),
				pCurrentUser
			);
			
SET State = 5;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pPurchaseReturnDetailsID;
			
		ELSE
				
SET State = 6;
			
			UPDATE 
				transaction_purchasereturndetails
			SET
				ItemID = pItemID,
				BranchID = pBranchID,
				Quantity = pQuantity,
				BuyPrice = pBuyPrice,
				ModifiedBy = pCurrentUser
			WHERE
				PurchaseReturnDetailsID = pPurchaseReturnDetailsID;
			
		END IF;
			
SET State = 7;
		
		SELECT
			pID AS 'ID',
			pPurchaseReturnDetailsID AS 'PurchaseReturnDetailsID',
			pPurchaseReturnNumber AS 'PurchaseReturnNumber',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseReport;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseReport (
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
	pWhere 			TEXT,
    pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								1
							FROM
								transaction_purchase TP
								JOIN transaction_purchasedetails PD
									ON TP.PurchaseID = PD.PurchaseID
								JOIN master_supplier MS
									ON MS.SupplierID = TP.SupplierID
							WHERE 
								PD.BranchID = ", pBranchID ,"
								AND CAST(TP.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TP.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere, "
							GROUP BY
								TP.PurchaseID
							UNION ALL
		                    SELECT
								1
							FROM
								transaction_purchasereturn TPR
								JOIN transaction_purchasereturndetails PRD
									ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
								JOIN master_supplier MS
									ON MS.SupplierID = TPR.SupplierID
							WHERE 
								PRD.BranchID = ", pBranchID ,"
								AND CAST(TPR.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TPR.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere2, "
		                    GROUP BY
								TPR.PurchaseReturnID
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TP.PurchaseID,
                        'Pembelian' TransactionType,
						TP.PurchaseNumber,
						DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
						MS.SupplierName,
						SUM(PD.Quantity * PD.BuyPrice) Total
					FROM
						transaction_purchase TP
						JOIN transaction_purchasedetails PD
							ON TP.PurchaseID = PD.PurchaseID
						JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
					WHERE 
						PD.BranchID = ", pBranchID ,"
						AND CAST(TP.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TP.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TP.PurchaseID,
						TP.PurchaseNumber,
						TP.TransactionDate,
						MS.SupplierName
					UNION ALL
                    SELECT
						TPR.PurchaseReturnID,
                        'Retur' TransactionType,
                        TPR.PurchaseReturnNumber,
                        DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
						MS.SupplierName,
						SUM(PRD.Quantity * PRD.BuyPrice) Total
					FROM
						transaction_purchasereturn TPR
						JOIN transaction_purchasereturndetails PRD
							ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
						JOIN master_supplier MS
							ON MS.SupplierID = TPR.SupplierID
					WHERE 
						PRD.BranchID = ", pBranchID ,"
						AND CAST(TPR.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TPR.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere2, "
					GROUP BY
						TPR.PurchaseReturnID,
                        TPR.PurchaseReturnNumber,
                        TPR.TransactionDate,
                        MS.SupplierName
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
                    
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseDetailsReport (
	pPurchaseID			BIGINT,
	pBranchID			INT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	IF(pTransactionType = 'Pembelian')
	THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        PD.Quantity,
	        PD.BuyPrice,
			(PD.Quantity * PD.BuyPrice) SubTotal
		FROM
			transaction_purchasedetails PD
	        JOIN master_item MI
				ON MI.ItemID = PD.ItemID
		WHERE
			PD.PurchaseID = pPurchaseID
            AND PD.BranchID = pBranchID
		ORDER BY
			PD.PurchaseDetailsID;
	ELSE
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        PRD.Quantity,
	        PRD.BuyPrice,
            (PRD.Quantity * PRD.BuyPrice) SubTotal
		FROM
			transaction_purchasereturndetails PRD
	        JOIN master_item MI
				ON MI.ItemID = PRD.ItemID
		WHERE
			PRD.PurchaseReturnID = pPurchaseID
            AND PRD.BranchID = pBranchID
		ORDER BY
			PRD.PurchaseReturnDetailsID;
            
	END IF;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleReport;

DELIMITER $$
CREATE PROCEDURE spSelSaleReport (
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
	pWhere 			TEXT,
    pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								1
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
							WHERE 
								SD.BranchID = ", pBranchID ,"
								AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere, "
							GROUP BY
								TS.SaleID
							UNION ALL
		                    SELECT
								1
							FROM
								transaction_salereturn TSR
								JOIN transaction_sale TS
									ON TS.SaleID = TSR.SaleID
		                        JOIN transaction_salereturndetails SRD
									ON SRD.SaleReturnID = TSR.SaleReturnID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
							WHERE 
								SRD.BranchID = ", pBranchID ,"
								AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere2, "
		                    GROUP BY
								TSR.SaleReturnID
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
						'Penjualan' TransactionType,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        MC.CustomerName,
                        SUM(SD.Quantity * SD.SalePrice) - SD.Discount Total
					FROM
						transaction_sale TS
                        JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE 
						SD.BranchID = ", pBranchID ,"
						AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TS.SaleID,
                        TS.SaleNumber,
                        TS.TransactionDate,
                        MC.CustomerName
                    UNION ALL
                    SELECT
						TSR.SaleReturnID,
						'Retur' TransactionType,
                        CONCAT('R', TS.SaleNumber),
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        MC.CustomerName,
                        SUM(SRD.Quantity * SRD.SalePrice) Total
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN transaction_salereturndetails SRD
							ON SRD.SaleReturnID = TSR.SaleReturnID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE 
						SRD.BranchID = ", pBranchID ,"
						AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere2, "
                    GROUP BY
						TSR.SaleReturnID,
                        TS.SaleNumber,
                        TSR.TransactionDate,
                        MC.CustomerName
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select return purchase transaction
Created Date: 22 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseReturn;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseReturn (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseReturn', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_purchasereturn TPR
                        JOIN master_supplier MS
							ON MS.SupplierID = TPR.SupplierID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TPR.PurchaseReturnID,
						TPR.PurchaseReturnNumber,
                        DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TransactionDate PlainTransactionDate,
                        MS.SupplierID,
                        MS.SupplierName,
						IFNULL(TPRD.Total, 0) Total
					FROM
						transaction_purchasereturn TPR
                        JOIN master_supplier MS
							ON MS.SupplierID = TPR.SupplierID
						LEFT JOIN
                        (
							SELECT
								TPR.PurchaseReturnID,
                                SUM(TPRD.Quantity * TPRD.BuyPrice) Total
							FROM
								transaction_purchasereturn TPR
                                JOIN master_supplier MS
									ON MS.SupplierID = TPR.SupplierID
                                LEFT JOIN transaction_purchasereturndetails TPRD
									ON TPRD.PurchaseReturnID = TPR.PurchaseReturnID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TPRD.PurchaseReturnID
                        )TPRD
							ON TPR.PurchaseReturnID = TPRD.PurchaseReturnID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelSaleDetailsReport (
	pSaleID				BIGINT,
	pBranchID			INT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	IF(pTransactionType = 'Penjualan')
	THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        SD.Quantity,
	        SD.SalePrice,
			SD.Discount,
			((SD.Quantity * SD.SalePrice) - SD.Discount) SubTotal
		FROM
			transaction_saledetails SD
	        JOIN master_item MI
				ON MI.ItemID = SD.ItemID
		WHERE
			SD.SaleID = pSaleID
            AND SD.BranchID = pBranchID
		ORDER BY
			SD.SaleDetailsID;
	ELSE
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        SRD.Quantity,
	        SRD.SalePrice,
            0 Discount,
			(SRD.Quantity * SRD.SalePrice) SubTotal
		FROM
			transaction_salereturndetails SRD
	        JOIN master_item MI
				ON MI.ItemID = SRD.ItemID
		WHERE
			SRD.SaleReturnID = pSaleID
            AND SRD.BranchID = pBranchID
		ORDER BY
			SRD.SaleReturnDetailsID;
            
	END IF;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelStockDetailsReport (
	pItemCode		VARCHAR(100),
	pBranchID 		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelStockDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		'Stok Awal' TransactionType,
		DATE_FORMAT(pFromDate, '%d-%m-%Y') TransactionDate,
		pFromDate DateNoFormat,
		'' CustomerName,
		(IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0)) Quantity
	FROM
		master_item MI
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(TPD.Quantity) Quantity
			FROM
				transaction_purchase TP
				JOIN transaction_purchasedetails TPD
					ON TP.PurchaseID = TPD.PurchaseID
				JOIN master_item MI
					ON TPD.ItemID = MI.ItemID
			WHERE
				TPD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(TP.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)TP
			ON TP.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SRD.SaleReturnID = SR.SaleReturnID
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
			WHERE
				SRD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(SR.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)SR
			ON SR.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_sale TS
				JOIN transaction_saledetails SD
					ON TS.SaleID = SD.SaleID
				JOIN master_item MI
					ON SD.ItemID = MI.ItemID
			WHERE
				SD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(TS.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)S
			ON S.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(PRD.Quantity) Quantity
			FROM
				transaction_purchasereturn TPR
				JOIN transaction_purchasereturndetails PRD
					ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
				JOIN master_item MI
					ON MI.ItemID = PRD.ItemID
			WHERE
				PRD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(TPR.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)PR
			ON MI.ItemID = PR.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SMD.Quantity) Quantity
			FROM
				transaction_stockmutation SM
				JOIN transaction_stockmutationdetails SMD
					ON SM.StockMutationID = SMD.StockMutationID
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
			WHERE
				SMD.DestinationID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(SM.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SAD.AdjustedQuantity - SAD.Quantity) Quantity
			FROM
				transaction_stockadjust SA
				JOIN transaction_stockadjustdetails SAD
					ON SA.StockAdjustID = SAD.StockAdjustID
				JOIN master_item MI
					ON MI.ItemID = SAD.ItemID
			WHERE
				SAD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(SA.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
	WHERE
		TRIM(MI.ItemCode) = TRIM(pItemCode)
	UNION ALL
	SELECT
		'Pembelian',
		DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
		TP.TransactionDate DateNoFormat,
		MS.SupplierName,
		TPD.Quantity
	FROM
		transaction_purchase TP
		JOIN transaction_purchasedetails TPD
			ON TP.PurchaseID = TPD.PurchaseID
		JOIN master_item MI
			ON TPD.ItemID = MI.ItemID
		JOIN master_supplier MS
			ON MS.SupplierID = TP.SupplierID
	WHERE
		TPD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(TP.TransactionDate AS DATE) >= pFromDate
		AND CAST(TP.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Retur Penjualan',
		DATE_FORMAT(SR.TransactionDate, '%d-%m-%Y') TransactionDate,
		SR.TransactionDate DateNoFormat,
		MC.CustomerName,
		SRD.Quantity
	FROM
		transaction_salereturn SR
		JOIN transaction_sale TS
			ON TS.SaleID = SR.SaleID
		JOIN transaction_salereturndetails SRD
			ON SRD.SaleReturnID = SR.SaleReturnID
		JOIN master_item MI
			ON SRD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
	WHERE
		SRD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(SR.TransactionDate AS DATE) >= pFromDate
		AND CAST(SR.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Penjualan',
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
		TS.TransactionDate DateNoFormat,
		MC.CustomerName,
		-SD.Quantity
	FROM
		transaction_sale TS
		JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_item MI
			ON SD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
	WHERE
		SD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Retur Pembelian',
		DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
		TPR.TransactionDate DateNoFormat,
		MS.SupplierName,
		-PRD.Quantity
	FROM
		transaction_purchasereturn TPR
		JOIN transaction_purchasereturndetails PRD
			ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
		JOIN master_item MI
			ON MI.ItemID = PRD.ItemID
		JOIN master_supplier MS
			ON MS.SupplierID = TPR.SupplierID
	WHERE
		PRD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(TPR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TPR.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Mutasi Stok',
		DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') TransactionDate,
		SM.TransactionDate DateNoFormat,
		'',
		SMD.Quantity
	FROM
		transaction_stockmutation SM
		JOIN transaction_stockmutationdetails SMD
			ON SM.StockMutationID = SMD.StockMutationID
		JOIN master_item MI
			ON MI.ItemID = SMD.ItemID
	WHERE
		SMD.DestinationID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(SM.TransactionDate AS DATE) >= pFromDate
		AND CAST(SM.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Adjust Stok',
		DATE_FORMAT(SA.TransactionDate, '%d-%m-%Y') TransactionDate,
		SA.TransactionDate DateNoFormat,
		'',
		(SAD.AdjustedQuantity - SAD.Quantity)
	FROM
		transaction_stockadjust SA
		JOIN transaction_stockadjustdetails SAD
			ON SA.StockAdjustID = SAD.StockAdjustID
		JOIN master_item MI
			ON MI.ItemID = SAD.ItemID
	WHERE
		SAD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(SA.TransactionDate AS DATE) >= pFromDate
		AND CAST(SA.TransactionDate AS DATE) <= pToDate
	ORDER BY
		DateNoFormat;

        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockReport;

DELIMITER $$
CREATE PROCEDURE spSelStockReport (
	pCategoryID		BIGINT,
	pBranchID 		INT,
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelStockReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_item MI
						CROSS JOIN master_branch MB
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								TPD.BranchID,
								SUM(TPD.Quantity) Quantity
							FROM
								transaction_purchasedetails TPD
								JOIN master_item MI
									ON TPD.ItemID = MI.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ",pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN TPD.BranchID
										ELSE ",pBranchID,"
									END = TPD.BranchID
							GROUP BY
								MI.ItemID,
								TPD.BranchID
						)TP
							ON TP.ItemID = MI.ItemID
							AND MB.BranchID = TP.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SRD.BranchID,
								SUM(SRD.Quantity) Quantity
							FROM
								transaction_salereturndetails SRD
								JOIN master_item MI
									ON SRD.ItemID = MI.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SRD.BranchID
										ELSE ",pBranchID,"
									END = SRD.BranchID
							GROUP BY
								MI.ItemID,
								SRD.BranchID
						)SR
							ON SR.ItemID = MI.ItemID
							AND SR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SD.BranchID,
								SUM(SD.Quantity) Quantity
							FROM
								transaction_saledetails SD
								JOIN master_item MI
									ON SD.ItemID = MI.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SD.BranchID
										ELSE ",pBranchID,"
									END = SD.BranchID
							GROUP BY
								MI.ItemID,
								SD.BranchID
						)S
							ON S.ItemID = MI.ItemID
							AND S.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								PRD.BranchID,
								SUM(PRD.Quantity) Quantity
							FROM
								transaction_purchasereturndetails PRD
								JOIN master_item MI
									ON MI.ItemID = PRD.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN PRD.BranchID
										ELSE ",pBranchID,"
									END = PRD.BranchID
							GROUP BY
								MI.ItemID,
								PRD.BranchID
						)PR
							ON MI.ItemID = PR.ItemID
							AND PR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SMD.DestinationID,
								SUM(SMD.Quantity) Quantity
							FROM
								transaction_stockmutationdetails SMD
								JOIN master_item MI
									ON MI.ItemID = SMD.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SMD.DestinationID
										ELSE ",pBranchID,"
									END = SMD.DestinationID
							GROUP BY
								MI.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
							AND SM.DestinationID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SAD.BranchID,
								SUM(SAD.AdjustedQuantity - SAD.Quantity) Quantity
							FROM
								transaction_stockadjustdetails SAD
								JOIN master_item MI
									ON MI.ItemID = SAD.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SAD.BranchID
										ELSE ",pBranchID,"
									END = SAD.BranchID
							GROUP BY
								MI.ItemID,
								SAD.BranchID
						)SA
							ON MI.ItemID = SA.ItemID
							AND SA.BranchID = MB.BranchID
					WHERE
						CASE
							WHEN ", pCategoryID," = 0
							THEN MC.CategoryID
							ELSE ", pCategoryID,"
						END = MC.CategoryID
						AND CASE
								WHEN ",pBranchID," = 0
								THEN MB.BranchID
								ELSE ",pBranchID,"
							END = MB.BranchID AND ", pWhere);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MI.ItemCode,
						MI.ItemName,
						MC.CategoryName,
						MB.BranchName,
						(IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0)) Stock
					FROM
						master_item MI
						CROSS JOIN master_branch MB
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								TPD.BranchID,
								SUM(TPD.Quantity) Quantity
							FROM
								transaction_purchasedetails TPD
								JOIN master_item MI
									ON TPD.ItemID = MI.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ",pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN TPD.BranchID
										ELSE ",pBranchID,"
									END = TPD.BranchID
							GROUP BY
								MI.ItemID,
								TPD.BranchID
						)TP
							ON TP.ItemID = MI.ItemID
							AND MB.BranchID = TP.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SRD.BranchID,
								SUM(SRD.Quantity) Quantity
							FROM
								transaction_salereturndetails SRD
								JOIN master_item MI
									ON SRD.ItemID = MI.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SRD.BranchID
										ELSE ",pBranchID,"
									END = SRD.BranchID
							GROUP BY
								MI.ItemID,
								SRD.BranchID
						)SR
							ON SR.ItemID = MI.ItemID
							AND SR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SD.BranchID,
								SUM(SD.Quantity) Quantity
							FROM
								transaction_saledetails SD
								JOIN master_item MI
									ON SD.ItemID = MI.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SD.BranchID
										ELSE ",pBranchID,"
									END = SD.BranchID
							GROUP BY
								MI.ItemID,
								SD.BranchID
						)S
							ON S.ItemID = MI.ItemID
							AND S.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								PRD.BranchID,
								SUM(PRD.Quantity) Quantity
							FROM
								transaction_purchasereturndetails PRD
								JOIN master_item MI
									ON MI.ItemID = PRD.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN PRD.BranchID
										ELSE ",pBranchID,"
									END = PRD.BranchID
							GROUP BY
								MI.ItemID,
								PRD.BranchID
						)PR
							ON MI.ItemID = PR.ItemID
							AND PR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SMD.DestinationID,
								SUM(SMD.Quantity) Quantity
							FROM
								transaction_stockmutationdetails SMD
								JOIN master_item MI
									ON MI.ItemID = SMD.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SMD.DestinationID
										ELSE ",pBranchID,"
									END = SMD.DestinationID
							GROUP BY
								MI.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
							AND SM.DestinationID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SAD.BranchID,
								SUM(SAD.AdjustedQuantity - SAD.Quantity) Quantity
							FROM
								transaction_stockadjustdetails SAD
								JOIN master_item MI
									ON MI.ItemID = SAD.ItemID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SAD.BranchID
										ELSE ",pBranchID,"
									END = SAD.BranchID
							GROUP BY
								MI.ItemID,
								SAD.BranchID
						)SA
							ON MI.ItemID = SA.ItemID
							AND SA.BranchID = MB.BranchID
					WHERE
						CASE
							WHEN ", pCategoryID," = 0
							THEN MC.CategoryID
							ELSE ", pCategoryID,"
						END = MC.CategoryID
						AND CASE
								WHEN ",pBranchID," = 0
								THEN MB.BranchID
								ELSE ",pBranchID,"
							END = MB.BranchID AND ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
                    
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item details by itemCode
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemQtyDetails;

DELIMITER $$
CREATE PROCEDURE spSelItemQtyDetails (
	pItemCode		VARCHAR(100),
	pBranchID		INT,
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
		CALL spInsEventLog(@full_error, 'spSelItemQtyDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MI.ItemID,
		MI.ItemCode,
		MI.ItemName,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
		(IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0)) Quantity
	FROM
		master_item MI
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(TPD.Quantity) Quantity
			FROM
				transaction_purchasedetails TPD
				JOIN master_item MI
					ON TPD.ItemID = MI.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND TPD.BranchID = pBranchID
			GROUP BY
				MI.ItemID
		)TP
			ON TP.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_salereturndetails SRD
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND SRD.BranchID = pBranchID
			GROUP BY
				MI.ItemID
		)SR
			ON SR.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_saledetails SD
				JOIN master_item MI
					ON SD.ItemID = MI.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND SD.BranchID = pBranchID
			GROUP BY
				MI.ItemID
		)S
			ON S.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(PRD.Quantity) Quantity
			FROM
				transaction_purchasereturndetails PRD
				JOIN master_item MI
					ON MI.ItemID = PRD.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND PRD.BranchID = pBranchID
			GROUP BY
				MI.ItemID
		)PR
			ON MI.ItemID = PR.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SMD.Quantity) Quantity
			FROM
				transaction_stockmutationdetails SMD
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND SMD.SourceID = pBranchID
			GROUP BY
				MI.ItemID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SAD.AdjustedQuantity - SAD.Quantity) Quantity
			FROM
				transaction_stockadjustdetails SAD
				JOIN master_item MI
					ON MI.ItemID = SAD.ItemID
			WHERE
				TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND SAD.BranchID = pBranchID
			GROUP BY
				MI.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
	WHERE
		TRIM(MI.ItemCode) = TRIM(pItemCode);
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale return details
Created Date: 24 February 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleReturnDetails;

DELIMITER $$
CREATE PROCEDURE spSelSaleReturnDetails (
	pSaleReturnID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleReturnDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TSR.SaleID,
		TSR.SaleReturnID,
		TSRD.SaleReturnDetailsID,
		TSRD.SaleDetailsID,
        TSRD.ItemID,
        TSRD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        TSRD.Quantity,
        TSRD.BuyPrice,
        TSRD.SalePrice,
        (IFNULL(TS.Quantity, 0) - IFNULL(SR.Quantity, 0) + IFNULL(TSRD.Quantity, 0)) Maksimum
	FROM
		transaction_salereturn TSR
		JOIN transaction_salereturndetails TSRD
			ON TSRD.SaleReturnID = TSR.SaleReturnID
		JOIN master_item MI
			ON MI.ItemID = TSRD.ItemID
        LEFT JOIN
		(
			SELECT
				TS.SaleID,
				SD.ItemID,
				SD.BranchID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_sale TS
				JOIN transaction_saledetails SD
					ON TS.SaleID = SD.SaleID
			GROUP BY
				TS.SaleID,
				SD.ItemID,
				SD.BranchID
		)TS
			ON TSR.SaleID = TS.SaleID
			AND MI.ItemID = TS.ItemID
			AND TS.BranchID = TSRD.BranchID

		LEFT JOIN
		(
			SELECT
				SR.SaleID,
				SRD.ItemID,
				SRD.BranchID,
				SRD.SaleDetailsID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SR.SaleReturnID = SRD.SaleReturnID
			GROUP BY
				SR.SaleID,
				SRD.ItemID,
				SRD.BranchID,
				SRD.SaleDetailsID
		)SR
			ON TSR.SaleID = SR.SaleID
			AND MI.ItemID = SR.ItemID
			AND SR.BranchID = TSRD.BranchID
			AND TSRD.SaleDetailsID = SR.SaleDetailsID

	WHERE
		TSR.SaleReturnID = pSaleReturnID
	ORDER BY
		TSRD.SaleReturnDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleNumber
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleDetailsByNumber;

DELIMITER $$
CREATE PROCEDURE spSelSaleDetailsByNumber (
	pSaleNumber		VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelSaleDetailsByNumber', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TS.SaleID,
		SD.SaleDetailsID,
        SD.ItemID,
        SD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        (SD.Quantity - IFNULL(TSR.Quantity, 0)) Quantity,
        SD.BuyPrice,
        SD.SalePrice,
        MC.CustomerName
	FROM
		transaction_sale TS
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
        JOIN master_branch MB
			ON MB.BranchID = SD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN
		(
			SELECT
				SR.SaleID,
				SRD.ItemID,
				SRD.BranchID,
				SRD.SaleDetailsID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SR.SaleReturnID = SRD.SaleReturnID
			GROUP BY
				SR.SaleID,
				SRD.ItemID,
				SRD.BranchID,
				SRD.SaleDetailsID
		)TSR
			ON TSR.SaleID = TS.SaleID
			AND MI.ItemID = TSR.ItemID
			AND TSR.BranchID = SD.BranchID
			AND TSR.SaleDetailsID = SD.SaleDetailsID
	WHERE
		TRIM(TS.SaleNumber) = TRIM(pSaleNumber)
	ORDER BY
		SD.SaleDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert sale return
Created Date: 23 February 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsSaleReturn;

DELIMITER $$
CREATE PROCEDURE spInsSaleReturn (
	pID 				BIGINT, 
	pSaleID 			BIGINT,
	pTransactionDate 	DATETIME,
	pSaleReturnData 	TEXT,
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
		CALL spInsEventLog(@full_error, 'spInsSaleReturn', pCurrentUser);
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
			INSERT INTO transaction_salereturn
			(
				SaleID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES (
				pSaleID,
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
				transaction_salereturn
			SET
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				SaleReturnID = pID;
	
		END IF;
	
SET State = 4;

		DELETE 
		FROM 
			transaction_salereturndetails
		WHERE
			SaleReturnID = pID;
					
SET State = 5;
		IF(pSaleReturnData <> "" ) THEN
			SET @query = CONCAT("INSERT INTO transaction_salereturndetails
								(
									SaleReturnID,
									ItemID,
									BranchID,
									Quantity,
									BuyPrice,
									SalePrice,
									SaleDetailsID,
									CreatedDate,
									CreatedBy
								)
								VALUES", REPLACE(REPLACE(pSaleReturnData, ', UserLogin)', CONCAT(', "', pCurrentUser, '")')), '(0,', CONCAT('(', pID, ','))
								);
								
			PREPARE stmt FROM @query;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
			
		END IF;

SET State = 6;

		IF(pIsEdit = 0) THEN
			SELECT
				pID AS 'ID',
				'Retur Berhasil Ditambahkan' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
		ELSE
	
SET State = 7;

			SELECT
				pID AS 'ID',
				'Retur Berhasil Diubah' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
		END IF;
    COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking details by BookingID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBookingDetails;

DELIMITER $$
CREATE PROCEDURE spSelBookingDetails (
	pBookingID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelBookingDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		BD.BookingDetailsID,
        BD.ItemID,
        BD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        BD.Quantity,
        BD.BuyPrice,
        BD.BookingPrice,
		BD.Discount,
		MI.RetailPrice,
        MI.Price1,
        MI.Qty1,
        MI.Price2,
        MI.Qty2,
		MI.Weight
	FROM
		transaction_bookingdetails SD
        JOIN master_branch MB
			ON MB.BranchID = BD.BranchID
		JOIN master_item MI
			ON MI.ItemID = BD.ItemID
	WHERE
		BD.BookingID = pBookingID
	ORDER BY
		BD.BookingDetailsID;
        
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdSalePayment;

DELIMITER $$
CREATE PROCEDURE spUpdSalePayment (
	pID 			BIGINT, 
	pPayment 		DOUBLE,
	pCurrentUser	VARCHAR(255)
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdSalePayment', pCurrentUser);
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
			UPDATE
				transaction_sale
			SET
				Payment = pPayment,
				ModifiedBy = pCurrentUser
			WHERE
				SaleID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Pembayaran berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSale;

DELIMITER $$
CREATE PROCEDURE spSelSale (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelSale', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_sale TS
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TS.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TSD.Total, 0) Total,
						IFNULL(TSD.Weight, 0) Weight,
						TS.RetailFlag,
						TS.Payment
					FROM
						transaction_sale TS
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN
                        (
							SELECT
								TS.SaleID,
                                SUM(TSD.Quantity * TSD.SalePrice - TSD.Discount) Total,
								SUM(TSD.Quantity * MI.Weight) Weight
							FROM
								transaction_sale TS
                                JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
                                LEFT JOIN transaction_saledetails TSD
									ON TS.SaleID = TSD.SaleID
								LEFT JOIN master_item MI
									ON MI.ItemID = TSD.ItemID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TS.SaleID
                        )TSD
							ON TSD.SaleID = TS.SaleID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsBooking;

DELIMITER $$
CREATE PROCEDURE spInsBooking (
	pID 				BIGINT,
	pBookingNumber		VARCHAR(100),
	pRetailFlag			BIT,
    pCustomerID			BIGINT,
	pTransactionDate 	DATETIME,
	pBookingDetailsID	BIGINT,
    pBranchID			INT,
    pItemID				BIGINT,
	pQuantity			DOUBLE,
    pBuyPrice			DOUBLE,
    pBookingPrice		DOUBLE,
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
				CONCAT(RIGHT(CONCAT('00', pUserID), 2), DATE_FORMAT(NOW(), '%Y%m'), RIGHT(CONCAT('00000', (IFNULL(MAX(CAST(RIGHT(BookingNumber, 5) AS UNSIGNED)), 0) + 1)), 5))
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
DROP PROCEDURE IF EXISTS spInsSale;

DELIMITER $$
CREATE PROCEDURE spInsSale (
	pID 				BIGINT,
	pSaleNumber			VARCHAR(100),
	pRetailFlag			BIT,
    pCustomerID			BIGINT,
	pTransactionDate 	DATETIME,
	pSaleDetailsID		BIGINT,
    pBranchID			INT,
    pItemID				BIGINT,
	pQuantity			DOUBLE,
    pBuyPrice			DOUBLE,
    pSalePrice			DOUBLE,
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
		CALL spInsEventLog(@full_error, 'spInsSale', pCurrentUser);
		SELECT
			pID AS 'ID',
            pSaleDetailsID AS 'SaleDetailsID',
			pSaleNumber AS 'SaleNumber',
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
				CONCAT(RIGHT(CONCAT('00', pUserID), 2), DATE_FORMAT(NOW(), '%Y%m'), RIGHT(CONCAT('00000', (IFNULL(MAX(CAST(RIGHT(SaleNumber, 5) AS UNSIGNED)), 0) + 1)), 5))
			FROM
				transaction_sale TS
			WHERE
				MONTH(TS.TransactionDate) = MONTH(NOW())
				AND YEAR(TS.TransactionDate) = YEAR(NOW())
			INTO 
				pSaleNumber;
				
SET State = 2;
			INSERT INTO transaction_sale
			(
				SaleNumber,
				RetailFlag,
				CustomerID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
				pSaleNumber,
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
				transaction_sale
			SET
				customerID = pCustomerID,
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				SaleID = pID;
				
		END IF;
		
SET State = 5;
		
		IF(pSaleDetailsID = 0) THEN
			INSERT INTO transaction_saledetails
			(
				SaleID,
				ItemID,
				BranchID,
				Quantity,
				BuyPrice,
				SalePrice,
				Discount,
				CreatedDate,
				CreatedBy
			)
			VALUES
			(
				pID,
				pItemID,
				pBranchID,
				pQuantity,
				pBuyPrice,
				pSalePrice,
				pDiscount,
				NOW(),
				pCurrentUser
			);
			
SET State = 6;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pSaleDetailsID;
		
		ELSE
				
SET State = 7;
			
			UPDATE 
				transaction_saledetails
			SET
				ItemID = pItemID,
				BranchID = pBranchID,
				Quantity = pQuantity,
				BuyPrice = pBuyPrice,
				SalePrice = pSalePrice,
				Discount = pDiscount,
				ModifiedBy = pCurrentUser
			WHERE
				SaleDetailsID = pSaleDetailsID;
			
		END IF;
		
SET State = 8;

		SELECT
			pID AS 'ID',
			pSaleDetailsID AS 'SaleDetailsID',
			pSaleNumber AS 'SaleNumber',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
                
	COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdBookingDetailsBranch;

DELIMITER $$
CREATE PROCEDURE spUpdBookingDetailsBranch (
	pID 			BIGINT, 
	pBranchID 		INT,
	pCurrentUser	VARCHAR(255)
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdBookingDetailsBranch', pCurrentUser);
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
			UPDATE
				transaction_bookingdetails
			SET
				BranchID = pBranchID,
				ModifiedBy = pCurrentUser
			WHERE
				BookingDetailsID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Cabang berhasil diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete booking details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelBookingDetails;

DELIMITER $$
CREATE PROCEDURE spDelBookingDetails (
	pBookingDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelBookingDetails', pCurrentUser);
        SELECT
			pBookingDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_bookingdetails
		WHERE
			BookingDetailsID = pBookingDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pBookingDetailsID AS 'ID',
			'Pemesanan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete booking
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelBooking;

DELIMITER $$
CREATE PROCEDURE spDelBooking (
	pBookingID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelBooking', pCurrentUser);
        SELECT
			pBookingID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_booking
		WHERE
			BookingID = pBookingID;

    COMMIT;
    
SET State = 2;

		SELECT
			pBookingID AS 'ID',
			'Pemesanan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBooking;

DELIMITER $$
CREATE PROCEDURE spSelBooking (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelBooking', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_booking TB
                        JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TB.BookingID,
                        TB.BookingNumber,
                        DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TB.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TBD.Total, 0) Total,
						IFNULL(TBD.Weight, 0) Weight,
						TB.RetailFlag
					FROM
						transaction_booking TB
                        JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
						LEFT JOIN
                        (
							SELECT
								TB.BookingID,
                                SUM(TBD.Quantity * TBD.BookingPrice - TBD.Discount) Total,
								SUM(TBD.Quantity * MI.Weight) Weight
							FROM
								transaction_booking TB
                                JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
                                LEFT JOIN transaction_bookingdetails TBD
									ON TB.BookingID = TBD.BookingID
								LEFT JOIN master_item MI
									ON MI.ItemID = TBD.ItemID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TB.BookingID
                        )TBD
							ON TBD.BookingID = TB.BookingID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockAdjustDetails;

DELIMITER $$
CREATE PROCEDURE spSelStockAdjustDetails (
	pStockAdjustID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelStockAdjustDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		SA.StockAdjustID,
		SAD.StockAdjustDetailsID,
		MI.ItemID,
		SAD.BranchID,
		CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
		MI.ItemCode,
        MI.ItemName,
        SAD.Quantity,
        SAD.AdjustedQuantity
	FROM
		transaction_stockadjust SA
		JOIN transaction_stockadjustdetails SAD
			ON SA.StockAdjustID = SAD.StockAdjustID
        JOIN master_branch MB
			ON MB.BranchID = SAD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SAD.ItemID
	WHERE
		SA.StockAdjustID = pStockAdjustID
	ORDER BY
		SAD.StockAdjustDetailsID;
        
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsStockAdjust;

DELIMITER $$
CREATE PROCEDURE spInsStockAdjust (
	pID 						BIGINT,
	pBranchID					INT,
	pTransactionDate 			DATETIME,
	pStockAdjustDetailsID		BIGINT,
    pItemID						BIGINT,
	pQuantity					DOUBLE,
	pAdjustedQuantity			DOUBLE,
    pUserID						BIGINT,
    pCurrentUser				VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spInsStockAdjust', pCurrentUser);
		SELECT
			pID AS 'ID',
            pStockAdjustDetailsID AS 'StockAdjustDetailsID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		IF(pID = 0)	THEN /*Tambah baru*/
			INSERT INTO transaction_stockadjust
			(
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
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
				transaction_stockadjust
			SET
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				StockAdjustID = pID;
				
		END IF;
		
SET State = 5;
		
		IF(pStockAdjustDetailsID = 0) THEN
			INSERT INTO transaction_stockadjustdetails
			(
				StockAdjustID,
				BranchID,
				ItemID,
				Quantity,
				AdjustedQuantity,
				CreatedDate,
				CreatedBy
			)
			VALUES
			(
				pID,
				pBranchID,
				pItemID,
				pQuantity,
				pAdjustedQuantity,
				NOW(),
				pCurrentUser
			);
			
SET State = 6;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pStockAdjustDetailsID;
		
		ELSE
				
SET State = 7;
			
			UPDATE 
				transaction_stockadjustdetails
			SET
				ItemID = pItemID,
				BranchID = pBranchID,
				Quantity = pQuantity,
				AdjustedQuantity = pAdjustedQuantity,
				ModifiedBy = pCurrentUser
			WHERE
				StockAdjustDetailsID = pStockAdjustDetailsID;
			
		END IF;
		
SET State = 8;

		SELECT
			pID AS 'ID',
			pStockAdjustDetailsID AS 'StockAdjustDetailsID',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
                
	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelStockAdjustDetails;

DELIMITER $$
CREATE PROCEDURE spDelStockAdjustDetails (
	pStockAdjustDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelStockAdjustDetails', pCurrentUser);
        SELECT
			pStockAdjustDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_stockadjustdetails
		WHERE
			StockAdjustDetailsID = pStockAdjustDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pStockAdjustDetailsID AS 'ID',
			'Adjust Stok berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelStockAdjust;

DELIMITER $$
CREATE PROCEDURE spDelStockAdjust (
	pStockAdjustDetailsID		BIGINT,
	pCurrentUser				VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelStockAdjust', pCurrentUser);
        SELECT
			pStockAdjustDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_stockadjustdetails
		WHERE
			StockAdjustDetailsID = pStockAdjustDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pStockAdjustDetailsID AS 'ID',
			'Adjust Stok berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockAdjust;

DELIMITER $$
CREATE PROCEDURE spSelStockAdjust (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelStockAdjust', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_stockadjust SA
						JOIN transaction_stockadjustdetails SAD
							ON SA.StockAdjustID = SAD.StockAdjustID
                        JOIN master_branch MB
							ON MB.BranchID = SAD.BranchID
						JOIN master_item MI
							ON MI.ItemID = SAD.ItemID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;
		
SET @query = CONCAT("SELECT
						SA.StockAdjustID,
						SAD.StockAdjustDetailsID,
                        DATE_FORMAT(SA.TransactionDate, '%d-%m-%Y') TransactionDate,
                        SA.TransactionDate PlainTransactionDate,
                        MB.BranchID,
                        CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
                       	MI.ItemID,
                        MI.ItemCode,
                        MI.ItemName,
                        SAD.Quantity,
                        SAD.AdjustedQuantity
					FROM
						transaction_stockadjust SA
						JOIN transaction_stockadjustdetails SAD
							ON SA.StockAdjustID = SAD.StockAdjustID
                        JOIN master_branch MB
							ON MB.BranchID = SAD.BranchID
						JOIN master_item MI
							ON MI.ItemID = SAD.ItemID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockMutation;

DELIMITER $$
CREATE PROCEDURE spSelStockMutation (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelStockMutation', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_stockmutation SM
						JOIN transaction_stockmutationdetails SMD
							ON SM.StockMutationID = SMD.StockMutationID
                        JOIN master_branch SB
							ON SB.BranchID = SMD.SourceID
						JOIN master_branch DB
							ON DB.BranchID = SMD.DestinationID
						JOIN master_item MI
							ON MI.ItemID = SMD.ItemID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						SM.StockMutationID,
						SMD.StockMutationDetailsID,
                        DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') TransactionDate,
                        SM.TransactionDate PlainTransactionDate,
                        CONCAT(SB.BranchCode, ' - ', SB.BranchName) SourceBranchName,
                        CONCAT(DB.BranchCode, ' - ', DB.BranchName) DestinationBranchName,
                        MI.ItemID,
                        MI.ItemCode,
                        MI.ItemName,
                        SMD.Quantity,
                        SMD.SourceID,
                        SMD.DestinationID
					FROM
						transaction_stockmutation SM
						JOIN transaction_stockmutationdetails SMD
							ON SM.StockMutationID = SMD.StockMutationID
                        JOIN master_branch SB
							ON SB.BranchID = SMD.SourceID
						JOIN master_branch DB
							ON DB.BranchID = SMD.DestinationID
						JOIN master_item MI
							ON MI.ItemID = SMD.ItemID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockMutationDetails;

DELIMITER $$
CREATE PROCEDURE spSelStockMutationDetails (
	pStockMutationID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelStockMutationDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		SM.StockMutationID,
		SMD.StockMutationDetailsID,
		MI.ItemID,
		SMD.SourceID,
		SMD.DestinationID,
		CONCAT(SB.BranchCode, ' - ', SB.BranchName) SourceBranchName,
		CONCAT(DB.BranchCode, ' - ', DB.BranchName) DestinationBranchName,
        MI.ItemCode,
        MI.ItemName,
        SMD.Quantity
	FROM
		transaction_stockmutation SM
		JOIN transaction_stockmutationdetails SMD
			ON SM.StockMutationID = SMD.StockMutationID
        JOIN master_branch SB
			ON SB.BranchID = SMD.SourceID
		JOIN master_branch DB
			ON DB.BranchID = SMD.DestinationID
		JOIN master_item MI
			ON MI.ItemID = SMD.ItemID
	WHERE
		SM.StockMutationID = pStockMutationID
	ORDER BY
		SMD.StockMutationDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelStockMutation;

DELIMITER $$
CREATE PROCEDURE spDelStockMutation (
	pStockMutationDetailsID		BIGINT,
	pCurrentUser				VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelStockMutation', pCurrentUser);
        SELECT
			pStockMutationDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_stockmutationdetails
		WHERE
			StockMutationDetailsID = pStockMutationDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pStockMutationDetailsID AS 'ID',
			'Mutasi Stok berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelStockMutationDetails;

DELIMITER $$
CREATE PROCEDURE spDelStockMutationDetails (
	pStockMutationDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelStockMutationDetails', pCurrentUser);
        SELECT
			pStockMutationDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_stockmutationdetails
		WHERE
			StockMutationDetailsID = pStockMutationDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pStockMutationDetailsID AS 'ID',
			'Mutasi Stok berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelSaleDetails;

DELIMITER $$
CREATE PROCEDURE spDelSaleDetails (
	pSaleDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelSaleDetails', pCurrentUser);
        SELECT
			pSaleDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_saledetails
		WHERE
			SaleDetailsID = pSaleDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pSaleDetailsID AS 'ID',
			'Penjualan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsStockMutation;

DELIMITER $$
CREATE PROCEDURE spInsStockMutation (
	pID 						BIGINT,
	pSourceID					INT,
	pDestinationID				INT,
	pTransactionDate 			DATETIME,
	pStockMutationDetailsID		BIGINT,
    pItemID						BIGINT,
	pQuantity					DOUBLE,
    pUserID						BIGINT,
    pCurrentUser				VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spInsStockMutation', pCurrentUser);
		SELECT
			pID AS 'ID',
            pStockMutationDetailsID AS 'StockMutationDetailsID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	SET PassValidate = 1;
	
	START TRANSACTION;
	
SET State = 1;

		IF(pID = 0)	THEN /*Tambah baru*/
			INSERT INTO transaction_stockmutation
			(
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
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
				transaction_stockmutation
			SET
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				StockMutationID = pID;
				
		END IF;
		
SET State = 5;
		
		IF(pStockMutationDetailsID = 0) THEN
			INSERT INTO transaction_stockmutationdetails
			(
				StockMutationID,
				SourceID,
				DestinationID,
				ItemID,
				Quantity,
				CreatedDate,
				CreatedBy
			)
			VALUES
			(
				pID,
				pSourceID,
				pDestinationID,
				pItemID,
				pQuantity,
				NOW(),
				pCurrentUser
			);
			
SET State = 6;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pStockMutationDetailsID;
		
		ELSE
				
SET State = 7;
			
			UPDATE 
				transaction_stockmutationdetails
			SET
				ItemID = pItemID,
				SourceID = pSourceID,
				DestinationID = pDestinationID,
				Quantity = pQuantity,
				ModifiedBy = pCurrentUser
			WHERE
				StockMutationDetailsID = pStockMutationDetailsID;
			
		END IF;
		
SET State = 8;

		SELECT
			pID AS 'ID',
			pStockMutationDetailsID AS 'StockMutationDetailsID',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
                
	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleDetails;

DELIMITER $$
CREATE PROCEDURE spSelSaleDetails (
	pSaleID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		SD.SaleDetailsID,
        SD.ItemID,
        SD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        SD.Quantity,
        SD.BuyPrice,
        SD.SalePrice,
		SD.Discount,
		MI.RetailPrice,
        MI.Price1,
        MI.Qty1,
        MI.Price2,
        MI.Qty2,
		MI.Weight
	FROM
		transaction_saledetails SD
        JOIN master_branch MB
			ON MB.BranchID = SD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
	WHERE
		SD.SaleID = pSaleID
	ORDER BY
		SD.SaleDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale return
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelSaleReturn;

DELIMITER $$
CREATE PROCEDURE spDelSaleReturn (
	pSaleReturnID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelSaleReturn', pCurrentUser);
        SELECT
			pSaleReturnID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_salereturn
		WHERE
			SaleReturnID = pSaleReturnID;

    COMMIT;
    
SET State = 2;

		SELECT
			pSaleReturnID AS 'ID',
			'Retur penjualan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleReturn;

DELIMITER $$
CREATE PROCEDURE spSelSaleReturn (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleReturn', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TSR.SaleReturnID,
                        TS.SaleNumber,
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TSR.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TSRD.Total, 0) Total
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN
                        (
							SELECT
								TSR.SaleReturnID,
                                SUM(TSRD.Quantity * TSRD.SalePrice) Total
							FROM
								transaction_sale TS
                                JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
								JOIN transaction_salereturn TSR
									ON TS.SaleID = TSR.SaleID
                                LEFT JOIN transaction_salereturndetails TSRD
									ON TSR.SaleReturnID = TSRD.SaleReturnID
								LEFT JOIN master_item MI
									ON MI.ItemID = TSRD.ItemID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TSR.SaleReturnID
                        )TSRD
							ON TSRD.SaleReturnID = TSR.SaleReturnID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelSale;

DELIMITER $$
CREATE PROCEDURE spDelSale (
	pSaleID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelSale', pCurrentUser);
        SELECT
			pSaleID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_sale
		WHERE
			SaleID = pSaleID;

    COMMIT;
    
SET State = 2;

		SELECT
			pSaleID AS 'ID',
			'Penjualan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item details by itemCode
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemDetails;

DELIMITER $$
CREATE PROCEDURE spSelItemDetails (
	pItemCode		VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelItemDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MI.ItemID,
		MI.ItemCode,
		MI.ItemName,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight
	FROM
		master_item MI
	WHERE
		TRIM(MI.ItemCode) = TRIM(pItemCode);
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select purchase details by PurchaseID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseDetails;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseDetails (
	pPurchaseID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		PD.PurchaseDetailsID,
        PD.ItemID,
        PD.BranchID,
        CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
        MI.ItemCode,
        MI.ItemName,
        PD.Quantity,
        PD.BuyPrice,
        PD.RetailPrice,
        PD.Price1,
        PD.Price2
	FROM
		transaction_purchasedetails PD
        JOIN master_branch MB
			ON MB.BranchID = PD.BranchID
		JOIN master_item MI
			ON MI.ItemID = PD.ItemID
	WHERE
		PD.PurchaseID = pPurchaseID
	ORDER BY
		PD.PurchaseDetailsID;
        
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdSaleDetailsBranch;

DELIMITER $$
CREATE PROCEDURE spUpdSaleDetailsBranch (
	pID 			BIGINT, 
	pBranchID 		INT,
	pCurrentUser	VARCHAR(255)
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdSaleDetailsBranch', pCurrentUser);
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
			UPDATE
				transaction_saledetails
			SET
				BranchID = pBranchID,
				ModifiedBy = pCurrentUser
			WHERE
				SaleDetailsID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Cabang berhasil diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdUserPassword;

DELIMITER $$
CREATE PROCEDURE spUpdUserPassword (
	pID 			BIGINT, 
	pPassword 		VARCHAR(255),
	pCurrentUser	VARCHAR(255)
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdUserPassword', pCurrentUser);
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
			UPDATE
				master_user
			SET
				UserPassword = pPassword,
				ModifiedBy = pCurrentUser
			WHERE
				UserID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Password berhasil diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsPurchase;

DELIMITER $$
CREATE PROCEDURE spInsPurchase (
	pID 				BIGINT,
    pPurchaseNumber		VARCHAR(100),
    pSupplierID			BIGINT,
	pTransactionDate 	DATETIME,
	pPurchaseDetailsID	BIGINT,
    pBranchID			INT,
    pItemID				BIGINT,
	pQuantity			DOUBLE,
    pBuyPrice			DOUBLE,
    pRetailPrice		DOUBLE,
    pPrice1				DOUBLE,
    pPrice2				DOUBLE,
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
		CALL spInsEventLog(@full_error, 'spInsPurchase', pCurrentUser);
		SELECT
			pID AS 'ID',
            pPurchaseDetailsID AS 'PurchaseDetailsID',
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
			transaction_purchase
		WHERE
			TRIM(PurchaseNumber) = TRIM(pPurchaseNumber)
			AND PurchaseID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
                pPurchaseDetailsID AS 'PurchaseDetailsID',
				'No. Invoice sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pID = 0)	THEN /*Tambah baru*/
				INSERT INTO transaction_purchase
				(
                    PurchaseNumber,
                    SupplierID,
					TransactionDate,
					CreatedDate,
					CreatedBy
				)
				VALUES 
                (
					pPurchaseNumber,
					pSupplierID,
					pTransactionDate,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;	               
				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;
                    
			ELSE
SET State = 5;
				UPDATE
					transaction_purchase
				SET
					PurchaseNumber = pPurchaseNumber,
                    SupplierID = pSupplierID,
					TransactionDate = pTransactionDate,
					ModifiedBy = pCurrentUser
				WHERE
					PurchaseID = pID;
                    
			END IF;
            
SET State = 6;
			
			IF(pPurchaseDetailsID = 0) THEN
				INSERT INTO transaction_purchasedetails
                (
					PurchaseID,
                    ItemID,
                    BranchID,
                    Quantity,
                    BuyPrice,
                    RetailPrice,
                    Price1,
                    Price2,
                    CreatedDate,
                    CreatedBy
                )
                VALUES
                (
					pID,
                    pItemID,
                    pBranchID,
                    pQuantity,
                    pBuyPrice,
                    pRetailPrice,
                    pPrice1,
                    pPrice2,
                    NOW(),
                    pCurrentUser
                );
                
SET State = 7;
				
				SELECT
					LAST_INSERT_ID()
				INTO 
					pPurchaseDetailsID;
			
			ELSE
					
SET State = 8;
				
				UPDATE 
					transaction_purchasedetails
				SET
					ItemID = pItemID,
                    BranchID = pBranchID,
                    Quantity = pQuantity,
                    BuyPrice = pBuyPrice,
                    RetailPrice = pRetailPrice,
                    Price1 = pPrice1,
                    Price2 = pPrice2,
					ModifiedBy = pCurrentUser
				WHERE
					PurchaseDetailsID = pPurchaseDetailsID;
				
			END IF;
			
SET State = 9;

				UPDATE 
					master_item
				SET
					BuyPrice = pBuyPrice,
					RetailPrice = pRetailPrice,
					Price1 = pPrice1,
					Price2 = pPrice2
				WHERE
					ItemID = pItemID;

SET State = 9;
				
				SELECT
					pID AS 'ID',
                    pPurchaseDetailsID AS 'PurchaseDetailsID',
					'Transaksi Berhasil Disimpan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select purchase transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchase;

DELIMITER $$
CREATE PROCEDURE spSelPurchase (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchase', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_purchase TP
                        JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TP.PurchaseID,
                        TP.PurchaseNumber,
                        DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TP.TransactionDate PlainTransactionDate,
                        MS.SupplierID,
                        MS.SupplierName,
						IFNULL(TPD.Total, 0) Total
					FROM
						transaction_purchase TP
                        JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
						LEFT JOIN
                        (
							SELECT
								TP.PurchaseID,
                                SUM(TPD.Quantity * TPD.BuyPrice) Total
							FROM
								transaction_purchase TP
                                JOIN master_supplier MS
									ON MS.SupplierID = TP.SupplierID
                                LEFT JOIN transaction_purchasedetails TPD
									ON TP.PurchaseID = TPD.PurchaseID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TP.PurchaseID
                        )TPD
							ON TPD.PurchaseID = TP.PurchaseID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select customer from dropdown list
Created Date: 9 january 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDDLCustomer;

DELIMITER $$
CREATE PROCEDURE spSelDDLCustomer (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLCustomer', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		MC.CustomerID,
		MC.CustomerCode,
		MC.CustomerName
	FROM 
		master_customer MC
	ORDER BY 
		MC.CustomerCode;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete return purchase details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPurchaseReturnDetails;

DELIMITER $$
CREATE PROCEDURE spDelPurchaseReturnDetails (
	pPurchaseReturnDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelPurchaseReturnDetails', pCurrentUser);
        SELECT
			pPurchaseReturnID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_purchasereturndetails
		WHERE
			PurchaseReturnDetailsID = pPurchaseReturnDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPurchaseReturnDetailsID AS 'ID',
			'Retur pembelian berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete return purchase
Created Date: 23 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPurchaseReturn;

DELIMITER $$
CREATE PROCEDURE spDelPurchaseReturn (
	pPurchaseReturnID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelPurchaseReturn', pCurrentUser);
        SELECT
			pPurchaseReturnID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_purchasereturn
		WHERE
			PurchaseReturnID = pPurchaseReturnID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPurchaseReturnID AS 'ID',
			'Retur pembelian berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select return purchase details by PurchaseReturnID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseReturnDetails;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseReturnDetails (
	pPurchaseReturnID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseReturnDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TPRD.PurchaseReturnDetailsID,
        TPRD.ItemID,
        TPRD.BranchID,
        CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
        MI.ItemCode,
        MI.ItemName,
        TPRD.Quantity,
        TPRD.BuyPrice
	FROM
		transaction_purchasereturndetails TPRD
        JOIN master_branch MB
			ON MB.BranchID = TPRD.BranchID
		JOIN master_item MI
			ON MI.ItemID = TPRD.ItemID
	WHERE
		TPRD.PurchaseReturnID = pPurchaseReturnID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert the user
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsUser;

DELIMITER $$
CREATE PROCEDURE spInsUser (
	pID 			BIGINT, 
	pUserName 		VARCHAR(255),
	pUserTypeID		SMALLINT,
	pUserLogin 		VARCHAR(100),
	pPassword 		VARCHAR(255),
	pIsActive		BIT,
	pRoleValues		TEXT,
	pIsEdit			INT,
    pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spInsUser', pCurrentUser);
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
			master_user 
		WHERE
			(UserName = pUserName
			OR UserLogin = pUserLogin)
			AND UserID <> pID
		LIMIT 1;
			
SET State = 2;

		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				'Username sudah dipakai' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
		
SET State = 3;

			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_user
				(
					UserName,
					UserLogin,
					UserTypeID,
					UserPassword,
					IsActive,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pUserName,
					pUserLogin,
					pUserTypeID,
					pPassword,
					pIsActive,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;
					
			ELSE
			
SET State = 5;
				UPDATE
					master_user
				SET
					UserName = pUserName,
					UserLogin = pUserLogin,
					UserTypeID = pUserTypeID,
					UserPassword = pPassword,
					IsActive = pIsActive,
					ModifiedBy = pCurrentUser
				WHERE
					UserID = pID;
		
			END IF;
	
SET State = 6;

				DELETE 
				FROM 
					master_role
				WHERE
					UserID = pID;
					
SET State = 7;
			IF(pRoleValues <> "" ) THEN
				SET @query = CONCAT("INSERT INTO master_role
									(
										UserID,
										MenuID,
										EditFlag,
										DeleteFlag
									)
									VALUES", REPLACE(pRoleValues, '(0,', CONCAT('(', pID, ',')));
									
				PREPARE stmt FROM @query;
				EXECUTE stmt;
				DEALLOCATE PREPARE stmt;
				
			END IF;

		END IF;

SET State = 8;

	IF(pIsEdit = 0) THEN
		SELECT
			pID AS 'ID',
			'User Berhasil Ditambahkan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	ELSE
	
SET State = 9;

		SELECT
			pID AS 'ID',
			'User Berhasil Diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	END IF;
	
    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsItem;

DELIMITER $$
CREATE PROCEDURE spInsItem (
	pID 				BIGINT, 
    pItemCode			VARCHAR(100),
	pItemName 			VARCHAR(255),
    pCategoryID			BIGINT,
    pBuyPrice			DOUBLE,
    pRetailPrice		DOUBLE,
    pPrice1				DOUBLE,
    pQty1				DOUBLE,
    pPrice2				DOUBLE,
    pQty2				DOUBLE,
    pWeight				DOUBLE,
	pMinimumStock		DOUBLE,
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
		CALL spInsEventLog(@full_error, 'spInsItem', pCurrentUser);
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
			master_item
		WHERE
			(TRIM(ItemName) = TRIM(pItemName)
            OR TRIM(ItemCode) = TRIM(pItemCode))
			AND ItemID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Barang sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_item
				(
                    ItemCode,
                    ItemName,
					CategoryID,
					BuyPrice,
					RetailPrice,
					Price1,
					Qty1,
					Price2,
					Qty2,
					Weight,
					MinimumStock,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pItemCode,
					pItemName,
					pCategoryID,
					pBuyPrice,
					pRetailPrice,
					pPrice1,
					pQty1,
					pPrice2,
					pQty2,
					pWeight,
					pMinimumStock,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;
				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;
					
SET State = 5;

				SELECT
					pID AS 'ID',
					'Barang Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 6;
				UPDATE
					master_item
				SET
					ItemCode = pItemCode,
                    ItemName = pItemName,
					CategoryID = pCategoryID,
					BuyPrice = pBuyPrice,
					RetailPrice = pRetailPrice,
					Price1 = pPrice1,
					Qty1 = pQty1,
					Price2 = pPrice2,
					Qty2 = pQty2,
					Weight = pWeight,
					MinimumStock = pMinimumStock,
					ModifiedBy = pCurrentUser
				WHERE
					ItemID = pID;

SET State = 7;
				SELECT
					pID AS 'ID',
					'Barang Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete purchase
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPurchase;

DELIMITER $$
CREATE PROCEDURE spDelPurchase (
	pPurchaseID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelPurchase', pCurrentUser);
        SELECT
			pPurchaseID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_purchase
		WHERE
			PurchaseID = pPurchaseID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPurchaseID AS 'ID',
			'Pembelian berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete purchase details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPurchaseDetails;

DELIMITER $$
CREATE PROCEDURE spDelPurchaseDetails (
	pPurchaseDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelPurchaseDetails', pCurrentUser);
        SELECT
			pPurchaseID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_purchasedetails
		WHERE
			PurchaseDetailsID = pPurchaseDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPurchaseDetailsID AS 'ID',
			'Pembelian berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelItem;

DELIMITER $$
CREATE PROCEDURE spDelItem (
	pItemID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelItem', pCurrentUser);
        SELECT
			pItemID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_item
		WHERE
			ItemID = pItemID;

    COMMIT;
    
SET State = 2;

		SELECT
			pItemID AS 'ID',
			'Barang berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete user
Created Date: 27 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelUser;

DELIMITER $$
CREATE PROCEDURE spDelUser (
	pUserID			BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelUser', pCurrentUser);
        SELECT
			pUserID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_role
		WHERE
			UserID = pUserID;
			
SET State = 2;

		DELETE FROM
			master_user
		WHERE
			UserID = pUserID;

    COMMIT;
    
SET State = 3;

		SELECT
			pUserID AS 'ID',
			'User berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete supplier
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelSupplier;

DELIMITER $$
CREATE PROCEDURE spDelSupplier (
	pSupplierID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelSupplier', pCurrentUser);
        SELECT
			pSupplierID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_supplier
		WHERE
			SupplierID = pSupplierID;

    COMMIT;
    
SET State = 2;

		SELECT
			pSupplierID AS 'ID',
			'Supplier berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete Customer
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelCustomer;

DELIMITER $$
CREATE PROCEDURE spDelCustomer (
	pCustomerID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelCustomer', pCurrentUser);
        SELECT
			pCustomerID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_customer
		WHERE
			CustomerID = pCustomerID;

    COMMIT;
    
SET State = 2;

		SELECT
			pCustomerID AS 'ID',
			'Pelanggan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete category
Created Date: 1 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelCategory;

DELIMITER $$
CREATE PROCEDURE spDelCategory (
	pCategoryID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelCategory', pCurrentUser);
        SELECT
			pCategoryID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_category
		WHERE
			CategoryID = pCategoryID;

    COMMIT;
    
SET State = 2;

		 SELECT
			pCategoryID AS 'ID',
			'Kategori berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
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
			(TRIM(SupplierName) = TRIM(pSupplierName)
            OR TRIM(SupplierCode) = TRIM(pSupplierCode))
			AND SupplierID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Supplier sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
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
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Supplier Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_Supplier
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

SET State = 6;
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
DROP PROCEDURE IF EXISTS spInsCustomer;

DELIMITER $$
CREATE PROCEDURE spInsCustomer (
	pID 				BIGINT, 
    pCustomerCode		VARCHAR(100),
	pCustomerName 		VARCHAR(255),
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
		CALL spInsEventLog(@full_error, 'spInsCustomer', pCurrentUser);
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
			master_customer
		WHERE
			(TRIM(CustomerName) = TRIM(pCustomerName)
            OR TRIM(CustomerCode) = TRIM(pCustomerCode))
			AND CustomerID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Pelanggan sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_customer
				(
                    CustomerCode,
                    CustomerName,
					Telephone,
					Address,
					City,
					Remarks,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pCustomerCode,
					pCustomerName,
					pTelephone,
					pAddress,
					pCity,
					pRemarks,
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
					CustomerCode = pCustomerCode,
                    CustomerName = pCustomerName,
					Telephone = pTelephone,
					Address = pAddress,
					City = pCity,
					Remarks = pRemarks,
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
DROP PROCEDURE IF EXISTS spInsCategory;

DELIMITER $$
CREATE PROCEDURE spInsCategory (
	pID 				BIGINT, 
    pCategoryCode		VARCHAR(100),
	pCategoryName 		VARCHAR(255),
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
		CALL spInsEventLog(@full_error, 'spInsCategory', pCurrentUser);
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
			master_category
		WHERE
			(TRIM(CategoryName) = TRIM(pCategoryName)
            OR TRIM(CategoryCode) = TRIM(pCategoryCode))
			AND CategoryID <> pID
		LIMIT 1;
        
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Kategori sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_category
				(
					CategoryCode,
					CategoryName,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pCategoryCode,
					pCategoryName,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Kategori Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_category
				SET
					CategoryCode = pCategoryCode,
					CategoryName= pCategoryName,
					ModifiedBy = pCurrentUser
				WHERE
					CategoryID = pID;

SET State = 6;
				SELECT
					pID AS 'ID',
					'Kategori Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItem;

DELIMITER $$
CREATE PROCEDURE spSelItem (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelItem', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_item MI
                        JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MI.ItemID,
                        MI.ItemCode,
						MI.ItemName,
                        MC.CategoryID,
						MC.CategoryName,
                        MI.BuyPrice,
						MI.RetailPrice,
                        MI.Price1,
                        MI.Qty1,
                        MI.Price2,
                        MI.Qty2,
                        MI.Weight,
                        MI.MinimumStock
					FROM
						master_item MI
                        JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select branch from dropdown list
Created Date: 11 january 2018
Modified Date: 
===============================================================*/


DROP PROCEDURE IF EXISTS spSelDDLBranch;
DELIMITER $$
CREATE PROCEDURE spSelDDLBranch (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLBranch', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		MB.BranchID,
		MB.BranchCode,
		MB.BranchName
	FROM 
		master_branch MB
	ORDER BY 
		MB.BranchID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select category
Created Date: 30 Desember 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelCategory;

DELIMITER $$
CREATE PROCEDURE spSelCategory (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUser', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_category MC
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MC.CategoryID,
						MC.CategoryCode,
						MC.CategoryName
					FROM
						master_category MC
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select Customer
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelCustomer;

DELIMITER $$
CREATE PROCEDURE spSelCustomer (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelCustomer', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_customer MC
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MC.CustomerID,
                        MC.CustomerCode,
                        MC.CustomerName,
                        MC.Telephone,
                        MC.Address,
                        MC.City,
                        MC.Remarks
					FROM
						master_customer MC
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select supplier
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSupplier;

DELIMITER $$
CREATE PROCEDURE spSelSupplier (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelSupplier', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_supplier MS
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MS.SupplierID,
                        MS.SupplierCode,
                        MS.SupplierName,
                        MS.Telephone,
                        MS.Address,
                        MS.City,
                        MS.Remarks
					FROM
						master_supplier MS
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select user login
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUser;

DELIMITER $$
CREATE PROCEDURE spSelUser (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUser', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_user MU
						JOIN master_usertype MUT
							ON MU.UserTypeID = MUT.UserTypeID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MU.UserID,
						MU.UserName,
						MU.UserLogin,
						CASE
							WHEN MU.IsActive = 0
							THEN 'Tidak Aktif'
							ELSE 'Aktif'
						END AS Status,
						MU.IsActive,
                        MUT.UserTypeID,
						MUT.UserTypeName,
                        IFNULL(GC.MenuID, '') MenuID,
                        IFNULL(GC.EditFlag, '') EditFlag,
                        IFNULL(GC.DeleteFlag, '') DeleteFlag
					FROM
						master_user MU
						JOIN master_usertype MUT
							ON MU.UserTypeID = MUT.UserTypeID
						LEFT JOIN 
                        (
							SELECT
								GC.UserID,
                                GROUP_CONCAT(MenuID SEPARATOR ', ') MenuID,
                                GROUP_CONCAT(EditFlag SEPARATOR ', ') EditFlag,
                                GROUP_CONCAT(DeleteFlag SEPARATOR ', ') DeleteFlag
							FROM
								master_role GC
							GROUP BY
								GC.UserID
                        )GC
							ON MU.UserID = GC.UserID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select supplier from dropdown list
Created Date: 9 january 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDDLSupplier;

DELIMITER $$
CREATE PROCEDURE spSelDDLSupplier (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLSupplier', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		MS.SupplierID,
		MS.SupplierCode,
		MS.SupplierName
	FROM 
		master_supplier MS
	ORDER BY 
		MS.SupplierCode;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select user login
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUserLogin;

DELIMITER $$
CREATE PROCEDURE spSelUserLogin (
	pUserLogin 		VARCHAR(100),
	pPassword 		VARCHAR(255),
	pIsActive		BIT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUserLogin', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		UserID,
		UserName,
		UserLogin,
		UserPassword,
		UserTypeID
	FROM
		master_user 
	WHERE 
		UserLogin = pUserLogin
		AND IsActive = pIsActive
		AND UserPassword = pPassword
	LIMIT 1;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select user type from dropdown list
Created Date: 7 january 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDDLUserType;

DELIMITER $$
CREATE PROCEDURE spSelDDLUserType (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLUserType', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		UT.UserTypeID,
        UT.UserTypeName
	FROM 
		master_usertype UT
	ORDER BY 
		UT.UserTypeID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Get all menu
Created Date: 24 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelMenu;

DELIMITER $$
CREATE PROCEDURE spSelMenu (
	 pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN
	
    DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelMenu', pCurrentUser);
	END;

SET State = 1;

	SELECT 
		MG.GroupMenuID,
		MG.GroupMenuName,
		MM.MenuID,
		MM.MenuName
	FROM
		master_groupmenu MG
		JOIN master_menu MM 
			ON MG.GroupMenuID = MM.GroupMenuID
	GROUP BY
		MM.MenuID
	ORDER BY 
		MG.OrderNo ASC , 
		MM.OrderNo ASC;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Get user menu permission
Created Date: 24 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUserMenuNavigation;

DELIMITER $$
CREATE PROCEDURE spSelUserMenuNavigation (
	pUserID			BIGINT,
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN
	
    DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUserMenuNavigation', pCurrentUser);
	END;

SET State = 1;

	SELECT 
		MG.GroupMenuID,
		MG.GroupMenuName,
		MM.MenuID,
		MM.MenuName,
		MM.Url,
		MG.Icon
	FROM
		master_groupmenu MG
		JOIN master_menu MM 
			ON MG.GroupMenuID = MM.GroupMenuID
		JOIN master_role MR 
			ON MR.MenuID = MM.MenuID
	WHERE
		MR.UserID = pUserID
	GROUP BY
		MM.MenuID
	ORDER BY 
		MG.OrderNo ASC , 
		MM.OrderNo ASC;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select category from dropdown list
Created Date: 3 january 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDDLCategory;

DELIMITER $$
CREATE PROCEDURE spSelDDLCategory (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLCategory', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		MC.CategoryID,
		MC.CategoryCode,
		MC.CategoryName
	FROM 
		master_category MC
	ORDER BY 
		MC.CategoryCode;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select user login
Created Date: 1 Desember 2017to show user details
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUserDetails;

DELIMITER $$
CREATE PROCEDURE spSelUserDetails (
	pUserID 		BIGINT,
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUserDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MR.RoleID,
		MR.UserID,
		MR.MenuID,
		MR.EditFlag,
		MR.DeleteFlag,
		MU.UserName,
		MU.UserLogin,
		MU.IsActive
	FROM
		master_user MU
		LEFT JOIN master_role MR
			ON MU.UserID = MR.UserID
	WHERE
		MU.UserID = pUserID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert event log
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsEventLog;

DELIMITER $$
CREATE PROCEDURE spInsEventLog (
	pDescription	TEXT,
	pSource			VARCHAR(100),
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (State ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spInsEventLog', pCurrentUser);
	END;
	
	START TRANSACTION;
	
SET State = 1;

		INSERT INTO master_eventlog
		(
			EventLogDate,
			Description,
			Source,
			CreatedDate,
			CreatedBy
		)
		VALUES
		(
			NOW(),
			pDescription,
			pSource,
			NOW(),
			pCurrentUser
		);
		
    COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Get user menu permission
Created Date: 16 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUserMenuPermission;

DELIMITER $$
CREATE PROCEDURE spSelUserMenuPermission (
	pApplicationPath	VARCHAR(255),
    pRequestedPath		VARCHAR(255),
	pCurrentUser		VARCHAR(255)
)
StoredProcedure:BEGIN
	
    DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUserMenuPermission', pCurrentUser);
	END;

SET State = 1;

	SELECT
		MR.EditFlag,
		MR.DeleteFlag
	FROM
		master_role MR
		JOIN master_menu MM
			ON MM.MenuID = MR.MenuID
	WHERE
		CONCAT(pApplicationPath, MM.Url) = pRequestedPath
		AND MR.UserID = pCurrentUser;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for return all parameter
Created Date: 16 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelParameter;

DELIMITER $$
CREATE PROCEDURE spSelParameter (
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN
	
    DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelParameter', pCurrentUser);
	END;

SET State = 1;

	SELECT
		ParameterName,
		ParameterValue
	FROM
		master_parameter;
		
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelStockDetailsReport (
	pItemCode		VARCHAR(100),
	pBranchID 		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelStockDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		'Stok Awal' TransactionType,
		DATE_FORMAT(pFromDate, '%d-%m-%Y') TransactionDate,
		pFromDate DateNoFormat,
		'' CustomerName,
		(IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0)) Quantity
	FROM
		master_item MI
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(TPD.Quantity) Quantity
			FROM
				transaction_purchase TP
				JOIN transaction_purchasedetails TPD
					ON TP.PurchaseID = TPD.PurchaseID
				JOIN master_item MI
					ON TPD.ItemID = MI.ItemID
			WHERE
				TPD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(TP.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)TP
			ON TP.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SRD.SaleReturnID = SR.SaleReturnID
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
			WHERE
				SRD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(SR.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)SR
			ON SR.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_sale TS
				JOIN transaction_saledetails SD
					ON TS.SaleID = SD.SaleID
				JOIN master_item MI
					ON SD.ItemID = MI.ItemID
			WHERE
				SD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(TS.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)S
			ON S.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(PRD.Quantity) Quantity
			FROM
				transaction_purchasereturn TPR
				JOIN transaction_purchasereturndetails PRD
					ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
				JOIN master_item MI
					ON MI.ItemID = PRD.ItemID
			WHERE
				PRD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(TPR.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)PR
			ON MI.ItemID = PR.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SMD.Quantity) Quantity
			FROM
				transaction_stockmutation SM
				JOIN transaction_stockmutationdetails SMD
					ON SM.StockMutationID = SMD.StockMutationID
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
			WHERE
				SMD.DestinationID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(SM.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SUM(SAD.AdjustedQuantity - SAD.Quantity) Quantity
			FROM
				transaction_stockadjust SA
				JOIN transaction_stockadjustdetails SAD
					ON SA.StockAdjustID = SAD.StockAdjustID
				JOIN master_item MI
					ON MI.ItemID = SAD.ItemID
			WHERE
				SAD.BranchID = pBranchID
				AND TRIM(MI.ItemCode) = TRIM(pItemCode)
				AND CAST(SA.TransactionDate AS DATE) < pFromDate
			GROUP BY
				MI.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
	WHERE
		TRIM(MI.ItemCode) = TRIM(pItemCode)
	UNION ALL
	SELECT
		'Pembelian',
		DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
		TP.TransactionDate DateNoFormat,
		MS.SupplierName,
		TPD.Quantity
	FROM
		transaction_purchase TP
		JOIN transaction_purchasedetails TPD
			ON TP.PurchaseID = TPD.PurchaseID
		JOIN master_item MI
			ON TPD.ItemID = MI.ItemID
		JOIN master_supplier MS
			ON MS.SupplierID = TP.SupplierID
	WHERE
		TPD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(TP.TransactionDate AS DATE) >= pFromDate
		AND CAST(TP.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Retur Penjualan',
		DATE_FORMAT(SR.TransactionDate, '%d-%m-%Y') TransactionDate,
		SR.TransactionDate DateNoFormat,
		MC.CustomerName,
		SRD.Quantity
	FROM
		transaction_salereturn SR
		JOIN transaction_sale TS
			ON TS.SaleID = SR.SaleID
		JOIN transaction_salereturndetails SRD
			ON SRD.SaleReturnID = SR.SaleReturnID
		JOIN master_item MI
			ON SRD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
	WHERE
		SRD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(SR.TransactionDate AS DATE) >= pFromDate
		AND CAST(SR.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Penjualan',
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
		TS.TransactionDate DateNoFormat,
		MC.CustomerName,
		-SD.Quantity
	FROM
		transaction_sale TS
		JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_item MI
			ON SD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
	WHERE
		SD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Retur Pembelian',
		DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
		TPR.TransactionDate DateNoFormat,
		MS.SupplierName,
		-PRD.Quantity
	FROM
		transaction_purchasereturn TPR
		JOIN transaction_purchasereturndetails PRD
			ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
		JOIN master_item MI
			ON MI.ItemID = PRD.ItemID
		JOIN master_supplier MS
			ON MS.SupplierID = TPR.SupplierID
	WHERE
		PRD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(TPR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TPR.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Mutasi Stok',
		DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') TransactionDate,
		SM.TransactionDate DateNoFormat,
		'',
		SMD.Quantity
	FROM
		transaction_stockmutation SM
		JOIN transaction_stockmutationdetails SMD
			ON SM.StockMutationID = SMD.StockMutationID
		JOIN master_item MI
			ON MI.ItemID = SMD.ItemID
	WHERE
		SMD.DestinationID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(SM.TransactionDate AS DATE) >= pFromDate
		AND CAST(SM.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Adjust Stok',
		DATE_FORMAT(SA.TransactionDate, '%d-%m-%Y') TransactionDate,
		SA.TransactionDate DateNoFormat,
		'',
		(SAD.AdjustedQuantity - SAD.Quantity)
	FROM
		transaction_stockadjust SA
		JOIN transaction_stockadjustdetails SAD
			ON SA.StockAdjustID = SAD.StockAdjustID
		JOIN master_item MI
			ON MI.ItemID = SAD.ItemID
	WHERE
		SAD.BranchID = pBranchID
		AND TRIM(MI.ItemCode) = TRIM(pItemCode)
		AND CAST(SA.TransactionDate AS DATE) >= pFromDate
		AND CAST(SA.TransactionDate AS DATE) <= pToDate
	ORDER BY
		DateNoFormat;

        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSale;

DELIMITER $$
CREATE PROCEDURE spSelSale (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelSale', pCurrentUser);
	END;
	
SET State = 1;
	
    DELETE FROM 
		transaction_sale
    WHERE
		FinishFlag = 0
        AND DATE_FORMAT(TransactionDate, '%Y-%m-%d') <> DATE_FORMAT(NOW(), '%Y-%m-%d');
    
SET State = 2;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_sale TS
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TS.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TSD.Total, 0) Total,
						IFNULL(TSD.Weight, 0) Weight,
						TS.RetailFlag,
						IFNULL(TS.Payment, 0) Payment,
                        IFNULL(PT.PaymentTypeName, '') PaymentTypeName,
                        CASE
							WHEN FinishFlag = 0
                            THEN 'Belum Selesai'
                            ELSE 'Selesai'
						END Status,
                        IFNULL(PT.PaymentTypeID, 1) PaymentTypeID,
                        IFNULL(TS.ServiceCost, 0) ServiceCost
					FROM
						transaction_sale TS
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN master_paymenttype PT
							ON PT.PaymentTypeID = TS.PaymentTypeID
						LEFT JOIN
                        (
							SELECT
								TS.SaleID,
                                SUM(TSD.Quantity * (TSD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - TSD.Discount)) Total,
								SUM(TSD.Quantity * MI.Weight * IFNULL(MID.ConversionQuantity, 1)) Weight
							FROM
								transaction_sale TS
                                JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
                                LEFT JOIN transaction_saledetails TSD
									ON TS.SaleID = TSD.SaleID
								LEFT JOIN master_item MI
									ON MI.ItemID = TSD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TSD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TS.SaleID
                        )TSD
							ON TSD.SaleID = TS.SaleID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdSalePayment;

DELIMITER $$
CREATE PROCEDURE spUpdSalePayment (
	pID 			BIGINT, 
	pPayment 		DOUBLE,
	pPaymentTypeID	SMALLINT,
	pServiceCost	DOUBLE,
    pFinishFlag		BIT,
	pCurrentUser	VARCHAR(255)
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdSalePayment', pCurrentUser);
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
		UPDATE
			transaction_sale
		SET
			Payment = pPayment,
            FinishFlag = pFinishFlag,
			PaymentTypeID = pPaymentTypeID,
			ModifiedBy = pCurrentUser,
			ServiceCost = pServiceCost
		WHERE
			SaleID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Pembayaran berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportPurchaseReport;

DELIMITER $$
CREATE PROCEDURE spSelExportPurchaseReport (
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelExportPurchaseReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TP.PurchaseID,
		TP.PurchaseNumber,
		DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
		MS.SupplierName,
        MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        PD.Quantity,
        MU.UnitName,
        PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
        PD.RetailPrice * IFNULL(MID.ConversionQuantity, 1) RetailPrice,
		(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) SubTotal
	FROM
		transaction_purchase TP
		JOIN transaction_purchasedetails PD
			ON TP.PurchaseID = PD.PurchaseID
		JOIN master_supplier MS
			ON MS.SupplierID = TP.SupplierID
		JOIN master_item MI
			ON MI.ItemID = PD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = PD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pBranchID = 0
			THEN PD.BranchID
			ELSE pBranchID
		END = PD.BranchID
		AND CAST(TP.TransactionDate AS DATE) >= pFromDate
		AND CAST(TP.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		TPR.PurchaseReturnID,
		TPR.PurchaseReturnNumber,
		DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
		MS.SupplierName,
        MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        PRD.Quantity,
        MU.UnitName,
        PRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
        0 RetailPrice,
		-(PRD.Quantity * PRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) SubTotal
	FROM
		transaction_purchasereturn TPR
		JOIN transaction_purchasereturndetails PRD
			ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
		JOIN master_supplier MS
			ON MS.SupplierID = TPR.SupplierID
		JOIN master_item MI
			ON MI.ItemID = PRD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = PRD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pBranchID = 0
			THEN PRD.BranchID
			ELSE pBranchID
		END = PRD.BranchID
		AND CAST(TPR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TPR.TransactionDate AS DATE) <= pToDate
	ORDER BY
		PurchaseNumber,
        PurchaseID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelTopSellingReport;

DELIMITER $$
CREATE PROCEDURE spSelTopSellingReport (
	pCategoryID		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelTopSellingReport', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MI.ItemName,
        IFNULL(S.Quantity, 0) + IFNULL(B.Quantity, 0) - IFNULL(SR.Quantity, 0) SellingCount
	FROM
		master_item MI
        LEFT JOIN
        (
			SELECT
				MI.ItemID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_sale TS
                JOIN transaction_saledetails SD
					ON SD.SaleID = TS.SaleID
				JOIN master_item MI
					ON MI.ItemID = SD.ItemID
				LEFT JOIN master_itemdetails MID
					ON MID.ItemDetailsID = SD.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
                AND CAST(TS.TransactionDate AS DATE) >= pFromDate
				AND CAST(TS.TransactionDate AS DATE) <= pToDate
			GROUP BY
				MI.ItemID
		)S
			ON MI.ItemID = S.ItemID
		LEFT join
        (
            SELECT
				MI.ItemID,
				SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_booking TB
                JOIN transaction_bookingdetails BD
					ON TB.BookingID = BD.BookingID
				JOIN master_item MI
					ON MI.ItemID = BD.ItemID
				LEFT JOIN master_itemdetails MID
					ON MID.ItemDetailsID = BD.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
                AND CAST(TB.TransactionDate AS DATE) >= pFromDate
				AND CAST(TB.TransactionDate AS DATE) <= pToDate
			GROUP BY
				MI.ItemID
		)B
			ON B.ItemID = MI.ItemID
		LEFT JOIN
        (
            SELECT
				MI.ItemID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SR.SaleReturnID = SRD.SaleReturnID
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CAST(SR.TransactionDate AS DATE) >= pFromDate
				AND CAST(SR.TransactionDate AS DATE) <= pToDate
			GROUP BY
				MI.ItemID
        )SR
			ON SR.ItemID = MI.ItemID
	WHERE
		CASE
			WHEN pCategoryID = 0
			THEN MI.CategoryID
			ELSE pCategoryID
		END = MI.CategoryID
	ORDER BY
		SellingCount DESC
	LIMIT
		0, 10;
    
    
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelStockDetailsReport (
	pItemID					BIGINT,
	pBranchID 				INT,
	pFromDate				DATE,
	pToDate					DATE,
    pConversionQuantity		DOUBLE,
    pCurrentUser			VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelStockDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		'Mutasi Sebelumnya' TransactionType,
		'-' TransactionDate,
		pFromDate DateNoFormat,
		'' CustomerName,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / pConversionQuantity, 2) Quantity,
		'0000-00-00' CreatedDate
	FROM
		master_item MI
        LEFT JOIN
		(
			SELECT
				FSD.ItemID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststock FS
				JOIN transaction_firststockdetails FSD
					ON FS.FirstStockID = FSD.FirstStockID
				LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				FSD.BranchID = pBranchID
				AND CAST(FS.TransactionDate AS DATE) < pFromDate
			GROUP BY
				FSD.ItemID
		)FS
			ON FS.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				TPD.ItemID,
				SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchase TP
				JOIN transaction_purchasedetails TPD
					ON TP.PurchaseID = TPD.PurchaseID
				LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				TPD.BranchID = pBranchID
				AND CAST(TP.TransactionDate AS DATE) < pFromDate
			GROUP BY
				TPD.ItemID
		)TP
			ON TP.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SRD.ItemID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SRD.SaleReturnID = SR.SaleReturnID
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				SRD.BranchID = pBranchID
				AND CAST(SR.TransactionDate AS DATE) < pFromDate
			GROUP BY
				SRD.ItemID
		)SR
			ON SR.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SD.ItemID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_sale TS
				JOIN transaction_saledetails SD
					ON TS.SaleID = SD.SaleID
				LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				SD.BranchID = pBranchID
				AND CAST(TS.TransactionDate AS DATE) < pFromDate
			GROUP BY
				SD.ItemID
		)S
			ON S.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PRD.ItemID,
				SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasereturn TPR
				JOIN transaction_purchasereturndetails PRD
					ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
				LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				PRD.BranchID = pBranchID
				AND CAST(TPR.TransactionDate AS DATE) < pFromDate
			GROUP BY
				PRD.ItemID
		)PR
			ON MI.ItemID = PR.ItemID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutation SM
				JOIN transaction_stockmutationdetails SMD
					ON SM.StockMutationID = SMD.StockMutationID
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				SMD.DestinationID = pBranchID
				AND CAST(SM.TransactionDate AS DATE) < pFromDate
			GROUP BY
				SMD.ItemID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
        (
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutation SM
				JOIN transaction_stockmutationdetails SMD
					ON SM.StockMutationID = SMD.StockMutationID
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				SMD.SourceID = pBranchID
				AND CAST(SM.TransactionDate AS DATE) < pFromDate
			GROUP BY
				SMD.ItemID
		)SMM
			ON MI.ItemID = SMM.ItemID
		LEFT JOIN
		(
			SELECT
				SAD.ItemID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity)  * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjust SA
				JOIN transaction_stockadjustdetails SAD
					ON SA.StockAdjustID = SAD.StockAdjustID
				LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				SAD.BranchID = pBranchID
				AND CAST(SA.TransactionDate AS DATE) < pFromDate
			GROUP BY
				SAD.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
                JOIN transaction_booking TB
					ON TB.BookingID = BD.BookingID
                LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				BD.BranchID = pBranchID
				AND CAST(TB.TransactionDate AS DATE) < pFromDate
			GROUP BY
				BD.ItemID
		)B
			ON B.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pick TP
				JOIN transaction_pickdetails PD
					ON TP.PickID = PD.PickID
				LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = PD.BranchID
				AND CAST(TP.TransactionDate AS DATE) < pFromDate
			GROUP BY
				PD.ItemID
		)P
			ON P.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_booking B
				JOIN transaction_bookingdetails BD
					ON B.BookingID = BD.BookingID
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				pBranchID = PD.BranchID
				AND CAST(B.TransactionDate AS DATE) < pFromDate
			GROUP BY
				BD.ItemID
		)BN
			ON BN.ItemID = MI.ItemID
	WHERE
		MI.ItemID = pItemID
	UNION ALL
    SELECT
		'Stok Awal',
		DATE_FORMAT(FS.TransactionDate, '%d-%m-%Y') TransactionDate,
		FS.TransactionDate DateNoFormat,
		'-',
		ROUND((FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		FS.CreatedDate
	FROM
		transaction_firststock FS
		JOIN transaction_firststockdetails FSD
			ON FS.FirstStockID = FSD.FirstStockID
		JOIN master_item MI
			ON FSD.ItemID = MI.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = FSD.ItemDetailsID
	WHERE
		FSD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(FS.TransactionDate AS DATE) >= pFromDate
		AND CAST(FS.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Pembelian',
		DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
		TP.TransactionDate DateNoFormat,
		MS.SupplierName,
		ROUND((TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2) ,
		TP.CreatedDate
	FROM
		transaction_purchase TP
		JOIN transaction_purchasedetails TPD
			ON TP.PurchaseID = TPD.PurchaseID
		JOIN master_item MI
			ON TPD.ItemID = MI.ItemID
		JOIN master_supplier MS
			ON MS.SupplierID = TP.SupplierID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = TPD.ItemDetailsID
	WHERE
		TPD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(TP.TransactionDate AS DATE) >= pFromDate
		AND CAST(TP.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Retur Penjualan',
		DATE_FORMAT(SR.TransactionDate, '%d-%m-%Y') TransactionDate,
		SR.TransactionDate DateNoFormat,
		MC.CustomerName,
		ROUND((SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		SR.CreatedDate
	FROM
		transaction_salereturn SR
		JOIN transaction_sale TS
			ON TS.SaleID = SR.SaleID
		JOIN transaction_salereturndetails SRD
			ON SRD.SaleReturnID = SR.SaleReturnID
		JOIN master_item MI
			ON SRD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SRD.ItemDetailsID
	WHERE
		SRD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(SR.TransactionDate AS DATE) >= pFromDate
		AND CAST(SR.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Penjualan',
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
		TS.TransactionDate DateNoFormat,
		MC.CustomerName,
		ROUND((-SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		TS.CreatedDate
	FROM
		transaction_sale TS
		JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_item MI
			ON SD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
	WHERE
		SD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Retur Pembelian',
		DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
		TPR.TransactionDate DateNoFormat,
		MS.SupplierName,
		ROUND((-PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) /pConversionQuantity, 2),
		TPR.CreatedDate
	FROM
		transaction_purchasereturn TPR
		JOIN transaction_purchasereturndetails PRD
			ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
		JOIN master_item MI
			ON MI.ItemID = PRD.ItemID
		JOIN master_supplier MS
			ON MS.SupplierID = TPR.SupplierID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = PRD.ItemDetailsID
	WHERE
		PRD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(TPR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TPR.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Mutasi Stok',
		DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') TransactionDate,
		SM.TransactionDate DateNoFormat,
		'',
		ROUND((SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) /pConversionQuantity, 2),
		SM.CreatedDate
	FROM
		transaction_stockmutation SM
		JOIN transaction_stockmutationdetails SMD
			ON SM.StockMutationID = SMD.StockMutationID
		JOIN master_item MI
			ON MI.ItemID = SMD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SMD.ItemDetailsID
	WHERE
		SMD.DestinationID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(SM.TransactionDate AS DATE) >= pFromDate
		AND CAST(SM.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		'Mutasi Stok',
		DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') TransactionDate,
		SM.TransactionDate DateNoFormat,
		'',
		ROUND((-SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		SM.CreatedDate
	FROM
		transaction_stockmutation SM
		JOIN transaction_stockmutationdetails SMD
			ON SM.StockMutationID = SMD.StockMutationID
		JOIN master_item MI
			ON MI.ItemID = SMD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SMD.ItemDetailsID
	WHERE
		SMD.SourceID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(SM.TransactionDate AS DATE) >= pFromDate
		AND CAST(SM.TransactionDate AS DATE) <= pToDate
	UNION ALL
	SELECT
		'Adjust Stok',
		DATE_FORMAT(SA.TransactionDate, '%d-%m-%Y') TransactionDate,
		SA.TransactionDate DateNoFormat,
		'',
		ROUND(((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		SA.CreatedDate
	FROM
		transaction_stockadjust SA
		JOIN transaction_stockadjustdetails SAD
			ON SA.StockAdjustID = SAD.StockAdjustID
		JOIN master_item MI
			ON MI.ItemID = SAD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SAD.ItemDetailsID
	WHERE
		SAD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(SA.TransactionDate AS DATE) >= pFromDate
		AND CAST(SA.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		'Pemesanan',
		DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
		TB.TransactionDate DateNoFormat,
		MC.CustomerName,
		ROUND((-(BD.Quantity  - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		TB.CreatedDate
	FROM
		transaction_booking TB
		JOIN transaction_bookingdetails BD
			ON TB.BookingID = BD.BookingID
		JOIN master_item MI
			ON BD.ItemID = MI.ItemID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = BD.ItemDetailsID
		LEFT JOIN transaction_pickdetails PD
			ON PD.BookingDetailsID = BD.BookingDetailsID
			AND PD.BranchID <> BD.BranchID
	WHERE
		BD.BranchID = pBranchID
		AND MI.ItemID = pItemID
		AND CAST(TB.TransactionDate AS DATE) >= pFromDate
		AND CAST(TB.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		'Pengambilan',
        DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
		TP.TransactionDate DateNoFormat,
		MC.CustomerName,
		ROUND((-PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) / pConversionQuantity, 2),
		TP.CreatedDate
	FROM
		transaction_bookingdetails BD
        JOIN transaction_pickdetails PD
			ON PD.BookingDetailsID = BD.BookingDetailsID
			AND PD.BranchID <> BD.BranchID
		JOIN transaction_pick TP
			ON TP.PickID = PD.PickID
		JOIN transaction_booking TB
			ON TB.BookingID = TP.BookingID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		LEFT JOIN master_itemdetails MID
			ON BD.ItemDetailsID = MID.ItemDetailsID
	WHERE
		PD.BranchID = pBranchID
		AND PD.ItemID = pItemID
		AND CAST(TP.TransactionDate AS DATE) >= pFromDate
		AND CAST(TP.TransactionDate AS DATE) <= pToDate
	ORDER BY
		DateNoFormat,
		CreatedDate;

        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDailyReport;

DELIMITER $$
CREATE PROCEDURE spSelDailyReport (
	pUserID				BIGINT,
	pTransactionDate	DATE,
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
		CALL spInsEventLog(@full_error, 'spSelDailyReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		0 UnionLevel,
        MUS.UserName,
        'Saldo Awal' TransactionName,
		'' TransactionNumber,
        '' CustomerName,
        '' ItemName,
        '' ItemCode,
        0 Quantity,
        '' UnitName,
        0  SalePrice,
        0 Discount,
        FB.FirstBalanceAmount SubTotal,
        0 Payment
	FROM
		transaction_firstbalance FB
		JOIN master_user MUS
			ON MUS.UserID = FB.UserID
	WHERE
		CAST(FB.TransactionDate AS DATE) = pTransactionDate
        AND CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
	UNION ALL
	SELECT
		1 UnionLevel,
		MUS.UserName,
        'Penjualan Tunai' TransactionName,
		TS.SaleNumber,
        MC.CustomerName,
		MI.ItemName,
        MI.ItemCode,
        SD.Quantity,
        MU.UnitName,
        SD.SalePrice * IFNULL(MID.ConversionQuantity, 1)  SalePrice,
        SD.Discount,
        SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount) SubTotal,
        0 Payment
    FROM
		transaction_sale TS
        JOIN master_user MUS
			ON TS.CreatedBy = MUS.UserLogin
        JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND CAST(TS.TransactionDate AS DATE) = pTransactionDate
        AND IFNULL(TS.PaymentTypeID, 0) = 1
	UNION ALL
    SELECT
		2 UnionLevel,
		MUS.UserName,
        'Pemesanan Tunai' TransactionName,
		TB.BookingNumber,
        MC.CustomerName,
		MI.ItemName,
        MI.ItemCode,
        BD.Quantity,
        MU.UnitName,
        BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1)  SalePrice,
        BD.Discount,
        BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount) SubTotal,
        0 Payment
    FROM
		transaction_booking TB
		JOIN master_user MUS
			ON TB.CreatedBy = MUS.UserLogin
        JOIN transaction_bookingdetails BD
			ON TB.BookingID = BD.BookingID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		JOIN master_item MI
			ON MI.ItemID = BD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = BD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND CAST(TB.TransactionDate AS DATE) = pTransactionDate
        AND IFNULL(TB.PaymentTypeID, 0) = 1
	UNION ALL
    SELECT
		3 UnionLevel,
		MUS.UserName,
        'Retur Penjualan',
		CONCAT('R', TS.SaleNumber) SaleNumber,
        MC.CustomerName,
		MI.ItemName,
        MI.ItemCode,
        SRD.Quantity,
        MU.UnitName,
        SRD.SalePrice,
        0 Discount,
        -(SRD.Quantity * SRD.SalePrice) SubTotal,
        0 Payment
    FROM
		transaction_salereturn TSR
        JOIN master_user MUS
			ON TSR.CreatedBy = MUS.UserLogin
		JOIN transaction_sale TS
			ON TSR.SaleID = TS.SaleID
        JOIN transaction_salereturndetails SRD
			ON TSR.SaleReturnID = SRD.SaleReturnID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SRD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SRD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND CAST(TSR.TransactionDate AS DATE) = pTransactionDate
	UNION ALL
    SELECT
		4 UnionLevel,
		MUS.UserName,
        'DP Penjualan' TransactionName,
		TS.SaleNumber,
        MC.CustomerName,
		MI.ItemName,
        MI.ItemCode,
        SD.Quantity,
        MU.UnitName,
        SD.SalePrice * IFNULL(MID.ConversionQuantity, 1)  SalePrice,
        SD.Discount,
        SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount) SubTotal,
        IFNULL(TS.Payment, 0) Payment
    FROM
		transaction_sale TS
        JOIN master_user MUS
			ON TS.CreatedBy = MUS.UserLogin
        JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND CAST(TS.TransactionDate AS DATE) = pTransactionDate
        AND IFNULL(TS.PaymentTypeID, 0) = 2
        AND IFNULL(TS.Payment, 0) > 0
	UNION ALL
    SELECT
		5 UnionLevel,
		MUS.UserName,
        'DP Pemesanan' TransactionName,
		TB.BookingNumber,
        MC.CustomerName,
		MI.ItemName,
        MI.ItemCode,
        BD.Quantity,
        MU.UnitName,
        BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1)  SalePrice,
        BD.Discount,
        BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount) SubTotal,
        IFNULL(TB.Payment, 0) Payment
    FROM
		transaction_booking TB
		JOIN master_user MUS
			ON TB.CreatedBy = MUS.UserLogin
        JOIN transaction_bookingdetails BD
			ON TB.BookingID = BD.BookingID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		JOIN master_item MI
			ON MI.ItemID = BD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = BD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND CAST(TB.TransactionDate AS DATE) = pTransactionDate
		AND IFNULL(TB.PaymentTypeID, 0) = 2
        AND IFNULL(TB.Payment, 0) > 0
	UNION ALL
    SELECT
		6 UnionLevel,
		MUS.UserName,
        'Pembayaran Piutang' TransactionName,
		IFNULL(TS.SaleNumber, TB.BookingNumber) TransactionNumber,
        MC.CustomerName,
		'',
        '',
        0,
        '',
        0,
        
        0,
        PD.Amount,
        0 Payment
	FROM
		transaction_paymentdetails PD
        JOIN master_user MUS
			ON MUS.UserLogin = PD.CreatedBy
		LEFT JOIN transaction_sale TS
			ON TS.SaleID = PD.TransactionID
            AND PD.TransactionType = 'S'
		LEFT JOIN transaction_booking TB
			ON TB.BookingID = PD.TransactionID
            AND PD.TransactionType = 'B'
		JOIN master_customer MC
			ON MC.CustomerID = IFNULL(TS.CustomerID, TB.CustomerID)
	WHERE
		CASE
			WHEN pUserID = 0
			THEN MUS.UserID
			ELSE pUserID
		END = MUS.UserID
		AND PD.TransactionType IN ('S', 'B')
        AND CAST(PD.PaymentDate AS DATE) = pTransactionDate
	ORDER BY
		UserName,
        UnionLevel;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDailyReportPrint;

DELIMITER $$
CREATE PROCEDURE spSelDailyReportPrint (
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
		CALL spInsEventLog(@full_error, 'spSelDailyReportPrint', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
        FB.FirstBalanceAmount Amount
	FROM
		transaction_firstbalance FB
		JOIN master_user MUS
			ON MUS.UserID = FB.UserID
	WHERE
		CAST(FB.TransactionDate AS DATE) = CAST(NOW() AS DATE)
        AND FB.CreatedBy = pCurrentUser
	UNION ALL
	SELECT
		IFNULL(S.Amount, 0) Amount
	FROM
		(
			SELECT
				SUM(TSD.Quantity * (TSD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - TSD.Discount)) Amount
			FROM
				transaction_sale TS
				LEFT JOIN transaction_saledetails TSD
					ON TS.SaleID = TSD.SaleID
				LEFT JOIN master_item MI
					ON MI.ItemID = TSD.ItemID
				LEFT JOIN master_itemdetails MID
					ON MID.ItemDetailsID = TSD.ItemDetailsID
			WHERE
				DATE_FORMAT(TS.TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
				AND TS.PaymentTypeID = 1
                AND TS.CreatedBy = pCurrentUser
		)S
	UNION ALL
    SELECT
		IFNULL(B.Amount, 0) Amount
	FROM
		(
			SELECT
				SUM(TBD.Quantity * (TBD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - TBD.Discount)) Amount
			FROM
				transaction_booking TB
				LEFT JOIN transaction_bookingdetails TBD
					ON TB.BookingID = TBD.BookingID
				LEFT JOIN master_item MI
					ON MI.ItemID = TBD.ItemID
				LEFT JOIN master_itemdetails MID
					ON MID.ItemDetailsID = TBD.ItemDetailsID
			WHERE
				DATE_FORMAT(TB.TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
				AND TB.PaymentTypeID = 1
                AND TB.CreatedBy = pCurrentUser
		)B
	UNION ALL
    SELECT
		-IFNULL(SR.Amount, 0)
	FROM
		(
			SELECT
				SUM(TSRD.Quantity * TSRD.SalePrice) Amount
			FROM
				transaction_sale TS
				JOIN transaction_salereturn TSR
					ON TS.SaleID = TSR.SaleID
				LEFT JOIN transaction_salereturndetails TSRD
					ON TSR.SaleReturnID = TSRD.SaleReturnID
				LEFT JOIN master_item MI
					ON MI.ItemID = TSRD.ItemID
			WHERE
				DATE_FORMAT(TSR.TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
                AND TS.CreatedBy = pCurrentUser
		)SR
	UNION ALL
    SELECT
		IFNULL(DP.Amount, 0) Amount
	FROM
		(
			SELECT
				SUM(T.Amount) Amount
			FROM
				(
					SELECT
						SUM(TS.Payment) Amount
					FROM
						transaction_sale TS
					WHERE
						DATE_FORMAT(TS.TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
						AND TS.PaymentTypeID = 2
                        AND TS.CreatedBy = pCurrentUser
					UNION ALL
					SELECT
						SUM(TB.Payment) Amount
					FROM
						transaction_booking TB
					WHERE
						DATE_FORMAT(TB.TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
						AND TB.PaymentTypeID = 2
                        AND TB.CreatedBy = pCurrentUser
				)T
		)DP
	UNION ALL
    SELECT
		IFNULL(PD.Amount, 0) Amount
	FROM
		(
			SELECT
				SUM(PD.Amount) Amount
			FROM
				transaction_paymentdetails PD
			WHERE
				DATE_FORMAT(PD.PaymentDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
				AND PD.TransactionType IN('S', 'B')
                AND PD.CreatedBy = pCurrentUser
		)PD;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select branch from dropdown list
Created Date: 11 january 2018
Modified Date: 
===============================================================*/


DROP PROCEDURE IF EXISTS spSelDDLCashier;
DELIMITER $$
CREATE PROCEDURE spSelDDLCashier (
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDDLCashier', pCurrentUser);
	END;
	
SET State = 1;

	SELECT 
		MU.UserID,
        MU.UserName
	FROM 
		master_user MU
	WHERE
		MU.UserTypeID = 2
        AND MU.IsActive = 1
	ORDER BY 
		MU.UserName;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDeadlinePurchase;

DELIMITER $$
CREATE PROCEDURE spSelDeadlinePurchase (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelDeadlinePurchase', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								PD.TotalPayment
							FROM
								transaction_purchase TP
								JOIN master_supplier MS
									ON MS.SupplierID = TP.SupplierID
								LEFT JOIN transaction_purchasedetails TPD
									ON TP.PurchaseID = TPD.PurchaseID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TPD.ItemDetailsID
								LEFT JOIN
								(
									SELECT
										PD.TransactionID,
										SUM(PD.Amount) TotalPayment
									FROM
										transaction_paymentdetails PD
									WHERE
										PD.TransactionType = 'P'
									GROUP BY
										TransactionID
								)PD
									ON PD.TransactionID = TP.PurchaseID
							WHERE 
								", pWhere, "
								AND TP.PaymentTypeID = 2
								AND DATE_FORMAT(TP.Deadline, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')
							GROUP BY
								TP.PurchaseID
							HAVING
								SUM(TPD.Quantity * TPD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - IFNULL(PD.TotalPayment, 0) > 0
						)TP"
				);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TP.PurchaseNumber,
                        DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
                        DATE_FORMAT(TP.Deadline, '%d-%m-%Y') Deadline,
                        MS.SupplierName,
                        SUM(TPD.Quantity * TPD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total,
						IFNULL(PD.TotalPayment, 0) TotalPay,
                        PD.TotalPayment,
                        SUM(TPD.Quantity * TPD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - IFNULL(PD.TotalPayment, 0) Debt
					FROM
						transaction_purchase TP
						JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
						LEFT JOIN transaction_purchasedetails TPD
							ON TP.PurchaseID = TPD.PurchaseID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = TPD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) TotalPayment
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'P'
							GROUP BY
								TransactionID
						)PD
							ON PD.TransactionID = TP.PurchaseID
					WHERE 
						", pWhere, "
						AND TP.PaymentTypeID = 2
						AND DATE_FORMAT(TP.Deadline, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')
					GROUP BY
						TP.PurchaseID
					HAVING
						SUM(TPD.Quantity * TPD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - IFNULL(PD.TotalPayment, 0) > 0 
					  ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdPayment;

DELIMITER $$
CREATE PROCEDURE spUpdPayment (
	pID 				BIGINT, 
	pPayment 			DOUBLE,
	pTransactionType	VARCHAR(1),
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdPayment', pCurrentUser);
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
		UPDATE
			transaction_sale
		SET
			Payment = pPayment,
            ModifiedBy = pCurrentUser
		WHERE
			SaleID = pID
            AND pTransactionType = 'S';
            
SET State = 2;
		UPDATE
			transaction_booking
		SET
			Payment = pPayment,
            ModifiedBy = pCurrentUser
		WHERE
			BookingID = pID
            AND pTransactionType = 'B';

SET State = 3;
		SELECT
			pID AS 'ID',
			'Pembayaran berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportCreditReport;

DELIMITER $$
CREATE PROCEDURE spSelExportCreditReport (
	pFromDate		DATE,
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
		CALL spInsEventLog(@full_error, 'spSelExportCreditReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TS.SaleID,
		TS.SaleNumber,
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
		TS.TransactionDate PlainTransactionDate,
		MC.CustomerID,
		MC.CustomerName,
		SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1)  - SD.Discount)) TotalSale,
		IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
		SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0)) Credit,
		'Penjualan' TransactionType,
		TS.Payment,
		TP.Amount
	FROM
		transaction_sale TS
		JOIN transaction_saledetails SD
			ON SD.SaleID = TS.SaleID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN
		(
			SELECT
				PD.TransactionID,
				SUM(PD.Amount) Amount
			FROM
				transaction_paymentdetails PD
			WHERE
				PD.TransactionType = 'S'
				AND PD.PaymentDate <= pFromDate
			GROUP BY
				TransactionID
		)TP
			ON TP.TransactionID = TS.SaleID
	WHERE 
		TS.PaymentTypeID = 2
		AND TS.TransactionDate <= pFromDate
	GROUP BY
		TS.SaleID,
		TS.SaleNumber,
		TS.TransactionDate,
		MC.CustomerID,
		MC.CustomerName,
		TS.Payment,
		TP.Amount
	HAVING
		SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0)) > 0
	UNION ALL
	SELECT
		TB.BookingID,
		TB.BookingNumber,
		DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
		TB.TransactionDate PlainTransactionDate,
		MC.CustomerID,
		MC.CustomerName,
		SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) Total,
		IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
		SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0)),
		'Pemesanan' TransactionType,
		TB.Payment,
		TP.Amount
	FROM
		transaction_booking TB
		JOIN transaction_bookingdetails BD
			ON BD.BookingID = TB.BookingID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = BD.ItemDetailsID
		LEFT JOIN
		(
			SELECT
				PD.TransactionID,
				SUM(PD.Amount) Amount
			FROM
				transaction_paymentdetails PD
			WHERE
				PD.TransactionType = 'B'
				AND PD.PaymentDate <= pFromDate
			GROUP BY
				TransactionID
		)TP
			ON TP.TransactionID = TB.BookingID
	WHERE
		TB.PaymentTypeID = 2
		AND TB.TransactionDate <= pFromDate
	GROUP BY
		TB.BookingID,
		TB.BookingNumber,
		TB.TransactionDate,
		MC.CustomerID,
		MC.CustomerName,
		TB.Payment,
		TP.Amount
	HAVING
		SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0)) > 0;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportDebtReport;

DELIMITER $$
CREATE PROCEDURE spSelExportDebtReport (
	pFromDate		DATE,
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
		CALL spInsEventLog(@full_error, 'spSelExportDebtReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TP.PurchaseID,
		TP.PurchaseNumber,
		DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
		TP.TransactionDate PlainTransactionDate,
		MS.SupplierID,
		MS.SupplierName,
		SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) TotalPurchase,
		IFNULL(TPD.Amount, 0) TotalPayment,
		SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - (IFNULL(TPD.Amount, 0)) Debt,
		'Pembelian' TransactionType,
		TPD.Amount
	FROM
		transaction_purchase TP
		JOIN transaction_purchasedetails PD
			ON PD.PurchaseID = TP.PurchaseID
		JOIN master_supplier MS
			ON MS.SupplierID = TP.SupplierID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = PD.ItemDetailsID
		LEFT JOIN
		(
			SELECT
				PD.TransactionID,
				SUM(PD.Amount) Amount
			FROM
				transaction_paymentdetails PD
			WHERE
				PD.TransactionType = 'P'
				AND PD.PaymentDate <= pFromDate
			GROUP BY
				TransactionID
		)TPD
			ON TPD.TransactionID = TP.PurchaseID
	WHERE 
		TP.PaymentTypeID = 2
		AND TP.TransactionDate <= pFromDate
	GROUP BY
		TP.PurchaseID,
		TP.PurchaseNumber,
		TP.TransactionDate,
		MS.SupplierID,
		MS.SupplierName,
		TPD.Amount
	HAVING
		SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - (IFNULL(TPD.Amount, 0)) > 0;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDebtReport;

DELIMITER $$
CREATE PROCEDURE spSelDebtReport (
	pFromDate		DATE,
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelDebtReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows,
                        SUM(Debt) GrandTotal
					FROM
						(
							SELECT
								SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - (IFNULL(TPD.Amount, 0)) Debt
							FROM
								transaction_purchase TP
								JOIN transaction_purchasedetails PD
									ON PD.PurchaseID = TP.PurchaseID
								JOIN master_supplier MS
									ON MS.SupplierID = TP.SupplierID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = PD.ItemDetailsID
								LEFT JOIN
								(
									SELECT
										PD.TransactionID,
										SUM(PD.Amount) Amount
									FROM
										transaction_paymentdetails PD
									WHERE
										PD.TransactionType = 'P'
                                        AND PD.PaymentDate <= '", pFromDate, "'
									GROUP BY
										TransactionID
								)TPD
									ON TPD.TransactionID = TP.PurchaseID
							WHERE 
								TP.PaymentTypeID = 2
                                AND TP.TransactionDate <= '", pFromDate, "'
								AND ", pWhere, "
							GROUP BY
								TP.PurchaseID,
								TP.PurchaseNumber,
								TP.TransactionDate,
								MS.SupplierID,
								MS.SupplierName,
								TPD.Amount
							HAVING
								SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - (IFNULL(TPD.Amount, 0)) > 0
						) TP"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TP.PurchaseID,
						TP.PurchaseNumber,
						DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
						TP.TransactionDate PlainTransactionDate,
						MS.SupplierID,
						MS.SupplierName,
						SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) TotalPurchase,
						IFNULL(TPD.Amount, 0) TotalPayment,
					    SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - (IFNULL(TPD.Amount, 0)) Debt,
                        'Pembelian' TransactionType,
					    TPD.Amount
					FROM
						transaction_purchase TP
						JOIN transaction_purchasedetails PD
							ON PD.PurchaseID = TP.PurchaseID
						JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = PD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'P'
								AND PD.PaymentDate <= '", pFromDate, "'
							GROUP BY
								TransactionID
						)TPD
							ON TPD.TransactionID = TP.PurchaseID
					WHERE 
						TP.PaymentTypeID = 2
						AND TP.TransactionDate <= '", pFromDate, "'
						AND ", pWhere, "
					GROUP BY
						TP.PurchaseID,
						TP.PurchaseNumber,
						TP.TransactionDate,
						MS.SupplierID,
						MS.SupplierName,
						TPD.Amount
					HAVING
						SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - (IFNULL(TPD.Amount, 0)) > 0
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelCreditReport;

DELIMITER $$
CREATE PROCEDURE spSelCreditReport (
	pFromDate		DATE,
	pWhere 			TEXT,
	pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelCreditReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows,
                        SUM(Credit) GrandTotal
					FROM
						(
							SELECT
								SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0)) Credit
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = SD.ItemDetailsID
								LEFT JOIN
								(
									SELECT
										PD.TransactionID,
										SUM(PD.Amount) Amount
									FROM
										transaction_paymentdetails PD
									WHERE
										PD.TransactionType = 'S'
                                        AND PD.PaymentDate <= '", pFromDate, "'
									GROUP BY
										TransactionID
								)TP
									ON TP.TransactionID = TS.SaleID
							WHERE 
								TS.PaymentTypeID = 2
                                AND TS.TransactionDate <= '", pFromDate, "'
								AND ", pWhere, "
							GROUP BY
								TS.SaleID,
								TS.SaleNumber,
								TS.TransactionDate,
								MC.CustomerID,
								MC.CustomerName,
								TS.Payment,
								TP.Amount
							HAVING
								SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0)) > 0
							UNION ALL
		                    SELECT
								SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0))
							FROM
								transaction_booking TB
								JOIN transaction_bookingdetails BD
									ON BD.BookingID = TB.BookingID
								JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = BD.ItemDetailsID
								LEFT JOIN
								(
									SELECT
										PD.TransactionID,
										SUM(PD.Amount) Amount
									FROM
										transaction_paymentdetails PD
									WHERE
										PD.TransactionType = 'B'
                                        AND PD.PaymentDate <= '", pFromDate, "'
									GROUP BY
										TransactionID
								)TP
									ON TP.TransactionID = TB.BookingID
							WHERE
								TB.PaymentTypeID = 2
                                AND TB.TransactionDate <= '", pFromDate, "'
								AND ", pWhere2, "
							GROUP BY
								TB.BookingID,
								TB.BookingNumber,
								TB.TransactionDate,
								MC.CustomerID,
								MC.CustomerName,
								TB.Payment,
								TP.Amount
							HAVING
								SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0)) > 0
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
						TS.SaleNumber,
						DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
						TS.TransactionDate PlainTransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1)  - SD.Discount)) TotalSale,
						IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
					    SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0)) Credit,
                        'Penjualan' TransactionType,
                        TS.Payment,
					    TP.Amount
					FROM
						transaction_sale TS
						JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'S'
                                AND PD.PaymentDate <= '", pFromDate, "'
							GROUP BY
								TransactionID
						)TP
							ON TP.TransactionID = TS.SaleID
					WHERE 
						TS.PaymentTypeID = 2
                        AND TS.TransactionDate <= '", pFromDate, "'
						AND ", pWhere, "
					GROUP BY
						TS.SaleID,
						TS.SaleNumber,
						TS.TransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						TS.Payment,
					    TP.Amount
					HAVING
						SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0)) > 0
					UNION ALL
                    SELECT
						TB.BookingID,
						TB.BookingNumber,
						DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
						TB.TransactionDate PlainTransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) Total,
						IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
						SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0)),
                        'Pemesanan' TransactionType,
						TB.Payment,
					    TP.Amount
					FROM
						transaction_booking TB
						JOIN transaction_bookingdetails BD
							ON BD.BookingID = TB.BookingID
						JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = BD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'B'
                                AND PD.PaymentDate <= '", pFromDate, "'
							GROUP BY
								TransactionID
						)TP
							ON TP.TransactionID = TB.BookingID
					WHERE
						TB.PaymentTypeID = 2
                        AND TB.TransactionDate <= '", pFromDate, "'
						AND ", pWhere2, "
					GROUP BY
						TB.BookingID,
						TB.BookingNumber,
						TB.TransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						TB.Payment,
					    TP.Amount
					HAVING
						SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0)) > 0
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
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
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelSaleDetailsReport (
	pSaleID				BIGINT,
	pBranchID			INT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	IF(pTransactionType = 'Penjualan')
	THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        SD.Quantity,
            MU.UnitName,
	        SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) SalePrice,
			SD.Discount,
			SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount) SubTotal
		FROM
			transaction_saledetails SD
	        JOIN master_item MI
				ON MI.ItemID = SD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = SD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			SD.SaleID = pSaleID
            AND CASE
					WHEN pBranchID = 0
					THEN SD.BranchID
					ELSE pBranchID
				END = SD.BranchID
		ORDER BY
			SD.SaleDetailsID;
	ELSEIF(pTransactionType = 'Pemesanan')
    THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        BD.Quantity,
            MU.UnitName,
	        BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) SalePrice,
			BD.Discount,
			BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount) SubTotal
		FROM
			transaction_bookingdetails BD
	        JOIN master_item MI
				ON MI.ItemID = BD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = BD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			BD.BookingID = pSaleID
            AND CASE
					WHEN pBranchID = 0
					THEN BD.BranchID
					ELSE pBranchID
				END = BD.BranchID
		ORDER BY
			BD.BookingDetailsID;
    ELSE
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        SRD.Quantity,
            MU.UnitName,
	        SRD.SalePrice,
            0 Discount,
			-(SRD.Quantity * SRD.SalePrice) SubTotal
		FROM
			transaction_salereturndetails SRD
	        JOIN master_item MI
				ON MI.ItemID = SRD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = SRD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			SRD.SaleReturnID = pSaleID
            AND CASE
					WHEN pBranchID = 0
					THEN SRD.BranchID
					ELSE pBranchID
				END = SRD.BranchID
		ORDER BY
			SRD.SaleReturnDetailsID;
            
	END IF;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPayment;

DELIMITER $$
CREATE PROCEDURE spSelPayment (
	pWhere 			TEXT,
	pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelPayment', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								1
							FROM
								transaction_sale TS
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
							WHERE 
								TS.PaymentTypeID = 2
								AND ", pWhere, "
							UNION ALL
		                    SELECT
								1
							FROM
								transaction_booking TB
								JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
							WHERE
								TB.PaymentTypeID = 2
								AND ", pWhere2, "
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
						TS.SaleNumber,
						DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
						TS.TransactionDate PlainTransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1)  - SD.Discount)) Total,
						IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
					    SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0)) Credit,
                        'S' TransactionType,
                        IFNULL(TS.Payment, 0) Payment,
					    IFNULL(TP.Amount, 0) Amount
					FROM
						transaction_sale TS
						JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'S'
							GROUP BY
								TransactionID
						)TP
							ON TP.TransactionID = TS.SaleID
					WHERE 
						TS.PaymentTypeID = 2
						AND ", pWhere, "
					GROUP BY
						TS.SaleID,
						TS.SaleNumber,
						TS.TransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						IFNULL(TS.Payment, 0),
					    IFNULL(TP.Amount, 0)
					UNION ALL
                    SELECT
						TB.BookingID,
						TB.BookingNumber,
						DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
						TB.TransactionDate PlainTransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) Total,
						IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
						SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0)),
                        'B' TransactionType,
						IFNULL(TB.Payment, 0) Payment,
					    IFNULL(TP.Amount, 0) Amount
					FROM
						transaction_booking TB
						JOIN transaction_bookingdetails BD
							ON BD.BookingID = TB.BookingID
						JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = BD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'B'
							GROUP BY
								TransactionID
						)TP
							ON TP.TransactionID = TB.BookingID
					WHERE
						TB.PaymentTypeID = 2
						AND ", pWhere2, "
					GROUP BY
						TB.BookingID,
						TB.BookingNumber,
						TB.TransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						IFNULL(TB.Payment, 0),
						IFNULL(TP.Amount, 0)
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportIncomeReport;

DELIMITER $$
CREATE PROCEDURE spSelExportIncomeReport (
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelExportIncomeReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TS.SaleID,
		TS.SaleNumber,
        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        SD.Quantity,
        MU.UnitName,
        SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
        (SD.Quantity * SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) TotalBuy,
        SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) SalePrice,
        SD.Discount,
        (SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) TotalSale,
		(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (SD.Quantity * SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Income
    FROM
		transaction_sale TS
        JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pBranchID = 0
			THEN SD.BranchID
			ELSE pBranchID
		END = SD.BranchID
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		TB.BookingID SaleID,
		TB.BookingNumber SaleNumber,
        DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        BD.Quantity,
        MU.UnitName,
        BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
        (BD.Quantity * BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) TotalBuy,
        BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) SalePrice,
        BD.Discount,
        (BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) TotalSale,
		(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (BD.Quantity * BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Income
    FROM
		transaction_booking TB
        JOIN transaction_bookingdetails BD
			ON TB.BookingID = BD.BookingID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		JOIN master_item MI
			ON MI.ItemID = BD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = BD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pBranchID = 0
			THEN BD.BranchID
			ELSE pBranchID
		END = BD.BranchID
		AND CAST(TB.TransactionDate AS DATE) >= pFromDate
		AND CAST(TB.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		TSR.SaleReturnID,
		CONCAT('R', TS.SaleNumber) SaleNumber,
        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        SRD.Quantity,
        MU.UnitName,
        SRD.BuyPrice,
		(SRD.Quantity * SRD.BuyPrice) TotalBuy,
        SRD.SalePrice,
        0 Discount,
        (SRD.Quantity * SRD.SalePrice) TotalSale,
        -((SRD.Quantity * SRD.SalePrice) - (SRD.Quantity * SRD.BuyPrice)) Income
    FROM
		transaction_salereturn TSR
		JOIN transaction_sale TS
			ON TSR.SaleID = TS.SaleID
        JOIN transaction_salereturndetails SRD
			ON TSR.SaleReturnID = SRD.SaleReturnID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SRD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SRD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pBranchID = 0
			THEN SRD.BranchID
			ELSE pBranchID
		END = SRD.BranchID
		AND CAST(TSR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TSR.TransactionDate AS DATE) <= pToDate
	ORDER BY
		SaleNumber,
        SaleID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelIncomeDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelIncomeDetailsReport (
	pSaleID				BIGINT,
	pBranchID			INT,
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
		CALL spInsEventLog(@full_error, 'spSelIncomeDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	IF(pTransactionType = 'Penjualan')
	THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        SD.Quantity,
            MU.UnitName,
            SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
            (SD.Quantity * SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) TotalBuy,
	        SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) SalePrice,
			SD.Discount,
			(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) TotalSale,
            (SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount))  - (SD.Quantity * SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Income
		FROM
			transaction_saledetails SD
	        JOIN master_item MI
				ON MI.ItemID = SD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = SD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			SD.SaleID = pSaleID
            AND CASE
					WHEN pBranchID = 0
					THEN SD.BranchID
					ELSE pBranchID
				END = SD.BranchID
		ORDER BY
			SD.SaleDetailsID;
	ELSEIF(pTransactionType = 'Pemesanan')
    THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        BD.Quantity,
            MU.UnitName,
            BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
            (BD.Quantity * BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) TotalBuy,
	        BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) SalePrice,
			BD.Discount,
			(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) TotalSale,
            (BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount))  - (BD.Quantity * BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Income
		FROM
			transaction_bookingdetails BD
	        JOIN master_item MI
				ON MI.ItemID = BD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = BD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			BD.BookingID = pSaleID
            AND CASE
					WHEN pBranchID = 0
					THEN BD.BranchID
					ELSE pBranchID
				END = BD.BranchID
		ORDER BY
			BD.BookingDetailsID;
    ELSE
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        SRD.Quantity,
            MU.UnitName,
            SRD.BuyPrice,
            (SRD.Quantity * SRD.BuyPrice) TotalBuy,
	        SRD.SalePrice,
            0 Discount,
			(SRD.Quantity * SRD.SalePrice) TotalSale,
            -((SRD.Quantity * SRD.SalePrice) - (SRD.Quantity * SRD.BuyPrice)) Income
		FROM
			transaction_salereturndetails SRD
	        JOIN master_item MI
				ON MI.ItemID = SRD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = SRD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			SRD.SaleReturnID = pSaleID
            AND CASE
					WHEN pBranchID = 0
					THEN SRD.BranchID
					ELSE pBranchID
				END = SRD.BranchID
		ORDER BY
			SRD.SaleReturnDetailsID;
            
	END IF;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelIncomeReport;

DELIMITER $$
CREATE PROCEDURE spSelIncomeReport(
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
	pWhere 			TEXT,
    pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelIncomeReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows,
						SUM(Total) GrandTotal
					FROM
						(
							SELECT
								SUM((SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (SD.Quantity * SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1))) Total
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = SD.ItemDetailsID
							WHERE 
								CASE
									WHEN ",pBranchID," = 0
									THEN SD.BranchID
									ELSE ",pBranchID,"
								END = SD.BranchID
								AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere, "
							GROUP BY
								TS.SaleID
							UNION ALL
                            SELECT
								SUM((BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (BD.Quantity * BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1))) Total
							FROM
								transaction_booking TB
								JOIN transaction_bookingdetails BD
									ON TB.BookingID = BD.BookingID
								JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = BD.ItemDetailsID
							WHERE 
								CASE
									WHEN ",pBranchID," = 0
									THEN BD.BranchID
									ELSE ",pBranchID,"
								END = BD.BranchID
								AND CAST(TB.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TB.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere, "
							GROUP BY
								TB.BookingID
							UNION ALL
		                    SELECT
								-SUM(((SRD.Quantity * SRD.SalePrice * IFNULL(MID.ConversionQuantity, 1)) - (SRD.Quantity * SRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1))))
							FROM
								transaction_salereturn TSR
								JOIN transaction_sale TS
									ON TS.SaleID = TSR.SaleID
		                        JOIN transaction_salereturndetails SRD
									ON SRD.SaleReturnID = TSR.SaleReturnID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = SRD.ItemDetailsID
							WHERE 
								CASE
									WHEN ",pBranchID," = 0
									THEN SRD.BranchID
									ELSE ",pBranchID,"
								END = SRD.BranchID
								AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere2, "
		                    GROUP BY
								TSR.SaleReturnID
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
						'Penjualan' TransactionType,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        MC.CustomerName,
                        SUM((SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) - (SD.Quantity * SD.BuyPrice * IFNULL(MID.ConversionQuantity, 1))) Total
					FROM
						transaction_sale TS
                        JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SD.ItemDetailsID
					WHERE 
						CASE
							WHEN ",pBranchID," = 0
							THEN SD.BranchID
							ELSE ",pBranchID,"
						END = SD.BranchID
						AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TS.SaleID,
                        TS.SaleNumber,
                        TS.TransactionDate,
                        MC.CustomerName
                    UNION ALL
                    SELECT
						TB.BookingID,
						'Pemesanan' TransactionType,
                        TB.BookingNumber,
                        DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
                        MC.CustomerName,
                        SUM((BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) - (BD.Quantity * BD.BuyPrice * IFNULL(MID.ConversionQuantity, 1))) Total
					FROM
						transaction_booking TB
                        JOIN transaction_bookingdetails BD
							ON TB.BookingID = BD.BookingID
						JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = BD.ItemDetailsID
					WHERE 
						CASE
							WHEN ",pBranchID," = 0
							THEN BD.BranchID
							ELSE ",pBranchID,"
						END = BD.BranchID
						AND CAST(TB.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TB.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TB.BookingID,
                        TB.BookingNumber,
                        TB.TransactionDate,
                        MC.CustomerName
                    UNION ALL
                    SELECT
						TSR.SaleReturnID,
						'Retur' TransactionType,
                        CONCAT('R', TS.SaleNumber),
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        MC.CustomerName,
                        -SUM(((SRD.Quantity * SRD.SalePrice * IFNULL(MID.ConversionQuantity, 1)) - (SRD.Quantity * SRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)))) Total
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN transaction_salereturndetails SRD
							ON SRD.SaleReturnID = TSR.SaleReturnID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SRD.ItemDetailsID
					WHERE 
						CASE
							WHEN ",pBranchID," = 0
							THEN SRD.BranchID
							ELSE ",pBranchID,"
						END = SRD.BranchID
						AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere2, "
                    GROUP BY
						TSR.SaleReturnID,
                        TS.SaleNumber,
                        TSR.TransactionDate,
                        MC.CustomerName
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportSaleReport;

DELIMITER $$
CREATE PROCEDURE spSelExportSaleReport (
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelExportSaleReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TS.SaleID,
		TS.SaleNumber,
        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        SD.Quantity,
        MU.UnitName,
        SD.SalePrice * IFNULL(MID.ConversionQuantity, 1)  SalePrice,
        SD.Discount,
        SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount) SubTotal
    FROM
		transaction_sale TS
        JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pBranchID = 0
			THEN SD.BranchID
			ELSE pBranchID
		END = SD.BranchID
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		TB.BookingID,
		TB.BookingNumber,
        DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        BD.Quantity,
        MU.UnitName,
        BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1)  SalePrice,
        BD.Discount,
        BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount) SubTotal
    FROM
		transaction_booking TB
        JOIN transaction_bookingdetails BD
			ON TB.BookingID = BD.BookingID
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
		JOIN master_item MI
			ON MI.ItemID = BD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = BD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pBranchID = 0
			THEN BD.BranchID
			ELSE pBranchID
		END = BD.BranchID
		AND CAST(TB.TransactionDate AS DATE) >= pFromDate
		AND CAST(TB.TransactionDate AS DATE) <= pToDate
	UNION ALL
    SELECT
		TSR.SaleReturnID,
		CONCAT('R', TS.SaleNumber) SaleNumber,
        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
        MC.CustomerName,
		MI.ItemID,
        MI.ItemName,
        MI.ItemCode,
        SRD.Quantity,
        MU.UnitName,
        SRD.SalePrice,
        0 Discount,
        -(SRD.Quantity * SRD.SalePrice) SubTotal
    FROM
		transaction_salereturn TSR
		JOIN transaction_sale TS
			ON TSR.SaleID = TS.SaleID
        JOIN transaction_salereturndetails SRD
			ON TSR.SaleReturnID = SRD.SaleReturnID
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN master_item MI
			ON MI.ItemID = SRD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SRD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
	WHERE
		CASE
			WHEN pBranchID = 0
			THEN SRD.BranchID
			ELSE pBranchID
		END = SRD.BranchID
		AND CAST(TSR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TSR.TransactionDate AS DATE) <= pToDate
	ORDER BY
		SaleNumber,
        SaleID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleReport;

DELIMITER $$
CREATE PROCEDURE spSelSaleReport (
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
	pWhere 			TEXT,
    pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows,
						SUM(Total) GrandTotal
					FROM
						(
							SELECT
								SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) Total
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = SD.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SD.BranchID
									ELSE ",pBranchID,"
								END = SD.BranchID
								AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere, "
							GROUP BY
								TS.SaleID
							UNION ALL
							SELECT
								SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) Total
							FROM
								transaction_booking TB
								JOIN transaction_bookingdetails BD
									ON TB.BookingID = BD.BookingID
								JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = BD.ItemDetailsID
							WHERE 
								CASE
									WHEN ",pBranchID," = 0
									THEN BD.BranchID
									ELSE ",pBranchID,"
								END = BD.BranchID
								AND CAST(TB.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TB.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere, "
							GROUP BY
								TB.BookingID
							UNION ALL
		                    SELECT
								-SUM(SRD.Quantity * SRD.SalePrice) Total
							FROM
								transaction_salereturn TSR
								JOIN transaction_sale TS
									ON TS.SaleID = TSR.SaleID
		                        JOIN transaction_salereturndetails SRD
									ON SRD.SaleReturnID = TSR.SaleReturnID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
							WHERE 
								CASE
									WHEN ",pBranchID," = 0
									THEN SRD.BranchID
									ELSE ",pBranchID,"
								END = SRD.BranchID
								AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere2, "
		                    GROUP BY
								TSR.SaleReturnID
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
						'Penjualan' TransactionType,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        MC.CustomerName,
                        SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) Total
					FROM
						transaction_sale TS
                        JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SD.ItemDetailsID
					WHERE 
						CASE
							WHEN ",pBranchID," = 0
							THEN SD.BranchID
							ELSE ",pBranchID,"
						END = SD.BranchID
						AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TS.SaleID,
                        TS.SaleNumber,
                        TS.TransactionDate,
                        MC.CustomerName
                    UNION ALL
                    SELECT
						TB.BookingID,
						'Pemesanan' TransactionType,
                        TB.BookingNumber,
                        DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
                        MC.CustomerName,
                        SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) Total
					FROM
						transaction_booking TB
                        JOIN transaction_bookingdetails BD
							ON TB.BookingID = BD.BookingID
						JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = BD.ItemDetailsID
					WHERE 
						CASE
							WHEN ",pBranchID," = 0
							THEN BD.BranchID
							ELSE ",pBranchID,"
						END = BD.BranchID
						AND CAST(TB.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TB.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TB.BookingID,
                        TB.BookingNumber,
                        TB.TransactionDate,
                        MC.CustomerName
                    UNION ALL
                    SELECT
						TSR.SaleReturnID,
						'Retur' TransactionType,
                        CONCAT('R', TS.SaleNumber),
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        MC.CustomerName,
                        -SUM(SRD.Quantity * SRD.SalePrice) Total
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN transaction_salereturndetails SRD
							ON SRD.SaleReturnID = TSR.SaleReturnID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE 
						CASE
							WHEN ",pBranchID," = 0
							THEN SRD.BranchID
							ELSE ",pBranchID,"
						END = SRD.BranchID
						AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere2, "
                    GROUP BY
						TSR.SaleReturnID,
                        TS.SaleNumber,
                        TSR.TransactionDate,
                        MC.CustomerName
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportCustomerPurchaseReport;

DELIMITER $$
CREATE PROCEDURE spSelExportCustomerPurchaseReport (
	pCustomerID		INT,
	pFromDate		DATE,
	pToDate			DATE,
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
		CALL spInsEventLog(@full_error, 'spSelExportCustomerPurchaseReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		TS.SaleID,
		TS.SaleNumber,
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
		SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) Total
	FROM
		transaction_sale TS
		JOIN transaction_saledetails SD
			ON SD.SaleID = TS.SaleID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
	WHERE 
		TS.CustomerID = pCustomerID
		AND CAST(TS.TransactionDate AS DATE) >= pFromDate
		AND CAST(TS.TransactionDate AS DATE) <= pToDate
	GROUP BY
		TS.SaleID,
		TS.SaleNumber,
		TS.TransactionDate
	UNION ALL
    SELECT
		TB.BookingID,
		TB.BookingNumber,
		DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
		SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) Total
	FROM
		transaction_booking TB
		JOIN transaction_bookingdetails BD
			ON TB.BookingID = BD.BookingID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = BD.ItemDetailsID
	WHERE 
		TB.CustomerID = pCustomerID
		AND CAST(TB.TransactionDate AS DATE) >= pFromDate
		AND CAST(TB.TransactionDate AS DATE) <= pToDate
	GROUP BY
		TB.BookingID,
		TB.BookingNumber,
		TB.TransactionDate
	UNION ALL
	SELECT
		TSR.SaleReturnID,
		CONCAT('R', TS.SaleNumber),
		DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
		-SUM(SRD.Quantity * SRD.SalePrice) Total
	FROM
		transaction_salereturn TSR
		JOIN transaction_sale TS
			ON TS.SaleID = TSR.SaleID
		JOIN transaction_salereturndetails SRD
			ON SRD.SaleReturnID = TSR.SaleReturnID
	WHERE 
		TS.CustomerID = pCustomerID
		AND CAST(TSR.TransactionDate AS DATE) >= pFromDate
		AND CAST(TSR.TransactionDate AS DATE) <= pToDate
	GROUP BY
		TSR.SaleReturnID,
		TS.SaleNumber,
		TSR.TransactionDate
	ORDER BY
		SaleNumber,
        SaleID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelCustomerPurchaseReport;

DELIMITER $$
CREATE PROCEDURE spSelCustomerPurchaseReport (
	pCustomerID		INT,
	pFromDate		DATE,
	pToDate			DATE,
	pWhere 			TEXT,
    pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows,
						SUM(Total) GrandTotal
					FROM
						(
							SELECT
								SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) Total
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = SD.ItemDetailsID
							WHERE 
								TS.CustomerID = ", pCustomerID ,"
								AND CAST(TS.TransactionDate AS DATE) >= '", pFromDate, "'
								AND CAST(TS.TransactionDate AS DATE) <= '", pToDate, "'
								AND ", pWhere, "
							GROUP BY
								TS.SaleID
							UNION ALL
                            SELECT
								SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) Total
							FROM
								transaction_booking TB
								JOIN transaction_bookingdetails BD
									ON BD.BookingID = TB.BookingID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = BD.ItemDetailsID
							WHERE 
								TB.CustomerID = ", pCustomerID ,"
								AND CAST(TB.TransactionDate AS DATE) >= '", pFromDate, "'
								AND CAST(TB.TransactionDate AS DATE) <= '", pToDate, "'
								AND ", pWhere, "
							GROUP BY
								TB.BookingID
							UNION ALL
		                    SELECT
								-SUM(SRD.Quantity * SRD.SalePrice) Total
							FROM
								transaction_salereturn TSR
								JOIN transaction_sale TS
									ON TS.SaleID = TSR.SaleID
		                        JOIN transaction_salereturndetails SRD
									ON SRD.SaleReturnID = TSR.SaleReturnID
							WHERE 
								TS.CustomerID = ", pCustomerID ,"
								AND CAST(TSR.TransactionDate AS DATE) >= '", pFromDate, "'
								AND CAST(TSR.TransactionDate AS DATE) <= '", pToDate, "'
								AND ", pWhere2, "
		                    GROUP BY
								TSR.SaleReturnID
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
						'Penjualan' TransactionType,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        SUM(SD.Quantity * (SD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount)) Total
					FROM
						transaction_sale TS
                        JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SD.ItemDetailsID
					WHERE 
						TS.CustomerID = ", pCustomerID ,"
						AND CAST(TS.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TS.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TS.SaleID,
                        TS.SaleNumber,
                        TS.TransactionDate
                    UNION ALL
                    SELECT
						TB.BookingID,
						'Pemesanan' TransactionType,
                        TB.BookingNumber,
                        DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
                        SUM(BD.Quantity * (BD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - BD.Discount)) Total
					FROM
						transaction_booking TB
                        JOIN transaction_bookingdetails BD
							ON BD.BookingID = TB.BookingID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = BD.ItemDetailsID
					WHERE 
						TB.CustomerID = ", pCustomerID ,"
						AND CAST(TB.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TB.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TB.BookingID,
                        TB.BookingNumber,
                        TB.TransactionDate
                    UNION ALL
                    SELECT
						TSR.SaleReturnID,
						'Retur' TransactionType,
                        CONCAT('R', TS.SaleNumber),
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
						-SUM(SRD.Quantity * SRD.SalePrice) Total
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN transaction_salereturndetails SRD
							ON SRD.SaleReturnID = TSR.SaleReturnID
					WHERE 
						TS.CustomerID = ", pCustomerID ,"
						AND CAST(TSR.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TSR.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere2, "
                    GROUP BY
						TSR.SaleReturnID,
                        TS.SaleNumber,
                        TSR.TransactionDate
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item details by itemCode
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemDetails;

DELIMITER $$
CREATE PROCEDURE spSelItemDetails (
	pItemCode		VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelItemDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MI.ItemID,
        NULL ItemDetailsID,
		MI.ItemCode,
		MI.ItemName,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        MI.UnitID,
        1 ConversionQuantity
	FROM
		master_item MI
	WHERE
		TRIM(MI.ItemCode) = TRIM(pItemCode)
	UNION ALL
    SELECT
		MI.ItemID,
        MID.ItemDetailsID,
		MID.ItemDetailsCode,
		MI.ItemName,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        MID.UnitID,
        MID.ConversionQuantity
	FROM
		master_itemdetails MID
        JOIN master_item MI
			ON MI.ItemID = MID.ItemID
	WHERE
		TRIM(MID.ItemDetailsCode) = TRIM(pItemCode);

SET State = 2;
	
	SELECT
		NULL ItemDetailsID,
        MI.ItemCode,
		MU.UnitID,
		MU.UnitName,
        MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        1 ConversionQuantity
	FROM
		master_unit MU
		JOIN master_item MI
			ON MI.UnitID = MU.UnitID
	WHERE 
        TRIM(MI.ItemCode) = TRIM(pItemCode)
    UNION ALL
    SELECT
		MID.ItemDetailsID,
        MID.ItemDetailsCode,
    	MU.UnitID,
		MU.UnitName,
        MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        MID.ConversionQuantity
    FROM
		master_unit MU
		JOIN master_itemdetails MID
			ON MID.UnitID = MU.UnitID
		JOIN master_item MI
			ON MI.ItemID = MID.ItemID
	WHERE
        TRIM(MI.ItemCode) = TRIM(pItemCode)
	UNION ALL
    SELECT
		NULL ItemDetailsID,
        MI.ItemCode,
		MU.UnitID,
		MU.UnitName,
        MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        1 ConversionQuantity
	FROM
		master_unit MU
		JOIN master_item MI
			ON MI.UnitID = MU.UnitID
		JOIN master_itemdetails MID
			ON MID.ItemID = MI.ItemID
	WHERE 
        TRIM(MID.ItemDetailsCode) = TRIM(pItemCode)
	UNION ALL
    SELECT
		MID.ItemDetailsID,
        MID.ItemDetailsCode,
    	MU.UnitID,
		MU.UnitName,
        MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        MID.ConversionQuantity
    FROM
		master_unit MU
		JOIN master_itemdetails MID
			ON MID.UnitID = MU.UnitID
		JOIN 
        (
			SELECT
				MID.ItemID
			FROM
				master_itemdetails MID
			WHERE
				TRIM(MID.ItemDetailsCode) = TRIM(pItemCode)
        )A
			ON A.ItemID = MID.ItemID
		JOIN master_item MI
			ON MI.ItemID = A.ItemID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item details by itemCode
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemQtyDetails;

DELIMITER $$
CREATE PROCEDURE spSelItemQtyDetails (
	pItemCode		VARCHAR(100),
	pBranchID		INT,
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
		CALL spInsEventLog(@full_error, 'spSelItemQtyDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MI.ItemID,
        NULL ItemDetailsID,
		MI.ItemCode,
		MI.ItemName,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        MI.UnitID,
        1 ConversionQty,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)), 2) Stock,
        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)), 2) StockNoConversion
	FROM
		master_item MI
        LEFT JOIN
		(
			SELECT
				FSD.ItemID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststockdetails FSD
                LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = FSD.BranchID
			GROUP BY
				FSD.ItemID
		)FS
			ON FS.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				TPD.ItemID,
				SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasedetails TPD
                LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = TPD.BranchID
			GROUP BY
				TPD.ItemID
		)TP
			ON TP.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SRD.ItemID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturndetails SRD
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SRD.BranchID
			GROUP BY
				SRD.ItemID
		)SR
			ON SR.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SD.ItemID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_saledetails SD
				LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SD.BranchID
                /*AND TS.FinishFlag = 1*/
			GROUP BY
				SD.ItemID
		)S
			ON S.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PRD.ItemID,
				SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasereturndetails PRD
                LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = PRD.BranchID
			GROUP BY
				PRD.ItemID
		)PR
			ON MI.ItemID = PR.ItemID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
                LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SMD.DestinationID
			GROUP BY
				SMD.ItemID,
				SMD.DestinationID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
        (
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SMD.SourceID
			GROUP BY
				SMD.ItemID
		)SMM
			ON MI.ItemID = SMM.ItemID
		LEFT JOIN
		(
			SELECT
				SAD.ItemID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjustdetails SAD
                LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SAD.BranchID
			GROUP BY
				SAD.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
               LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				pBranchID = BD.BranchID
			GROUP BY
				BD.ItemID
		)B
			ON B.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pickdetails PD
                LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = PD.BranchID
			GROUP BY
				PD.ItemID
		)P
			ON P.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				pBranchID = PD.BranchID
				/*AND TB.FinishFlag = 1*/
			GROUP BY
				BD.ItemID
		)BN
			ON BN.ItemID = MI.ItemID
	WHERE
		TRIM(MI.ItemCode) = TRIM(pItemCode)
	UNION ALL
    SELECT
		MI.ItemID,
        MID.ItemDetailsID,
		MID.ItemDetailsCode,
		MI.ItemName,
		MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        MID.UnitID,
        MID.ConversionQuantity,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)), 2) StockNoConversion
	FROM
		master_itemdetails MID
        JOIN master_item MI
			ON MI.ItemID = MID.ItemID
		LEFT JOIN
		(
			SELECT
				FSD.ItemID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststockdetails FSD
				LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = FSD.BranchID
			GROUP BY
				FSD.ItemID
		)FS
			ON FS.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				TPD.ItemID,
				SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasedetails TPD
                LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = TPD.BranchID
			GROUP BY
				TPD.ItemID
		)TP
			ON TP.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SRD.ItemID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturndetails SRD
                LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SRD.BranchID
			GROUP BY
				SRD.ItemID
		)SR
			ON SR.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SD.ItemID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_saledetails SD
				LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SD.BranchID
                /*AND TS.FinishFlag = 1*/
			GROUP BY
				SD.ItemID
		)S
			ON S.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PRD.ItemID,
				SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasereturndetails PRD
				LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = PRD.BranchID
			GROUP BY
				PRD.ItemID
		)PR
			ON MI.ItemID = PR.ItemID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
                LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SMD.DestinationID
			GROUP BY
				SMD.ItemID,
				SMD.DestinationID
		)SM
			ON MI.ItemID = SM.ItemID
		LEFT JOIN
        (
			SELECT
				SMD.ItemID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SMD.SourceID
			GROUP BY
				SMD.ItemID
		)SMM
			ON MI.ItemID = SMM.ItemID
		LEFT JOIN
		(
			SELECT
				SAD.ItemID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjustdetails SAD
                LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = SAD.BranchID
			GROUP BY
				SAD.ItemID
		)SA
			ON MI.ItemID = SA.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
                LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				pBranchID = BD.BranchID
			GROUP BY
				BD.ItemID,
				BD.BranchID
		)B
			ON B.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pickdetails PD
                LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				pBranchID = PD.BranchID
			GROUP BY
				PD.ItemID
		)P
			ON P.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				pBranchID = PD.BranchID
				/*AND TB.FinishFlag = 1*/
			GROUP BY
				BD.ItemID
		)BN
			ON BN.ItemID = MI.ItemID
	WHERE
		TRIM(MID.ItemDetailsCode) = TRIM(pItemCode);

SET State = 2;
	SELECT
		NULL ItemDetailsID,
        MI.ItemCode,
		MU.UnitID,
		MU.UnitName,
        MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        1 ConversionQuantity
	FROM
		master_unit MU
		JOIN master_item MI
			ON MI.UnitID = MU.UnitID
	WHERE 
        TRIM(MI.ItemCode) = TRIM(pItemCode)
    UNION ALL
    SELECT
		MID.ItemDetailsID,
        MID.ItemDetailsCode,
    	MU.UnitID,
		MU.UnitName,
        MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        MID.ConversionQuantity
    FROM
		master_unit MU
		JOIN master_itemdetails MID
			ON MID.UnitID = MU.UnitID
		JOIN master_item MI
			ON MI.ItemID = MID.ItemID
	WHERE
        TRIM(MI.ItemCode) = TRIM(pItemCode)
	UNION ALL
    SELECT
		NULL ItemDetailsID,
        MI.ItemCode,
		MU.UnitID,
		MU.UnitName,
        MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        1 ConversionQuantity
	FROM
		master_unit MU
		JOIN master_item MI
			ON MI.UnitID = MU.UnitID
		JOIN master_itemdetails MID
			ON MID.ItemID = MI.ItemID
	WHERE 
        TRIM(MID.ItemDetailsCode) = TRIM(pItemCode)
	UNION ALL
    SELECT
		MID.ItemDetailsID,
        MID.ItemDetailsCode,
    	MU.UnitID,
		MU.UnitName,
        MI.BuyPrice,
		MI.RetailPrice,
		MI.Price1,
		MI.Qty1,
		MI.Price2,
		MI.Qty2,
		MI.Weight,
        MID.ConversionQuantity
    FROM
		master_unit MU
		JOIN master_itemdetails MID
			ON MID.UnitID = MU.UnitID
		JOIN 
        (
			SELECT
				MID.ItemID
			FROM
				master_itemdetails MID
			WHERE
				TRIM(MID.ItemDetailsCode) = TRIM(pItemCode)
        )A
			ON A.ItemID = MID.ItemID
		JOIN master_item MI
			ON MI.ItemID = A.ItemID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelExportStockReport;

DELIMITER $$
CREATE PROCEDURE spSelExportStockReport (
	pCategoryID		BIGINT,
	pBranchID 		INT,
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
		CALL spInsEventLog(@full_error, 'spSelExportStockReport', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		MI.ItemCode,
		MI.ItemName,
        MC.CategoryID,
		MC.CategoryName,
		MB.BranchName,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)), 2) Stock,
        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)), 2) PhysicalStock,
        MU.UnitName
	FROM
		master_item MI
		CROSS JOIN master_branch MB
		JOIN master_category MC
			ON MC.CategoryID = MI.CategoryID
		JOIN master_unit MU
			ON MU.UnitID = MI.UnitID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				FSD.BranchID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststockdetails FSD
				JOIN master_item MI
					ON FSD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN FSD.BranchID
						ELSE pBranchID
					END = FSD.BranchID
			GROUP BY
				MI.ItemID,
				FSD.BranchID
		)FS
			ON FS.ItemID = MI.ItemID
			AND MB.BranchID = FS.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				TPD.BranchID,
				SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasedetails TPD
				JOIN master_item MI
					ON TPD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN TPD.BranchID
						ELSE pBranchID
					END = TPD.BranchID
			GROUP BY
				MI.ItemID,
				TPD.BranchID
		)TP
			ON TP.ItemID = MI.ItemID
			AND MB.BranchID = TP.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SRD.BranchID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturndetails SRD
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SRD.BranchID
						ELSE pBranchID
					END = SRD.BranchID
			GROUP BY
				MI.ItemID,
				SRD.BranchID
		)SR
			ON SR.ItemID = MI.ItemID
			AND SR.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SD.BranchID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_saledetails SD
                JOIN transaction_sale TS
					ON TS.SaleID = SD.SaleID
				JOIN master_item MI
					ON SD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SD.BranchID
						ELSE pBranchID
					END = SD.BranchID
				AND TS.FinishFlag = 1
			GROUP BY
				MI.ItemID,
				SD.BranchID
		)S
			ON S.ItemID = MI.ItemID
			AND S.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				PRD.BranchID,
				SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasereturndetails PRD
				JOIN master_item MI
					ON MI.ItemID = PRD.ItemID
				LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN PRD.BranchID
						ELSE pBranchID
					END = PRD.BranchID
			GROUP BY
				MI.ItemID,
				PRD.BranchID
		)PR
			ON MI.ItemID = PR.ItemID
			AND PR.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SMD.DestinationID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SMD.DestinationID
						ELSE pBranchID
					END = SMD.DestinationID
			GROUP BY
				MI.ItemID,
				SMD.DestinationID
		)SM
			ON MI.ItemID = SM.ItemID
			AND SM.DestinationID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SMD.SourceID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SMD.SourceID
						ELSE pBranchID
					END = SMD.SourceID
			GROUP BY
				SMD.ItemID,
				SMD.SourceID
		)SMM
			ON MI.ItemID = SMM.ItemID
			AND SMM.SourceID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SAD.BranchID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjustdetails SAD
				JOIN master_item MI
					ON MI.ItemID = SAD.ItemID
				LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SAD.BranchID
						ELSE pBranchID
					END = SAD.BranchID
			GROUP BY
				MI.ItemID,
				SAD.BranchID
		)SA
			ON MI.ItemID = SA.ItemID
			AND SA.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				BD.BranchID,
				SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
                JOIN transaction_booking TB
					ON TB.BookingID = BD.BookingID
				JOIN master_item MI
					ON MI.ItemID = BD.ItemID
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN BD.BranchID
						ELSE pBranchID
					END = BD.BranchID
				AND TB.FinishFlag = 1
			GROUP BY
				BD.ItemID,
				BD.BranchID
		)B
			ON B.ItemID = MI.ItemID
			AND B.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				PD.BranchID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pickdetails PD
				JOIN master_item MI
					ON MI.ItemID = PD.ItemID
				LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN PD.BranchID
						ELSE pBranchID
					END = PD.BranchID
			GROUP BY
				PD.ItemID,
				PD.BranchID
		)P
			ON P.ItemID = MI.ItemID
			AND P.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				PD.BranchID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			GROUP BY
				BD.ItemID,
				PD.BranchID
		)BN
			ON BN.ItemID = MI.ItemID
			AND BN.BranchID = MB.BranchID
	WHERE
		CASE
			WHEN pCategoryID = 0
			THEN MC.CategoryID
			ELSE pCategoryID
		END = MC.CategoryID
		AND CASE
				WHEN pBranchID = 0
				THEN MB.BranchID
				ELSE pBranchID
			END = MB.BranchID
	UNION ALL
	SELECT
		MID.ItemDetailsCode,
		MI.ItemName,
        MC.CategoryID,
		MC.CategoryName,
		MB.BranchName,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
		ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity, 2) PhysicalStock,
		MU.UnitName
	FROM
		master_itemdetails MID
		CROSS JOIN master_branch MB
		JOIN master_unit MU
			ON MU.UnitID = MID.UnitID
		JOIN master_item MI
			ON MI.ItemID = MID.ItemID
		JOIN master_category MC
			ON MC.CategoryID = MI.CategoryID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				FSD.BranchID,
				SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_firststockdetails FSD
				JOIN master_item MI
					ON FSD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON FSD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN FSD.BranchID
						ELSE pBranchID
					END = FSD.BranchID
			GROUP BY
				MI.ItemID,
				FSD.BranchID
		)FS
			ON FS.ItemID = MI.ItemID
			AND MB.BranchID = FS.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				TPD.BranchID,
				SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasedetails TPD
				JOIN master_item MI
					ON TPD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON TPD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN TPD.BranchID
						ELSE pBranchID
					END = TPD.BranchID
			GROUP BY
				MI.ItemID,
				TPD.BranchID
		)TP
			ON TP.ItemID = MI.ItemID
			AND MB.BranchID = TP.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SRD.BranchID,
				SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_salereturndetails SRD
				JOIN master_item MI
					ON SRD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON SRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SRD.BranchID
						ELSE pBranchID
					END = SRD.BranchID
			GROUP BY
				MI.ItemID,
				SRD.BranchID
		)SR
			ON SR.ItemID = MI.ItemID
			AND SR.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SD.BranchID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_saledetails SD
                JOIN transaction_sale TS
					ON TS.SaleID = SD.SaleID
				JOIN master_item MI
					ON SD.ItemID = MI.ItemID
				LEFT JOIN master_itemdetails MID
					ON SD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SD.BranchID
						ELSE pBranchID
					END = SD.BranchID
				AND TS.FinishFlag = 1
			GROUP BY
				MI.ItemID,
				SD.BranchID
		)S
			ON S.ItemID = MI.ItemID
			AND S.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				PRD.BranchID,
				SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_purchasereturndetails PRD
				JOIN master_item MI
					ON MI.ItemID = PRD.ItemID
				LEFT JOIN master_itemdetails MID
					ON PRD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN PRD.BranchID
						ELSE pBranchID
					END = PRD.BranchID
			GROUP BY
				MI.ItemID,
				PRD.BranchID
		)PR
			ON MI.ItemID = PR.ItemID
			AND PR.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SMD.DestinationID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SMD.DestinationID
						ELSE pBranchID
					END = SMD.DestinationID
			GROUP BY
				MI.ItemID,
				SMD.DestinationID
		)SM
			ON MI.ItemID = SM.ItemID
			AND SM.DestinationID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				SMD.ItemID,
				SMD.SourceID,
				SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockmutationdetails SMD
				JOIN master_item MI
					ON MI.ItemID = SMD.ItemID
				LEFT JOIN master_itemdetails MID
					ON SMD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SMD.SourceID
						ELSE pBranchID
					END = SMD.SourceID
			GROUP BY
				SMD.ItemID,
				SMD.SourceID
		)SMM
			ON MI.ItemID = SMM.ItemID
			AND SMM.SourceID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				MI.ItemID,
				SAD.BranchID,
				SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_stockadjustdetails SAD
				JOIN master_item MI
					ON MI.ItemID = SAD.ItemID
				LEFT JOIN master_itemdetails MID
					ON SAD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN SAD.BranchID
						ELSE pBranchID
					END = SAD.BranchID
			GROUP BY
				MI.ItemID,
				SAD.BranchID
		)SA
			ON MI.ItemID = SA.ItemID
			AND SA.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				BD.BranchID,
				SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
                JOIN transaction_booking TB
					ON TB.BookingID = BD.BookingID
				JOIN master_item MI
					ON MI.ItemID = BD.ItemID
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN BD.BranchID
						ELSE pBranchID
					END = BD.BranchID
				AND TB.FinishFlag = 1
			GROUP BY
				BD.ItemID,
				BD.BranchID
		)B
			ON B.ItemID = MI.ItemID
			AND B.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				PD.BranchID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_pickdetails PD
				JOIN master_item MI
					ON MI.ItemID = PD.ItemID
				LEFT JOIN master_itemdetails MID
					ON PD.ItemDetailsID = MID.ItemDetailsID
			WHERE
				CASE
					WHEN pCategoryID = 0
					THEN MI.CategoryID
					ELSE pCategoryID
				END = MI.CategoryID
				AND CASE
						WHEN pBranchID = 0
						THEN PD.BranchID
						ELSE pBranchID
					END = PD.BranchID
			GROUP BY
				PD.ItemID,
				PD.BranchID
		)P
			ON P.ItemID = MI.ItemID
			AND P.BranchID = MB.BranchID
		LEFT JOIN
		(
			SELECT
				BD.ItemID,
				PD.BranchID,
				SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
				LEFT JOIN transaction_pickdetails PD
					ON PD.BookingDetailsID = BD.BookingDetailsID
					AND PD.BranchID <> BD.BranchID
			GROUP BY
				BD.ItemID,
				PD.BranchID
		)BN
			ON BN.ItemID = MI.ItemID
			AND BN.BranchID = MB.BranchID
	WHERE
		CASE
			WHEN pCategoryID = 0
			THEN MC.CategoryID
			ELSE pCategoryID
		END = MC.CategoryID
		AND CASE
				WHEN pBranchID = 0
				THEN MB.BranchID
				ELSE pBranchID
			END = MB.BranchID
	ORDER BY
		CategoryID ASC,
        ItemCode ASC;
                    
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockReport;

DELIMITER $$
CREATE PROCEDURE spSelStockReport (
	pCategoryID		BIGINT,
	pBranchID 		INT,
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelStockReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								1
							FROM
								master_item MI
                                CROSS JOIN master_branch MB
								JOIN master_category MC
									ON MC.CategoryID = MI.CategoryID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MC.CategoryID
									ELSE ", pCategoryID,"
								END = MC.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN MB.BranchID
										ELSE ",pBranchID,"
									END = MB.BranchID AND ", pWhere, "
                            UNION ALL
                            SELECT
								1
							FROM
								master_itemdetails MID
                                CROSS JOIN master_branch MB
                                JOIN master_item MI
									ON MI.ItemID = MID.ItemID
								JOIN master_category MC
									ON MC.CategoryID = MI.CategoryID
							 WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MC.CategoryID
									ELSE ", pCategoryID,"
								END = MC.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN MB.BranchID
										ELSE ",pBranchID,"
									END = MB.BranchID AND ", pWhere, "
						)A"
					);
					
                    
                   
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MI.ItemCode,
						MI.ItemName,
						MC.CategoryName,
						MB.BranchName,
						ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)), 2) Stock,
                        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)), 2) PhysicalStock,
						MU.UnitName
					FROM
						master_item MI
						CROSS JOIN master_branch MB
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						JOIN master_unit MU
							ON MI.UnitID = MU.UnitID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								FSD.BranchID,
								SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_firststockdetails FSD
								JOIN master_item MI
									ON FSD.ItemID = MI.ItemID
								LEFT JOIN master_itemdetails MID
									ON FSD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ",pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN FSD.BranchID
										ELSE ",pBranchID,"
									END = FSD.BranchID
							GROUP BY
								MI.ItemID,
								FSD.BranchID
						)FS
							ON FS.ItemID = MI.ItemID
							AND MB.BranchID = FS.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								TPD.BranchID,
								SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasedetails TPD
								JOIN master_item MI
									ON TPD.ItemID = MI.ItemID
								LEFT JOIN master_itemdetails MID
									ON TPD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ",pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN TPD.BranchID
										ELSE ",pBranchID,"
									END = TPD.BranchID
							GROUP BY
								MI.ItemID,
								TPD.BranchID
						)TP
							ON TP.ItemID = MI.ItemID
							AND MB.BranchID = TP.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SRD.BranchID,
								SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_salereturndetails SRD
								JOIN master_item MI
									ON SRD.ItemID = MI.ItemID
								LEFT JOIN master_itemdetails MID
									ON SRD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SRD.BranchID
										ELSE ",pBranchID,"
									END = SRD.BranchID
							GROUP BY
								MI.ItemID,
								SRD.BranchID
						)SR
							ON SR.ItemID = MI.ItemID
							AND SR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SD.BranchID,
								SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_saledetails SD
                                JOIN transaction_sale TS
									ON TS.SaleID = SD.SaleID
								JOIN master_item MI
									ON SD.ItemID = MI.ItemID
								LEFT JOIN master_itemdetails MID
									ON SD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SD.BranchID
										ELSE ",pBranchID,"
									END = SD.BranchID
								AND TS.FinishFlag = 1
							GROUP BY
								MI.ItemID,
								SD.BranchID
						)S
							ON S.ItemID = MI.ItemID
							AND S.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								PRD.BranchID,
								SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasereturndetails PRD
								JOIN master_item MI
									ON MI.ItemID = PRD.ItemID
								LEFT JOIN master_itemdetails MID
									ON PRD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN PRD.BranchID
										ELSE ",pBranchID,"
									END = PRD.BranchID
							GROUP BY
								MI.ItemID,
								PRD.BranchID
						)PR
							ON MI.ItemID = PR.ItemID
							AND PR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SMD.DestinationID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
								JOIN master_item MI
									ON MI.ItemID = SMD.ItemID
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SMD.DestinationID
										ELSE ",pBranchID,"
									END = SMD.DestinationID
							GROUP BY
								MI.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
							AND SM.DestinationID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
                                SMD.SourceID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
                                JOIN master_item MI
									ON MI.ItemID = SMD.ItemID
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SMD.SourceID
										ELSE ",pBranchID,"
									END = SMD.SourceID
							GROUP BY
								SMD.ItemID,
                                SMD.SourceID
						)SMM
							ON MI.ItemID = SMM.ItemID
							AND SMM.SourceID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SAD.BranchID,
								SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockadjustdetails SAD
								JOIN master_item MI
									ON MI.ItemID = SAD.ItemID
								LEFT JOIN master_itemdetails MID
									ON SAD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SAD.BranchID
										ELSE ",pBranchID,"
									END = SAD.BranchID
							GROUP BY
								MI.ItemID,
								SAD.BranchID
						)SA
							ON MI.ItemID = SA.ItemID
							AND SA.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								BD.BranchID,
								SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                JOIN transaction_booking TB
									ON TB.BookingID = BD.BookingID
                                JOIN master_item MI
									ON MI.ItemID = BD.ItemID
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
                                    AND PD.BranchID <> BD.BranchID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN BD.BranchID
										ELSE ",pBranchID,"
									END = BD.BranchID
								AND TB.FinishFlag = 1
							GROUP BY
								BD.ItemID,
								BD.BranchID
						)B
							ON B.ItemID = MI.ItemID
							AND B.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								PD.ItemID,
								PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_pickdetails PD
                                JOIN master_item MI
									ON MI.ItemID = PD.ItemID
                                LEFT JOIN master_itemdetails MID
									ON PD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN PD.BranchID
										ELSE ",pBranchID,"
									END = PD.BranchID
							GROUP BY
								PD.ItemID,
								PD.BranchID
						)P
							ON P.ItemID = MI.ItemID
							AND P.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
                                PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							GROUP BY
								BD.ItemID,
                                PD.BranchID
						)BN
							ON BN.ItemID = MI.ItemID
                            AND BN.BranchID = MB.BranchID
					WHERE
						CASE
							WHEN ", pCategoryID," = 0
							THEN MC.CategoryID
							ELSE ", pCategoryID,"
						END = MC.CategoryID
						AND CASE
								WHEN ",pBranchID," = 0
								THEN MB.BranchID
								ELSE ",pBranchID,"
							END = MB.BranchID AND ", pWhere, 
					"UNION ALL
                    SELECT
						MID.ItemDetailsCode,
						MI.ItemName,
						MC.CategoryName,
						MB.BranchName,
						ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
                        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity, 2) PhysicalStock,
                        MU.UnitName
					FROM
						master_itemdetails MID
                        CROSS JOIN master_branch MB
                        JOIN master_unit MU
							ON MU.UnitID = MID.UnitID
                        JOIN master_item MI
							ON MI.ItemID = MID.ItemID
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								FSD.BranchID,
								SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_firststockdetails FSD
								JOIN master_item MI
									ON FSD.ItemID = MI.ItemID
								LEFT JOIN master_itemdetails MID
									ON FSD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ",pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN FSD.BranchID
										ELSE ",pBranchID,"
									END = FSD.BranchID
							GROUP BY
								MI.ItemID,
								FSD.BranchID
						)FS
							ON FS.ItemID = MI.ItemID
							AND MB.BranchID = FS.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								TPD.BranchID,
								SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasedetails TPD
								JOIN master_item MI
									ON TPD.ItemID = MI.ItemID
								LEFT JOIN master_itemdetails MID
									ON TPD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ",pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN TPD.BranchID
										ELSE ",pBranchID,"
									END = TPD.BranchID
							GROUP BY
								MI.ItemID,
								TPD.BranchID
						)TP
							ON TP.ItemID = MI.ItemID
							AND MB.BranchID = TP.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SRD.BranchID,
								SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_salereturndetails SRD
								JOIN master_item MI
									ON SRD.ItemID = MI.ItemID
								LEFT JOIN master_itemdetails MID
									ON SRD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SRD.BranchID
										ELSE ",pBranchID,"
									END = SRD.BranchID
							GROUP BY
								MI.ItemID,
								SRD.BranchID
						)SR
							ON SR.ItemID = MI.ItemID
							AND SR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SD.BranchID,
								SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_saledetails SD
								JOIN transaction_sale TS
									ON TS.SaleID = SD.SaleID
								JOIN master_item MI
									ON SD.ItemID = MI.ItemID
								LEFT JOIN master_itemdetails MID
									ON SD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SD.BranchID
										ELSE ",pBranchID,"
									END = SD.BranchID
								AND TS.FinishFlag = 1
							GROUP BY
								MI.ItemID,
								SD.BranchID
						)S
							ON S.ItemID = MI.ItemID
							AND S.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								PRD.BranchID,
								SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasereturndetails PRD
								JOIN master_item MI
									ON MI.ItemID = PRD.ItemID
								LEFT JOIN master_itemdetails MID
									ON PRD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN PRD.BranchID
										ELSE ",pBranchID,"
									END = PRD.BranchID
							GROUP BY
								MI.ItemID,
								PRD.BranchID
						)PR
							ON MI.ItemID = PR.ItemID
							AND PR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SMD.DestinationID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
								JOIN master_item MI
									ON MI.ItemID = SMD.ItemID
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SMD.DestinationID
										ELSE ",pBranchID,"
									END = SMD.DestinationID
							GROUP BY
								MI.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
							AND SM.DestinationID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
                                SMD.SourceID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
                                JOIN master_item MI
									ON MI.ItemID = SMD.ItemID
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SMD.SourceID
										ELSE ",pBranchID,"
									END = SMD.SourceID
							GROUP BY
								SMD.ItemID,
                                SMD.SourceID
						)SMM
							ON MI.ItemID = SMM.ItemID
							AND SMM.SourceID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								MI.ItemID,
								SAD.BranchID,
								SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockadjustdetails SAD
								JOIN master_item MI
									ON MI.ItemID = SAD.ItemID
								LEFT JOIN master_itemdetails MID
									ON SAD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN SAD.BranchID
										ELSE ",pBranchID,"
									END = SAD.BranchID
							GROUP BY
								MI.ItemID,
								SAD.BranchID
						)SA
							ON MI.ItemID = SA.ItemID
							AND SA.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								BD.BranchID,
								SUM((BD.Quantity - IFNULL(PD.Quantity,0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                JOIN transaction_booking TB
									ON TB.BookingID = BD.BookingID
                                JOIN master_item MI
									ON MI.ItemID = BD.ItemID
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
                                    AND PD.BranchID <> BD.BranchID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN BD.BranchID
										ELSE ",pBranchID,"
									END = BD.BranchID
								AND TB.FinishFlag = 1
							GROUP BY
								BD.ItemID,
								BD.BranchID
						)B
							ON B.ItemID = MI.ItemID
							AND B.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								PD.ItemID,
								PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_pickdetails PD
                                JOIN master_item MI
									ON MI.ItemID = PD.ItemID
                                LEFT JOIN master_itemdetails MID
									ON PD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ", pCategoryID," = 0
									THEN MI.CategoryID
									ELSE ", pCategoryID,"
								END = MI.CategoryID
								AND CASE
										WHEN ",pBranchID," = 0
										THEN PD.BranchID
										ELSE ",pBranchID,"
									END = PD.BranchID
							GROUP BY
								PD.ItemID,
								PD.BranchID
						)P
							ON P.ItemID = MI.ItemID
							AND P.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
                                PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							GROUP BY
								BD.ItemID,
                                PD.BranchID
						)BN
							ON BN.ItemID = MI.ItemID
                            AND BN.BranchID = MB.BranchID
					WHERE
						CASE
							WHEN ", pCategoryID," = 0
							THEN MC.CategoryID
							ELSE ", pCategoryID,"
						END = MC.CategoryID
						AND CASE
								WHEN ",pBranchID," = 0
								THEN MB.BranchID
								ELSE ",pBranchID,"
							END = MB.BranchID AND ", pWhere, 
                    " ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
                    
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBooking;

DELIMITER $$
CREATE PROCEDURE spSelBooking (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelBooking', pCurrentUser);
	END;
	
SET State = 1;

	DELETE FROM 
		transaction_booking
    WHERE
		FinishFlag = 0
        AND DATE_FORMAT(TransactionDate, '%Y-%m-%d') <> DATE_FORMAT(NOW(), '%Y-%m-%d');

SET State = 2;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_booking TB
                        JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TB.BookingID,
                        TB.BookingNumber,
                        DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TB.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TBD.Total, 0) Total,
						IFNULL(TBD.Weight, 0) Weight,
						TB.RetailFlag,
                        IFNULL(TB.Payment, 0) Payment,
                        IFNULL(PT.PaymentTypeName, '') PaymentTypeName,
                        CASE
							WHEN FinishFlag = 0
                            THEN 'Belum Selesai'
                            ELSE 'Selesai'
						END Status,
                        IFNULL(PT.PaymentTypeID, 1) PaymentTypeID
					FROM
						transaction_booking TB
                        JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
						LEFT JOIN master_paymenttype PT
							ON PT.PaymentTypeID = TB.PaymentTypeID
						LEFT JOIN
                        (
							SELECT
								TB.BookingID,
                                SUM(TBD.Quantity * (TBD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - TBD.Discount)) Total,
								SUM(TBD.Quantity * MI.Weight * IFNULL(MID.ConversionQuantity, 1)) Weight
							FROM
								transaction_booking TB
                                JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
                                LEFT JOIN transaction_bookingdetails TBD
									ON TB.BookingID = TBD.BookingID
								LEFT JOIN master_item MI
									ON MI.ItemID = TBD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TBD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TB.BookingID
                        )TBD
							ON TBD.BookingID = TB.BookingID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking details by BookingID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBookingDetails;

DELIMITER $$
CREATE PROCEDURE spSelBookingDetails (
	pBookingID			BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelBookingDetails', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		SD.BookingDetailsID,
        SD.ItemID,
        SD.BranchID,
        MB.BranchName,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        SD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID) UnitID,
        MU.UnitName,
        SD.BuyPrice,
		IFNULL(MID.ConversionQuantity, 1) * SD.BookingPrice BookingPrice,
		SD.Discount,
		MI.RetailPrice,
        MI.Price1,
        MI.Qty1,
        MI.Price2,
        MI.Qty2,
		MI.Weight,
        CONCAT('[', GROUP_CONCAT(AU.AvailableUnit SEPARATOR ', '), ']') AvailableUnit,
        SD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1) ConversionQty
	FROM
		transaction_bookingdetails SD
        JOIN master_branch MB
			ON MB.BranchID = SD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		JOIN 
        (
			SELECT
				MI.ItemID,
				CONCAT('[', 
							MU.UnitID, ',"', 
                            MU.UnitName, '", 
                            "NULL", "', 
                            MI.ItemCode, '", ', 
                            MI.BuyPrice, ', ', 
                            MI.RetailPrice, ', ', 
                            MI.Price1, ', ', 
                            MI.Price2, ', ', 
                            MI.Qty1, ', ', 
                            MI.Qty2, ', ',
                            1,
						']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', 
							MU.UnitID, ',"', 
                            MU.UnitName, '",', 
                            MID.ItemDetailsID, ',"', 
                            MID.ItemDetailsCode, '", ', 
                            MI.BuyPrice, ', ', 
                            MI.RetailPrice, ', ', 
                            MI.Price1, ', ', 
                            MI.Price2, ', ', 
                            MI.Qty1, ', ', 
                            MI.Qty2, ', ', 
                            MID.ConversionQuantity,
						']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_itemdetails MID
					ON MID.UnitID = MU.UnitID
				JOIN master_item MI
					ON MI.ItemID = MID.ItemID
			GROUP BY
				MID.ItemDetailsID
		)AU
			ON AU.ItemID = SD.ItemID
	WHERE
		SD.BookingID = pBookingID
	GROUP BY
		SD.BookingDetailsID,
        SD.ItemID,
        SD.BranchID,
        MB.BranchName,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        SD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID),
        SD.BuyPrice,
        SD.BookingPrice,
		SD.Discount,
		MI.RetailPrice,
        MI.Price1,
        MI.Qty1,
        MI.Price2,
        MI.Qty2,
		MI.Weight,
        SD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1)
	ORDER BY
		SD.BookingDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking details by BookingNumber
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBookingDetailsByNumber;

DELIMITER $$
CREATE PROCEDURE spSelBookingDetailsByNumber (
	pBookingNumber	VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelBookingDetailsByNumber', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TS.BookingID,
		SD.BookingDetailsID,
        SD.ItemID,
        IFNULL(MID.ItemDetailsID, '') ItemDetailsID,
        SD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        SD.Quantity - IFNULL(TSR.Quantity, 0) Quantity,
        SD.BuyPrice,
        SD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - SD.Discount BookingPrice,
        SD.Discount,
        MC.CustomerName,
        MU.UnitName,
        IFNULL(MID.ConversionQuantity, 1) ConversionQuantity
	FROM
		transaction_booking TS
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN transaction_bookingdetails SD
			ON TS.BookingID = SD.BookingID
        JOIN master_branch MB
			ON MB.BranchID = SD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON IFNULL(MID.UnitID, MI.UnitID) = MU.UnitID
		LEFT JOIN
		(
			SELECT
				SR.BookingID,
				SRD.ItemID,
				SRD.BookingDetailsID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_pick SR
				JOIN transaction_pickdetails SRD
					ON SR.PickID = SRD.PickID
			GROUP BY
				SR.BookingID,
				SRD.ItemID,
				SRD.BookingDetailsID
		)TSR
			ON TSR.BookingID = TS.BookingID
			AND MI.ItemID = TSR.ItemID
			AND TSR.BookingDetailsID = SD.BookingDetailsID
	WHERE
		TRIM(TS.BookingNumber) = TRIM(pBookingNumber)
	ORDER BY
		SD.BookingDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBookingDetailsPrint;

DELIMITER $$
CREATE PROCEDURE spSelBookingDetailsPrint (
	pBookingDetailsID	TEXT,
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
		CALL spInsEventLog(@full_error, 'spSelBookingDetailsPrint', pCurrentUser);
	END;
	
SET State = 1;

	IF(pBookingDetailsID <> "" ) THEN
		SET @query = CONCAT("SELECT
								BD.Quantity,
								MI.ItemName,
                                MU.UnitName
							 FROM
								transaction_bookingdetails BD
								JOIN master_item MI
									ON MI.ItemID = BD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = BD.ItemDetailsID
								JOIN master_unit MU
									ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
							WHERE
								BD.BookingDetailsID IN ", pBookingDetailsID );
							
		PREPARE stmt FROM @query;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
SET State = 2;
		SET @query = CONCAT("UPDATE transaction_bookingdetails
							SET
								PrintCount = IFNULL(PrintCount, 0) + 1,
								PrintedDate = NOW()
							WHERE
								BookingDetailsID IN ", pBookingDetailsID );
							
		PREPARE stmt FROM @query;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
	END IF;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBookingHeader;

DELIMITER $$
CREATE PROCEDURE spSelBookingHeader (
	pBookingID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelBookingHeader', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TB.BookingNumber,
		DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
		TB.CreatedBy,
		MC.CustomerName,
		MC.Address,
		MC.City,
		MC.Telephone
	FROM
		transaction_booking TB
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
	WHERE
		TB.BookingID = pBookingID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelDebtPayment;

DELIMITER $$
CREATE PROCEDURE spSelDebtPayment (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelDebtPayment', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_purchase TP
						JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
					WHERE 
						TP.PaymentTypeID = 2
						AND ", pWhere
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TP.PurchaseID,
						TP.PurchaseNumber,
						DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
						TP.TransactionDate PlainTransactionDate,
						MS.SupplierID,
						MS.SupplierName,
						SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total,
						IFNULL(TPM.Amount, 0) TotalPayment,
					    SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) - IFNULL(TPM.Amount, 0) Debit,
                        'P' TransactionType
					FROM
						transaction_purchase TP
						JOIN transaction_purchasedetails PD
							ON TP.PurchaseID = PD.PurchaseID
						JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = PD.ItemDetailsID
						LEFT JOIN
						(
							SELECT
								PD.TransactionID,
								SUM(PD.Amount) Amount
							FROM
								transaction_paymentdetails PD
							WHERE
								PD.TransactionType = 'P'
							GROUP BY
								TransactionID
						)TPM
							ON TPM.TransactionID = TP.PurchaseID
					WHERE 
						TP.PaymentTypeID = 2
						AND ", pWhere, "
					GROUP BY
						TP.PurchaseID,
						TP.PurchaseNumber,
						TP.TransactionDate,
						MS.SupplierID,
						MS.SupplierName,
						IFNULL(TPM.Amount, 0)
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
	                
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select firststock transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelFirstStock;

DELIMITER $$
CREATE PROCEDURE spSelFirstStock (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelFirstStock', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_firststock FS
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						FS.FirstStockID,
                        FS.FirstStockNumber,
                        DATE_FORMAT(FS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        FS.TransactionDate PlainTransactionDate,
                        IFNULL(FSD.Total, 0) Total
					FROM
						transaction_firststock FS
						LEFT JOIN
                        (
							SELECT
								FS.FirstStockID,
                                SUM(FSD.Quantity * FSD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total
							FROM
								transaction_firststock FS
                                LEFT JOIN transaction_firststockdetails FSD
									ON FS.FirstStockID = FSD.FirstStockID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = FSD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								FS.FirstStockID
                        )FSD
							ON FSD.FirstStockID = FS.FirstStockID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select firststock details by FirstStockID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelFirstStockDetails;

DELIMITER $$
CREATE PROCEDURE spSelFirstStockDetails (
	pFirstStockID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelFirstStockDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		PD.FirstStockDetailsID,
        PD.ItemID,
        PD.BranchID,
        CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        PD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID) UnitID,
        MU.UnitName,
		IFNULL(MID.ConversionQuantity, 1) ConversionQuantity,
        IFNULL(MID.ConversionQuantity, 1) * PD.BuyPrice BuyPrice,
        CASE
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price2
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price1
            WHEN MID.ConversionQuantity IS NULL
			THEN PD.RetailPrice
			ELSE IFNULL(MID.ConversionQuantity, 1) * PD.RetailPrice
		END RetailPrice,
		CASE
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price2
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price1
            WHEN MID.ConversionQuantity IS NULL
			THEN PD.Price1
			ELSE IFNULL(MID.ConversionQuantity, 1) * PD.RetailPrice
		END Price1,
        CASE
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price2
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price1
            WHEN MID.ConversionQuantity IS NULL
			THEN PD.Price2
			ELSE IFNULL(MID.ConversionQuantity, 1) * PD.RetailPrice
		END Price2,
        CONCAT('[', GROUP_CONCAT(AU.AvailableUnit SEPARATOR ', '), ']') AvailableUnit,
        PD.ItemDetailsID
	FROM
		transaction_firststockdetails PD
        JOIN master_branch MB
			ON MB.BranchID = PD.BranchID
		JOIN master_item MI
			ON MI.ItemID = PD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = PD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		JOIN 
        (
			SELECT
				MI.ItemID,
				CONCAT('[', 
							MU.UnitID, ',"', 
                            MU.UnitName, '", 
                            "NULL", "', 
                            MI.ItemCode, '", ', 
                            MI.BuyPrice, ', ', 
                            MI.RetailPrice, ', ', 
                            MI.Price1, ', ', 
                            MI.Price2 , ', ', 
                            MI.Qty1, ', ', 
                            MI.Qty2, ', ', 
                            1, 
						']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', 
							MU.UnitID, ',"',
                            MU.UnitName, '",',
                            MID.ItemDetailsID, ',"', 
                            MID.ItemDetailsCode, '", ', 
                            MI.BuyPrice, ', ', 
                            MI.RetailPrice, ', ', 
                            MI.Price1, ', ', 
                            MI.Price2, ', ', 
                            MI.Qty1, ', ', 
                            MI.Qty2, ', ', 
                            MID.ConversionQuantity,
						']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_itemdetails MID
					ON MID.UnitID = MU.UnitID
				JOIN master_item MI
					ON MI.ItemID = MID.ItemID
			GROUP BY
				MID.ItemDetailsID
		)AU
			ON AU.ItemID = PD.ItemID
	WHERE
		PD.FirstStockID = pFirstStockID
	GROUP BY
		PD.FirstStockDetailsID,
        PD.ItemID,
        PD.BranchID,
		MB.BranchCode,
		MB.BranchName,
		IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        IFNULL(MID.UnitID, MI.UnitID),
        PD.Quantity,
        MU.UnitName,
        PD.BuyPrice,
        PD.RetailPrice,
        PD.Price1,
        PD.Price2,
        PD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1)
	ORDER BY
		PD.FirstStockDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemList;

DELIMITER $$
CREATE PROCEDURE spSelItemList (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelItemList', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						master_item MI
                        JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MI.ItemID,
                        MI.ItemCode,
						MI.ItemName,
                        MC.CategoryID,
						MC.CategoryName,
                        MI.BuyPrice,
						MI.RetailPrice,
                        MI.Price1,
                        MI.Qty1,
                        MI.Price2,
                        MI.Qty2,
                        MI.Weight,
                        MI.MinimumStock
					FROM
						master_item MI
                        JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
					WHERE
						", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemListBranch;

DELIMITER $$
CREATE PROCEDURE spSelItemListBranch (
	pBranchID		INT,
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelItemListBranch', pCurrentUser);
	END;
	
SET State = 1;

/*SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								1
							FROM
								master_item MI
								JOIN master_category MC
									ON MC.CategoryID = MI.CategoryID
							WHERE ", pWhere, "
                            UNION ALL
                            SELECT
								1
							FROM
								master_itemdetails MID
                                JOIN master_item MI
									ON MI.ItemID = MID.ItemID
								JOIN master_category MC
									ON MC.CategoryID = MI.CategoryID
							WHERE ", pWhere, "
						)A
					LIMIT ", pLimit_s, ", ", pLimit_l);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;*/
        
SET State = 2;

SET @query = CONCAT("SELECT
						MI.ItemID,
                        0 ItemDetailsID,
                        MI.ItemCode,
						MI.ItemName,
                        MC.CategoryID,
						MC.CategoryName,
                        MI.BuyPrice,
						MI.RetailPrice,
                        MI.Price1,
                        MI.Qty1,
                        MI.Price2,
                        MI.Qty2,
                        MI.Weight,
                        MI.MinimumStock,
                        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)), 2) Stock,
                        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)), 2) PhysicalStock,
                        MU.UnitName
					FROM
						master_item MI
                        CROSS JOIN master_branch MB
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						JOIN master_unit MU
							ON MU.UnitID = MI.UnitID
						LEFT JOIN
						(
							SELECT
								FSD.ItemID,
								FSD.BranchID,
								SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_firststockdetails FSD
                                LEFT JOIN master_itemdetails MID
									ON FSD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN FSD.BranchID
									ELSE ",pBranchID,"
								END = FSD.BranchID
							GROUP BY
								FSD.ItemID,
								FSD.BranchID
						)FS
							ON FS.ItemID = MI.ItemID
							AND MB.BranchID = FS.BranchID
						LEFT JOIN
						(
							SELECT
								TPD.ItemID,
								TPD.BranchID,
								SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasedetails TPD
                                LEFT JOIN master_itemdetails MID
									ON TPD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN TPD.BranchID
									ELSE ",pBranchID,"
								END = TPD.BranchID
							GROUP BY
								TPD.ItemID,
								TPD.BranchID
						)TP
							ON TP.ItemID = MI.ItemID
							AND MB.BranchID = TP.BranchID
						LEFT JOIN
						(
							SELECT
								SRD.ItemID,
								SRD.BranchID,
								SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_salereturndetails SRD
                                LEFT JOIN master_itemdetails MID
									ON SRD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SRD.BranchID
									ELSE ",pBranchID,"
								END = SRD.BranchID
							GROUP BY
								SRD.ItemID,
								SRD.BranchID
						)SR
							ON SR.ItemID = MI.ItemID
							AND SR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SD.ItemID,
								SD.BranchID,
								SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_saledetails SD
                                /*JOIN transaction_sale TS
									ON TS.SaleID = SD.SaleID*/
                                LEFT JOIN master_itemdetails MID
									ON SD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SD.BranchID
									ELSE ",pBranchID,"
								END = SD.BranchID
                                /*AND TS.FinishFlag = 1*/
							GROUP BY
								SD.ItemID,
								SD.BranchID
						)S
							ON S.ItemID = MI.ItemID
							AND S.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								PRD.ItemID,
								PRD.BranchID,
								SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasereturndetails PRD
                                LEFT JOIN master_itemdetails MID
									ON PRD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN PRD.BranchID
									ELSE ",pBranchID,"
								END = PRD.BranchID
							GROUP BY
								PRD.ItemID,
								PRD.BranchID
						)PR
							ON MI.ItemID = PR.ItemID
							AND PR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SMD.DestinationID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SMD.DestinationID
									ELSE ",pBranchID,"
								END = SMD.DestinationID
							GROUP BY
								SMD.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
							AND SM.DestinationID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SMD.SourceID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SMD.SourceID
									ELSE ",pBranchID,"
								END = SMD.SourceID
							GROUP BY
								SMD.ItemID,
								SMD.SourceID
						)SMM
							ON MI.ItemID = SMM.ItemID
							AND SMM.SourceID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SAD.ItemID,
								SAD.BranchID,
								SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockadjustdetails SAD
                                LEFT JOIN master_itemdetails MID
									ON SAD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SAD.BranchID
									ELSE ",pBranchID,"
								END = SAD.BranchID
							GROUP BY
								SAD.ItemID,
								SAD.BranchID
						)SA
							ON MI.ItemID = SA.ItemID
							AND SA.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								BD.BranchID,
								SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1) ) Quantity
							FROM
								transaction_bookingdetails BD
                                /*JOIN transaction_booking TB
									ON TB.BookingID = BD.BookingID*/
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN BD.BranchID
									ELSE ",pBranchID,"
								END = BD.BranchID
                                /*AND TB.FinishFlag = 1*/
							GROUP BY
								BD.ItemID,
								BD.BranchID
						)B
							ON B.ItemID = MI.ItemID
							AND B.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								PD.ItemID,
								PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_pickdetails PD
                                LEFT JOIN master_itemdetails MID
									ON PD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN PD.BranchID
									ELSE ",pBranchID,"
								END = PD.BranchID
							GROUP BY
								PD.ItemID,
								PD.BranchID
						)P
							ON P.ItemID = MI.ItemID
							AND P.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN PD.BranchID
									ELSE ",pBranchID,"
								END = PD.BranchID
                                /*AND TB.FinishFlag = 1*/
							GROUP BY
								BD.ItemID,
								PD.BranchID
						)BN
							ON BN.ItemID = MI.ItemID
							AND BN.BranchID = MB.BranchID
					WHERE
						CASE
							WHEN ",pBranchID," = 0
							THEN MB.BranchID
							ELSE ",pBranchID,"
						END = MB.BranchID AND ", pWhere, "
					
                    UNION ALL
                    SELECT
						MI.ItemID,
                        MID.ItemDetailsID,
                        MID.ItemDetailsCode,
						MI.ItemName,
                        MC.CategoryID,
						MC.CategoryName,
                        IFNULL(MID.ConversionQuantity, 1) * MI.BuyPrice BuyPrice,
                        CASE
							WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price2
                            WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price1
                            ELSE IFNULL(MID.ConversionQuantity, 1) * MI.RetailPrice
						END RetailPrice,
                        CASE
							WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price2
                            WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price1
                            ELSE IFNULL(MID.ConversionQuantity, 1) * MI.RetailPrice
						END Price1,
                        MI.Qty1,
                        CASE
							WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price2
                            WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price1
                            ELSE IFNULL(MID.ConversionQuantity, 1) * MI.RetailPrice
						END Price2,
                        MI.Qty2,
                        MID.Weight,
                        MID.MinimumStock,
                        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
                        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity, 2) PhysicalStock,
                        MU.UnitName
					FROM
						master_itemdetails MID
                        CROSS JOIN master_branch MB
                        JOIN master_unit MU
							ON MU.UnitID = MID.UnitID
                        JOIN master_item MI
							ON MI.ItemID = MID.ItemID
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						LEFT JOIN
                        (
							SELECT
								FSD.ItemID,
								FSD.BranchID,
								SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_firststockdetails FSD
                                LEFT JOIN master_itemdetails MID
									ON FSD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN FSD.BranchID
									ELSE ",pBranchID,"
								END = FSD.BranchID
							GROUP BY
								FSD.ItemID,
								FSD.BranchID
						)FS
							ON FS.ItemID = MI.ItemID
							AND MB.BranchID = FS.BranchID
						LEFT JOIN
						(
							SELECT
								TPD.ItemID,
								TPD.BranchID,
								SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasedetails TPD
                                LEFT JOIN master_itemdetails MID
									ON TPD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN TPD.BranchID
									ELSE ",pBranchID,"
								END = TPD.BranchID
							GROUP BY
								TPD.ItemID,
								TPD.BranchID
						)TP
							ON TP.ItemID = MI.ItemID
							AND MB.BranchID = TP.BranchID
						LEFT JOIN
						(
							SELECT
								SRD.ItemID,
								SRD.BranchID,
								SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_salereturndetails SRD
                                LEFT JOIN master_itemdetails MID
									ON SRD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SRD.BranchID
									ELSE ",pBranchID,"
								END = SRD.BranchID
							GROUP BY
								SRD.ItemID,
								SRD.BranchID
						)SR
							ON SR.ItemID = MI.ItemID
							AND SR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SD.ItemID,
								SD.BranchID,
								SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_saledetails SD
                                /*JOIN transaction_sale TS
									ON TS.SaleID = SD.SaleID*/
                                LEFT JOIN master_itemdetails MID
									ON SD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SD.BranchID
									ELSE ",pBranchID,"
								END = SD.BranchID
                                /*AND TS.FinishFlag = 1*/
							GROUP BY
								SD.ItemID,
								SD.BranchID
						)S
							ON S.ItemID = MI.ItemID
							AND S.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								PRD.ItemID,
								PRD.BranchID,
								SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasereturndetails PRD
                                LEFT JOIN master_itemdetails MID
									ON PRD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN PRD.BranchID
									ELSE ",pBranchID,"
								END = PRD.BranchID
							GROUP BY
								PRD.ItemID,
								PRD.BranchID
						)PR
							ON MI.ItemID = PR.ItemID
							AND PR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SMD.DestinationID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SMD.DestinationID
									ELSE ",pBranchID,"
								END = SMD.DestinationID
							GROUP BY
								SMD.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
							AND SM.DestinationID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SMD.SourceID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SMD.SourceID
									ELSE ",pBranchID,"
								END = SMD.SourceID
							GROUP BY
								SMD.ItemID,
								SMD.SourceID
						)SMM
							ON MI.ItemID = SMM.ItemID
							AND SMM.SourceID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SAD.ItemID,
								SAD.BranchID,
								SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockadjustdetails SAD
                                LEFT JOIN master_itemdetails MID
									ON SAD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SAD.BranchID
									ELSE ",pBranchID,"
								END = SAD.BranchID
							GROUP BY
								SAD.ItemID,
								SAD.BranchID
						)SA
							ON MI.ItemID = SA.ItemID
							AND SA.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								BD.BranchID,
								SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                /*JOIN transaction_booking TB
									ON TB.BookingID = BD.BookingID*/
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN BD.BranchID
									ELSE ",pBranchID,"
								END = BD.BranchID
                                /*AND TB.FinishFlag = 1*/
							GROUP BY
								BD.ItemID,
								BD.BranchID
						)B
							ON B.ItemID = MI.ItemID
							AND B.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								PD.ItemID,
								PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_pickdetails PD
                                LEFT JOIN master_itemdetails MID
									ON PD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN PD.BranchID
									ELSE ",pBranchID,"
								END = PD.BranchID
							GROUP BY
								PD.ItemID,
								PD.BranchID
						)P
							ON P.ItemID = MI.ItemID
							AND P.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN PD.BranchID
									ELSE ",pBranchID,"
								END = PD.BranchID
                                /*AND TB.FinishFlag = 1*/
							GROUP BY
								BD.ItemID,
								PD.BranchID
						)BN
							ON BN.ItemID = MI.ItemID
							AND BN.BranchID = MB.BranchID
					WHERE
						CASE
							WHEN ",pBranchID," = 0
							THEN MB.BranchID
							ELSE ",pBranchID,"
						END = MB.BranchID AND ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemListStock;

DELIMITER $$
CREATE PROCEDURE spSelItemListStock(
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelItemListStock', pCurrentUser);
	END;
	
SET State = 1;

/*SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								1
							FROM
								master_item MI
								JOIN master_category MC
									ON MC.CategoryID = MI.CategoryID
							WHERE ", pWhere, "
                            UNION ALL
                            SELECT
								1
							FROM
								master_itemdetails MID
                                JOIN master_item MI
									ON MI.ItemID = MID.ItemID
								JOIN master_category MC
									ON MC.CategoryID = MI.CategoryID
							WHERE ", pWhere, "
						)A
					LIMIT ", pLimit_s, ", ", pLimit_l);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;*/
        
SET State = 2;

SET @query = CONCAT("SELECT
						MI.ItemID,
						0 ItemDetailsID,
						MI.ItemCode,
						MI.ItemName,
						MC.CategoryID,
						MC.CategoryName,
						MI.BuyPrice,
						MI.RetailPrice,
						MI.Price1,
						MI.Qty1,
						MI.Price2,
						MI.Qty2,
						MI.Weight,
						MI.MinimumStock,
						ROUND((IFNULL(FS.Toko, 0) + IFNULL(TP.Toko, 0) + IFNULL(SR.Toko, 0) - IFNULL(S.Toko, 0) - IFNULL(PR.Toko, 0) + IFNULL(SM.Toko, 0) - IFNULL(SMM.Toko, 0) + IFNULL(SA.Toko, 0) - IFNULL(B.Toko, 0) - IFNULL(BN.Toko, 0)), 2) Toko,
						ROUND((IFNULL(FS.Gudang, 0) + IFNULL(TP.Gudang, 0) + IFNULL(SR.Gudang, 0) - IFNULL(S.Gudang, 0) - IFNULL(PR.Gudang, 0) + IFNULL(SM.Gudang, 0) - IFNULL(SMM.Gudang, 0) + IFNULL(SA.Gudang, 0) - IFNULL(B.Gudang, 0) - IFNULL(BN.Gudang, 0)), 2) Gudang,
						MU.UnitName
					FROM
						master_item MI
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						JOIN master_unit MU
							ON MU.UnitID = MI.UnitID
						LEFT JOIN
						(
							SELECT
								FSD.ItemID,
								SUM(
									CASE
										WHEN FSD.BranchID = 1
										THEN FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)
										ELSE 0
									END
								) Toko,
								SUM(
										CASE
											WHEN FSD.BranchID = 2
											THEN FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_firststockdetails FSD
								LEFT JOIN master_itemdetails MID
									ON FSD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								FSD.ItemID
						)FS
							ON FS.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								TPD.ItemID,
								SUM(
										CASE
											WHEN TPD.BranchID = 1
											THEN TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN TPD.BranchID = 2
											THEN TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_purchasedetails TPD
								LEFT JOIN master_itemdetails MID
									ON TPD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								TPD.ItemID
						)TP
							ON TP.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								SRD.ItemID,
								SUM(
										CASE
											WHEN SRD.BranchID = 1
											THEN SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN SRD.BranchID = 2
											THEN SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_salereturndetails SRD
								LEFT JOIN master_itemdetails MID
									ON SRD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SRD.ItemID
						)SR
							ON SR.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								SD.ItemID,
								SUM(
										CASE
											WHEN SD.BranchID = 1
											THEN SD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN SD.BranchID = 2
											THEN SD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_saledetails SD
                                /*JOIN transaction_sale TS
									ON TS.SaleID = SD.SaleID*/
								LEFT JOIN master_itemdetails MID
									ON SD.ItemDetailsID = MID.ItemDetailsID
							/*WHERE
								TS.FinishFlag = 1*/
							GROUP BY
								SD.ItemID
						)S
							ON S.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								PRD.ItemID,
								SUM(
										CASE
											WHEN PRD.BranchID = 1
											THEN PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN PRD.BranchID = 2
											THEN PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_purchasereturndetails PRD
								LEFT JOIN master_itemdetails MID
									ON PRD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								PRD.ItemID
						)PR
							ON MI.ItemID = PR.ItemID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SUM(
										CASE
											WHEN SMD.DestinationID = 1
											THEN SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN SMD.DestinationID = 2
											THEN SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SMD.ItemID
						)SM
							ON MI.ItemID = SM.ItemID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SUM(
										CASE
											WHEN SMD.SourceID = 1
											THEN SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN SMD.SourceID = 2
											THEN SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SMD.ItemID
						)SMM
							ON MI.ItemID = SMM.ItemID
						LEFT JOIN
						(
							SELECT
								SAD.ItemID,
								SUM(
										CASE
											WHEN SAD.BranchID = 1
											THEN (SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN SAD.BranchID = 2
											THEN (SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_stockadjustdetails SAD
								LEFT JOIN master_itemdetails MID
									ON SAD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SAD.ItemID
						)SA
							ON MI.ItemID = SA.ItemID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								SUM(
										CASE
											WHEN BD.BranchID = 1
											THEN (BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN BD.BranchID = 2
											THEN (BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_bookingdetails BD
								/*JOIN transaction_booking TB
									ON TB.BookingID = BD.BookingID*/
								LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
                                    AND PD.BranchID <> BD.BranchID
							/*WHERE
								TB.FinishFlag = 1*/
							GROUP BY
								BD.ItemID
						)B
							ON B.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								PD.ItemID,
								SUM(
										CASE
											WHEN PD.BranchID = 1
											THEN PD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN PD.BranchID = 2
											THEN PD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END 
									) Gudang
							FROM
								transaction_pickdetails PD
								LEFT JOIN master_itemdetails MID
									ON PD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								PD.ItemID
						)P
							ON P.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								SUM(
										CASE
											WHEN PD.BranchID = 1
                                            THEN PD.Quantity * IFNULL(MID.ConversionQuantity, 1)
                                            ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN PD.BranchID = 2
                                            THEN PD.Quantity * IFNULL(MID.ConversionQuantity, 1)
                                            ELSE 0
										END
									) Gudang
							FROM
								transaction_bookingdetails BD
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							GROUP BY
								BD.ItemID
						)BN
							ON BN.ItemID = MI.ItemID
					WHERE
						", pWhere, "
					
                    UNION ALL
                    SELECT
						MI.ItemID,
                        MID.ItemDetailsID,
                        MID.ItemDetailsCode,
						MI.ItemName,
                        MC.CategoryID,
						MC.CategoryName,
                        IFNULL(MID.ConversionQuantity, 1) * MI.BuyPrice BuyPrice,
                        CASE
							WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price2
                            WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price1
                            ELSE IFNULL(MID.ConversionQuantity, 1) * MI.RetailPrice
						END RetailPrice,
                        CASE
							WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price2
                            WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price1
                            ELSE IFNULL(MID.ConversionQuantity, 1) * MI.RetailPrice
						END Price1,
                        MI.Qty1,
                        CASE
							WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price2
                            WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
                            THEN IFNULL(MID.ConversionQuantity, 1) * MI.Price1
                            ELSE IFNULL(MID.ConversionQuantity, 1) * MI.RetailPrice
						END Price2,
                        MI.Qty2,
                        MID.Weight,
                        MID.MinimumStock,
                        ROUND((IFNULL(FS.Toko, 0) + IFNULL(TP.Toko, 0) + IFNULL(SR.Toko, 0) - IFNULL(S.Toko, 0) - IFNULL(PR.Toko, 0) + IFNULL(SM.Toko, 0) - IFNULL(SMM.Toko, 0) + IFNULL(SA.Toko, 0) - IFNULL(B.Toko, 0) - IFNULL(BN.Toko, 0))  / MID.ConversionQuantity, 2) Toko,
						ROUND((IFNULL(FS.Gudang, 0) + IFNULL(TP.Gudang, 0) + IFNULL(SR.Gudang, 0) - IFNULL(S.Gudang, 0) - IFNULL(PR.Gudang, 0) + IFNULL(SM.Gudang, 0) - IFNULL(SMM.Gudang, 0) + IFNULL(SA.Gudang, 0) - IFNULL(B.Gudang, 0) - IFNULL(BN.Gudang, 0))  / MID.ConversionQuantity, 2) Gudang,
						MU.UnitName
					FROM
						master_itemdetails MID
                        JOIN master_unit MU
							ON MU.UnitID = MID.UnitID
                        JOIN master_item MI
							ON MI.ItemID = MID.ItemID
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						LEFT JOIN
                        (
							SELECT
								FSD.ItemID,
								SUM(
										CASE
											WHEN FSD.BranchID = 1
											THEN FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN FSD.BranchID = 2
											THEN FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_firststockdetails FSD
                                LEFT JOIN master_itemdetails MID
									ON FSD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								FSD.ItemID
						)FS
							ON FS.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								TPD.ItemID,
								SUM(
										CASE
											WHEN TPD.BranchID = 1
											THEN TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN TPD.BranchID = 2
											THEN TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_purchasedetails TPD
                                LEFT JOIN master_itemdetails MID
									ON TPD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								TPD.ItemID
						)TP
							ON TP.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								SRD.ItemID,
								SUM(
										CASE
											WHEN SRD.BranchID = 1
											THEN SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN SRD.BranchID = 2
											THEN SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_salereturndetails SRD
                                LEFT JOIN master_itemdetails MID
									ON SRD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SRD.ItemID
						)SR
							ON SR.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								SD.ItemID,
								SUM(
										CASE
											WHEN SD.BranchID = 1
											THEN SD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN SD.BranchID = 2
											THEN SD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_saledetails SD
                                /*JOIN transaction_sale TS
									ON TS.SaleID = SD.SaleID*/
                                LEFT JOIN master_itemdetails MID
									ON SD.ItemDetailsID = MID.ItemDetailsID
							/*WHERE
								TS.FinishFlag = 1*/
							GROUP BY
								SD.ItemID
						)S
							ON S.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								PRD.ItemID,
								SUM(
										CASE
											WHEN PRD.BranchID = 1
											THEN PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN PRD.BranchID = 2
											THEN PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_purchasereturndetails PRD
                                LEFT JOIN master_itemdetails MID
									ON PRD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								PRD.ItemID
						)PR
							ON MI.ItemID = PR.ItemID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SUM(
										CASE
											WHEN SMD.DestinationID = 1
											THEN SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN SMD.DestinationID = 2
											THEN SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SMD.ItemID
						)SM
							ON MI.ItemID = SM.ItemID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SUM(
										CASE
											WHEN SMD.SourceID = 1
											THEN SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN SMD.SourceID = 2
											THEN SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SMD.ItemID
						)SMM
							ON MI.ItemID = SMM.ItemID
						LEFT JOIN
						(
							SELECT
								SAD.ItemID,
								SUM(
										CASE
											WHEN SAD.BranchID = 1
											THEN(SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN SAD.BranchID = 2
											THEN (SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_stockadjustdetails SAD
                                LEFT JOIN master_itemdetails MID
									ON SAD.ItemDetailsID = MID.ItemDetailsID\
							GROUP BY
								SAD.ItemID
						)SA
							ON MI.ItemID = SA.ItemID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								SUM(
										CASE
											WHEN BD.BranchID = 1
											THEN (BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN BD.BranchID = 2
											THEN (BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_bookingdetails BD
                                /*JOIN transaction_booking TB
									ON TB.BookingID = BD.BookingID*/
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
                                    AND PD.BranchID <> BD.BranchID
							/*WHERE
								TB.FinishFlag = 1*/
							GROUP BY
								BD.ItemID
						)B
							ON B.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								PD.ItemID,
								SUM(
										CASE
											WHEN PD.BranchID = 1
											THEN PD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN PD.BranchID = 2
											THEN PD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END 
									) Gudang
							FROM
								transaction_pickdetails PD
                                LEFT JOIN master_itemdetails MID
									ON PD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								PD.ItemID
						)P
							ON P.ItemID = MI.ItemID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								SUM(
										CASE
											WHEN PD.BranchID = 1
                                            THEN PD.Quantity * IFNULL(MID.ConversionQuantity, 1)
                                            ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN PD.BranchID = 2
                                            THEN PD.Quantity * IFNULL(MID.ConversionQuantity, 1)
                                            ELSE 0
										END
									) Gudang
							FROM
								transaction_bookingdetails BD
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							GROUP BY
								BD.ItemID
						)BN
							ON BN.ItemID = MI.ItemID
					WHERE
					", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelItemMinimumStock;

DELIMITER $$
CREATE PROCEDURE spSelItemMinimumStock (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelItemMinimumStock', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								1
							FROM
								master_item MI
		                        CROSS JOIN master_branch MB
								JOIN master_category MC
									ON MC.CategoryID = MI.CategoryID
								JOIN master_unit MU
									ON MU.UnitID = MI.UnitID
								LEFT JOIN
								(
									SELECT
										FSD.ItemID,
										FSD.BranchID,
										SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_firststockdetails FSD
										LEFT JOIN master_itemdetails MID
											ON FSD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										FSD.ItemID,
										FSD.BranchID
								)FS
									ON FS.ItemID = MI.ItemID
									AND MB.BranchID = FS.BranchID
								LEFT JOIN
								(
									SELECT
										TPD.ItemID,
										TPD.BranchID,
										SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_purchasedetails TPD
										LEFT JOIN master_itemdetails MID
											ON TPD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										TPD.ItemID,
										TPD.BranchID
								)TP
									ON TP.ItemID = MI.ItemID
									AND MB.BranchID = TP.BranchID
								LEFT JOIN
								(
									SELECT
										SRD.ItemID,
										SRD.BranchID,
										SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_salereturndetails SRD
										LEFT JOIN master_itemdetails MID
											ON SRD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										SRD.ItemID,
										SRD.BranchID
								)SR
									ON SR.ItemID = MI.ItemID
									AND SR.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										SD.ItemID,
										SD.BranchID,
										SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_saledetails SD
										LEFT JOIN master_itemdetails MID
											ON SD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										SD.ItemID,
										SD.BranchID
								)S
									ON S.ItemID = MI.ItemID
									AND S.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										PRD.ItemID,
										PRD.BranchID,
										SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_purchasereturndetails PRD
										LEFT JOIN master_itemdetails MID
											ON PRD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										PRD.ItemID,
										PRD.BranchID
								)PR
									ON MI.ItemID = PR.ItemID
									AND PR.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										SMD.ItemID,
										SMD.DestinationID,
										SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_stockmutationdetails SMD
										LEFT JOIN master_itemdetails MID
											ON SMD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										SMD.ItemID,
										SMD.DestinationID
								)SM
									ON MI.ItemID = SM.ItemID
									AND SM.DestinationID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										SMD.ItemID,
										SMD.SourceID,
										SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_stockmutationdetails SMD
										LEFT JOIN master_itemdetails MID
											ON SMD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										SMD.ItemID,
										SMD.SourceID
								)SMM
									ON MI.ItemID = SMM.ItemID
									AND SMM.SourceID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										SAD.ItemID,
										SAD.BranchID,
										SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_stockadjustdetails SAD
										LEFT JOIN master_itemdetails MID
											ON SAD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										SAD.ItemID,
										SAD.BranchID
								)SA
									ON MI.ItemID = SA.ItemID
									AND SA.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										BD.ItemID,
										BD.BranchID,
										SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_bookingdetails BD
										LEFT JOIN master_itemdetails MID
											ON BD.ItemDetailsID = MID.ItemDetailsID
										LEFT JOIN transaction_pickdetails PD
											ON PD.BookingDetailsID = BD.BookingDetailsID
											AND PD.BranchID <> BD.BranchID
									GROUP BY
										BD.ItemID,
										BD.BranchID
								)B
									ON B.ItemID = MI.ItemID
									AND B.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										PD.ItemID,
										PD.BranchID,
										SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_pickdetails PD
										LEFT JOIN master_itemdetails MID
											ON PD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										PD.ItemID,
										PD.BranchID
								)P
									ON P.ItemID = MI.ItemID
									AND P.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										BD.ItemID,
										PD.BranchID,
										SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_bookingdetails BD
										LEFT JOIN master_itemdetails MID
											ON BD.ItemDetailsID = MID.ItemDetailsID
										LEFT JOIN transaction_pickdetails PD
											ON PD.BookingDetailsID = BD.BookingDetailsID
											AND PD.BranchID <> BD.BranchID
									GROUP BY
										BD.ItemID,
										PD.BranchID
								)BN
									ON BN.ItemID = MI.ItemID
									AND BN.BranchID = MB.BranchID
							WHERE
								((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) <= MI.MinimumStock
								OR (IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)) <= MI.MinimumStock) AND ", pWhere, "
							UNION ALL
                            SELECT
								1
							FROM
								master_itemdetails MID
								CROSS JOIN master_branch MB
								JOIN master_unit MU
									ON MU.UnitID = MID.UnitID
								JOIN master_item MI
									ON MI.ItemID = MID.ItemID
								JOIN master_category MC
									ON MC.CategoryID = MI.CategoryID
								LEFT JOIN
								(
									SELECT
										FSD.ItemID,
										FSD.BranchID,
										SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_firststockdetails FSD
										LEFT JOIN master_itemdetails MID
											ON FSD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										FSD.ItemID,
										FSD.BranchID
								)FS
									ON FS.ItemID = MI.ItemID
									AND MB.BranchID = FS.BranchID
								LEFT JOIN
								(
									SELECT
										TPD.ItemID,
										TPD.BranchID,
										SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_purchasedetails TPD
										LEFT JOIN master_itemdetails MID
											ON TPD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										TPD.ItemID,
										TPD.BranchID
								)TP
									ON TP.ItemID = MI.ItemID
									AND MB.BranchID = TP.BranchID
								LEFT JOIN
								(
									SELECT
										SRD.ItemID,
										SRD.BranchID,
										SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_salereturndetails SRD
										LEFT JOIN master_itemdetails MID
											ON SRD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										SRD.ItemID,
										SRD.BranchID
								)SR
									ON SR.ItemID = MI.ItemID
									AND SR.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										SD.ItemID,
										SD.BranchID,
										SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_saledetails SD
										LEFT JOIN master_itemdetails MID
											ON SD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										SD.ItemID,
										SD.BranchID
								)S
									ON S.ItemID = MI.ItemID
									AND S.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										PRD.ItemID,
										PRD.BranchID,
										SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_purchasereturndetails PRD
										LEFT JOIN master_itemdetails MID
											ON PRD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										PRD.ItemID,
										PRD.BranchID
								)PR
									ON MI.ItemID = PR.ItemID
									AND PR.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										SMD.ItemID,
										SMD.DestinationID,
										SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_stockmutationdetails SMD
										LEFT JOIN master_itemdetails MID
											ON SMD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										SMD.ItemID,
										SMD.DestinationID
								)SM
									ON MI.ItemID = SM.ItemID
									AND SM.DestinationID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										SMD.ItemID,
										SMD.SourceID,
										SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_stockmutationdetails SMD
										LEFT JOIN master_itemdetails MID
											ON SMD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										SMD.ItemID,
										SMD.SourceID
								)SMM
									ON MI.ItemID = SMM.ItemID
									AND SMM.SourceID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										SAD.ItemID,
										SAD.BranchID,
										SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_stockadjustdetails SAD
										LEFT JOIN master_itemdetails MID
											ON SAD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										SAD.ItemID,
										SAD.BranchID
								)SA
									ON MI.ItemID = SA.ItemID
									AND SA.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										BD.ItemID,
										BD.BranchID,
										SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_bookingdetails BD
										LEFT JOIN master_itemdetails MID
											ON BD.ItemDetailsID = MID.ItemDetailsID
										LEFT JOIN transaction_pickdetails PD
											ON PD.BookingDetailsID = BD.BookingDetailsID
											AND PD.BranchID <> BD.BranchID
									GROUP BY
										BD.ItemID,
										BD.BranchID
								)B
									ON B.ItemID = MI.ItemID
									AND B.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										PD.ItemID,
										PD.BranchID,
										SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_pickdetails PD
										LEFT JOIN master_itemdetails MID
											ON PD.ItemDetailsID = MID.ItemDetailsID
									GROUP BY
										PD.ItemID,
										PD.BranchID
								)P
									ON P.ItemID = MI.ItemID
									AND P.BranchID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										BD.ItemID,
										PD.BranchID,
										SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_bookingdetails BD
										LEFT JOIN master_itemdetails MID
											ON BD.ItemDetailsID = MID.ItemDetailsID
										LEFT JOIN transaction_pickdetails PD
											ON PD.BookingDetailsID = BD.BookingDetailsID
											AND PD.BranchID <> BD.BranchID
									GROUP BY
										BD.ItemID,
										PD.BranchID
								)BN
									ON BN.ItemID = MI.ItemID
									AND BN.BranchID = MB.BranchID
							WHERE
								(((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / MID.ConversionQuantity) <= MI.MinimumStock
								OR ((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity) <= MI.MinimumStock) AND ", pWhere, "
						)ST" 
				);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MB.BranchName,
						MI.ItemID,
                        MI.ItemCode,
						MI.ItemName,
						MC.CategoryName,
                        ROUND(MI.MinimumStock, 2) MinimumStock,
                        MU.UnitName,
						ROUND(IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0), 2) Stock,
                        ROUND(IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0), 2) PhysicalStock                        
					FROM
						master_item MI
                        CROSS JOIN master_branch MB
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						JOIN master_unit MU
							ON MU.UnitID = MI.UnitID
						LEFT JOIN
						(
							SELECT
								FSD.ItemID,
								FSD.BranchID,
								SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_firststockdetails FSD
								LEFT JOIN master_itemdetails MID
									ON FSD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								FSD.ItemID,
								FSD.BranchID
						)FS
							ON FS.ItemID = MI.ItemID
							AND MB.BranchID = FS.BranchID
						LEFT JOIN
						(
							SELECT
								TPD.ItemID,
								TPD.BranchID,
								SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasedetails TPD
								LEFT JOIN master_itemdetails MID
									ON TPD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								TPD.ItemID,
								TPD.BranchID
						)TP
							ON TP.ItemID = MI.ItemID
							AND MB.BranchID = TP.BranchID
						LEFT JOIN
						(
							SELECT
								SRD.ItemID,
								SRD.BranchID,
								SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_salereturndetails SRD
								LEFT JOIN master_itemdetails MID
									ON SRD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SRD.ItemID,
								SRD.BranchID
						)SR
							ON SR.ItemID = MI.ItemID
							AND SR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SD.ItemID,
								SD.BranchID,
								SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_saledetails SD
								LEFT JOIN master_itemdetails MID
									ON SD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SD.ItemID,
								SD.BranchID
						)S
							ON S.ItemID = MI.ItemID
							AND S.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								PRD.ItemID,
								PRD.BranchID,
								SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasereturndetails PRD
								LEFT JOIN master_itemdetails MID
									ON PRD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								PRD.ItemID,
								PRD.BranchID
						)PR
							ON MI.ItemID = PR.ItemID
							AND PR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SMD.DestinationID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SMD.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
							AND SM.DestinationID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SMD.SourceID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SMD.ItemID,
								SMD.SourceID
						)SMM
							ON MI.ItemID = SMM.ItemID
							AND SMM.SourceID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SAD.ItemID,
								SAD.BranchID,
								SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockadjustdetails SAD
								LEFT JOIN master_itemdetails MID
									ON SAD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SAD.ItemID,
								SAD.BranchID
						)SA
							ON MI.ItemID = SA.ItemID
							AND SA.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								BD.BranchID,
								SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
								LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							GROUP BY
								BD.ItemID,
								BD.BranchID
						)B
							ON B.ItemID = MI.ItemID
							AND B.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								PD.ItemID,
								PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_pickdetails PD
								LEFT JOIN master_itemdetails MID
									ON PD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								PD.ItemID,
								PD.BranchID
						)P
							ON P.ItemID = MI.ItemID
							AND P.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							GROUP BY
								BD.ItemID,
								PD.BranchID
						)BN
							ON BN.ItemID = MI.ItemID
							AND BN.BranchID = MB.BranchID
					WHERE
						((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) <= MI.MinimumStock
						OR (IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)) <= MI.MinimumStock) AND ", pWhere,
					"UNION ALL 
                    SELECT
						MB.BranchName,
						MI.ItemID,
                        MID.ItemDetailsCode,
						MI.ItemName,
						MC.CategoryName,
                        ROUND(MID.MinimumStock, 2) MinimumStock,
                        MU.UnitName,
						ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
                        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity, 2) PhysicalStock                        
					FROM
						master_itemdetails MID
                        CROSS JOIN master_branch MB
                        JOIN master_unit MU
							ON MU.UnitID = MID.UnitID
                        JOIN master_item MI
							ON MI.ItemID = MID.ItemID
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						LEFT JOIN
						(
							SELECT
								FSD.ItemID,
								FSD.BranchID,
								SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_firststockdetails FSD
								LEFT JOIN master_itemdetails MID
									ON FSD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								FSD.ItemID,
								FSD.BranchID
						)FS
							ON FS.ItemID = MI.ItemID
							AND MB.BranchID = FS.BranchID
						LEFT JOIN
						(
							SELECT
								TPD.ItemID,
								TPD.BranchID,
								SUM(TPD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasedetails TPD
								LEFT JOIN master_itemdetails MID
									ON TPD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								TPD.ItemID,
								TPD.BranchID
						)TP
							ON TP.ItemID = MI.ItemID
							AND MB.BranchID = TP.BranchID
						LEFT JOIN
						(
							SELECT
								SRD.ItemID,
								SRD.BranchID,
								SUM(SRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_salereturndetails SRD
								LEFT JOIN master_itemdetails MID
									ON SRD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SRD.ItemID,
								SRD.BranchID
						)SR
							ON SR.ItemID = MI.ItemID
							AND SR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SD.ItemID,
								SD.BranchID,
								SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_saledetails SD
								LEFT JOIN master_itemdetails MID
									ON SD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SD.ItemID,
								SD.BranchID
						)S
							ON S.ItemID = MI.ItemID
							AND S.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								PRD.ItemID,
								PRD.BranchID,
								SUM(PRD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_purchasereturndetails PRD
								LEFT JOIN master_itemdetails MID
									ON PRD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								PRD.ItemID,
								PRD.BranchID
						)PR
							ON MI.ItemID = PR.ItemID
							AND PR.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SMD.DestinationID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SMD.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
							AND SM.DestinationID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SMD.ItemID,
								SMD.SourceID,
								SUM(SMD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockmutationdetails SMD
								LEFT JOIN master_itemdetails MID
									ON SMD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SMD.ItemID,
								SMD.SourceID
						)SMM
							ON MI.ItemID = SMM.ItemID
							AND SMM.SourceID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SAD.ItemID,
								SAD.BranchID,
								SUM((SAD.AdjustedQuantity - SAD.Quantity) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_stockadjustdetails SAD
								LEFT JOIN master_itemdetails MID
									ON SAD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								SAD.ItemID,
								SAD.BranchID
						)SA
							ON MI.ItemID = SA.ItemID
							AND SA.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								BD.BranchID,
								SUM((BD.Quantity - IFNULL(PD.Quantity, 0)) * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
								LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							GROUP BY
								BD.ItemID,
								BD.BranchID
						)B
							ON B.ItemID = MI.ItemID
							AND B.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								PD.ItemID,
								PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_pickdetails PD
								LEFT JOIN master_itemdetails MID
									ON PD.ItemDetailsID = MID.ItemDetailsID
							GROUP BY
								PD.ItemID,
								PD.BranchID
						)P
							ON P.ItemID = MI.ItemID
							AND P.BranchID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								BD.ItemID,
								PD.BranchID,
								SUM(PD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
								LEFT JOIN transaction_pickdetails PD
									ON PD.BookingDetailsID = BD.BookingDetailsID
									AND PD.BranchID <> BD.BranchID
							GROUP BY
								BD.ItemID,
								PD.BranchID
						)BN
							ON BN.ItemID = MI.ItemID
							AND BN.BranchID = MB.BranchID
					WHERE
						(((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / MID.ConversionQuantity) <= MID.MinimumStock
						OR ((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)) / MID.ConversionQuantity) <= MID.MinimumStock) AND ", pWhere,
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
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
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPickUp;

DELIMITER $$
CREATE PROCEDURE spSelPickUp (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelPickUp', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_pick TSR
						JOIN transaction_booking TS
							ON TS.BookingID = TSR.BookingID
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TSR.PickID,
                        TS.BookingNumber,
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TSR.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TSRD.Total, 0) Total
					FROM
						transaction_pick TSR
						JOIN transaction_booking TS
							ON TS.BookingID = TSR.BookingID
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN
                        (
							SELECT
								TSR.PickID,
                                SUM(TSRD.Quantity * (TSRD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - IFNULL(TSRD.Discount, 0))) Total
							FROM
								transaction_booking TS
                                JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
								JOIN transaction_pick TSR
									ON TS.BookingID = TSR.BookingID
                                LEFT JOIN transaction_pickdetails TSRD
									ON TSR.PickID = TSRD.PickID
								LEFT JOIN master_item MI
									ON MI.ItemID = TSRD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TSRD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TSR.PickID
                        )TSRD
							ON TSRD.PickID = TSR.PickID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select booking return details
Created Date: 24 February 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPickUpDetails;

DELIMITER $$
CREATE PROCEDURE spSelPickUpDetails (
	pPickID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelPickUpDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TSR.BookingID,
		TSR.PickID,
		TSRD.PickDetailsID,
		TSRD.BookingDetailsID,
        TSRD.ItemID,
        TSRD.ItemDetailsID,
        TSRD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        TSRD.Quantity,
        TSRD.BuyPrice,
        TSRD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - TSRD.Discount SalePrice,
        (IFNULL(TS.Quantity, 0) - IFNULL(SR.Quantity, 0) + IFNULL(TSRD.Quantity, 0)) Maksimum,
        MU.UnitName,
        TSRD.Discount,
        IFNULL(MID.ConversionQuantity, 1) ConversionQuantity
	FROM
		transaction_pick TSR
		JOIN transaction_pickdetails TSRD
			ON TSRD.PickID = TSR.PickID
		JOIN master_item MI
			ON MI.ItemID = TSRD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = TSRD.ItemDetailsID
		JOIN master_unit MU
			ON IFNULL(MID.UnitID, MI.UnitID) = MU.UnitID
        LEFT JOIN
		(
			SELECT
				TS.BookingID,
				SD.ItemID,
				SD.BookingDetailsID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_booking TS
				JOIN transaction_bookingdetails SD
					ON TS.BookingID = SD.BookingID
			GROUP BY
				TS.BookingID,
				SD.ItemID,
				SD.BookingDetailsID
		)TS
			ON TSR.BookingID = TS.BookingID
			AND MI.ItemID = TS.ItemID
			AND TSRD.BookingDetailsID = TS.BookingDetailsID
		LEFT JOIN
		(
			SELECT
				SR.BookingID,
				SRD.ItemID,
				SRD.BookingDetailsID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_pick SR
				JOIN transaction_pickdetails SRD
					ON SR.PickID = SRD.PickID
			GROUP BY
				SR.BookingID,
				SRD.ItemID,
				SRD.BookingDetailsID
		)SR
			ON TSR.BookingID = SR.BookingID
			AND MI.ItemID = SR.ItemID
			AND TSRD.BookingDetailsID = SR.BookingDetailsID

	WHERE
		TSR.PickID = pPickID
	ORDER BY
		TSRD.PickDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPrint;

DELIMITER $$
CREATE PROCEDURE spSelPrint (
	pWhere 			TEXT,
    pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelPrint', pCurrentUser);
	END;
	
SET State = 1;
	
    DELETE FROM 
		transaction_sale
    WHERE
		FinishFlag = 0
        AND DATE_FORMAT(TransactionDate, '%Y-%m-%d') <> DATE_FORMAT(NOW(), '%Y-%m-%d');
    
SET State = 2;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						(
							SELECT
								COUNT(1) AS nRows
							FROM
								transaction_sale TS
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
							WHERE ", pWhere, "
							UNION ALL
							SELECT
								COUNT(1) AS nRows
							FROM
								transaction_booking TB
								JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
							WHERE ", pWhere2,
						")TS"
					);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TS.SaleID,
                        TS.SaleNumber,
                        DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TS.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TSD.Total, 0) Total,
						IFNULL(TSD.Weight, 0) Weight,
						TS.RetailFlag,
						IFNULL(TS.Payment, 0) Payment,
                        IFNULL(PT.PaymentTypeName, '') PaymentTypeName,
                        CASE
							WHEN FinishFlag = 0
                            THEN 'Belum Selesai'
                            ELSE 'Selesai'
						END Status,
                        IFNULL(PT.PaymentTypeID, 1) PaymentTypeID,
                        1 TransactionType
					FROM
						transaction_sale TS
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN master_paymenttype PT
							ON PT.PaymentTypeID = TS.PaymentTypeID
						LEFT JOIN
                        (
							SELECT
								TS.SaleID,
                                SUM(TSD.Quantity * (TSD.SalePrice * IFNULL(MID.ConversionQuantity, 1) - TSD.Discount)) Total,
								SUM(TSD.Quantity * MI.Weight * IFNULL(MID.ConversionQuantity, 1)) Weight
							FROM
								transaction_sale TS
                                JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
                                LEFT JOIN transaction_saledetails TSD
									ON TS.SaleID = TSD.SaleID
								LEFT JOIN master_item MI
									ON MI.ItemID = TSD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TSD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TS.SaleID
                        )TSD
							ON TSD.SaleID = TS.SaleID
					WHERE ", pWhere, 
                    "UNION ALL
                    SELECT
						TB.BookingID,
                        TB.BookingNumber,
                        DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TB.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TBD.Total, 0) Total,
						IFNULL(TBD.Weight, 0) Weight,
						TB.RetailFlag,
						IFNULL(TB.Payment, 0) Payment,
                        IFNULL(PT.PaymentTypeName, '') PaymentTypeName,
                        CASE
							WHEN FinishFlag = 0
                            THEN 'Belum Selesai'
                            ELSE 'Selesai'
						END Status,
                        IFNULL(PT.PaymentTypeID, 1) PaymentTypeID,
                        2 TransactionType
					FROM
						transaction_booking TB
                        JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
						LEFT JOIN master_paymenttype PT
							ON PT.PaymentTypeID = TB.PaymentTypeID
						LEFT JOIN
                        (
							SELECT
								TB.BookingID,
                                SUM(TBD.Quantity * (TBD.BookingPrice * IFNULL(MID.ConversionQuantity, 1) - TBD.Discount)) Total,
								SUM(TBD.Quantity * MI.Weight * IFNULL(MID.ConversionQuantity, 1)) Weight
							FROM
								transaction_booking TB
                                JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
                                LEFT JOIN transaction_bookingdetails TBD
									ON TB.BookingID = TBD.BookingID
								LEFT JOIN master_item MI
									ON MI.ItemID = TBD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TBD.ItemDetailsID
							WHERE ", 
								pWhere2, 
                            " GROUP BY
								TB.BookingID
                        )TBD
							ON TBD.BookingID = TB.BookingID
					WHERE ", pWhere2, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select purchase transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchase;

DELIMITER $$
CREATE PROCEDURE spSelPurchase (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchase', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_purchase TP
                        JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TP.PurchaseID,
                        TP.PurchaseNumber,
                        DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
                        CASE
							WHEN TP.PaymentTypeID = 2 
                            THEN DATE_FORMAT(TP.Deadline, '%d-%m-%Y')
                            ELSE '-'
						END Deadline,
                        TP.TransactionDate PlainTransactionDate,
                        MS.SupplierID,
                        MS.SupplierName,
						IFNULL(TPD.Total, 0) Total,
                        TP.Deadline PlainDeadline,
                        TP.PaymentTypeID
					FROM
						transaction_purchase TP
                        JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
						LEFT JOIN
                        (
							SELECT
								TP.PurchaseID,
                                SUM(TPD.Quantity * TPD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total
							FROM
								transaction_purchase TP
                                JOIN master_supplier MS
									ON MS.SupplierID = TP.SupplierID
                                LEFT JOIN transaction_purchasedetails TPD
									ON TP.PurchaseID = TPD.PurchaseID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TPD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TP.PurchaseID
                        )TPD
							ON TPD.PurchaseID = TP.PurchaseID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select purchase details by PurchaseID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseDetails;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseDetails (
	pPurchaseID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		PD.PurchaseDetailsID,
        PD.ItemID,
        PD.BranchID,
        CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        PD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID) UnitID,
        MU.UnitName,
        IFNULL(MID.ConversionQuantity, 1) ConversionQuantity,
        IFNULL(MID.ConversionQuantity, 1) * PD.BuyPrice BuyPrice,
        CASE
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price2
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price1
            WHEN MID.ConversionQuantity IS NULL
			THEN PD.RetailPrice
			ELSE IFNULL(MID.ConversionQuantity, 1) * PD.RetailPrice
		END RetailPrice,
        CASE
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price2
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price1
            WHEN MID.ConversionQuantity IS NULL
			THEN PD.Price1
			ELSE IFNULL(MID.ConversionQuantity, 1) * PD.RetailPrice
		END Price1,
        CASE
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty2 AND MI.Qty2 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price2
			WHEN IFNULL(MID.ConversionQuantity, 1) >= MI.Qty1 AND MI.Qty1 > 1
			THEN IFNULL(MID.ConversionQuantity, 1) * PD.Price1
            WHEN MID.ConversionQuantity IS NULL
			THEN PD.Price2
			ELSE IFNULL(MID.ConversionQuantity, 1) * PD.RetailPrice
		END Price2,
        CONCAT('[', GROUP_CONCAT(AU.AvailableUnit SEPARATOR ', '), ']') AvailableUnit,
        PD.ItemDetailsID
	FROM
		transaction_purchasedetails PD
        JOIN master_branch MB
			ON MB.BranchID = PD.BranchID
		JOIN master_item MI
			ON MI.ItemID = PD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = PD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		JOIN 
        (
			SELECT
				MI.ItemID,
				CONCAT('[', 
							MU.UnitID, ',"', 
                            MU.UnitName, '", 
                            "NULL", "', 
                            MI.ItemCode, '", ', 
                            MI.BuyPrice, ', ', 
                            MI.RetailPrice, ', ', 
                            MI.Price1, ', ', 
                            MI.Price2 , ', ', 
                            MI.Qty1, ', ', 
                            MI.Qty2, ', ', 
                            1, 
						']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', 
							MU.UnitID, ',"', 
							MU.UnitName, '",', 
                            MID.ItemDetailsID, ',"', 
                            MID.ItemDetailsCode, '", ', 
                            MI.BuyPrice, ', ', 
                            MI.RetailPrice, ', ', 
                            MI.Price1, ', ', 
                            MI.Price2, ', ', 
                            MI.Qty1, ', ', 
                            MI.Qty2, ', ', 
                            MID.ConversionQuantity,
						']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_itemdetails MID
					ON MID.UnitID = MU.UnitID
				JOIN master_item MI
					ON MI.ItemID = MID.ItemID
			GROUP BY
				MID.ItemDetailsID
		)AU
			ON AU.ItemID = PD.ItemID
	WHERE
		PD.PurchaseID = pPurchaseID
	GROUP BY
		PD.PurchaseDetailsID,
        PD.ItemID,
        PD.BranchID,
		MB.BranchCode,
		MB.BranchName,
		IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        IFNULL(MID.UnitID, MI.UnitID),
        PD.Quantity,
        MU.UnitName,
        PD.BuyPrice,
        PD.RetailPrice,
        PD.Price1,
        PD.Price2,
        PD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1)
	ORDER BY
		PD.PurchaseDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseDetailsReport;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseDetailsReport (
	pPurchaseID			BIGINT,
	pBranchID			INT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseDetailsReport', pCurrentUser);
	END;
	
SET State = 1;

	IF(pTransactionType = 'Pembelian')
	THEN
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        PD.Quantity,
            MU.UnitName,
	        PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
			(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) SubTotal
		FROM
			transaction_purchasedetails PD
	        JOIN master_item MI
				ON MI.ItemID = PD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = PD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			PD.PurchaseID = pPurchaseID
            AND CASE
					WHEN pBranchID = 0
					THEN PD.BranchID
					ELSE pBranchID
				END = PD.BranchID
		ORDER BY
			PD.PurchaseDetailsID;
	ELSE
		SELECT
			MI.ItemCode,
	        MI.ItemName,
	        PRD.Quantity,
            MU.UnitName,
	        PRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1) BuyPrice,
            -(PRD.Quantity * PRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) SubTotal
		FROM
			transaction_purchasereturndetails PRD
	        JOIN master_item MI
				ON MI.ItemID = PRD.ItemID
			LEFT JOIN master_itemdetails MID
				ON MID.ItemDetailsID = PRD.ItemDetailsID
			LEFT JOIN master_unit MU
				ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		WHERE
			PRD.PurchaseReturnID = pPurchaseID
            AND CASE
					WHEN pBranchID = 0
					THEN PRD.BranchID
					ELSE pBranchID
				END = PRD.BranchID
		ORDER BY
			PRD.PurchaseReturnDetailsID;
            
	END IF;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseReport;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseReport (
	pBranchID		INT,
	pFromDate		DATE,
	pToDate			DATE,
	pWhere 			TEXT,
    pWhere2			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseReport', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows,
						SUM(Total) GrandTotal
					FROM
						(
							SELECT
								SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total
							FROM
								transaction_purchase TP
								JOIN transaction_purchasedetails PD
									ON TP.PurchaseID = PD.PurchaseID
								JOIN master_supplier MS
									ON MS.SupplierID = TP.SupplierID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = PD.ItemDetailsID
							WHERE 
								CASE
									WHEN ",pBranchID," = 0
									THEN PD.BranchID
									ELSE ",pBranchID,"
								END = PD.BranchID
								AND CAST(TP.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TP.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere, "
							GROUP BY
								TP.PurchaseID
							UNION ALL
		                    SELECT
								-SUM(PRD.Quantity * PRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total
							FROM
								transaction_purchasereturn TPR
								JOIN transaction_purchasereturndetails PRD
									ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
								JOIN master_supplier MS
									ON MS.SupplierID = TPR.SupplierID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = PRD.ItemDetailsID
							WHERE 
								CASE
									WHEN ",pBranchID," = 0
									THEN PRD.BranchID
									ELSE ",pBranchID,"
								END = PRD.BranchID
								AND CAST(TPR.TransactionDate AS DATE) >= '",pFromDate,"'
								AND CAST(TPR.TransactionDate AS DATE) <= '",pToDate,"'
								AND ", pWhere2, "
		                    GROUP BY
								TPR.PurchaseReturnID
						) TS"
					);
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TP.PurchaseID,
                        'Pembelian' TransactionType,
						TP.PurchaseNumber,
						DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') TransactionDate,
						MS.SupplierName,
						SUM(PD.Quantity * PD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total
					FROM
						transaction_purchase TP
						JOIN transaction_purchasedetails PD
							ON TP.PurchaseID = PD.PurchaseID
						JOIN master_supplier MS
							ON MS.SupplierID = TP.SupplierID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = PD.ItemDetailsID
					WHERE 
						CASE
							WHEN ",pBranchID," = 0
							THEN PD.BranchID
							ELSE ",pBranchID,"
						END = PD.BranchID
						AND CAST(TP.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TP.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere, "
                    GROUP BY
						TP.PurchaseID,
						TP.PurchaseNumber,
						TP.TransactionDate,
						MS.SupplierName
					UNION ALL
                    SELECT
						TPR.PurchaseReturnID,
                        'Retur' TransactionType,
                        TPR.PurchaseReturnNumber,
                        DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
						MS.SupplierName,
						-SUM(PRD.Quantity * PRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total
					FROM
						transaction_purchasereturn TPR
						JOIN transaction_purchasereturndetails PRD
							ON TPR.PurchaseReturnID = PRD.PurchaseReturnID
						JOIN master_supplier MS
							ON MS.SupplierID = TPR.SupplierID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = PRD.ItemDetailsID
					WHERE 
						CASE
							WHEN ",pBranchID," = 0
							THEN PRD.BranchID
							ELSE ",pBranchID,"
						END = PRD.BranchID
						AND CAST(TPR.TransactionDate AS DATE) >= '",pFromDate,"'
						AND CAST(TPR.TransactionDate AS DATE) <= '",pToDate,"'
						AND ", pWhere2, "
					GROUP BY
						TPR.PurchaseReturnID,
                        TPR.PurchaseReturnNumber,
                        TPR.TransactionDate,
                        MS.SupplierName
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
                    
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select return purchase transaction
Created Date: 22 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseReturn;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseReturn (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseReturn', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_purchasereturn TPR
                        JOIN master_supplier MS
							ON MS.SupplierID = TPR.SupplierID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TPR.PurchaseReturnID,
						TPR.PurchaseReturnNumber,
                        DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TransactionDate PlainTransactionDate,
                        MS.SupplierID,
                        MS.SupplierName,
						IFNULL(TPRD.Total, 0) Total
					FROM
						transaction_purchasereturn TPR
                        JOIN master_supplier MS
							ON MS.SupplierID = TPR.SupplierID
						LEFT JOIN
                        (
							SELECT
								TPR.PurchaseReturnID,
                                SUM(TPRD.Quantity * TPRD.BuyPrice * IFNULL(MID.ConversionQuantity, 1)) Total
							FROM
								transaction_purchasereturn TPR
                                JOIN master_supplier MS
									ON MS.SupplierID = TPR.SupplierID
                                LEFT JOIN transaction_purchasereturndetails TPRD
									ON TPRD.PurchaseReturnID = TPR.PurchaseReturnID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = TPRD.ItemDetailsID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TPRD.PurchaseReturnID
                        )TPRD
							ON TPR.PurchaseReturnID = TPRD.PurchaseReturnID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select return purchase details by PurchaseReturnID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPurchaseReturnDetails;

DELIMITER $$
CREATE PROCEDURE spSelPurchaseReturnDetails (
	pPurchaseReturnID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelPurchaseReturnDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TPRD.PurchaseReturnDetailsID,
        TPRD.ItemID,
        TPRD.BranchID,
        CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        TPRD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID) UnitID,
        MU.UnitName,
        IFNULL(MID.ConversionQuantity, 1) ConversionQuantity,
        IFNULL(MID.ConversionQuantity, 1) * TPRD.BuyPrice BuyPrice,
        CONCAT('[', GROUP_CONCAT(AU.AvailableUnit SEPARATOR ', '), ']') AvailableUnit,
        TPRD.ItemDetailsID
	FROM
		transaction_purchasereturndetails TPRD
        JOIN master_branch MB
			ON MB.BranchID = TPRD.BranchID
		JOIN master_item MI
			ON MI.ItemID = TPRD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = TPRD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		JOIN 
        (
			SELECT
				MI.ItemID,
				CONCAT('[', 
							MU.UnitID, ',"', 
                            MU.UnitName, '", 
                            "NULL", "', 
                            MI.ItemCode, '", ', 
                            MI.BuyPrice, ', ', 
                            MI.RetailPrice, ', ', 
                            MI.Price1, ', ', 
                            MI.Price2, ', ',
                            1,
						']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[',
							MU.UnitID, ',"', 
                            MU.UnitName, '",', 
                            MID.ItemDetailsID, ',"', 
                            MID.ItemDetailsCode, '", ', 
                            MI.BuyPrice, ', ', 
                            MI.RetailPrice, ', ', 
                            MI.Price1, ', ', 
                            MI.Price2 , ', ',
                            MID.ConversionQuantity,
						']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_itemdetails MID
					ON MID.UnitID = MU.UnitID
				JOIN master_item MI
					ON MI.ItemID = MID.ItemID
			GROUP BY
				MID.ItemDetailsID
		)AU
			ON AU.ItemID = TPRD.ItemID
	WHERE
		TPRD.PurchaseReturnID = pPurchaseReturnID
	GROUP BY
		TPRD.PurchaseReturnDetailsID,
        TPRD.ItemID,
        TPRD.BranchID,
        MB.BranchCode,
        MB.BranchName,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        TPRD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID),
		MU.UnitName,
		TPRD.BuyPrice,
		TPRD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1)
	ORDER BY
		TPRD.PurchaseReturnDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleDetails;

DELIMITER $$
CREATE PROCEDURE spSelSaleDetails (
	pSaleID			BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleDetails', pCurrentUser);
	END;
	
SET State = 1;
	SELECT
		SD.SaleDetailsID,
        SD.ItemID,
        SD.BranchID,
        MB.BranchName,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        SD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID) UnitID,
        MU.UnitName,
        SD.BuyPrice,
        IFNULL(MID.ConversionQuantity, 1) * SD.SalePrice SalePrice,
		SD.Discount,
		MI.RetailPrice,
        MI.Price1,
        MI.Qty1,
        MI.Price2,
        MI.Qty2,
		MI.Weight,
        CONCAT('[', GROUP_CONCAT(AU.AvailableUnit SEPARATOR ', '), ']') AvailableUnit,
        SD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1) ConversionQty
	FROM
		transaction_saledetails SD
        JOIN master_branch MB
			ON MB.BranchID = SD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		JOIN 
        (
			SELECT
				MI.ItemID,
				CONCAT('[', 
							MU.UnitID, ',"', 
                            MU.UnitName, '", 
                            "NULL", "', 
                            MI.ItemCode, '", ', 
                            MI.BuyPrice, ', ', 
                            MI.RetailPrice, ', ', 
                            MI.Price1, ', ', 
                            MI.Price2, ', ', 
                            MI.Qty1, ', ', 
                            MI.Qty2, ', ', 
                            1,
						']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', 
							MU.UnitID, ',"', 
                            MU.UnitName, '",', 
                            MID.ItemDetailsID, ',"', 
                            MID.ItemDetailsCode, '", ', 
                            MI.BuyPrice, ', ', 
                            MI.RetailPrice, ', ', 
                            MI.Price1, ', ', 
                            MI.Price2, ', ', 
                            MI.Qty1, ', ', 
                            MI.Qty2, ', ', 
                            MID.ConversionQuantity,
						']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_itemdetails MID
					ON MID.UnitID = MU.UnitID
				JOIN master_item MI
					ON MI.ItemID = MID.ItemID
			GROUP BY
				MID.ItemDetailsID
		)AU
			ON AU.ItemID = SD.ItemID
	WHERE
		SD.SaleID = pSaleID
	GROUP BY
		SD.SaleDetailsID,
        SD.ItemID,
        SD.BranchID,
        MB.BranchName,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        SD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID),
        SD.BuyPrice,
        SD.SalePrice,
		SD.Discount,
		MI.RetailPrice,
        MI.Price1,
        MI.Qty1,
        MI.Price2,
        MI.Qty2,
		MI.Weight,
        SD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1)
	ORDER BY
		SD.SaleDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleNumber
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleDetailsByNumber;

DELIMITER $$
CREATE PROCEDURE spSelSaleDetailsByNumber (
	pSaleNumber		VARCHAR(100),
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
		CALL spInsEventLog(@full_error, 'spSelSaleDetailsByNumber', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TS.SaleID,
		SD.SaleDetailsID,
        SD.ItemID,
        SD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        (SD.Quantity * IFNULL(MID.ConversionQuantity, 1) - IFNULL(TSR.Quantity, 0)) Quantity,
        SD.BuyPrice,
        SD.SalePrice - (SD.Discount / IFNULL(MID.ConversionQuantity, 1)) SalePrice,
        MC.CustomerName,
        MU.UnitName
	FROM
		transaction_sale TS
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
		JOIN transaction_saledetails SD
			ON TS.SaleID = SD.SaleID
        JOIN master_branch MB
			ON MB.BranchID = SD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SD.ItemID
		JOIN master_unit MU
			ON MI.UnitID = MU.UnitID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SD.ItemDetailsID
		LEFT JOIN
		(
			SELECT
				SR.SaleID,
				SRD.ItemID,
				SRD.SaleDetailsID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SR.SaleReturnID = SRD.SaleReturnID
			GROUP BY
				SR.SaleID,
				SRD.ItemID,
				SRD.SaleDetailsID
		)TSR
			ON TSR.SaleID = TS.SaleID
			AND MI.ItemID = TSR.ItemID
			AND TSR.SaleDetailsID = SD.SaleDetailsID
	WHERE
		TRIM(TS.SaleNumber) = TRIM(pSaleNumber)
	ORDER BY
		SD.SaleDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleDetailsPrint;

DELIMITER $$
CREATE PROCEDURE spSelSaleDetailsPrint (
	pSaleDetailsID	TEXT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleDetailsPrint', pCurrentUser);
	END;
	
SET State = 1;

	IF(pSaleDetailsID <> "" ) THEN
		SET @query = CONCAT("SELECT
								SD.Quantity,
								MI.ItemName,
                                MU.UnitName
							 FROM
								transaction_saledetails SD
								JOIN master_item MI
									ON MI.ItemID = SD.ItemID
								LEFT JOIN master_itemdetails MID
									ON MID.ItemDetailsID = SD.ItemDetailsID
								JOIN master_unit MU
									ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
							WHERE
								SD.SaleDetailsID IN ", pSaleDetailsID );
							
		PREPARE stmt FROM @query;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
SET State = 2;
		SET @query = CONCAT("UPDATE transaction_saledetails
							SET
								PrintCount = IFNULL(PrintCount, 0) + 1,
								PrintedDate = NOW()
							WHERE
								SaleDetailsID IN ", pSaleDetailsID );
							
		PREPARE stmt FROM @query;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
	END IF;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleHeader;

DELIMITER $$
CREATE PROCEDURE spSelSaleHeader (
	pSaleID			BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleHeader', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TS.SaleNumber,
		DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') TransactionDate,
		TS.CreatedBy,
		MC.CustomerName,
		MC.Address,
		MC.City,
		MC.Telephone
	FROM
		transaction_sale TS
		JOIN master_customer MC
			ON MC.CustomerID = TS.CustomerID
	WHERE
		TS.SaleID = pSaleID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleReturn;

DELIMITER $$
CREATE PROCEDURE spSelSaleReturn (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleReturn', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						TSR.SaleReturnID,
                        TS.SaleNumber,
                        DATE_FORMAT(TSR.TransactionDate, '%d-%m-%Y') TransactionDate,
                        TSR.TransactionDate PlainTransactionDate,
                        MC.CustomerID,
                        MC.CustomerName,
						IFNULL(TSRD.Total, 0) Total
					FROM
						transaction_salereturn TSR
						JOIN transaction_sale TS
							ON TS.SaleID = TSR.SaleID
                        JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
						LEFT JOIN
                        (
							SELECT
								TSR.SaleReturnID,
                                SUM(TSRD.Quantity * TSRD.SalePrice) Total
							FROM
								transaction_sale TS
                                JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
								JOIN transaction_salereturn TSR
									ON TS.SaleID = TSR.SaleID
                                LEFT JOIN transaction_salereturndetails TSRD
									ON TSR.SaleReturnID = TSRD.SaleReturnID
								LEFT JOIN master_item MI
									ON MI.ItemID = TSRD.ItemID
							WHERE ", 
								pWhere, 
                            " GROUP BY
								TSR.SaleReturnID
                        )TSRD
							ON TSRD.SaleReturnID = TSR.SaleReturnID
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale return details
Created Date: 24 February 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelSaleReturnDetails;

DELIMITER $$
CREATE PROCEDURE spSelSaleReturnDetails (
	pSaleReturnID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelSaleReturnDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TSR.SaleID,
		TSR.SaleReturnID,
		TSRD.SaleReturnDetailsID,
		TSRD.SaleDetailsID,
        TSRD.ItemID,
        TSRD.BranchID,
        MI.ItemCode,
        MI.ItemName,
        TSRD.Quantity,
        TSRD.BuyPrice,
        TSRD.SalePrice,
        (IFNULL(TS.Quantity, 0) - IFNULL(SR.Quantity, 0) + IFNULL(TSRD.Quantity, 0)) Maksimum,
        MU.UnitName
	FROM
		transaction_salereturn TSR
		JOIN transaction_salereturndetails TSRD
			ON TSRD.SaleReturnID = TSR.SaleReturnID
		JOIN master_item MI
			ON MI.ItemID = TSRD.ItemID
		JOIN master_unit MU
			ON MU.UnitID = MI.UnitID
        LEFT JOIN
		(
			SELECT
				TS.SaleID,
				SD.ItemID,
				SD.SaleDetailsID,
				SUM(SD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_sale TS
				JOIN transaction_saledetails SD
					ON TS.SaleID = SD.SaleID
				LEFT JOIN master_itemdetails MID
					ON MID.ItemDetailsID = SD.ItemDetailsID
			GROUP BY
				TS.SaleID,
				SD.ItemID,
				SD.SaleDetailsID
		)TS
			ON TSR.SaleID = TS.SaleID
			AND MI.ItemID = TS.ItemID
			AND TSRD.SaleDetailsID = TS.SaleDetailsID
		LEFT JOIN
		(
			SELECT
				SR.SaleID,
				SRD.ItemID,
				SRD.SaleDetailsID,
				SUM(SRD.Quantity) Quantity
			FROM
				transaction_salereturn SR
				JOIN transaction_salereturndetails SRD
					ON SR.SaleReturnID = SRD.SaleReturnID
			GROUP BY
				SR.SaleID,
				SRD.ItemID,
				SRD.SaleDetailsID
		)SR
			ON TSR.SaleID = SR.SaleID
			AND MI.ItemID = SR.ItemID
			AND TSRD.SaleDetailsID = SR.SaleDetailsID

	WHERE
		TSR.SaleReturnID = pSaleReturnID
	ORDER BY
		TSRD.SaleReturnDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockAdjust;

DELIMITER $$
CREATE PROCEDURE spSelStockAdjust (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelStockAdjust', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_stockadjust SA
						JOIN transaction_stockadjustdetails SAD
							ON SA.StockAdjustID = SAD.StockAdjustID
                        JOIN master_branch MB
							ON MB.BranchID = SAD.BranchID
						JOIN master_item MI
							ON MI.ItemID = SAD.ItemID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SAD.ItemDetailsID
						LEFT JOIN master_unit MU
							ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;
		
SET @query = CONCAT("SELECT
						SA.StockAdjustID,
						SAD.StockAdjustDetailsID,
                        DATE_FORMAT(SA.TransactionDate, '%d-%m-%Y') TransactionDate,
                        SA.TransactionDate PlainTransactionDate,
                        MB.BranchID,
                        CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
                       	MI.ItemID,
                        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
                        MI.ItemName,
                        MU.UnitID,
                        MU.UnitName,
                        ROUND(SAD.Quantity, 2) Quantity,
                        ROUND(SAD.AdjustedQuantity, 2) AdjustedQuantity
					FROM
						transaction_stockadjust SA
						JOIN transaction_stockadjustdetails SAD
							ON SA.StockAdjustID = SAD.StockAdjustID
                        JOIN master_branch MB
							ON MB.BranchID = SAD.BranchID
						JOIN master_item MI
							ON MI.ItemID = SAD.ItemID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SAD.ItemDetailsID
						LEFT JOIN master_unit MU
							ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockAdjustDetails;

DELIMITER $$
CREATE PROCEDURE spSelStockAdjustDetails (
	pStockAdjustID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelStockAdjustDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		SA.StockAdjustID,
		SAD.StockAdjustDetailsID,
		SAD.ItemID,
		SAD.BranchID,
		CONCAT(MB.BranchCode, ' - ', MB.BranchName) BranchName,
		IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        ROUND(SAD.Quantity, 2) Quantity,
        ROUND(SAD.AdjustedQuantity, 2) AdjustedQuantity,
        IFNULL(MID.UnitID, MI.UnitID) UnitID,
        MU.UnitName,
        CONCAT('[', GROUP_CONCAT(AU.AvailableUnit SEPARATOR ', '), ']') AvailableUnit,
        SAD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1) ConversionQty
	FROM
		transaction_stockadjust SA
		JOIN transaction_stockadjustdetails SAD
			ON SA.StockAdjustID = SAD.StockAdjustID
        JOIN master_branch MB
			ON MB.BranchID = SAD.BranchID
		JOIN master_item MI
			ON MI.ItemID = SAD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SAD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		JOIN 
        (
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '", "NULL", "', MI.ItemCode, '"]') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '",', MID.ItemDetailsID, ',"', MID.ItemDetailsCode, '"]') AvailableUnit
			FROM
				master_unit MU
				JOIN master_itemdetails MID
					ON MID.UnitID = MU.UnitID
				JOIN master_item MI
					ON MI.ItemID = MID.ItemID
			GROUP BY
				MID.ItemDetailsID
		)AU
			ON AU.ItemID = SAD.ItemID
	WHERE
		SA.StockAdjustID = pStockAdjustID
	GROUP BY
		SA.StockAdjustID,
		SAD.StockAdjustDetailsID,
		SAD.ItemID,
		SAD.BranchID,
		CONCAT(MB.BranchCode, ' - ', MB.BranchName),
		IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        SAD.Quantity,
        SAD.AdjustedQuantity,
        IFNULL(MID.UnitID, MI.UnitID),
        MU.UnitName,
        SAD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1)
	ORDER BY
		SAD.StockAdjustDetailsID;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockMutation;

DELIMITER $$
CREATE PROCEDURE spSelStockMutation (
	pWhere 			TEXT,
	pOrder			TEXT,
	pLimit_s		BIGINT,
    pLimit_l		INT,
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
		CALL spInsEventLog(@full_error, 'spSelStockMutation', pCurrentUser);
	END;
	
SET State = 1;

SET @query = CONCAT("SELECT
						COUNT(1) AS nRows
					FROM
						transaction_stockmutation SM
						JOIN transaction_stockmutationdetails SMD
							ON SM.StockMutationID = SMD.StockMutationID
                        JOIN master_branch SB
							ON SB.BranchID = SMD.SourceID
						JOIN master_branch DB
							ON DB.BranchID = SMD.DestinationID
						JOIN master_item MI
							ON MI.ItemID = SMD.ItemID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SMD.ItemDetailsID
						LEFT JOIN master_unit MU
							ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
					WHERE ", pWhere);
						
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						SM.StockMutationID,
						SMD.StockMutationDetailsID,
                        DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') TransactionDate,
                        SM.TransactionDate PlainTransactionDate,
                        CONCAT(SB.BranchCode, ' - ', SB.BranchName) SourceBranchName,
                        CONCAT(DB.BranchCode, ' - ', DB.BranchName) DestinationBranchName,
                        MI.ItemID,
                        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
                        MI.ItemName,
                        MU.UnitID,
                        SMD.Quantity,
                        SMD.SourceID,
                        SMD.DestinationID,
                        MU.UnitName,
                        MU.UnitID
					FROM
						transaction_stockmutation SM
						JOIN transaction_stockmutationdetails SMD
							ON SM.StockMutationID = SMD.StockMutationID
                        JOIN master_branch SB
							ON SB.BranchID = SMD.SourceID
						JOIN master_branch DB
							ON DB.BranchID = SMD.DestinationID
						JOIN master_item MI
							ON MI.ItemID = SMD.ItemID
						LEFT JOIN master_itemdetails MID
							ON MID.ItemDetailsID = SMD.ItemDetailsID
						LEFT JOIN master_unit MU
							ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
					WHERE ", pWhere, 
					" ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
					
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale details by SaleID
Created Date: 12 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelStockMutationDetails;

DELIMITER $$
CREATE PROCEDURE spSelStockMutationDetails (
	pStockMutationID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelStockMutationDetails', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		SM.StockMutationID,
		SMD.StockMutationDetailsID,
		MI.ItemID,
		SMD.SourceID,
		SMD.DestinationID,
		CONCAT(SB.BranchCode, ' - ', SB.BranchName) SourceBranchName,
		CONCAT(DB.BranchCode, ' - ', DB.BranchName) DestinationBranchName,
        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        SMD.Quantity,
        MU.UnitName,
        MU.UnitID,
        CONCAT('[', GROUP_CONCAT(AU.AvailableUnit SEPARATOR ', '), ']') AvailableUnit,
        SMD.ItemDetailsID
	FROM
		transaction_stockmutation SM
		JOIN transaction_stockmutationdetails SMD
			ON SM.StockMutationID = SMD.StockMutationID
        JOIN master_branch SB
			ON SB.BranchID = SMD.SourceID
		JOIN master_branch DB
			ON DB.BranchID = SMD.DestinationID
		JOIN master_item MI
			ON MI.ItemID = SMD.ItemID
		LEFT JOIN master_itemdetails MID
			ON MID.ItemDetailsID = SMD.ItemDetailsID
		LEFT JOIN master_unit MU
			ON MU.UnitID = IFNULL(MID.UnitID, MI.UnitID)
		JOIN 
        (
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '", "NULL", "', MI.ItemCode, '"]') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '",', MID.ItemDetailsID, ',"', MID.ItemDetailsCode, '"]') AvailableUnit
			FROM
				master_unit MU
				JOIN master_itemdetails MID
					ON MID.UnitID = MU.UnitID
				JOIN master_item MI
					ON MI.ItemID = MID.ItemID
			GROUP BY
				MID.ItemDetailsID
		)AU
			ON AU.ItemID = SMD.ItemID
	WHERE
		SM.StockMutationID = pStockMutationID
	GROUP BY
		SM.StockMutationID,
		SMD.StockMutationDetailsID,
		MI.ItemID,
		SMD.SourceID,
		SMD.DestinationID,
		CONCAT(SB.BranchCode, ' - ', SB.BranchName),
		CONCAT(DB.BranchCode, ' - ', DB.BranchName),
        IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        SMD.Quantity,
        MU.UnitName,
        MU.UnitID,
        SMD.ItemDetailsID
	ORDER BY
		SMD.StockMutationDetailsID;

END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdBooking;

DELIMITER $$
CREATE PROCEDURE spUpdBooking (
	pID 				BIGINT, 
	pCustomerID 		BIGINT,
	pTransactionDate	DATETIME,
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdBooking', pCurrentUser);
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
			UPDATE
				transaction_booking
			SET
				CustomerID = pCustomerID,
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				BookingID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Perubahan berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdBookingDetailsBranch;

DELIMITER $$
CREATE PROCEDURE spUpdBookingDetailsBranch (
	pID 			BIGINT, 
	pBranchID 		INT,
	pCurrentUser	VARCHAR(255)
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdBookingDetailsBranch', pCurrentUser);
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
			UPDATE
				transaction_bookingdetails
			SET
				BranchID = pBranchID,
				ModifiedBy = pCurrentUser
			WHERE
				BookingDetailsID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Cabang berhasil diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdBookingPayment;

DELIMITER $$
CREATE PROCEDURE spUpdBookingPayment (
	pID 			BIGINT, 
	pPayment 		DOUBLE,
	pPaymentTypeID	SMALLINT,
    pFinishFlag		BIT,
	pCurrentUser	VARCHAR(255)
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdBookingPayment', pCurrentUser);
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
		UPDATE
			transaction_booking
		SET
			Payment = pPayment,
            FinishFlag = pFinishFlag,
			PaymentTypeID = pPaymentTypeID,
			ModifiedBy = pCurrentUser
		WHERE
			BookingID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Pembayaran berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdFirstStock;

DELIMITER $$
CREATE PROCEDURE spUpdFirstStock (
	pID 				BIGINT,
    pFirstStockNumber	VARCHAR(100),
	pTransactionDate	DATETIME,
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdFirstStock', pCurrentUser);
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
			UPDATE
				transaction_firststock
			SET
				FirstStockNumber = pFirstStockNumber,
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				FirstStockID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Perubahan berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdItem;

DELIMITER $$
CREATE PROCEDURE spUpdItem (
	pID 				BIGINT,
	pBuyPrice			DOUBLE,
    pRetailPrice		DOUBLE,
    pPrice1				DOUBLE,
    pQty1				DOUBLE,
    pPrice2				DOUBLE,
    pQty2				DOUBLE,
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
		CALL spInsEventLog(@full_error, 'spUpdItem', pCurrentUser);
		SELECT
			pID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	SET PassValidate = 0;
	
	START TRANSACTION;
	
SET State = 1;

		SELECT 
			1
		INTO
			PassValidate
		FROM 
			master_item
		WHERE
			ItemID = pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'ID tidak valid!' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			UPDATE
				master_item
			SET
				BuyPrice = pBuyPrice,
				RetailPrice = pRetailPrice,
				Price1 = pPrice1,
				Qty1 = pQty1,
				Price2 = pPrice2,
				Qty2 = pQty2,
				ModifiedBy = pCurrentUser
			WHERE
				ItemID = pID;

SET State = 4;
			SELECT
				pID AS 'ID',
				'Barang Berhasil Diubah' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';

		END IF;
	COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdPurchase;

DELIMITER $$
CREATE PROCEDURE spUpdPurchase (
	pID 				BIGINT,
    pPurchaseNumber		VARCHAR(100),
	pSupplierID 		BIGINT,
	pTransactionDate	DATETIME,
    pDeadline			DATETIME,
    pPaymentTypeID		SMALLINT,
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdPurchase', pCurrentUser);
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
			UPDATE
				transaction_purchase
			SET
				PurchaseNumber = pPurchaseNumber,
				SupplierID = pSupplierID,
				TransactionDate = pTransactionDate,
                Deadline = pDeadline,
                PaymentTypeID = pPaymentTypeID,
				ModifiedBy = pCurrentUser
			WHERE
				PurchaseID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Perubahan berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdPurchaseReturn;

DELIMITER $$
CREATE PROCEDURE spUpdPurchaseReturn (
	pID 				BIGINT,
    pSupplierID 		BIGINT,
	pTransactionDate	DATETIME,
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdPurchaseReturn', pCurrentUser);
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
			UPDATE
				transaction_purchasereturn
			SET
				SupplierID = pSupplierID,
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				PurchaseReturnID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Perubahan berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdSale;

DELIMITER $$
CREATE PROCEDURE spUpdSale (
	pID 				BIGINT, 
	pCustomerID 		BIGINT,
	pTransactionDate	DATETIME,
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdSale', pCurrentUser);
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
			UPDATE
				transaction_sale
			SET
				CustomerID = pCustomerID,
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				SaleID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Perubahan berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdSaleDetailsBranch;

DELIMITER $$
CREATE PROCEDURE spUpdSaleDetailsBranch (
	pID 			BIGINT, 
	pBranchID 		INT,
	pCurrentUser	VARCHAR(255)
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdSaleDetailsBranch', pCurrentUser);
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
			UPDATE
				transaction_saledetails
			SET
				BranchID = pBranchID,
				ModifiedBy = pCurrentUser
			WHERE
				SaleDetailsID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Cabang berhasil diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spUpdUserPassword;

DELIMITER $$
CREATE PROCEDURE spUpdUserPassword (
	pID 			BIGINT, 
	pPassword 		VARCHAR(255),
	pCurrentUser	VARCHAR(255)
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
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spUpdUserPassword', pCurrentUser);
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
			UPDATE
				master_user
			SET
				UserPassword = pPassword,
				ModifiedBy = pCurrentUser
			WHERE
				UserID = pID;

SET State = 2;
		SELECT
			pID AS 'ID',
			'Password berhasil diubah' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

    COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item
Created Date: 2 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spValItem;

DELIMITER $$
CREATE PROCEDURE spValItem (
	pID				BIGINT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spValItem', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		1
	FROM
		master_item MI
	WHERE
		MI.ItemID = pID;

END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete booking
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelBooking;

DELIMITER $$
CREATE PROCEDURE spDelBooking (
	pBookingID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelBooking', pCurrentUser);
        SELECT
			pBookingID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_booking
		WHERE
			BookingID = pBookingID;

    COMMIT;
    
SET State = 2;

		SELECT
			pBookingID AS 'ID',
			'Pemesanan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete booking details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelBookingDetails;

DELIMITER $$
CREATE PROCEDURE spDelBookingDetails (
	pBookingDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelBookingDetails', pCurrentUser);
        SELECT
			pBookingDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_bookingdetails
		WHERE
			BookingDetailsID = pBookingDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pBookingDetailsID AS 'ID',
			'Pemesanan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete firststock
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelFirstStock;

DELIMITER $$
CREATE PROCEDURE spDelFirstStock (
	pFirstStockID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelFirstStock', pCurrentUser);
        SELECT
			pFirstStockID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_firststock
		WHERE
			FirstStockID = pFirstStockID;

    COMMIT;
    
SET State = 2;

		SELECT
			pFirstStockID AS 'ID',
			'Pembelian berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete firststock details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelFirstStockDetails;

DELIMITER $$
CREATE PROCEDURE spDelFirstStockDetails (
	pFirstStockDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelFirstStockDetails', pCurrentUser);
        SELECT
			pFirstStockID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_firststockdetails
		WHERE
			FirstStockDetailsID = pFirstStockDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pFirstStockDetailsID AS 'ID',
			'Pembelian berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
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
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale return
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPickUp;

DELIMITER $$
CREATE PROCEDURE spDelPickUp (
	pPickUpID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelPickUp', pCurrentUser);
        SELECT
			pPickUpID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_pick
		WHERE
			PickID = pPickUpID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPickUpID AS 'ID',
			'Pengambilan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete purchase
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPurchase;

DELIMITER $$
CREATE PROCEDURE spDelPurchase (
	pPurchaseID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelPurchase', pCurrentUser);
        SELECT
			pPurchaseID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_purchase
		WHERE
			PurchaseID = pPurchaseID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPurchaseID AS 'ID',
			'Pembelian berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete purchase details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPurchaseDetails;

DELIMITER $$
CREATE PROCEDURE spDelPurchaseDetails (
	pPurchaseDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelPurchaseDetails', pCurrentUser);
        SELECT
			pPurchaseID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_purchasedetails
		WHERE
			PurchaseDetailsID = pPurchaseDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPurchaseDetailsID AS 'ID',
			'Pembelian berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete return purchase
Created Date: 23 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPurchaseReturn;

DELIMITER $$
CREATE PROCEDURE spDelPurchaseReturn (
	pPurchaseReturnID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelPurchaseReturn', pCurrentUser);
        SELECT
			pPurchaseReturnID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_purchasereturn
		WHERE
			PurchaseReturnID = pPurchaseReturnID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPurchaseReturnID AS 'ID',
			'Retur pembelian berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete return purchase details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPurchaseReturnDetails;

DELIMITER $$
CREATE PROCEDURE spDelPurchaseReturnDetails (
	pPurchaseReturnDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelPurchaseReturnDetails', pCurrentUser);
        SELECT
			pPurchaseReturnID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_purchasereturndetails
		WHERE
			PurchaseReturnDetailsID = pPurchaseReturnDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPurchaseReturnDetailsID AS 'ID',
			'Retur pembelian berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelSale;

DELIMITER $$
CREATE PROCEDURE spDelSale (
	pSaleID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelSale', pCurrentUser);
        SELECT
			pSaleID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_sale
		WHERE
			SaleID = pSaleID;

    COMMIT;
    
SET State = 2;

		SELECT
			pSaleID AS 'ID',
			'Penjualan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelSaleDetails;

DELIMITER $$
CREATE PROCEDURE spDelSaleDetails (
	pSaleDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelSaleDetails', pCurrentUser);
        SELECT
			pSaleDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_saledetails
		WHERE
			SaleDetailsID = pSaleDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pSaleDetailsID AS 'ID',
			'Penjualan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale return
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelSaleReturn;

DELIMITER $$
CREATE PROCEDURE spDelSaleReturn (
	pSaleReturnID		BIGINT,
	pCurrentUser	VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelSaleReturn', pCurrentUser);
        SELECT
			pSaleReturnID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_salereturn
		WHERE
			SaleReturnID = pSaleReturnID;

    COMMIT;
    
SET State = 2;

		SELECT
			pSaleReturnID AS 'ID',
			'Retur penjualan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelStockAdjust;

DELIMITER $$
CREATE PROCEDURE spDelStockAdjust (
	pStockAdjustDetailsID		BIGINT,
	pCurrentUser				VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelStockAdjust', pCurrentUser);
        SELECT
			pStockAdjustDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_stockadjustdetails
		WHERE
			StockAdjustDetailsID = pStockAdjustDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pStockAdjustDetailsID AS 'ID',
			'Adjust Stok berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelStockAdjustDetails;

DELIMITER $$
CREATE PROCEDURE spDelStockAdjustDetails (
	pStockAdjustDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelStockAdjustDetails', pCurrentUser);
        SELECT
			pStockAdjustDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_stockadjustdetails
		WHERE
			StockAdjustDetailsID = pStockAdjustDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pStockAdjustDetailsID AS 'ID',
			'Adjust Stok berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelStockMutation;

DELIMITER $$
CREATE PROCEDURE spDelStockMutation (
	pStockMutationDetailsID		BIGINT,
	pCurrentUser				VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spDelStockMutation', pCurrentUser);
        SELECT
			pStockMutationDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_stockmutationdetails
		WHERE
			StockMutationDetailsID = pStockMutationDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pStockMutationDetailsID AS 'ID',
			'Mutasi Stok berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale details
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelStockMutationDetails;

DELIMITER $$
CREATE PROCEDURE spDelStockMutationDetails (
	pStockMutationDetailsID	BIGINT,
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
		CALL spInsEventLog(@full_error, 'spDelStockMutationDetails', pCurrentUser);
        SELECT
			pStockMutationDetailsID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_stockmutationdetails
		WHERE
			StockMutationDetailsID = pStockMutationDetailsID;

    COMMIT;
    
SET State = 2;

		SELECT
			pStockMutationDetailsID AS 'ID',
			'Mutasi Stok berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsBooking;

DELIMITER $$
CREATE PROCEDURE spInsBooking (
	pID 				BIGINT,
	pBookingNumber		VARCHAR(100),
	pRetailFlag			BIT,
    pFinishFlag			BIT,
    pCustomerID			BIGINT,
	pTransactionDate 	DATETIME,
	pBookingDetailsID	BIGINT,
    pBranchID			INT,
    pItemID				BIGINT,
	pItemDetailsID		BIGINT,
	pQuantity			DOUBLE,
    pBuyPrice			DOUBLE,
    pBookingPrice		DOUBLE,
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
                FinishFlag,
				CustomerID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
				pBookingNumber,
				pRetailFlag,
                pFinishFlag,
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
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert the category
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsCategory;

DELIMITER $$
CREATE PROCEDURE spInsCategory (
	pID 				INT, 
    pCategoryCode		VARCHAR(100),
	pCategoryName 		VARCHAR(255),
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
		CALL spInsEventLog(@full_error, 'spInsCategory', pCurrentUser);
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
			master_category
		WHERE
			TRIM(CategoryCode) = TRIM(pCategoryCode)
			AND CategoryID <> pID
		LIMIT 1;
        
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				CONCAT('Kode Kategori ', pCategoryCode, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
            
		END IF;
        
SET State = 2;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_category
		WHERE
			TRIM(CategoryName) = TRIM(pCategoryName)
            AND CategoryID <> pID
		LIMIT 1;
        
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				CONCAT('Nama Kategori ', pCategoryName, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_category
				(
					CategoryCode,
					CategoryName,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pCategoryCode,
					pCategoryName,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Kategori Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_category
				SET
					CategoryCode = pCategoryCode,
					CategoryName= pCategoryName,
					ModifiedBy = pCurrentUser
				WHERE
					CategoryID = pID;

SET State = 6;
				SELECT
					pID AS 'ID',
					'Kategori Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert the customer
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsCustomer;

DELIMITER $$
CREATE PROCEDURE spInsCustomer (
	pID 				BIGINT, 
    pCustomerCode		VARCHAR(100),
	pCustomerName 		VARCHAR(255),
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
		CALL spInsEventLog(@full_error, 'spInsCustomer', pCurrentUser);
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
			master_customer
		WHERE
			TRIM(CustomerCode) = TRIM(pCustomerCode)
			AND CustomerID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;

			SELECT
				pID AS 'ID',
				CONCAT('Kode Pelanggan ', pCustomerCode, ' sudah ada') AS 'Message',
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
			master_customer
		WHERE
			(TRIM(CustomerName) = TRIM(pCustomerName)
            AND TRIM(Address) = TRIM(pAddress))
			AND CustomerID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 3;

			SELECT
				pID AS 'ID',
				CONCAT('Nama Pelanggan ', pCustomerName, ' dengan alamat ', pAddress, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 4;

			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_customer
				(
                    CustomerCode,
                    CustomerName,
					Telephone,
					Address,
					City,
					Remarks,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pCustomerCode,
					pCustomerName,
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
					'Pelanggan Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 7;

				UPDATE
					master_customer
				SET
					CustomerCode = pCustomerCode,
                    CustomerName = pCustomerName,
					Telephone = pTelephone,
					Address = pAddress,
					City = pCity,
					Remarks = pRemarks,
					ModifiedBy = pCurrentUser
				WHERE
					CustomerID = pID;

SET State = 8;

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
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert event log
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsEventLog;

DELIMITER $$
CREATE PROCEDURE spInsEventLog (
	pDescription	TEXT,
	pSource			VARCHAR(100),
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (State ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spInsEventLog', pCurrentUser);
	END;
	
	START TRANSACTION;
	
SET State = 1;

		INSERT INTO master_eventlog
		(
			EventLogDate,
			Description,
			Source,
			CreatedDate,
			CreatedBy
		)
		VALUES
		(
			NOW(),
			pDescription,
			pSource,
			NOW(),
			pCurrentUser
		);
		
    COMMIT;
END;
$$
DELIMITER ;
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
DROP PROCEDURE IF EXISTS spInsFirstStock;

DELIMITER $$
CREATE PROCEDURE spInsFirstStock (
	pID 					BIGINT,
    pFirstStockNumber		VARCHAR(100),
    pTransactionDate 		DATETIME,
	pFirstStockDetailsID	BIGINT,
    pBranchID				INT,
    pItemID					BIGINT,
    pItemDetailsID			BIGINT,
	pQuantity				DOUBLE,
    pBuyPrice				DOUBLE,
    pRetailPrice			DOUBLE,
    pPrice1					DOUBLE,
    pPrice2					DOUBLE,
    pCurrentUser			VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spInsFirstStock', pCurrentUser);
		SELECT
			pID AS 'ID',
            pFirstStockDetailsID AS 'FirstStockDetailsID',
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
			transaction_firststock
		WHERE
			TRIM(FirstStockNumber) = TRIM(pFirstStockNumber)
			AND FirstStockID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
                pFirstStockDetailsID AS 'FirstStockDetailsID',
				CONCAT('No. Invoice ', pFirstStockNumber, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pID = 0)	THEN /*Tambah baru*/
				INSERT INTO transaction_firststock
				(
                    FirstStockNumber,
                    TransactionDate,
					CreatedDate,
					CreatedBy
				)
				VALUES 
                (
					pFirstStockNumber,
					pTransactionDate,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;	               
				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;
                    
			ELSE
SET State = 5;
				UPDATE
					transaction_firststock
				SET
					FirstStockNumber = pFirstStockNumber,
                    TransactionDate = pTransactionDate,
					ModifiedBy = pCurrentUser
				WHERE
					FirstStockID = pID;
                    
			END IF;
            
SET State = 6;
			
			IF(pFirstStockDetailsID = 0) THEN
				INSERT INTO transaction_firststockdetails
                (
					FirstStockID,
                    ItemID,
                    ItemDetailsID,
                    BranchID,
                    Quantity,
                    BuyPrice,
                    RetailPrice,
                    Price1,
                    Price2,
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
                    pRetailPrice,
                    pPrice1,
                    pPrice2,
                    NOW(),
                    pCurrentUser
                );
                
SET State = 7;
				
				SELECT
					LAST_INSERT_ID()
				INTO 
					pFirstStockDetailsID;
			
			ELSE
					
SET State = 8;
				
				UPDATE 
					transaction_firststockdetails
				SET
					ItemID = pItemID,
                    ItemDetailsID = pItemDetailsID,
                    BranchID = pBranchID,
                    Quantity = pQuantity,
                    BuyPrice = pBuyPrice,
                    RetailPrice = pRetailPrice,
                    Price1 = pPrice1,
                    Price2 = pPrice2,
					ModifiedBy = pCurrentUser
				WHERE
					FirstStockDetailsID = pFirstStockDetailsID;
				
			END IF;
			
SET State = 9;

				UPDATE 
					master_item
				SET
					BuyPrice = pBuyPrice,
					RetailPrice = pRetailPrice,
					Price1 = pPrice1,
					Price2 = pPrice2,
					ModifiedBy = pCurrentUser
				WHERE
					ItemID = pItemID;
                    
SET State = 10;
			
				SELECT
					pID AS 'ID',
                    pFirstStockDetailsID AS 'FirstStockDetailsID',
					'Transaksi Berhasil Disimpan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert the item
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsItem;

DELIMITER $$
CREATE PROCEDURE spInsItem (
	pID 				BIGINT, 
    pItemCode			VARCHAR(100),
	pItemName 			VARCHAR(255),
    pCategoryID			BIGINT,
    pUnitID				SMALLINT,
    pBuyPrice			DOUBLE,
    pRetailPrice		DOUBLE,
    pPrice1				DOUBLE,
    pQty1				DOUBLE,
    pPrice2				DOUBLE,
    pQty2				DOUBLE,
    pWeight				DOUBLE,
	pMinimumStock		DOUBLE,
    pItemDetails		TEXT,
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
		CALL spInsEventLog(@full_error, 'spInsItem', pCurrentUser);
        DELETE FROM temp_master_itemdetails;
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
		
        CREATE TEMPORARY TABLE IF NOT EXISTS temp_master_itemdetails
		(
			ItemDetailsID 		BIGINT,
			ItemID 				BIGINT,
			ItemDetailsCode		VARCHAR(100),
			UnitID				SMALLINT,
			ConversionQuantity	DOUBLE,
			BuyPrice			DOUBLE,
			RetailPrice			DOUBLE,
			Price1				DOUBLE,
			Qty1				DOUBLE,
			Price2				DOUBLE,
			Qty2				DOUBLE,
			Weight				DOUBLE,
			MinimumStock		DOUBLE
		);
        
SET State = 2;

		IF(pItemDetails <> "" ) THEN
			SET @query = CONCAT("INSERT INTO temp_master_itemdetails
								(
									ItemDetailsID,
									ItemID,
									ItemDetailsCode,
									UnitID,
									ConversionQuantity
								)
								VALUES", pItemDetails);
								
			PREPARE stmt FROM @query;
			EXECUTE stmt;
			DEALLOCATE PREPARE stmt;
		END IF;
       
SET State = 3;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_item
		WHERE
			TRIM(ItemCode) = TRIM(pItemCode)
			AND ItemID <> pID
		LIMIT 1;
        
SET State = 4;
        
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', pItemCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
		END IF;
        
SET State = 5;

        SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_itemdetails
		WHERE
			TRIM(ItemDetailsCode) = TRIM(pItemCode)
		LIMIT 1;
        
SET State = 6;
		
        IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', pItemCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
		END IF;
		
SET State = 7;
		
        SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_itemdetails MID
            JOIN temp_master_itemdetails TMID
				ON TRIM(MID.ItemDetailsCode) = TRIM(TMID.ItemDetailsCode)
                AND MID.ItemDetailsID <> TMID.ItemDetailsID
		LIMIT 1;
        
SET State = 8;
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', GC.ItemDetailsCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State'
			FROM
				(
					SELECT
						TMID.ItemID,
						GROUP_CONCAT(TRIM(TMID.ItemDetailsCode) SEPARATOR ', ') ItemDetailsCode
					FROM
						master_itemdetails MID
						JOIN temp_master_itemdetails TMID
							ON TRIM(MID.ItemDetailsCode) = TRIM(TMID.ItemDetailsCode)
							AND MID.ItemDetailsID <> TMID.ItemDetailsID
					GROUP BY
						TMID.ItemID
				)GC;
		
			LEAVE StoredProcedure;
		END IF;
			
SET State = 9;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_item MI
            JOIN temp_master_itemdetails TMID
				ON TRIM(MI.ItemCode) = TRIM(TMID.ItemDetailsCode)
		LIMIT 1;

SET State = 10;

		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', GC.ItemDetailsCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State'
			FROM
				(
					SELECT
						TMID.ItemID,
						GROUP_CONCAT(TRIM(TMID.ItemDetailsCode) SEPARATOR ', ') ItemDetailsCode
					FROM
						master_item MI
						JOIN temp_master_itemdetails TMID
							ON TRIM(MI.ItemCode) = TRIM(TMID.ItemDetailsCode)
					GROUP BY
						TMID.ItemID
				)GC;
		
			LEAVE StoredProcedure;
            
		END IF;
        
SET State = 11;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_item
		WHERE
			TRIM(ItemName) = TRIM(pItemName)
			AND ItemID <> pID
		LIMIT 1;
        
SET State = 12;

		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Nama Barang ', pItemName, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
        
SET State = 13;

			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_item
				(
                    ItemCode,
                    ItemName,
					CategoryID,
                    UnitID,
					BuyPrice,
					RetailPrice,
					Price1,
					Qty1,
					Price2,
					Qty2,
					Weight,
					MinimumStock,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pItemCode,
					pItemName,
					pCategoryID,
                    pUnitID,
					pBuyPrice,
					pRetailPrice,
					pPrice1,
					pQty1,
					pPrice2,
					pQty2,
					pWeight,
					pMinimumStock,
					NOW(),
					pCurrentUser
				);
			
SET State = 14;

				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;

SET State = 15;
				SET SQL_SAFE_UPDATES = 0;
                
				UPDATE temp_master_itemdetails
                SET ItemID = pID
                WHERE
					ItemDetailsID = 0;
				
                SET SQL_SAFE_UPDATES = 1;
                
SET State = 16;
				INSERT INTO master_itemdetails
                (
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
                    CreatedDate,
                    CreatedBy
                )
                SELECT
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
                    NOW(),
                    'Admin'
				FROM
					temp_master_itemdetails;
                
SET State = 17;

				SELECT
					pID AS 'ID',
					'Barang Berhasil Ditambahkan!' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
			ELSE
            
SET State = 18;

				UPDATE
					master_item
				SET
					ItemCode = pItemCode,
                    ItemName = pItemName,
					CategoryID = pCategoryID,
                    UnitID = pUnitID,
					BuyPrice = pBuyPrice,
					RetailPrice = pRetailPrice,
					Price1 = pPrice1,
					Qty1 = pQty1,
					Price2 = pPrice2,
					Qty2 = pQty2,
					Weight = pWeight,
					MinimumStock = pMinimumStock,
					ModifiedBy = pCurrentUser
				WHERE
					ItemID = pID;

SET State = 19;

				UPDATE master_itemdetails MID
                JOIN temp_master_itemdetails TMID
					ON TMID.ItemDetailsID = MID.ItemDetailsID
				SET
					MID.ItemDetailsCode = TMID.ItemDetailsCode,
					MID.UnitID = TMID.UnitID,
					MID.ConversionQuantity = TMID.ConversionQuantity,
					ModifiedBy = pCurrentUser;
                    
SET State = 20;
				
				DELETE FROM master_itemdetails
				WHERE 
					ItemDetailsID NOT IN(
											SELECT 
												TMID.ItemDetailsID
											FROM 
												temp_master_itemdetails TMID
										)
					AND ItemID = pID;
                                
SET State = 21;

				INSERT INTO master_itemdetails
                (
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
                    CreatedDate,
                    CreatedBy
                )
                SELECT
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
                    NOW(),
                    'Admin'
				FROM
					temp_master_itemdetails
				WHERE
					ItemDetailsID = 0;
                    
SET State = 22;

				SELECT
					pID AS 'ID',
					'Barang Berhasil Diubah!' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
			END IF;
		END IF;
        
        DROP TEMPORARY TABLE temp_master_itemdetails;
        
	COMMIT;
END;
$$
DELIMITER ;
/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert the item
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spInsItemImport;

DELIMITER $$
CREATE PROCEDURE spInsItemImport (
	pID 				BIGINT, 
    pItemCode			VARCHAR(100),
	pItemName 			VARCHAR(255),
    pCategoryID			BIGINT,
    pUnitID				SMALLINT,
    pBuyPrice			DOUBLE,
    pRetailPrice		DOUBLE,
    pPrice1				DOUBLE,
    pQty1				DOUBLE,
    pPrice2				DOUBLE,
    pQty2				DOUBLE,
    pWeight				DOUBLE,
	pMinimumStock		DOUBLE,
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
		CALL spInsEventLog(@full_error, 'spInsItemImport', pCurrentUser);
        DELETE FROM temp_master_itemdetails;
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
			master_item
		WHERE
			TRIM(ItemCode) = TRIM(pItemCode)
			AND ItemID <> pID
		LIMIT 1;
        
SET State = 2;
        
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', pItemCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
		END IF;
        
SET State = 3;

        SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_itemdetails
		WHERE
			TRIM(ItemDetailsCode) = TRIM(pItemCode)
		LIMIT 1;
        
SET State = 4;
		
        IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Kode Barang ', pItemCode, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
		END IF;
		        
SET State = 5;

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_item
		WHERE
			TRIM(ItemName) = TRIM(pItemName)
			AND ItemID <> pID
		LIMIT 1;
        
SET State = 6;

		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				CONCAT('Nama Barang ', pItemName, ' sudah ada!') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
        
SET State = 7;

			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_item
				(
                    ItemCode,
                    ItemName,
					CategoryID,
                    UnitID,
					BuyPrice,
					RetailPrice,
					Price1,
					Qty1,
					Price2,
					Qty2,
					Weight,
					MinimumStock,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pItemCode,
					pItemName,
					pCategoryID,
                    pUnitID,
					pBuyPrice,
					pRetailPrice,
					pPrice1,
					pQty1,
					pPrice2,
					pQty2,
					pWeight,
					pMinimumStock,
					NOW(),
					pCurrentUser
				);
			
SET State = 8;

				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;                

SET State = 9;

				SELECT
					pID AS 'ID',
					'Barang Berhasil Ditambahkan!' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
			ELSE
            
SET State = 10;

				UPDATE
					master_item
				SET
					ItemCode = pItemCode,
                    ItemName = pItemName,
					CategoryID = pCategoryID,
                    UnitID = pUnitID,
					BuyPrice = pBuyPrice,
					RetailPrice = pRetailPrice,
					Price1 = pPrice1,
					Qty1 = pQty1,
					Price2 = pPrice2,
					Qty2 = pQty2,
					Weight = pWeight,
					MinimumStock = pMinimumStock,
					ModifiedBy = pCurrentUser
				WHERE
					ItemID = pID;

SET State = 11;

				SELECT
					pID AS 'ID',
					'Barang Berhasil Diubah!' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
			END IF;
		END IF;

	COMMIT;
END;
$$
DELIMITER ;
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
DROP PROCEDURE IF EXISTS spInsPurchase;

DELIMITER $$
CREATE PROCEDURE spInsPurchase (
	pID 				BIGINT,
    pPurchaseNumber		VARCHAR(100),
    pSupplierID			BIGINT,
	pTransactionDate 	DATETIME,
	pPurchaseDetailsID	BIGINT,
    pBranchID			INT,
    pItemID				BIGINT,
    pItemDetailsID		BIGINT,
	pQuantity			DOUBLE,
    pBuyPrice			DOUBLE,
    pRetailPrice		DOUBLE,
    pPrice1				DOUBLE,
    pPrice2				DOUBLE,
    pDeadline			DATETIME,
    pPaymentTypeID		SMALLINT,
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
		CALL spInsEventLog(@full_error, 'spInsPurchase', pCurrentUser);
		SELECT
			pID AS 'ID',
            pPurchaseDetailsID AS 'PurchaseDetailsID',
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
			transaction_purchase
		WHERE
			TRIM(PurchaseNumber) = TRIM(pPurchaseNumber)
			AND PurchaseID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
                pPurchaseDetailsID AS 'PurchaseDetailsID',
				CONCAT('No. Invoice ', pPurchaseNumber, ' sudah ada') AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pID = 0)	THEN /*Tambah baru*/
				INSERT INTO transaction_purchase
				(
                    PurchaseNumber,
                    SupplierID,
					TransactionDate,
                    Deadline,
                    PaymentTypeID,
					CreatedDate,
					CreatedBy
				)
				VALUES 
                (
					pPurchaseNumber,
					pSupplierID,
					pTransactionDate,
                    pDeadline,
                    pPaymentTypeID,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;	               
				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;
                    
			ELSE
SET State = 5;
				UPDATE
					transaction_purchase
				SET
					PurchaseNumber = pPurchaseNumber,
                    SupplierID = pSupplierID,
					TransactionDate = pTransactionDate,
                    PaymentTypeID = pPaymentTypeID,
                    Deadline = pDeadline,
					ModifiedBy = pCurrentUser
				WHERE
					PurchaseID = pID;
                    
			END IF;
            
SET State = 6;
			
			IF(pPurchaseDetailsID = 0) THEN
				INSERT INTO transaction_purchasedetails
                (
					PurchaseID,
                    ItemID,
                    ItemDetailsID,
                    BranchID,
                    Quantity,
                    BuyPrice,
                    RetailPrice,
                    Price1,
                    Price2,
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
                    pRetailPrice,
                    pPrice1,
                    pPrice2,
                    NOW(),
                    pCurrentUser
                );
                
SET State = 7;
				
				SELECT
					LAST_INSERT_ID()
				INTO 
					pPurchaseDetailsID;
			
			ELSE
					
SET State = 8;
				
				UPDATE 
					transaction_purchasedetails
				SET
					ItemID = pItemID,
                    ItemDetailsID = pItemDetailsID,
                    BranchID = pBranchID,
                    Quantity = pQuantity,
                    BuyPrice = pBuyPrice,
                    RetailPrice = pRetailPrice,
                    Price1 = pPrice1,
                    Price2 = pPrice2,
					ModifiedBy = pCurrentUser
				WHERE
					PurchaseDetailsID = pPurchaseDetailsID;
				
			END IF;
			
SET State = 9;

				UPDATE 
					master_item
				SET
					BuyPrice = pBuyPrice,
					RetailPrice = pRetailPrice,
					Price1 = pPrice1,
					Price2 = pPrice2,
					ModifiedBy = pCurrentUser
				WHERE
					ItemID = pItemID
                    AND pItemDetailsID IS NULL;
                    
SET State = 10;

				UPDATE 
					master_itemdetails
				SET
					BuyPrice = pBuyPrice,
					RetailPrice = pRetailPrice,
					Price1 = pPrice1,
					Price2 = pPrice2,
					ModifiedBy = pCurrentUser
				WHERE
					ItemDetailsID = pItemDetailsID
                    AND pItemDetailsID IS NOT NULL;
SET State = 11;
				
				SELECT
					pID AS 'ID',
                    pPurchaseDetailsID AS 'PurchaseDetailsID',
					'Transaksi Berhasil Disimpan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
DROP PROCEDURE IF EXISTS spInsPurchaseReturn;

DELIMITER $$
CREATE PROCEDURE spInsPurchaseReturn (
	pID 						BIGINT,
	pPurchaseReturnNumber		VARCHAR(100),
	pSupplierID					BIGINT,
	pTransactionDate 			DATETIME,
    pPurchaseReturnDetailsID	BIGINT,
    pBranchID					INT,
    pItemID						BIGINT,
    pItemDetailsID				BIGINT,
	pQuantity					DOUBLE,
    pBuyPrice					DOUBLE,
	pUserID						BIGINT,
    pCurrentUser				VARCHAR(255)
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
		CALL spInsEventLog(@full_error, 'spInsPurchaseReturn', pCurrentUser);
		SELECT
			pID AS 'ID',
            pPurchaseReturnDetailsID AS 'PurchaseReturnDetailsID',
            pPurchaseReturnNumber AS 'PurchaseReturnNumber',
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
				CONCAT('RB', RIGHT(CONCAT('00', pUserID), 2), DATE_FORMAT(NOW(), '%Y%m'), RIGHT(CONCAT('00000', (IFNULL(MAX(CAST(RIGHT(PurchaseReturnNumber, 5) AS UNSIGNED)), 0) + 1)), 5))
			FROM
				transaction_purchasereturn PR
			WHERE
				MONTH(PR.TransactionDate) = MONTH(NOW())
				AND YEAR(PR.TransactionDate) = YEAR(NOW())
			INTO 
				pPurchaseReturnNumber;

			INSERT INTO transaction_purchasereturn
			(
				PurchaseReturnNumber,
				SupplierID,
				TransactionDate,
				CreatedDate,
				CreatedBy
			)
			VALUES 
			(
				pPurchaseReturnNumber,
				pSupplierID,
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
				transaction_purchasereturn
			SET
				SupplierID = pSupplierID,
				TransactionDate = pTransactionDate,
				ModifiedBy = pCurrentUser
			WHERE
				PurchaseReturnID = pID;
				
		END IF;
            
SET State = 4;
			
		IF(pPurchaseReturnDetailsID = 0) THEN
			INSERT INTO transaction_purchasereturndetails
			(
				PurchaseReturnID,
				ItemID,
                ItemDetailsID,
				BranchID,
				Quantity,
				BuyPrice,
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
				NOW(),
				pCurrentUser
			);
			
SET State = 5;
			
			SELECT
				LAST_INSERT_ID()
			INTO 
				pPurchaseReturnDetailsID;
			
		ELSE
				
SET State = 6;
			
			UPDATE 
				transaction_purchasereturndetails
			SET
				ItemID = pItemID,
                ItemDetailsID = pItemDetailsID,
				BranchID = pBranchID,
				Quantity = pQuantity,
				BuyPrice = pBuyPrice,
				ModifiedBy = pCurrentUser
			WHERE
				PurchaseReturnDetailsID = pPurchaseReturnDetailsID;
			
		END IF;
			
SET State = 7;
		
		SELECT
			pID AS 'ID',
			pPurchaseReturnDetailsID AS 'PurchaseReturnDetailsID',
			pPurchaseReturnNumber AS 'PurchaseReturnNumber',
			'Transaksi Berhasil Disimpan' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';

	COMMIT;
END;
$$
DELIMITER ;
