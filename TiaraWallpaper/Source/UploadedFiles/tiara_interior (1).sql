-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2016 at 05:30 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tiara_interior`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsBackup`(
	pFilePath	 	VARCHAR(255), 
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
		INSERT INTO backup_history
		(
			BackupDate,
			FilePath,
			CreatedDate,
			CreatedBy
		)
		VALUES (
			NOW(),
			pFilePath,
			NOW(),
			pCurrentUser
		);
			
SET State = 2;			               
		SELECT
			0 AS 'ID',
			'Backup Berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
			
	COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsBrand`(
	pID				BIGINT, 
	pBrandName 	VARCHAR(255), 
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
			master_brand
		WHERE
			TRIM(BrandName) = TRIM(pBrandName)
			AND BrandID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Merek sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_brand
				(
					BrandName,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pBrandName,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Merek Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
					
			ELSE
SET State = 5;
				UPDATE
					master_brand
				SET
					BrandName = pBrandName,
					ModifiedBy = pCurrentUser
				WHERE
					BrandID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Merek Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
				
			END IF;
		END IF;			
	COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsCustomer`(
	pID 			BIGINT, 
	pCustomerName 	VARCHAR(255),
	pAddress 		TEXT,
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
			AND TRIM(Address) = TRIM(pAddress)
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
					Address,
					City,
					Telephone,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pCustomerName,
					pAddress,
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
					Address = pAddress,
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsItem`(
	pID 			BIGINT, 
	pItemName 		VARCHAR(255),
	pBrandID 	BIGINT,
	pUnitID			BIGINT,
	pReminderCount	INT,
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
			master_item
		WHERE
			TRIM(ItemName) = TRIM(pItemName)
			AND BrandID = pBrandID
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
					ItemName,
					BrandID,
					UnitID,
					ReminderCount,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pItemName,
					pBrandID,
					pUnitID,
					pReminderCount,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Barang Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_item
				SET
					ItemName = pItemName,
					ReminderCount = pReminderCount,
					BrandID = pBrandID,
					UnitID = pUnitID,
					ModifiedBy = pCurrentUser
				WHERE
					ItemID = pID;

SET State = 6;
				SELECT
					pID AS 'ID',
					'Barang Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsRestore`(
	pFilePath	 	VARCHAR(255), 
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
		INSERT INTO restore_history
		(
			RestoreDate,
			FilePath,
			CreatedDate,
			CreatedBy
		)
		VALUES (
			NOW(),
			pFilePath,
			NOW(),
			pCurrentUser
		);
			
SET State = 2;			               
		SELECT
			0 AS 'ID',
			'Restore Berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
			
	COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsSales`(
	pID 			BIGINT, 
	pSalesName 		VARCHAR(255),
	pAddress 		TEXT,
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
			master_sales
		WHERE
			TRIM(SalesName) = TRIM(pSalesName)
			AND SalesID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Sales sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_sales
				(
					SalesName,
					Address,
					Telephone,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pSalesName,
					pAddress,
					pTelephone,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Sales Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';		
			ELSE
SET State = 5;
				UPDATE
					master_sales
				SET
					SalesName = pSalesName,
					Address = pAddress,
					Telephone = pTelephone,
					ModifiedBy = pCurrentUser
				WHERE
					SalesID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Sales Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
		
			END IF;	
		END IF;
	COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsSupplier`(
	pID 			BIGINT, 
	pSupplierName 	VARCHAR(255),
	pAddress		TEXT,
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
			master_supplier
		WHERE
			TRIM(SupplierName) = TRIM(pSupplierName)
			AND TRIM(Address) = TRIM(pAddress)
			AND TRIM(City) = TRIM(pCity)
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
					SupplierName,
					Address,
					City,
					Telephone,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pSupplierName,
					pAddress,
					pCity,
					pTelephone,
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
					master_supplier
				SET
					SupplierName = pSupplierName,
					Address = pAddress,
					City = pCity,
					Telephone = pTelephone,
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsType`(
	pID 			BIGINT, 
	pTypeName 		VARCHAR(255),
	pBrandID 		BIGINT,
	pUnitID			BIGINT,
	pReminderCount	INT,
	pBuyPrice		DOUBLE,
	pSalePrice		DOUBLE,
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
			master_type
		WHERE
			TRIM(TypeName) = TRIM(pTypeName)
			AND BrandID = pBrandID
			AND TypeID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Tipe sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_type
				(
					TypeName,
					BrandID,
					UnitID,
					ReminderCount,
					BuyPrice,
					SalePrice,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pTypeName,
					pBrandID,
					pUnitID,
					pReminderCount,
					pBuyPrice,
					pSalePrice,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					pID AS 'ID',
					'Tipe Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 5;
				UPDATE
					master_type
				SET
					TypeName = pTypeName,
					ReminderCount = pReminderCount,
					BrandID = pBrandID,
					UnitID = pUnitID,
					BuyPrice = pBuyPrice,
					SalePrice = pSalePrice,
					ModifiedBy = pCurrentUser
				WHERE
					TypeID = pID;

SET State = 6;
				SELECT
					pID AS 'ID',
					'Tipe Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			END IF;
		END IF;
	COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsUnit`(
	pID				BIGINT, 
	pUnitName 	VARCHAR(255), 
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
			master_unit
		WHERE
			TRIM(UnitName) = TRIM(pUnitName)
			AND UnitID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Satuan sudah ada' AS 'Message',
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
					UnitName = pUnitName,
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsUser`(
	pID 			BIGINT, 
	pUserName 		VARCHAR(255), 
	pUserLogin 		VARCHAR(100),
	pPassword 		VARCHAR(255),
	pIsActive		BIT,
	pMenuID 		VARCHAR(255),
	pEditMenuID 	VARCHAR(255),
	pDeleteMenuID	VARCHAR(255),
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
			pID AS 'ID', 
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
			master_user 
		WHERE
			UserName = pUserName
			AND UserLogin = pUserLogin
			AND UserID <> pID
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
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
					UserPassword,
					IsActive,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pUserName,
					pUserLogin,
					pPassword,
					pIsActive,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					MAX(UserID)
				INTO 
					pID
				FROM
					master_user;
					
			ELSE
SET State = 5;
				UPDATE
					master_user
				SET
					UserName = pUserName,
					UserLogin = pUserLogin,
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
			loopdata : WHILE pMenuID <> "" DO
				INSERT INTO master_role
				(
					UserID,
					MenuID,
					EditFlag,
					DeleteFlag
				)
				VALUES
				(
					pID,
					SUBSTRING(pMenuID, 1, IF((INSTR(pMenuID, ',') - 1) = -1, LENGTH(pMenuID), (INSTR(pMenuID, ',') - 1))),
					SUBSTRING(pEditMenuID, 1, IF((INSTR(pEditMenuID, ',') - 1) = -1, LENGTH(pEditMenuID), (INSTR(pEditMenuID, ',') - 1))),
					SUBSTRING(pDeleteMenuID, 1, IF((INSTR(pDeleteMenuID, ',') - 1) = -1, LENGTH(pDeleteMenuID), (INSTR(pDeleteMenuID, ',') - 1)))
				);
				
				IF(INSTR(pMenuID, ',') = 0) THEN SET pMenuID = "";
				ELSE
					SET pMenuID = SUBSTRING(pMenuID, INSTR(pMenuID, ',') + 1, LENGTH(pMenuID));
					SET pEditMenuID = SUBSTRING(pEditMenuID, INSTR(pEditMenuID, ',') + 1, LENGTH(pEditMenuID));
					SET pDeleteMenuID = SUBSTRING(pDeleteMenuID, INSTR(pDeleteMenuID, ',') + 1, LENGTH(pDeleteMenuID));
				END IF;
				
			END WHILE loopdata; 
		END IF;

	IF(pIsEdit = 0) THEN
SET State = 8;
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
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `backup_history`
--

CREATE TABLE IF NOT EXISTS `backup_history` (
`BackupHistoryID` bigint(20) NOT NULL,
  `BackupDate` date DEFAULT NULL,
  `FilePath` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `backup_history`
--

INSERT INTO `backup_history` (`BackupHistoryID`, `BackupDate`, `FilePath`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, '2016-03-12', 'BackupFiles\\tiara_interior_20160312171725.sql', '2016-03-12 00:00:00', 'Admin', '2016-03-12 10:55:42', NULL),
(2, '2016-03-12', 'BackupFiles	iara_interior_20160312214233', '2016-03-12 21:42:33', 'Admin', NULL, NULL),
(3, '2016-03-12', 'BackupFiles\\tiara_interior_20160312214323', '2016-03-12 21:43:23', 'Admin', NULL, NULL),
(4, '2016-03-12', 'BackupFiles\\tiara_interior_20160312214406.sql', '2016-03-12 21:44:07', 'Admin', NULL, NULL),
(5, '2016-03-12', 'BackupFiles\\tiara_interior_20160312214428.sql', '2016-03-12 21:44:28', 'Admin', NULL, NULL),
(6, '2016-03-12', 'BackupFiles\\tiara_interior_20160312214552.sql', '2016-03-12 21:45:52', 'Admin', NULL, NULL),
(7, '2016-03-12', 'BackupFiles\\tiara_interior_20160312214609.sql', '2016-03-12 21:46:09', 'Admin', NULL, NULL),
(8, '2016-03-12', 'BackupFiles\\tiara_interior_20160312214712.sql', '2016-03-12 21:47:14', 'Admin', NULL, NULL),
(9, '2016-03-12', 'BackupFiles\\tiara_interior_20160312214743.sql', '2016-03-12 21:47:44', 'Admin', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_brand`
--

CREATE TABLE IF NOT EXISTS `master_brand` (
`BrandID` bigint(20) NOT NULL,
  `BrandName` varchar(255) NOT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_brand`
--

INSERT INTO `master_brand` (`BrandID`, `BrandName`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'Eurowall', '2016-03-12 17:02:18', 'Admin', NULL, NULL),
(2, 'MAESTRO', '2016-03-12 17:02:36', 'Admin', NULL, NULL),
(3, 'KING', '2016-03-12 17:02:44', 'Admin', NULL, NULL),
(4, 'Queen', '2016-03-12 17:02:53', 'Admin', NULL, NULL),
(5, 'MONCHERI', '2016-03-12 17:03:06', 'Admin', NULL, NULL),
(6, 'WOW', '2016-03-12 17:03:12', 'Admin', NULL, NULL),
(7, 'Econia', '2016-03-12 17:03:33', 'Admin', NULL, NULL),
(8, 'Borneo', '2016-03-12 17:03:49', 'Admin', NULL, NULL),
(9, 'Bacan', '2016-03-12 17:03:56', 'Admin', NULL, NULL),
(13, 'Crown', '2016-03-12 17:06:03', 'Admin', NULL, NULL),
(15, 'Star', '2016-03-12 17:09:20', 'Admin', NULL, NULL),
(16, 'Bravo', '2016-03-12 17:12:21', 'Admin', NULL, NULL),
(17, 'Empire', '2016-03-12 17:12:30', 'Admin', NULL, NULL),
(18, 'Excellent', '2016-03-12 17:12:41', 'Admin', NULL, NULL),
(19, 'Sky Line', '2016-03-12 17:12:53', 'Admin', NULL, NULL),
(20, 'Supra', '2016-03-12 17:13:04', 'Admin', NULL, NULL),
(21, 'Delta', '2016-03-12 17:13:15', 'Admin', NULL, NULL),
(22, 'Ion', '2016-03-12 17:13:19', 'Admin', NULL, NULL),
(23, 'Focus', '2016-03-12 17:13:26', 'Admin', NULL, NULL),
(24, 'Renova', '2016-03-12 17:13:35', 'Admin', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_customer`
--

CREATE TABLE IF NOT EXISTS `master_customer` (
`CustomerID` bigint(20) NOT NULL,
  `CustomerName` varchar(255) NOT NULL,
  `Telephone` varchar(100) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `master_groupmenu`
--

CREATE TABLE IF NOT EXISTS `master_groupmenu` (
  `GroupMenuID` int(11) NOT NULL,
  `GroupMenuName` varchar(255) DEFAULT NULL,
  `Icon` varchar(255) DEFAULT NULL,
  `Url` varchar(255) DEFAULT NULL,
  `OrderNo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_groupmenu`
--

INSERT INTO `master_groupmenu` (`GroupMenuID`, `GroupMenuName`, `Icon`, `Url`, `OrderNo`) VALUES
(1, 'Home', 'fa fa-home fa-3x', './Home.php', 1),
(2, 'Master Data', 'fa fa-book fa-3x', NULL, 2),
(3, 'Transaksi', 'fa fa-cart-plus fa-3x', NULL, 3),
(4, 'Laporan', 'fa fa-line-chart fa-3x', NULL, 4),
(5, 'Tools', 'fa fa-cogs fa-3x', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `master_menu`
--

CREATE TABLE IF NOT EXISTS `master_menu` (
`MenuID` bigint(20) NOT NULL,
  `GroupMenuID` int(11) DEFAULT NULL,
  `MenuName` varchar(255) DEFAULT NULL,
  `Url` varchar(255) DEFAULT NULL,
  `Icon` varchar(255) DEFAULT NULL,
  `IsReport` bit(1) DEFAULT NULL,
  `OrderNo` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_menu`
--

INSERT INTO `master_menu` (`MenuID`, `GroupMenuID`, `MenuName`, `Url`, `Icon`, `IsReport`, `OrderNo`) VALUES
(1, 2, 'User', 'Master/User/', NULL, b'0', 1),
(2, 2, 'Merek', 'Master/Brand/', NULL, b'0', 2),
(3, 2, 'Satuan', 'Master/Unit/', NULL, b'0', 3),
(4, 2, 'Tipe', 'Master/Type/', NULL, b'0', 4),
(5, 2, 'Pelanggan', 'Master/Customer/', NULL, b'0', 6),
(6, 2, 'Supplier', 'Master/Supplier/', NULL, b'0', 7),
(7, 3, 'Stok Awal', 'Transaction/FirstStock/', NULL, b'0', 1),
(8, 3, 'Barang Masuk', 'Transaction/Incoming/', NULL, b'0', 2),
(9, 3, 'Barang Keluar', 'Transaction/Outgoing/', NULL, b'0', 3),
(10, 3, 'Retur Beli', 'Transaction/BuyReturn/', NULL, b'0', 4),
(11, 3, 'Retur Jual', 'Transaction/SaleReturn/', NULL, b'0', 5),
(12, 4, 'Pembelian', 'Report/Purchase/', NULL, b'1', 1),
(13, 4, 'Penjualan', 'Report/Selling/', NULL, b'1', 2),
(14, 4, 'Penjualan Per Barang', 'Report/SaleByItem/', NULL, b'1', 3),
(15, 2, 'Barang', 'Master/Item/', NULL, b'1', 5),
(16, 2, 'Sales', 'Master/Sales/', NULL, b'0', 8),
(17, 5, 'Bakcup Database', 'Tools/BackupDB/', NULL, b'0', 1),
(18, 5, 'Restore Database', 'Tools/RestoreDB/', NULL, b'0', 2),
(19, 5, 'Reset', 'Tools/Reset/', NULL, b'0', 3);

-- --------------------------------------------------------

--
-- Table structure for table `master_parameter`
--

CREATE TABLE IF NOT EXISTS `master_parameter` (
`ParameterID` bigint(20) NOT NULL,
  `ParameterName` varchar(255) NOT NULL,
  `ParameterValue` varchar(255) NOT NULL,
  `Remarks` text,
  `IsNumber` int(11) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_parameter`
--

INSERT INTO `master_parameter` (`ParameterID`, `ParameterName`, `ParameterValue`, `Remarks`, `IsNumber`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'APPLICATION_PATH', '/Projects/TiaraInterior/Source/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(2, 'MYSQL_DUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe', 'Path of mysqldump.exe', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(3, 'ERROR_LOG_PATH', 'C:\\xampp\\htdocs\\Projects\\TiaraInterior\\Source\\BackupFiles\\dumperrors.txt', 'log error when backup failed', 0, '2016-03-12 00:00:00', 'admin', NULL, NULL),
(4, 'BACKUP_FULLPATH', 'C:\\xampp\\htdocs\\Projects\\TiaraInterior\\Source\\BackupFiles\\', 'Directory where backup files located', 0, '2016-03-12 00:00:00', 'admin', '2016-03-12 14:25:59', NULL),
(5, 'BACKUP_FOLDER', 'BackupFiles\\\\', 'Backup path', 0, '2016-03-12 00:00:00', 'Admin', '2016-03-12 14:43:21', NULL),
(6, 'MYSQL_PATH', 'C:\\xampp\\mysql\\bin\\mysql.exe', 'mysql.exe path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_role`
--

CREATE TABLE IF NOT EXISTS `master_role` (
`RoleID` bigint(20) NOT NULL,
  `UserID` bigint(20) DEFAULT NULL,
  `MenuID` bigint(20) DEFAULT NULL,
  `EditFlag` tinyint(1) DEFAULT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_role`
--

INSERT INTO `master_role` (`RoleID`, `UserID`, `MenuID`, `EditFlag`, `DeleteFlag`) VALUES
(1, 1, 1, 1, 1),
(2, 1, 2, 1, 1),
(3, 1, 3, 1, 1),
(4, 1, 4, 1, 1),
(5, 1, 5, 1, 1),
(6, 1, 6, 1, 1),
(7, 1, 7, 1, 1),
(8, 1, 8, 1, 1),
(9, 1, 9, 1, 1),
(10, 1, 10, 1, 1),
(11, 1, 11, 1, 1),
(12, 1, 12, 1, 1),
(13, 1, 13, 1, 1),
(14, 1, 14, 1, 1),
(15, 1, 15, 1, 1),
(16, 1, 16, 1, 1),
(17, 1, 17, 1, 1),
(18, 1, 18, 1, 1),
(19, 1, 19, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `master_sales`
--

CREATE TABLE IF NOT EXISTS `master_sales` (
`SalesID` bigint(20) NOT NULL,
  `SalesName` varchar(255) NOT NULL,
  `Telephone` varchar(100) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `master_supplier`
--

CREATE TABLE IF NOT EXISTS `master_supplier` (
`SupplierID` bigint(20) NOT NULL,
  `SupplierName` varchar(255) NOT NULL,
  `Telephone` varchar(100) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `master_type`
--

CREATE TABLE IF NOT EXISTS `master_type` (
`TypeID` bigint(20) NOT NULL,
  `TypeName` varchar(255) NOT NULL,
  `UnitID` bigint(20) DEFAULT NULL,
  `BrandID` bigint(20) NOT NULL,
  `ReminderCount` int(11) DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `master_unit`
--

CREATE TABLE IF NOT EXISTS `master_unit` (
`UnitID` bigint(20) NOT NULL,
  `UnitName` varchar(255) NOT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_unit`
--

INSERT INTO `master_unit` (`UnitID`, `UnitName`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'Roll', '2016-03-12 17:09:55', 'Admin', NULL, NULL),
(2, 'mÂ²', '2016-03-12 17:10:54', 'Admin', NULL, NULL),
(4, 'm lari', '2016-03-12 17:11:14', 'Admin', NULL, NULL),
(5, 'Box', '2016-03-12 17:11:36', 'Admin', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_user`
--

CREATE TABLE IF NOT EXISTS `master_user` (
`UserID` bigint(20) NOT NULL,
  `UserName` varchar(255) NOT NULL,
  `UserLogin` varchar(100) NOT NULL,
  `UserPassword` varchar(255) NOT NULL,
  `IsActive` tinyint(1) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_user`
--

INSERT INTO `master_user` (`UserID`, `UserName`, `UserLogin`, `UserPassword`, `IsActive`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'System Administrator', 'Admin', 'e80b5017098950fc58aad83c8c14978e', 1, '2016-03-12 15:01:01', 'System', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reset_history`
--

CREATE TABLE IF NOT EXISTS `reset_history` (
`ResetHistoryID` bigint(20) NOT NULL,
  `ResetDate` date DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `restore_history`
--

CREATE TABLE IF NOT EXISTS `restore_history` (
`RestoreHistoryID` bigint(20) NOT NULL,
  `RestoreDate` date DEFAULT NULL,
  `FilePath` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `restore_history`
--

INSERT INTO `restore_history` (`RestoreHistoryID`, `RestoreDate`, `FilePath`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, '2016-03-12', 'BackupFiles\\tiara_interior_20160312231152.sql', '2016-03-12 23:11:52', 'Admin', NULL, NULL),
(2, '2016-03-12', 'BackupFiles\\tiara_interior_20160312231338.sql', '2016-03-12 23:13:38', 'Admin', NULL, NULL),
(3, '2016-03-12', 'BackupFiles\\tiara_interior_20160312232659.sql', '2016-03-12 23:26:59', 'Admin', NULL, NULL),
(4, '2016-03-12', 'BackupFiles\\tiara_interior_20160312232903.sql', '2016-03-12 23:29:04', 'Admin', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_buyreturn`
--

CREATE TABLE IF NOT EXISTS `transaction_buyreturn` (
`BuyReturnID` bigint(20) NOT NULL,
  `BuyReturnNumber` varchar(100) DEFAULT NULL,
  `SupplierID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_buyreturndetails`
--

CREATE TABLE IF NOT EXISTS `transaction_buyreturndetails` (
`BuyReturnDetailsID` bigint(20) NOT NULL,
  `BuyReturnID` bigint(20) DEFAULT NULL,
  `TypeID` bigint(20) NOT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_firststock`
--

CREATE TABLE IF NOT EXISTS `transaction_firststock` (
`FirstStockID` bigint(20) NOT NULL,
  `FirstStockNumber` varchar(100) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_firststockdetails`
--

CREATE TABLE IF NOT EXISTS `transaction_firststockdetails` (
`FirstStockDetailsID` bigint(20) NOT NULL,
  `FirstStockID` bigint(20) DEFAULT NULL,
  `TypeID` bigint(20) NOT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `Discount` int(11) DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_incoming`
--

CREATE TABLE IF NOT EXISTS `transaction_incoming` (
`IncomingID` bigint(20) NOT NULL,
  `IncomingNumber` varchar(100) DEFAULT NULL,
  `SupplierID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_incomingdetails`
--

CREATE TABLE IF NOT EXISTS `transaction_incomingdetails` (
`IncomingDetailsID` bigint(20) NOT NULL,
  `IncomingID` bigint(20) DEFAULT NULL,
  `TypeID` bigint(20) NOT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `Discount` int(11) DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_invoicenumber`
--

CREATE TABLE IF NOT EXISTS `transaction_invoicenumber` (
`InvoiceNumberID` bigint(20) NOT NULL,
  `InvoiceNumberType` varchar(2) DEFAULT NULL,
  `InvoiceDate` date DEFAULT NULL,
  `InvoiceNumber` varchar(20) DEFAULT NULL,
  `DeleteFlag` bit(1) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_outgoing`
--

CREATE TABLE IF NOT EXISTS `transaction_outgoing` (
`OutgoingID` bigint(20) NOT NULL,
  `OutgoingNumber` varchar(100) DEFAULT NULL,
  `CustomerID` bigint(20) DEFAULT NULL,
  `SalesID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `DeliveryCost` double DEFAULT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_outgoingdetails`
--

CREATE TABLE IF NOT EXISTS `transaction_outgoingdetails` (
`OutgoingDetailsID` bigint(20) NOT NULL,
  `OutgoingID` bigint(20) DEFAULT NULL,
  `TypeID` bigint(20) NOT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `Discount` int(11) DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_salereturn`
--

CREATE TABLE IF NOT EXISTS `transaction_salereturn` (
`SaleReturnID` bigint(20) NOT NULL,
  `SaleReturnNumber` varchar(100) DEFAULT NULL,
  `CustomerID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_salereturndetails`
--

CREATE TABLE IF NOT EXISTS `transaction_salereturndetails` (
`SaleReturnDetailsID` bigint(20) NOT NULL,
  `SaleReturnID` bigint(20) DEFAULT NULL,
  `TypeID` bigint(20) NOT NULL,
  `Quantity` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `backup_history`
--
ALTER TABLE `backup_history`
 ADD PRIMARY KEY (`BackupHistoryID`), ADD UNIQUE KEY `BACKUPHISTORY_INDEX` (`BackupHistoryID`);

--
-- Indexes for table `master_brand`
--
ALTER TABLE `master_brand`
 ADD PRIMARY KEY (`BrandID`), ADD UNIQUE KEY `BRAND_INDEX` (`BrandID`);

--
-- Indexes for table `master_customer`
--
ALTER TABLE `master_customer`
 ADD PRIMARY KEY (`CustomerID`), ADD UNIQUE KEY `CUSTOMER_INDEX` (`CustomerID`);

--
-- Indexes for table `master_groupmenu`
--
ALTER TABLE `master_groupmenu`
 ADD PRIMARY KEY (`GroupMenuID`), ADD UNIQUE KEY `GROUPMENU_INDEX` (`GroupMenuID`);

--
-- Indexes for table `master_menu`
--
ALTER TABLE `master_menu`
 ADD PRIMARY KEY (`MenuID`), ADD UNIQUE KEY `MENU_INDEX` (`MenuID`), ADD KEY `GroupMenuID` (`GroupMenuID`);

--
-- Indexes for table `master_parameter`
--
ALTER TABLE `master_parameter`
 ADD PRIMARY KEY (`ParameterID`);

--
-- Indexes for table `master_role`
--
ALTER TABLE `master_role`
 ADD PRIMARY KEY (`RoleID`), ADD UNIQUE KEY `ROLE_INDEX` (`RoleID`,`UserID`,`MenuID`), ADD KEY `UserID` (`UserID`), ADD KEY `MenuID` (`MenuID`);

--
-- Indexes for table `master_sales`
--
ALTER TABLE `master_sales`
 ADD PRIMARY KEY (`SalesID`), ADD UNIQUE KEY `SALES_INDEX` (`SalesID`);

--
-- Indexes for table `master_supplier`
--
ALTER TABLE `master_supplier`
 ADD PRIMARY KEY (`SupplierID`), ADD UNIQUE KEY `SUPPLIER_INDEX` (`SupplierID`);

--
-- Indexes for table `master_type`
--
ALTER TABLE `master_type`
 ADD PRIMARY KEY (`TypeID`), ADD UNIQUE KEY `TYPE_INDEX` (`TypeID`,`BrandID`,`UnitID`), ADD KEY `BrandID` (`BrandID`), ADD KEY `UnitID` (`UnitID`);

--
-- Indexes for table `master_unit`
--
ALTER TABLE `master_unit`
 ADD PRIMARY KEY (`UnitID`), ADD UNIQUE KEY `UNIT_INDEX` (`UnitID`);

--
-- Indexes for table `master_user`
--
ALTER TABLE `master_user`
 ADD PRIMARY KEY (`UserID`), ADD UNIQUE KEY `USER_INDEX` (`UserID`);

--
-- Indexes for table `reset_history`
--
ALTER TABLE `reset_history`
 ADD PRIMARY KEY (`ResetHistoryID`), ADD UNIQUE KEY `RESETHISTORY_INDEX` (`ResetHistoryID`);

--
-- Indexes for table `restore_history`
--
ALTER TABLE `restore_history`
 ADD PRIMARY KEY (`RestoreHistoryID`), ADD UNIQUE KEY `RESTOREHISTORY_INDEX` (`RestoreHistoryID`);

--
-- Indexes for table `transaction_buyreturn`
--
ALTER TABLE `transaction_buyreturn`
 ADD PRIMARY KEY (`BuyReturnID`), ADD UNIQUE KEY `BUYRETURN_INDEX` (`BuyReturnID`,`SupplierID`);

--
-- Indexes for table `transaction_buyreturndetails`
--
ALTER TABLE `transaction_buyreturndetails`
 ADD PRIMARY KEY (`BuyReturnDetailsID`), ADD UNIQUE KEY `BUYRETURNDETAILS_INDEX` (`BuyReturnDetailsID`,`BuyReturnID`), ADD KEY `BuyReturnID` (`BuyReturnID`), ADD KEY `TypeID` (`TypeID`);

--
-- Indexes for table `transaction_firststock`
--
ALTER TABLE `transaction_firststock`
 ADD PRIMARY KEY (`FirstStockID`), ADD UNIQUE KEY `FIRSTSTOCK_INDEX` (`FirstStockID`);

--
-- Indexes for table `transaction_firststockdetails`
--
ALTER TABLE `transaction_firststockdetails`
 ADD PRIMARY KEY (`FirstStockDetailsID`), ADD UNIQUE KEY `FIRSTSTOCKDETAILS_INDEX` (`FirstStockDetailsID`,`FirstStockID`,`TypeID`), ADD KEY `FirstStockID` (`FirstStockID`), ADD KEY `TypeID` (`TypeID`);

--
-- Indexes for table `transaction_incoming`
--
ALTER TABLE `transaction_incoming`
 ADD PRIMARY KEY (`IncomingID`), ADD UNIQUE KEY `INCOMING_INDEX` (`IncomingID`,`SupplierID`);

--
-- Indexes for table `transaction_incomingdetails`
--
ALTER TABLE `transaction_incomingdetails`
 ADD PRIMARY KEY (`IncomingDetailsID`), ADD UNIQUE KEY `INCOMINGDETAILS_INDEX` (`IncomingDetailsID`,`IncomingID`,`TypeID`), ADD KEY `IncomingID` (`IncomingID`), ADD KEY `TypeID` (`TypeID`);

--
-- Indexes for table `transaction_invoicenumber`
--
ALTER TABLE `transaction_invoicenumber`
 ADD PRIMARY KEY (`InvoiceNumberID`), ADD UNIQUE KEY `INVOICENUMBER_INDEX` (`InvoiceNumberID`,`InvoiceNumberType`,`InvoiceDate`);

--
-- Indexes for table `transaction_outgoing`
--
ALTER TABLE `transaction_outgoing`
 ADD PRIMARY KEY (`OutgoingID`), ADD UNIQUE KEY `OUTGOING_INDEX` (`OutgoingID`,`CustomerID`), ADD KEY `CustomerID` (`CustomerID`), ADD KEY `SalesID` (`SalesID`);

--
-- Indexes for table `transaction_outgoingdetails`
--
ALTER TABLE `transaction_outgoingdetails`
 ADD PRIMARY KEY (`OutgoingDetailsID`), ADD UNIQUE KEY `OUTGOINGDETAILS_INDEX` (`OutgoingDetailsID`,`OutgoingID`,`TypeID`), ADD KEY `OutgoingID` (`OutgoingID`), ADD KEY `TypeID` (`TypeID`);

--
-- Indexes for table `transaction_salereturn`
--
ALTER TABLE `transaction_salereturn`
 ADD PRIMARY KEY (`SaleReturnID`), ADD UNIQUE KEY `SALERETURN_INDEX` (`SaleReturnID`,`CustomerID`), ADD KEY `CustomerID` (`CustomerID`);

--
-- Indexes for table `transaction_salereturndetails`
--
ALTER TABLE `transaction_salereturndetails`
 ADD PRIMARY KEY (`SaleReturnDetailsID`), ADD UNIQUE KEY `SALERETURNDETAILS_INDEX` (`SaleReturnDetailsID`,`SaleReturnID`), ADD KEY `SaleReturnID` (`SaleReturnID`), ADD KEY `TypeID` (`TypeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `backup_history`
--
ALTER TABLE `backup_history`
MODIFY `BackupHistoryID` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `master_brand`
--
ALTER TABLE `master_brand`
MODIFY `BrandID` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `master_customer`
--
ALTER TABLE `master_customer`
MODIFY `CustomerID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `master_menu`
--
ALTER TABLE `master_menu`
MODIFY `MenuID` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `master_parameter`
--
ALTER TABLE `master_parameter`
MODIFY `ParameterID` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `master_role`
--
ALTER TABLE `master_role`
MODIFY `RoleID` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `master_sales`
--
ALTER TABLE `master_sales`
MODIFY `SalesID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `master_supplier`
--
ALTER TABLE `master_supplier`
MODIFY `SupplierID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `master_type`
--
ALTER TABLE `master_type`
MODIFY `TypeID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `master_unit`
--
ALTER TABLE `master_unit`
MODIFY `UnitID` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `master_user`
--
ALTER TABLE `master_user`
MODIFY `UserID` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `reset_history`
--
ALTER TABLE `reset_history`
MODIFY `ResetHistoryID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `restore_history`
--
ALTER TABLE `restore_history`
MODIFY `RestoreHistoryID` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `transaction_buyreturn`
--
ALTER TABLE `transaction_buyreturn`
MODIFY `BuyReturnID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transaction_buyreturndetails`
--
ALTER TABLE `transaction_buyreturndetails`
MODIFY `BuyReturnDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transaction_firststock`
--
ALTER TABLE `transaction_firststock`
MODIFY `FirstStockID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transaction_firststockdetails`
--
ALTER TABLE `transaction_firststockdetails`
MODIFY `FirstStockDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transaction_incoming`
--
ALTER TABLE `transaction_incoming`
MODIFY `IncomingID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transaction_incomingdetails`
--
ALTER TABLE `transaction_incomingdetails`
MODIFY `IncomingDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transaction_invoicenumber`
--
ALTER TABLE `transaction_invoicenumber`
MODIFY `InvoiceNumberID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transaction_outgoing`
--
ALTER TABLE `transaction_outgoing`
MODIFY `OutgoingID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transaction_outgoingdetails`
--
ALTER TABLE `transaction_outgoingdetails`
MODIFY `OutgoingDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transaction_salereturn`
--
ALTER TABLE `transaction_salereturn`
MODIFY `SaleReturnID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transaction_salereturndetails`
--
ALTER TABLE `transaction_salereturndetails`
MODIFY `SaleReturnDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `master_menu`
--
ALTER TABLE `master_menu`
ADD CONSTRAINT `master_menu_ibfk_1` FOREIGN KEY (`GroupMenuID`) REFERENCES `master_groupmenu` (`GroupMenuID`);

--
-- Constraints for table `master_role`
--
ALTER TABLE `master_role`
ADD CONSTRAINT `master_role_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `master_user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `master_role_ibfk_2` FOREIGN KEY (`MenuID`) REFERENCES `master_menu` (`MenuID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `master_type`
--
ALTER TABLE `master_type`
ADD CONSTRAINT `master_type_ibfk_1` FOREIGN KEY (`BrandID`) REFERENCES `master_brand` (`BrandID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `master_type_ibfk_2` FOREIGN KEY (`UnitID`) REFERENCES `master_unit` (`UnitID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_buyreturndetails`
--
ALTER TABLE `transaction_buyreturndetails`
ADD CONSTRAINT `transaction_buyreturndetails_ibfk_1` FOREIGN KEY (`BuyReturnID`) REFERENCES `transaction_buyreturn` (`BuyReturnID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `transaction_buyreturndetails_ibfk_2` FOREIGN KEY (`TypeID`) REFERENCES `master_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_firststockdetails`
--
ALTER TABLE `transaction_firststockdetails`
ADD CONSTRAINT `transaction_firststockdetails_ibfk_1` FOREIGN KEY (`FirstStockID`) REFERENCES `transaction_firststock` (`FirstStockID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `transaction_firststockdetails_ibfk_2` FOREIGN KEY (`TypeID`) REFERENCES `master_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_incomingdetails`
--
ALTER TABLE `transaction_incomingdetails`
ADD CONSTRAINT `transaction_incomingdetails_ibfk_1` FOREIGN KEY (`IncomingID`) REFERENCES `transaction_incoming` (`IncomingID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `transaction_incomingdetails_ibfk_2` FOREIGN KEY (`TypeID`) REFERENCES `master_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_outgoing`
--
ALTER TABLE `transaction_outgoing`
ADD CONSTRAINT `transaction_outgoing_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `master_customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `transaction_outgoing_ibfk_2` FOREIGN KEY (`SalesID`) REFERENCES `master_sales` (`SalesID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_outgoingdetails`
--
ALTER TABLE `transaction_outgoingdetails`
ADD CONSTRAINT `transaction_outgoingdetails_ibfk_1` FOREIGN KEY (`OutgoingID`) REFERENCES `transaction_outgoing` (`OutgoingID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `transaction_outgoingdetails_ibfk_2` FOREIGN KEY (`TypeID`) REFERENCES `master_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_salereturn`
--
ALTER TABLE `transaction_salereturn`
ADD CONSTRAINT `transaction_salereturn_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `master_customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_salereturndetails`
--
ALTER TABLE `transaction_salereturndetails`
ADD CONSTRAINT `transaction_salereturndetails_ibfk_1` FOREIGN KEY (`SaleReturnID`) REFERENCES `transaction_salereturn` (`SaleReturnID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `transaction_salereturndetails_ibfk_2` FOREIGN KEY (`TypeID`) REFERENCES `master_type` (`TypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
