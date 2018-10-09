-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2018 at 03:39 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 5.6.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelBooking` (`pBookingID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelBookingDetails` (`pBookingDetailsID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelCategory` (`pCategoryID` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelCustomer` (`pCustomerID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelFirstStock` (`pFirstStockID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelFirstStockDetails` (`pFirstStockDetailsID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelItem` (`pItemID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelPaymentDetails` (`pPaymentDetailsID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelPickUp` (`pPickUpID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelPurchase` (`pPurchaseID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelPurchaseDetails` (`pPurchaseDetailsID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelPurchaseReturn` (`pPurchaseReturnID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelPurchaseReturnDetails` (`pPurchaseReturnDetailsID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelSale` (`pSaleID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelSaleDetails` (`pSaleDetailsID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelSaleReturn` (`pSaleReturnID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelStockAdjust` (`pStockAdjustDetailsID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelStockAdjustDetails` (`pStockAdjustDetailsID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelStockMutation` (`pStockMutationDetailsID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelStockMutationDetails` (`pStockMutationDetailsID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelSupplier` (`pSupplierID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelUnit` (`pUnitID` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
            
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spDelUser` (`pUserID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsBooking` (`pID` BIGINT, `pBookingNumber` VARCHAR(100), `pRetailFlag` BIT, `pFinishFlag` BIT, `pCustomerID` BIGINT, `pTransactionDate` DATETIME, `pBookingDetailsID` BIGINT, `pBranchID` INT, `pItemID` BIGINT, `pItemDetailsID` BIGINT, `pQuantity` DOUBLE, `pBuyPrice` DOUBLE, `pBookingPrice` DOUBLE, `pDiscount` DOUBLE, `pUserID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsCategory` (`pID` INT, `pCategoryCode` VARCHAR(100), `pCategoryName` VARCHAR(255), `pIsEdit` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsCustomer` (`pID` BIGINT, `pCustomerCode` VARCHAR(100), `pCustomerName` VARCHAR(255), `pTelephone` VARCHAR(100), `pAddress` TEXT, `pCity` VARCHAR(100), `pRemarks` TEXT, `pIsEdit` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsEventLog` (`pDescription` TEXT, `pSource` VARCHAR(100), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsFirstBalance` (`pID` BIGINT, `pFirstBalanceAmount` DOUBLE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsFirstStock` (`pID` BIGINT, `pFirstStockNumber` VARCHAR(100), `pTransactionDate` DATETIME, `pFirstStockDetailsID` BIGINT, `pBranchID` INT, `pItemID` BIGINT, `pItemDetailsID` BIGINT, `pQuantity` DOUBLE, `pBuyPrice` DOUBLE, `pRetailPrice` DOUBLE, `pPrice1` DOUBLE, `pPrice2` DOUBLE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsItem` (`pID` BIGINT, `pItemCode` VARCHAR(100), `pItemName` VARCHAR(255), `pCategoryID` BIGINT, `pUnitID` SMALLINT, `pBuyPrice` DOUBLE, `pRetailPrice` DOUBLE, `pPrice1` DOUBLE, `pQty1` DOUBLE, `pPrice2` DOUBLE, `pQty2` DOUBLE, `pWeight` DOUBLE, `pMinimumStock` DOUBLE, `pItemDetails` TEXT, `pIsEdit` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsItemImport` (`pID` BIGINT, `pItemCode` VARCHAR(100), `pItemName` VARCHAR(255), `pCategoryID` BIGINT, `pUnitID` SMALLINT, `pBuyPrice` DOUBLE, `pRetailPrice` DOUBLE, `pPrice1` DOUBLE, `pQty1` DOUBLE, `pPrice2` DOUBLE, `pQty2` DOUBLE, `pWeight` DOUBLE, `pMinimumStock` DOUBLE, `pIsEdit` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsPayment` (`pID` BIGINT, `pPaymentDate` DATETIME, `pTransactionType` VARCHAR(1), `pPaymentDetailsID` BIGINT, `pAmount` DOUBLE, `pRemarks` TEXT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsPickUp` (`pID` BIGINT, `pBookingID` BIGINT, `pTransactionDate` DATETIME, `pPickUpData` TEXT, `pIsEdit` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsPurchase` (`pID` BIGINT, `pPurchaseNumber` VARCHAR(100), `pSupplierID` BIGINT, `pTransactionDate` DATETIME, `pPurchaseDetailsID` BIGINT, `pBranchID` INT, `pItemID` BIGINT, `pItemDetailsID` BIGINT, `pQuantity` DOUBLE, `pBuyPrice` DOUBLE, `pRetailPrice` DOUBLE, `pPrice1` DOUBLE, `pPrice2` DOUBLE, `pDeadline` DATETIME, `pPaymentTypeID` SMALLINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsPurchaseReturn` (`pID` BIGINT, `pPurchaseReturnNumber` VARCHAR(100), `pSupplierID` BIGINT, `pTransactionDate` DATETIME, `pPurchaseReturnDetailsID` BIGINT, `pBranchID` INT, `pItemID` BIGINT, `pItemDetailsID` BIGINT, `pQuantity` DOUBLE, `pBuyPrice` DOUBLE, `pUserID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsSale` (`pID` BIGINT, `pSaleNumber` VARCHAR(100), `pRetailFlag` BIT, `pFinishFlag` BIT, `pCustomerID` BIGINT, `pTransactionDate` DATETIME, `pSaleDetailsID` BIGINT, `pBranchID` INT, `pItemID` BIGINT, `pItemDetailsID` BIGINT, `pQuantity` DOUBLE, `pBuyPrice` DOUBLE, `pSalePrice` DOUBLE, `pDiscount` DOUBLE, `pUserID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsSaleReturn` (`pID` BIGINT, `pSaleID` BIGINT, `pTransactionDate` DATETIME, `pSaleReturnData` TEXT, `pIsEdit` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsStockAdjust` (`pID` BIGINT, `pBranchID` INT, `pTransactionDate` DATETIME, `pStockAdjustDetailsID` BIGINT, `pItemID` BIGINT, `pItemDetailsID` BIGINT, `pQuantity` DOUBLE, `pAdjustedQuantity` DOUBLE, `pUserID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsStockMutation` (`pID` BIGINT, `pSourceID` INT, `pDestinationID` INT, `pTransactionDate` DATETIME, `pStockMutationDetailsID` BIGINT, `pItemID` BIGINT, `pItemDetailsID` BIGINT, `pQuantity` DOUBLE, `pUserID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsSupplier` (`pID` BIGINT, `pSupplierCode` VARCHAR(100), `pSupplierName` VARCHAR(255), `pTelephone` VARCHAR(100), `pAddress` TEXT, `pCity` VARCHAR(100), `pRemarks` TEXT, `pIsEdit` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsUnit` (`pID` INT, `pUnitName` VARCHAR(255), `pIsEdit` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spInsUser` (`pID` BIGINT, `pUserName` VARCHAR(255), `pUserTypeID` SMALLINT, `pUserLogin` VARCHAR(100), `pPassword` VARCHAR(255), `pIsActive` BIT, `pRoleValues` TEXT, `pIsEdit` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelBooking` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelBookingDetails` (`pBookingID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelBookingDetailsByNumber` (`pBookingNumber` VARCHAR(100), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelBookingDetailsPrint` (`pBookingDetailsID` TEXT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelBookingHeader` (`pBookingID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelCategory` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelCategoryByName` (`pCategoryName` VARCHAR(100), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelCreditReport` (`pFromDate` DATE, `pWhere` TEXT, `pWhere2` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelCustomer` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelCustomerPurchaseReport` (`pCustomerID` INT, `pFromDate` DATE, `pToDate` DATE, `pWhere` TEXT, `pWhere2` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDailyReport` (`pUserID` BIGINT, `pTransactionDate` DATE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDailyReportPrint` (`pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDDLBranch` (`pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDDLCashier` (`pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDDLCategory` (`pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDDLCustomer` (`pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDDLSupplier` (`pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDDLUnit` (`pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDDLUserType` (`pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDeadlinePurchase` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
	
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDebtPayment` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelDebtReport` (`pFromDate` DATE, `pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelExportCreditReport` (`pFromDate` DATE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelExportCustomerPurchaseReport` (`pCustomerID` INT, `pFromDate` DATE, `pToDate` DATE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelExportDebtReport` (`pFromDate` DATE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelExportIncomeReport` (`pBranchID` INT, `pFromDate` DATE, `pToDate` DATE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelExportItem` (`pCategoryID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
		
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelExportPurchaseReport` (`pBranchID` INT, `pFromDate` DATE, `pToDate` DATE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelExportSaleReport` (`pBranchID` INT, `pFromDate` DATE, `pToDate` DATE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelExportStockReport` (`pCategoryID` BIGINT, `pBranchID` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
                    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelFirstStock` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelFirstStockDetails` (`pFirstStockID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelIncomeDetailsReport` (`pSaleID` BIGINT, `pBranchID` INT, `pTransactionType` VARCHAR(100), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelIncomeReport` (`pBranchID` INT, `pFromDate` DATE, `pToDate` DATE, `pWhere` TEXT, `pWhere2` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelItem` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelItemDetails` (`pItemCode` VARCHAR(100), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelItemList` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelItemListBranch` (`pBranchID` INT, `pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelItemListStock` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelItemMinimumStock` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelItemQtyDetails` (`pItemCode` VARCHAR(100), `pBranchID` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelItemUnitDetails` (`pItemID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelMenu` (`pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN
	
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
		
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelParameter` (`pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN
	
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
		
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPayment` (`pWhere` TEXT, `pWhere2` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPaymentDetails` (`pTransactionID` BIGINT, `pTransactionType` VARCHAR(1), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPaymentDetailsReport` (`pSaleID` BIGINT, `pFromDate` DATE, `pTransactionType` VARCHAR(100), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPickUp` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPickUpDetails` (`pPickID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPrint` (`pWhere` TEXT, `pWhere2` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPurchase` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPurchaseDetails` (`pPurchaseID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPurchaseDetailsReport` (`pPurchaseID` BIGINT, `pBranchID` INT, `pTransactionType` VARCHAR(100), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPurchaseReport` (`pBranchID` INT, `pFromDate` DATE, `pToDate` DATE, `pWhere` TEXT, `pWhere2` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPurchaseReturn` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelPurchaseReturnDetails` (`pPurchaseReturnID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelSale` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
                        IFNULL(PT.PaymentTypeID, 1) PaymentTypeID

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelSaleDetails` (`pSaleID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelSaleDetailsByNumber` (`pSaleNumber` VARCHAR(100), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelSaleDetailsPrint` (`pSaleDetailsID` TEXT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelSaleDetailsReport` (`pSaleID` BIGINT, `pBranchID` INT, `pTransactionType` VARCHAR(100), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelSaleHeader` (`pSaleID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelSaleReport` (`pBranchID` INT, `pFromDate` DATE, `pToDate` DATE, `pWhere` TEXT, `pWhere2` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelSaleReturn` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelSaleReturnDetails` (`pSaleReturnID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelStockAdjust` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelStockAdjustDetails` (`pStockAdjustID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelStockDetailsReport` (`pItemID` BIGINT, `pBranchID` INT, `pFromDate` DATE, `pToDate` DATE, `pConversionQuantity` DOUBLE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelStockMutation` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelStockMutationDetails` (`pStockMutationID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelStockReport` (`pCategoryID` BIGINT, `pBranchID` INT, `pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelSupplier` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelTopSellingReport` (`pCategoryID` INT, `pFromDate` DATE, `pToDate` DATE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
    
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelUnit` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelUnitByName` (`pUnitName` VARCHAR(100), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelUser` (`pWhere` TEXT, `pOrder` TEXT, `pLimit_s` BIGINT, `pLimit_l` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelUserDetails` (`pUserID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
        
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelUserLogin` (`pUserLogin` VARCHAR(100), `pPassword` VARCHAR(255), `pIsActive` BIT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
		
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelUserMenuNavigation` (`pUserID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN
	
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
		
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spSelUserMenuPermission` (`pApplicationPath` VARCHAR(255), `pRequestedPath` VARCHAR(255), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN
	
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
		
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdBooking` (`pID` BIGINT, `pCustomerID` BIGINT, `pTransactionDate` DATETIME, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdBookingDetailsBranch` (`pID` BIGINT, `pBranchID` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdBookingPayment` (`pID` BIGINT, `pPayment` DOUBLE, `pPaymentTypeID` SMALLINT, `pFinishFlag` BIT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdFirstStock` (`pID` BIGINT, `pFirstStockNumber` VARCHAR(100), `pTransactionDate` DATETIME, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdItem` (`pID` BIGINT, `pBuyPrice` DOUBLE, `pRetailPrice` DOUBLE, `pPrice1` DOUBLE, `pQty1` DOUBLE, `pPrice2` DOUBLE, `pQty2` DOUBLE, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdPayment` (`pID` BIGINT, `pPayment` DOUBLE, `pTransactionType` VARCHAR(1), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdPurchase` (`pID` BIGINT, `pPurchaseNumber` VARCHAR(100), `pSupplierID` BIGINT, `pTransactionDate` DATETIME, `pDeadline` DATETIME, `pPaymentTypeID` SMALLINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdPurchaseReturn` (`pID` BIGINT, `pSupplierID` BIGINT, `pTransactionDate` DATETIME, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdSale` (`pID` BIGINT, `pCustomerID` BIGINT, `pTransactionDate` DATETIME, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdSaleDetailsBranch` (`pID` BIGINT, `pBranchID` INT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdSalePayment` (`pID` BIGINT, `pPayment` DOUBLE, `pPaymentTypeID` SMALLINT, `pFinishFlag` BIT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spUpdUserPassword` (`pID` BIGINT, `pPassword` VARCHAR(255), `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spValItem` (`pID` BIGINT, `pCurrentUser` VARCHAR(255))  StoredProcedure:BEGIN

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

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `backup_history`
--

CREATE TABLE `backup_history` (
  `BackupHistoryID` bigint(20) NOT NULL,
  `BackupDate` date DEFAULT NULL,
  `FilePath` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `master_branch`
--

CREATE TABLE `master_branch` (
  `BranchID` int(11) NOT NULL,
  `BranchCode` varchar(100) DEFAULT NULL,
  `BranchName` varchar(255) NOT NULL,
  `Address` text,
  `Telephone` varchar(100) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_branch`
--

INSERT INTO `master_branch` (`BranchID`, `BranchCode`, `BranchName`, `Address`, `Telephone`, `City`, `Remarks`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'TK', 'Toko', '', '', '', '', '2018-09-19 07:30:04', 'Admin', NULL, NULL),
(2, 'GDG', 'Gudang', '', '', '', '', '2018-09-19 07:30:04', 'Admin', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_category`
--

CREATE TABLE `master_category` (
  `CategoryID` int(11) NOT NULL,
  `CategoryCode` varchar(100) DEFAULT NULL,
  `CategoryName` varchar(255) NOT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_category`
--

INSERT INTO `master_category` (`CategoryID`, `CategoryCode`, `CategoryName`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, '01', 'VARIASI', '2018-06-05 06:30:35', 'Admin1', NULL, NULL),
(2, '02', 'SPAREPART', '2018-06-05 06:30:45', 'Admin1', NULL, NULL),
(3, '03', 'BAUT', '2018-06-05 06:30:52', 'Admin1', NULL, NULL),
(4, '04', 'PILOK', '2018-06-05 06:31:00', 'Admin1', NULL, NULL),
(5, '05', 'OLI', '2018-06-05 06:31:09', 'Admin1', NULL, NULL),
(6, '06', 'BAN', '2018-06-05 06:31:16', 'Admin1', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_customer`
--

CREATE TABLE `master_customer` (
  `CustomerID` bigint(20) NOT NULL,
  `CustomerCode` varchar(100) DEFAULT NULL,
  `CustomerName` varchar(255) NOT NULL,
  `Address` text,
  `Telephone` varchar(100) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `master_eventlog`
--

CREATE TABLE `master_eventlog` (
  `EventLogID` bigint(20) NOT NULL,
  `EventLogDate` datetime DEFAULT NULL,
  `Description` text,
  `Source` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_eventlog`
--

INSERT INTO `master_eventlog` (`EventLogID`, `EventLogDate`, `Description`, `Source`, `CreatedDate`, `CreatedBy`) VALUES
(1, '2018-09-19 07:44:45', 'Unknown column \'NaN\' in \'field list\'', '/Transaction/Purchase/Insert.php', '2018-09-19 07:44:45', 'Admin1'),
(2, '2018-09-19 07:58:08', 'Unknown column \'NaN\' in \'field list\'', '/Transaction/Purchase/Insert.php', '2018-09-19 07:58:08', 'Admin1');

-- --------------------------------------------------------

--
-- Table structure for table `master_groupmenu`
--

CREATE TABLE `master_groupmenu` (
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
(1, 'Home', 'fa fa-home fa-2x', './Home.php', 1),
(2, 'Master Data', 'fa fa-book fa-2x', NULL, 2),
(3, 'Transaksi', 'fa fa-cart-plus fa-2x', NULL, 3),
(4, 'Laporan', 'fa fa-line-chart fa-2x', NULL, 4),
(5, 'Tools', 'fa fa-gear fa-2x', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `master_item`
--

CREATE TABLE `master_item` (
  `ItemID` bigint(20) NOT NULL,
  `SessionID` varchar(100) DEFAULT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  `UnitID` smallint(6) DEFAULT NULL,
  `ItemName` varchar(255) NOT NULL,
  `ItemCode` varchar(100) DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `RetailPrice` double DEFAULT NULL,
  `Price1` double DEFAULT NULL,
  `Qty1` double DEFAULT NULL,
  `Price2` double DEFAULT NULL,
  `Qty2` double DEFAULT NULL,
  `Weight` double DEFAULT NULL,
  `MinimumStock` double DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_item`
--

INSERT INTO `master_item` (`ItemID`, `SessionID`, `CategoryID`, `UnitID`, `ItemName`, `ItemCode`, `BuyPrice`, `RetailPrice`, `Price1`, `Qty1`, `Price2`, `Qty2`, `Weight`, `MinimumStock`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, NULL, 1, 1, 'Karpet Beat F1', 'T1618_1', 24000, 36000, 0, 1, 0, 1, 0, 0, '2018-06-05 06:40:25', 'Admin1', '2018-06-05 20:42:02', 'Admin1'),
(2, NULL, 1, 1, 'karpet vario', 'T1618_2', 24000, 36000, 0, 1, 0, 1, 0, 0, '2018-06-05 06:45:18', 'Admin1', '2018-06-05 20:45:34', 'Admin1'),
(3, NULL, 1, 1, 'karpet beat 2017', 't1618_3', 24000, 36000, 0, 1, 0, 1, 0, 0, '2018-06-05 06:46:54', 'Admin1', '2018-06-05 20:47:05', 'Admin1'),
(4, NULL, 1, 1, 'spion mini beat', 't1618_4', 10000, 16000, 0, 1, 0, 1, 0, 0, '2018-06-05 06:48:35', 'Admin1', '2018-06-09 14:01:37', 'Admin1'),
(5, NULL, 1, 1, 'spion mini jupiter', 't1618_5', 10000, 16000, 0, 1, 0, 1, 0, 0, '2018-06-09 00:00:37', 'Admin1', '2018-06-09 14:00:58', 'Admin1'),
(6, NULL, 1, 1, 'peninggi shock bebebk', 't1618_6', 12000, 20000, 0, 1, 0, 1, 0, 0, '2018-06-09 00:04:27', 'Admin1', '2018-06-09 14:04:55', 'Admin1'),
(7, NULL, 1, 1, 'proguard robot max', 't1618_7', 47500, 70000, 0, 1, 0, 1, 0, 0, '2018-06-09 00:07:01', 'Admin1', '2018-06-09 14:07:11', 'Admin1'),
(8, NULL, 1, 1, 'grip bulu amv', 't1618_8', 14500, 22000, 0, 0, 0, 0, 0, 0, '2018-06-09 00:08:17', 'Admin1', '2018-06-09 14:08:29', 'Admin1'),
(9, NULL, 1, 1, 'grip brige', 't1618_9', 17000, 26000, 0, 1, 0, 1, 0, 0, '2018-06-09 00:09:59', 'Admin1', '2018-06-09 14:10:09', 'Admin1'),
(10, NULL, 1, 1, 'grip litaco donat', 't1618_10', 14500, 22000, 0, 1, 0, 1, 0, 0, '2018-06-09 00:10:57', 'Admin1', '2018-06-09 14:11:11', 'Admin1'),
(11, NULL, 1, 1, 'grip transparan', 't1618_11', 14000, 22000, 0, 1, 0, 1, 0, 0, '2018-06-09 00:11:50', 'Admin1', '2018-06-09 14:12:02', 'Admin1'),
(12, NULL, 1, 1, 'grip kitaco karbon', 't1618_12', 15000, 23000, 0, 1, 0, 1, 0, 0, '2018-06-09 00:13:19', 'Admin1', '2018-06-09 14:13:28', 'Admin1'),
(13, NULL, 1, 1, 'grip kitaco polos', 't1618_13', 16500, 25000, 0, 1, 0, 1, 0, 0, '2018-06-09 00:14:26', 'Admin1', '2018-06-09 14:14:35', 'Admin1'),
(14, NULL, 1, 1, 'tutup oli jarum', 't1618_14', 21000, 32000, 0, 1, 0, 1, 0, 0, '2018-06-09 01:13:25', 'Admin1', '2018-06-09 15:13:35', 'Admin1'),
(15, NULL, 1, 1, 'saklar onoff', 't1618_15', 8000, 14000, 0, 1, 0, 1, 0, 0, '2018-06-09 01:14:24', 'Admin1', '2018-06-09 15:14:36', 'Admin1'),
(16, NULL, 1, 1, 'cop busi', 't1618_16', 7000, 12500, 0, 1, 0, 1, 0, 0, '2018-06-09 01:15:22', 'Admin1', '2018-06-09 15:15:31', 'Admin1'),
(17, NULL, 1, 1, 'gas woll', 't1618_17', 10000, 15000, 0, 1, 0, 1, 0, 0, '2018-06-09 02:19:18', 'Admin1', '2018-06-09 16:19:31', 'Admin1'),
(18, NULL, 1, 1, 'bohlam bebek led cr7', 't1618_18', 27000, 40000, 0, 1, 0, 1, 0, 0, '2018-06-09 02:20:19', 'Admin1', '2018-06-09 16:20:26', 'Admin1'),
(19, NULL, 1, 1, 'sen tempel g18', 't1618_19', 10000, 16000, 0, 1, 0, 1, 0, 0, '2018-06-09 02:21:11', 'Admin1', '2018-06-09 16:21:18', 'Admin1'),
(20, NULL, 1, 1, 'lampu stop luminos', 't1618_20', 12500, 20000, 0, 1, 0, 1, 0, 0, '2018-06-09 02:22:16', 'Admin1', '2018-06-09 16:22:26', 'Admin1'),
(21, NULL, 1, 1, 'sen colok 10 mata r1', 't1618_21', 6000, 10000, 0, 1, 0, 1, 0, 0, '2018-06-09 02:23:14', 'Admin1', '2018-06-09 16:23:21', 'Admin1'),
(22, NULL, 1, 1, 'sen colok plasma biasa', 't1618_22', 8500, 15000, 0, 1, 0, 1, 0, 0, '2018-06-09 02:23:55', 'Admin1', '2018-06-09 16:24:02', 'Admin1'),
(23, NULL, 1, 1, 'lampu owl 3 mata', 't1618_23', 100000, 150000, 0, 1, 0, 1, 0, 0, '2018-06-09 02:24:37', 'Admin1', '2018-06-09 16:24:42', 'Admin1'),
(24, NULL, 1, 1, 'lampu U7 C2R', 't1618_24', 90000, 135000, 0, 1, 0, 1, 0, 0, '2018-06-09 02:25:26', 'Admin1', '2018-06-09 16:25:30', 'Admin1'),
(25, NULL, 1, 1, 'CVT SPRING TDR MIO J 1500 RPM YELLOW', 'OM30518_1', 98000, 142000, 0, 1, 0, 1, 0, 0, '2018-06-10 20:54:07', 'Admin1', '2018-06-11 10:54:45', 'Admin1'),
(26, NULL, 1, 1, 'CVT SPRING TDR MIO J 2000 RPM RED', 'OM30518_2', 98000, 142000, 0, 1, 0, 1, 0, 0, '2018-06-10 20:55:47', 'Admin1', '2018-06-11 10:55:53', 'Admin1'),
(27, NULL, 1, 1, 'SPARK PLUG TDR V BALLISTIC 071', 'OM30518_3', 22500, 32000, 0, 1, 0, 1, 0, 0, '2018-06-10 20:56:46', 'Admin1', '2018-06-11 10:56:53', 'Admin1'),
(28, NULL, 1, 1, 'CVT SPRING TDR BEAT VAR 2000 RPM', 'OM30518_4', 98000, 142000, 0, 1, 0, 1, 0, 0, '2018-06-10 20:57:54', 'Admin1', '2018-06-11 10:58:00', 'Admin1'),
(29, NULL, 6, 1, 'BAN LUAR FLEMMO 90 90 -14 GD 1', 'JT5618_1', 134680, 150000, 0, 1, 0, 1, 0, 0, '2018-06-10 21:05:15', 'Admin1', '2018-07-07 16:27:22', 'Admin1'),
(30, NULL, 6, 1, 'BAN DALAM FDR 225 250 - 17  GD 1', 'JT5618_2', 22320, 27000, 0, 1, 0, 1, 0, 0, '2018-06-10 21:07:17', 'Admin1', '2018-06-11 11:07:23', 'Admin1'),
(31, NULL, 6, 1, 'BAN DALAM FDR 275-17 GD 1', 'JT5618_3', 25200, 30000, 0, 1, 0, 1, 0, 0, '2018-06-10 21:08:09', 'Admin1', '2018-06-11 11:08:16', 'Admin1'),
(32, NULL, 1, 1, 'CVT SPRING TDR VARIO 125 1500 RPM', 'OM12518_1', 98000, 142000, 0, 1, 0, 1, 0, 0, '2018-06-10 23:06:40', 'Admin1', '2018-06-11 13:06:46', 'Admin1'),
(33, NULL, 1, 1, 'CVT SPRING TDR BEATVAR 2000 RPM', 'OM12518_2', 98000, 142000, 0, 1, 0, 1, 0, 0, '2018-06-10 23:07:23', 'Admin1', '2018-06-11 13:07:28', 'Admin1'),
(34, NULL, 1, 1, 'TDR ROLLER CVT VARIO 9 GR', 'OM12518_3', 68500, 100000, 0, 1, 0, 1, 0, 0, '2018-06-10 23:08:07', 'Admin1', '2018-06-11 13:08:12', 'Admin1'),
(35, NULL, 1, 1, 'CLUTCH SPRING SET TDR MIO 1500 RPM Y', 'OM12518_4', 29500, 43000, 0, 1, 0, 1, 0, 0, '2018-06-10 23:08:53', 'Admin1', '2018-06-11 13:09:00', 'Admin1'),
(36, NULL, 1, 1, 'WEIGHT CLUTCH LONGER SHOE TDR MIO', 'OM12518_5', 300000, 435000, 0, 1, 0, 1, 0, 0, '2018-06-10 23:09:46', 'Admin1', '2018-06-11 13:09:49', 'Admin1'),
(37, NULL, 5, 1, 'MESRAN SUPER O.8 ', 'TJ24318_1', 29200, 34000, 0, 1, 0, 1, 0, 0, '2018-06-11 02:10:46', 'Admin1', '2018-06-11 16:10:53', 'Admin1'),
(38, NULL, 5, 1, 'ENDURO 4 T', 'TJ24318_2', 34875, 44000, 0, 1, 0, 1, 0, 0, '2018-06-11 02:11:27', 'Admin1', '2018-06-11 16:11:32', 'Admin1'),
(39, NULL, 5, 1, 'FEDERAL UTECH', 'TJ24318_3', 30000, 35000, 0, 1, 0, 1, 0, 0, '2018-06-11 02:12:10', 'Admin1', '2018-06-11 16:12:15', 'Admin1'),
(40, NULL, 5, 1, 'ENDURO MATIC 0.8', 'TJ4618_1', 37200, 44000, 0, 1, 0, 1, 0, 0, '2018-06-12 02:12:48', 'Admin1', '2018-06-12 16:12:59', 'Admin1'),
(41, NULL, 2, 1, 'B-GIGI SPEDO ASSY BEATVARIO OSK', 'SN11618_', 11025, 22000, 0, 1, 0, 1, 0, 0, '2018-06-12 02:15:50', 'Admin1', '2018-06-12 16:15:57', 'Admin1'),
(42, NULL, 2, 1, 'B-KNOP SEN KHARISMA T', 'SN11618_2', 2100, 6000, 0, 1, 0, 1, 0, 0, '2018-06-12 02:16:37', 'Admin1', '2018-06-12 16:16:42', 'Admin1'),
(43, NULL, 2, 1, 'B-KNOP DIM KHARISMA T', 'SN11618_3', 2100, 6000, 0, 1, 0, 1, 0, 0, '2018-06-12 02:17:21', 'Admin1', '2018-06-12 16:17:30', 'Admin1'),
(44, NULL, 2, 1, 'B-FILTER SARINGAN HAMA MIO SOUL TZENG', 'SN11618_4', 16275, 25000, 0, 1, 0, 1, 0, 0, '2018-06-12 02:18:24', 'Admin1', '2018-06-12 16:18:29', 'Admin1'),
(45, NULL, 2, 1, 'B-HOLDER KANAN MIO 2003 OSK', 'SN11618_5', 15225, 25000, 0, 1, 0, 1, 0, 0, '2018-06-12 02:19:12', 'Admin1', '2018-06-12 16:19:17', 'Admin1'),
(46, NULL, 2, 1, 'B- GIGI  SPEDO ASSY BEATVARIO OSK', 'SN11618_1', 11025, 22000, 0, 1, 0, 1, 0, 0, '2018-07-02 02:24:45', 'Admin1', '2018-07-02 16:25:05', 'Admin1'),
(47, NULL, 5, 1, 'MPX 2', 'TJ2518_1', 38600, 46000, 0, 1, 0, 1, 0, 0, '2018-07-03 00:51:33', 'Admin1', '2018-07-03 14:51:41', 'Admin1'),
(48, NULL, 5, 1, 'FEDERAL MATIC ORANGE', 'TJ2518_2', 30500, 36000, 0, 1, 0, 1, 0, 0, '2018-07-03 00:52:35', 'Admin1', '2018-07-03 14:52:43', 'Admin1'),
(49, NULL, 2, 1, 'DISC PAD VARIO CBS MERK INDOPART', 'AJM25618_1', 16000, 22500, 0, 1, 0, 1, 0, 0, '2018-07-03 01:00:10', 'Admin1', '2018-07-04 14:08:33', 'Admin1'),
(50, NULL, 5, 1, 'ENDURO RACING', 'TJ28618_1', 46500, 54000, 0, 1, 0, 1, 0, 0, '2018-07-03 02:39:21', 'Admin1', '2018-07-03 16:39:27', 'Admin1'),
(51, NULL, 5, 1, 'YAMALUBE 4 T', 'TJ28618_2', 33800, 38000, 0, 1, 0, 1, 0, 0, '2018-07-03 02:39:59', 'Admin1', '2018-07-03 16:40:05', 'Admin1'),
(52, NULL, 2, 1, 'DISC PAD SUPRA X INDOPART', 'AJM25618_2', 14150, 20000, 0, 1, 0, 1, 0, 0, '2018-07-04 00:11:12', 'Admin1', '2018-07-04 14:11:17', 'Admin1'),
(53, NULL, 2, 1, 'DISC PAD JUPITER MX INDOPART', 'AJM25618_3', 15750, 22000, 0, 1, 0, 1, 0, 0, '2018-07-04 00:12:05', 'Admin1', '2018-07-04 14:12:11', 'Admin1'),
(54, NULL, 2, 1, 'FILTER UDARA MIO MERK GENUIN', 'AJM25618_4', 34970, 50000, 0, 1, 0, 1, 0, 0, '2018-07-04 00:12:52', 'Admin1', '2018-07-04 14:12:57', 'Admin1'),
(55, NULL, 2, 1, 'GIGI SPEDO REVO ABS MERK AHM', 'AJM25618_5', 58250, 82000, 0, 1, 0, 1, 0, 0, '2018-07-04 00:13:52', 'Admin1', '2018-07-04 14:13:57', 'Admin1'),
(56, NULL, 2, 1, 'COMSTIR SET MIO MERK INDOPART', 'AJM25618_6', 36650, 52000, 0, 1, 0, 1, 0, 0, '2018-07-04 00:14:49', 'Admin1', '2018-07-04 14:15:02', 'Admin1'),
(57, NULL, 2, 1, 'TOMBOL SEN SUPRA X 125 MERK GENUIN', 'AJM25618_7', 15650, 22000, 0, 1, 0, 1, 0, 0, '2018-07-04 00:16:01', 'Admin1', '2018-07-04 14:16:06', 'Admin1'),
(58, NULL, 2, 1, 'MANUPAL CARBULATOR SCORPIO MERK GENUIN', 'AJM25618_8', 89000, 125000, 0, 1, 0, 1, 0, 0, '2018-07-04 00:16:51', 'Admin1', '2018-07-04 14:16:54', 'Admin1'),
(59, NULL, 6, 1, 'BAN TUBLES 90- 90 R 14', 'NT13618_1', 162750, 187000, 0, 1, 0, 1, 0, 0, '2018-07-06 20:35:30', 'Admin1', '2018-07-07 10:45:54', 'Admin1'),
(60, NULL, 6, 1, 'BAN 90-90 R 14', 'NT13618_2', 131625, 148000, 0, 1, 0, 1, 0, 0, '2018-07-06 20:53:05', 'Admin1', '2018-07-07 10:53:10', 'Admin1'),
(61, NULL, 6, 1, 'BAN TUBLES 80-90 R 14', 'NT13618_3', 141750, 163000, 0, 1, 0, 1, 0, 0, '2018-07-06 20:54:10', 'Admin1', '2018-07-07 10:54:16', 'Admin1'),
(62, NULL, 2, 1, 'CHAIN DRIVE RANTAI 428-106', 'AS21518_1', 48600, 70000, 0, 1, 0, 1, 0, 0, '2018-07-06 20:59:57', 'Admin1', '2018-07-07 11:00:04', 'Admin1'),
(63, NULL, 2, 1, 'CHAIN DRIVE RANTAI 428-108', 'AS21518_2', 49410, 70000, 0, 1, 0, 1, 0, 0, '2018-07-06 21:01:22', 'Admin1', '2018-07-07 11:01:28', 'Admin1'),
(64, NULL, 2, 1, 'BULP H4 12V25-25W', 'AS21518_3', 9720, 15000, 0, 1, 0, 1, 0, 0, '2018-07-06 21:02:34', 'Admin1', '2018-07-07 11:02:38', 'Admin1'),
(65, NULL, 6, 1, 'SUPR KAMPAS REM', 'NT13618_4', 37200, 55000, 0, 1, 0, 1, 0, 0, '2018-07-06 21:19:10', 'Admin1', '2018-07-07 11:19:17', 'Admin1'),
(66, NULL, 6, 1, 'SCOOTER GE', 'NT13618_5', 11700, 17000, 0, 1, 0, 1, 0, 0, '2018-07-06 21:21:57', 'Admin1', '2018-07-07 11:22:05', 'Admin1'),
(67, NULL, 2, 1, 'SHOE SET BRAKE', 'AS22618_1', 26827, 39000, 0, 1, 0, 1, 0, 0, '2018-07-07 02:14:17', 'Admin1', '2018-07-07 16:14:23', 'Admin1'),
(68, NULL, 2, 1, 'MIRROR BACK (SET)', 'AS22618_2', 22680, 33000, 0, 1, 0, 1, 0, 0, '2018-07-07 02:17:56', 'Admin1', '2018-07-07 16:18:00', 'Admin1'),
(69, NULL, 2, 1, 'PAD SET FR', 'AS22618_3', 19375, 28000, 0, 1, 0, 1, 0, 0, '2018-07-07 02:18:35', 'Admin1', '2018-07-07 16:18:42', 'Admin1'),
(70, NULL, 6, 1, 'IRC BAN LUAR  80-90 RING 14 NR76', 'PT23618_1', 109440, 125000, 0, 1, 0, 1, 0, 0, '2018-07-20 23:03:28', 'Admin1', '2018-07-21 13:03:51', 'Admin1'),
(71, NULL, 6, 1, 'IRC BAN LUAR 90-90 RING 14 SS530 R', 'PT23618_2', 131040, 150000, 0, 1, 0, 1, 0, 0, '2018-07-20 23:06:32', 'Admin1', '2018-07-21 13:06:47', 'Admin1'),
(72, NULL, 5, 1, 'OLI MPX 2 08', 'TJ23718_1', 39600, 46000, 0, 1, 0, 1, 0, 0, '2018-07-27 20:34:27', 'Admin1', '2018-07-28 10:34:33', 'Admin1'),
(73, NULL, 1, 1, '087.012 SPARK PLUG TDR TWIN IRIDIUM 071T', 'OM24718_1', 85000, 125000, 0, 1, 0, 1, 0, 0, '2018-07-27 20:37:20', 'Admin1', '2018-07-28 10:37:27', 'Admin1'),
(74, NULL, 1, 1, '087.013 SPAARK PLUG TDR TWIN IRIDIUM 085T', 'OM24718_2', 85000, 125000, 0, 1, 0, 1, 0, 0, '2018-07-27 20:38:10', 'Admin1', '2018-07-28 10:38:15', 'Admin1'),
(75, NULL, 1, 1, '087.001 SPARK PLUG TDR V BALLISTIC 065', 'OM24718_3', 22500, 34000, 0, 1, 0, 1, 0, 0, '2018-07-27 20:39:48', 'Admin1', '2018-07-28 10:39:53', 'Admin1'),
(76, NULL, 1, 1, 'BAGASI TGP SUPRA X 125 NEW FUEL INJECTION', 'TP24718_1', 30600, 45000, 0, 1, 0, 1, 0, 0, '2018-07-27 20:45:07', 'Admin1', '2018-07-28 10:45:11', 'Admin1'),
(77, NULL, 1, 1, 'LEG SHIELD CONTAINER MIO (HITAM)', 'TP24718_2', 45280, 66000, 0, 1, 0, 1, 0, 0, '2018-07-27 20:46:26', 'Admin1', '2018-07-28 10:47:03', 'Admin1'),
(78, NULL, 6, 1, 'IRC BAN LUAR 80-90  R 17 NR 69', 'PT26718_1', 136800, 155000, 0, 1, 0, 1, 0, 0, '2018-07-27 20:56:45', 'Admin1', '2018-07-28 10:56:50', 'Admin1'),
(79, NULL, 6, 1, 'IRC BAN LUAR 90-90 R 14 NR 83 TL', 'PT26718_2', 155940, 175000, 0, 1, 0, 1, 0, 0, '2018-07-27 20:59:16', 'Admin1', '2018-07-28 10:59:25', 'Admin1'),
(80, NULL, 1, 1, 'VISOR VARIO 110 F1 (HTAM)', 'TP25718_1', 30800, 45000, 0, 1, 0, 1, 0, 0, '2018-07-27 23:49:39', 'Admin1', '2018-07-28 13:49:44', 'Admin1'),
(81, NULL, 1, 1, 'WIND SHIELD SCOOPY (BENING)', 'TP25718_2', 69200, 100000, 0, 1, 0, 1, 0, 0, '2018-07-28 02:13:56', 'Admin1', '2018-07-28 16:14:01', 'Admin1'),
(82, NULL, 6, 1, 'BAN LUAR FLEMMO PRO GDS 70-90 R 14', 'JT26718_1', 91575, 105000, 0, 1, 0, 1, 0, 0, '2018-08-03 23:31:28', 'Admin1', '2018-08-04 14:47:21', 'Admin1'),
(83, NULL, 1, 1, 'SHOCK STLN Z SERIES MIO AMV', 'SP6818_1', 106700, 158000, 0, 1, 0, 1, 0, 0, '2018-08-10 20:21:01', 'Admin1', '2018-08-11 10:23:08', 'Admin1'),
(84, NULL, 1, 1, 'SHOCK STLN Z SERIES MIO (P) AMV', 'SP6818_2', 106700, 158000, 0, 1, 0, 1, 0, 0, '2018-08-10 21:26:34', 'Admin1', '2018-08-11 11:26:38', 'Admin1'),
(85, NULL, 1, 1, 'PENINGGI STANG FATBAR (H) AGRAS', 'SP6818_3', 49470, 73000, 0, 1, 0, 1, 0, 0, '2018-08-10 21:27:44', 'Admin1', '2018-08-11 11:27:49', 'Admin1'),
(86, NULL, 1, 1, 'PER KAMPAS KPL RACING JUP Z TDR', 'SP6818_4', 60140, 89000, 0, 1, 0, 1, 0, 0, '2018-08-10 21:30:49', 'Admin1', '2018-08-11 11:30:59', 'Admin1'),
(87, NULL, 1, 1, 'HANFAT TRANSPARAN CORAK IGAWAMOSH', 'SP6818_5', 15035, 25000, 0, 1, 0, 1, 0, 0, '2018-08-10 21:32:19', 'Admin1', '2018-08-11 11:32:29', 'Admin1'),
(88, NULL, 1, 1, 'SEN FAIRING LED SEGITIGA (K) LSMN  GMA', 'SP6818_6', 26675, 40000, 0, 1, 0, 1, 0, 0, '2018-08-10 21:33:43', 'Admin1', '2018-08-11 11:33:51', 'Admin1'),
(89, NULL, 2, 1, 'SELANG REM MERK TW', 'ajm19918_1', 15000, 22000, 22000, 1, 22000, 1, 0, 0, '2018-09-19 07:35:33', 'Admin1', '2018-09-19 14:36:25', 'Admin1'),
(90, NULL, 2, 1, 'BOLP DEPAN BEBEK MERK S TANLEE', 'AJM19918_2', 2100, 5000, 5000, 1, 5000, 1, 0, 0, '2018-09-19 07:37:55', 'Admin1', '2018-09-19 14:38:55', 'Admin1'),
(91, NULL, 2, 1, 'TUTUP KNALPOT BEAT F1 AHM', 'AJM19918_3', 24750, 36000, 36000, 1, 36000, 1, 0, 0, '2018-09-19 07:40:22', 'Admin1', '2018-09-19 14:40:33', 'Admin1'),
(92, NULL, 2, 1, 'CDI BEAT MERK ADL', 'AJM19918_4', 110000, 160000, 160000, 1, 160000, 1, 0, 0, '2018-09-19 07:41:57', 'Admin1', '2018-09-19 14:42:16', 'Admin1'),
(93, NULL, 2, 1, 'MIKA SPEDO VEGA R NEW GENUIN', 'AJM19918_5', 15250, 23000, 23000, 1, 23000, 1, 0, 0, '2018-09-19 07:44:01', 'Admin1', '2018-09-19 14:44:14', 'Admin1'),
(94, NULL, 2, 1, 'MIKA SPEDO MIO SOUL GENUIN', 'AJM19918_6', 16250, 24000, 24000, 1, 24000, 1, 0, 0, '2018-09-19 07:46:15', 'Admin1', '2018-09-19 14:46:31', 'Admin1'),
(95, NULL, 2, 1, 'MIKA SPEDO MIO GT', 'AJM19918_7', 15250, 23000, 23000, 1, 23000, 1, 0, 0, '2018-09-19 07:48:05', 'Admin1', '2018-09-19 14:48:20', 'Admin1'),
(96, NULL, 2, 1, ' ROLLER MIO CHOHO', 'AJM19918_8', 37750, 55000, 55000, 1, 55000, 1, 0, 0, '2018-09-19 07:49:44', 'Admin1', '2018-09-19 14:57:24', 'Admin1'),
(97, NULL, 2, 1, 'BAN DLM 8090 R 17 FDR', 'AJM19918_9', 25500, 37000, 37000, 1, 37000, 1, 0, 0, '2018-09-19 07:51:52', 'Admin1', '2018-09-19 14:52:07', 'Admin1'),
(98, NULL, 2, 1, 'KLAKSON BEBEK AHM', 'AJM19918_10', 28000, 42000, 42000, 1, 42000, 1, 0, 0, '2018-09-19 07:53:26', 'Admin1', '2018-09-19 14:53:41', 'Admin1'),
(99, NULL, 2, 1, 'BOLP CUMI RITING STANLEE', 'AJM19918_11', 675, 1500, 1500, 1, 1500, 1, 0, 0, '2018-09-19 07:55:05', 'Admin1', '2018-09-19 14:55:18', 'Admin1');

-- --------------------------------------------------------

--
-- Table structure for table `master_itemdetails`
--

CREATE TABLE `master_itemdetails` (
  `ItemDetailsID` bigint(20) NOT NULL,
  `ItemID` bigint(20) DEFAULT NULL,
  `ItemDetailsCode` varchar(100) DEFAULT NULL,
  `UnitID` smallint(6) DEFAULT NULL,
  `ConversionQuantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `RetailPrice` double DEFAULT NULL,
  `Price1` double DEFAULT NULL,
  `Qty1` double DEFAULT NULL,
  `Price2` double DEFAULT NULL,
  `Qty2` double DEFAULT NULL,
  `Weight` double DEFAULT NULL,
  `MinimumStock` double DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `master_menu`
--

CREATE TABLE `master_menu` (
  `MenuID` bigint(20) NOT NULL,
  `GroupMenuID` int(11) DEFAULT NULL,
  `MenuName` varchar(255) DEFAULT NULL,
  `Url` varchar(255) DEFAULT NULL,
  `Icon` varchar(255) DEFAULT NULL,
  `IsReport` bit(1) DEFAULT NULL,
  `OrderNo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_menu`
--

INSERT INTO `master_menu` (`MenuID`, `GroupMenuID`, `MenuName`, `Url`, `Icon`, `IsReport`, `OrderNo`) VALUES
(1, 2, 'User', 'Master/User/', NULL, b'0', 1),
(2, 2, 'Kategori', 'Master/Category/', NULL, b'0', 2),
(3, 2, 'Satuan', 'Master/Unit/', NULL, b'0', 3),
(4, 2, 'Barang', 'Master/Item/', NULL, b'0', 4),
(5, 2, 'Supplier', 'Master/Supplier/', NULL, b'0', 5),
(6, 2, 'Pelanggan', 'Master/Customer/', NULL, b'0', 6),
(7, 2, 'Upload Barang', 'Master/ItemUpload/', NULL, b'0', 7),
(8, 3, 'Stok Awal', 'Transaction/FirstStock/', NULL, b'0', 1),
(9, 3, 'Pembelian', 'Transaction/Purchase/', NULL, b'0', 2),
(10, 3, 'Retur Pembelian', 'Transaction/PurchaseReturn/', NULL, b'0', 3),
(11, 3, 'Penjualan', 'Transaction/Sale/', NULL, b'0', 4),
(12, 3, 'Retur Penjualan', 'Transaction/SaleReturn/', NULL, b'0', 5),
(13, 3, 'Mutasi Stok', 'Transaction/StockMutation/', NULL, b'0', 6),
(14, 3, 'Penyesuaian Stok', 'Transaction/StockAdjust/', NULL, b'0', 7),
(15, 3, 'D.O', 'Transaction/Booking/', NULL, b'0', 8),
(16, 3, 'Pembayaran Piutang', 'Transaction/Payment/', NULL, b'0', 9),
(17, 3, 'Pengambilan', 'Transaction/PickUp/', NULL, b'0', 10),
(18, 3, 'Cetak Nota', 'Transaction/Print/', NULL, b'0', 11),
(19, 4, 'Stok', 'Report/Stock/', NULL, b'1', 1),
(20, 4, 'Detail Stok', 'Report/StockDetails/', NULL, b'1', 2),
(21, 4, 'Penjualan', 'Report/Sale/', NULL, b'1', 3),
(22, 4, 'Pembelian', 'Report/Purchase/', NULL, b'1', 4),
(23, 4, 'Pendapatan', 'Report/Income/', NULL, b'1', 5),
(24, 4, 'Omset Pelanggan', 'Report/CustomerPurchase/', NULL, b'1', 6),
(25, 4, 'Harian', 'Report/Daily/', NULL, b'1', 7),
(26, 4, 'Piutang', 'Report/Credit/', NULL, b'1', 8),
(27, 5, 'Backup Data', 'Tools/Backup/', NULL, b'0', 1),
(28, 5, 'Restore Data', 'Tools/Restore/', NULL, b'0', 2),
(29, 5, 'Reset Data', 'Tools/Reset/', NULL, b'0', 3),
(30, 3, 'Pembayaran Hutang', 'Transaction/DebtPayment/', NULL, b'0', 12),
(31, 4, 'Hutang', 'Report/Debt/', NULL, b'1', 9),
(32, 4, 'Barang Terlaris', 'Report/TopSelling/', NULL, b'1', 10);

-- --------------------------------------------------------

--
-- Table structure for table `master_parameter`
--

CREATE TABLE `master_parameter` (
  `ParameterID` bigint(20) NOT NULL,
  `ParameterName` varchar(255) NOT NULL,
  `ParameterValue` varchar(255) NOT NULL,
  `Remarks` text,
  `IsNumber` int(11) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_parameter`
--

INSERT INTO `master_parameter` (`ParameterID`, `ParameterName`, `ParameterValue`, `Remarks`, `IsNumber`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'APPLICATION_PATH', '/POSAdmin/Source/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', '2018-09-19 14:30:54', NULL),
(2, 'MYSQL_DUMP_PATH', 'C:\\xampp\\mysql\\bin\\mysqldump.exe', 'Path of mysqldump.exe', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(3, 'ERROR_LOG_PATH', 'C:\\xampp\\htdocs\\POSAdmin\\Source\\BackupFiles\\dumperrors.txt', 'log error when backup failed', 0, '2016-03-12 00:00:00', 'admin', '2018-09-19 14:31:05', NULL),
(4, 'BACKUP_FULLPATH', 'C:\\xampp\\htdocs\\Projects\\POSAdmin\\Source\\BackupFiles\\', 'Directory where backup files located', 0, '2016-03-12 00:00:00', 'admin', '2016-03-12 22:25:59', NULL),
(5, 'BACKUP_FOLDER', 'BackupFiles\\\\', 'Backup path', 0, '2016-03-12 00:00:00', 'Admin', '2016-03-12 22:43:21', NULL),
(6, 'MYSQL_PATH', 'C:\\xampp\\mysql\\bin\\mysql.exe', 'mysql.exe path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(7, 'UPLOAD_PATH', 'C:\\xampp\\htdocs\\Projects\\POSAdmin\\Source\\UploadedFiles\\', 'Upload Path', 0, '2016-03-12 00:00:00', 'Admin', NULL, NULL),
(8, 'SHARED_PRINTER_ADDRESS', '//localhost/EPSON', 'For shared printer', 0, '2016-03-20 00:00:00', 'Admin', NULL, NULL),
(9, 'MOBILE_PATH', '/POSAdmin/Mobile/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', '2018-09-19 14:31:20', NULL),
(10, 'DESKTOP_PATH', '/POSAdmin/Desktop/', 'Location of the application', 0, '2016-03-12 15:01:05', 'System', '2018-09-19 14:31:15', NULL),
(11, 'MOBILE_HOME', 'http://192.168.1.21/Projects/POSAdmin/Mobile/Home.php', 'Location of the home for mobile view', 0, '2016-03-12 15:01:05', 'System', NULL, NULL),
(12, 'FINSH_DEFAULT', '0', 'Default value for finish flag', 0, '2016-03-12 15:01:05', 'System', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_paymenttype`
--

CREATE TABLE `master_paymenttype` (
  `PaymentTypeID` smallint(6) NOT NULL,
  `PaymentTypeName` varchar(100) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_paymenttype`
--

INSERT INTO `master_paymenttype` (`PaymentTypeID`, `PaymentTypeName`, `CreatedDate`, `CreatedBy`) VALUES
(1, 'Tunai', '2018-09-19 07:30:05', 'Admin'),
(2, 'Tempo', '2018-09-19 07:30:05', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `master_role`
--

CREATE TABLE `master_role` (
  `RoleID` bigint(20) NOT NULL,
  `UserID` bigint(20) DEFAULT NULL,
  `MenuID` bigint(20) DEFAULT NULL,
  `EditFlag` tinyint(1) DEFAULT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(19, 1, 19, 1, 1),
(20, 1, 20, 1, 1),
(21, 1, 21, 1, 1),
(22, 1, 22, 1, 1),
(23, 1, 23, 1, 1),
(24, 1, 24, 1, 1),
(25, 1, 25, 1, 1),
(26, 1, 26, 1, 1),
(27, 1, 27, 1, 1),
(28, 1, 28, 1, 1),
(29, 1, 29, 1, 1),
(30, 1, 30, 1, 1),
(31, 1, 31, 1, 1),
(32, 1, 32, 1, 1),
(71, 2, 8, 1, 1),
(72, 2, 9, 1, 1),
(73, 2, 10, 1, 1),
(74, 2, 11, 1, 1),
(75, 2, 12, 1, 1),
(76, 2, 13, 1, 1),
(77, 2, 14, 1, 1),
(78, 2, 15, 1, 1),
(79, 2, 16, 1, 1),
(80, 2, 17, 1, 1),
(81, 2, 18, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `master_supplier`
--

CREATE TABLE `master_supplier` (
  `SupplierID` bigint(20) NOT NULL,
  `SupplierCode` varchar(100) DEFAULT NULL,
  `SupplierName` varchar(255) NOT NULL,
  `Telephone` varchar(100) DEFAULT NULL,
  `Address` text,
  `City` varchar(100) DEFAULT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_supplier`
--

INSERT INTO `master_supplier` (`SupplierID`, `SupplierCode`, `SupplierName`, `Telephone`, `Address`, `City`, `Remarks`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, '001', 'AA MOTOR', '', '', '', '', '2018-06-05 06:32:21', 'Admin1', NULL, NULL),
(2, '002', 'Indomotor Sport', '', '', '', '', '2018-06-05 06:41:31', 'Admin1', NULL, NULL),
(3, '003', 'TRIJAYA', '', '', '', '', '2018-06-10 20:48:38', 'Admin1', NULL, NULL),
(4, '004', 'OMG WARID', '', '', '', '', '2018-06-10 20:52:24', 'Admin1', NULL, NULL),
(5, '005', 'PT. JATENG TOP - SEMARANG', '', '', '', '', '2018-06-10 21:03:21', 'Admin1', NULL, NULL),
(6, '006', 'UD SINAR MOTOR', '', '', '', '', '2018-06-12 02:14:18', 'Admin1', NULL, NULL),
(7, '007', 'ANUGRAH JAYA MOTOR', '', '', '', '', '2018-07-03 00:57:33', 'Admin1', NULL, NULL),
(8, '008', 'PT.NUSANTARA SAKTI', '', '', '', '', '2018-07-06 20:24:23', 'Admin1', NULL, NULL),
(9, '009', 'PT.ASTRA OTOPATS TBK', '', '', '', '', '2018-07-06 20:57:20', 'Admin1', NULL, NULL),
(10, '0010', 'PT PRIMA TUNGGAL MANDIRI', '', '', '', '', '2018-07-20 23:00:40', 'Admin1', NULL, NULL),
(11, '010', 'TGP', '', '', '', '', '2018-07-27 20:42:10', 'Admin1', NULL, NULL),
(12, '011', 'S.P.C', '', '', '', '', '2018-08-10 20:19:59', 'Admin1', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_unit`
--

CREATE TABLE `master_unit` (
  `UnitID` smallint(6) NOT NULL,
  `UnitName` varchar(255) NOT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_unit`
--

INSERT INTO `master_unit` (`UnitID`, `UnitName`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 'pcs', '2018-06-05 06:29:57', 'Admin1', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_user`
--

CREATE TABLE `master_user` (
  `UserID` bigint(20) NOT NULL,
  `UserTypeID` smallint(6) NOT NULL,
  `UserName` varchar(255) NOT NULL,
  `UserLogin` varchar(100) NOT NULL,
  `UserPassword` varchar(255) NOT NULL,
  `IsActive` tinyint(1) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_user`
--

INSERT INTO `master_user` (`UserID`, `UserTypeID`, `UserName`, `UserLogin`, `UserPassword`, `IsActive`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(1, 1, 'System Administrator', 'Admin1', 'e80b5017098950fc58aad83c8c14978e', 1, '2018-09-19 07:30:00', 'System', NULL, NULL),
(2, 1, 'Enny', 'enny', 'c4ca4238a0b923820dcc509a6f75849b', 1, '2018-06-05 06:37:50', 'Admin1', NULL, NULL),
(3, 2, 'zulfa', 'zulfa', '827ccb0eea8a706c4c34a16891f84e7b', 1, '2018-06-08 23:56:06', 'Admin1', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_usertype`
--

CREATE TABLE `master_usertype` (
  `UserTypeID` smallint(6) NOT NULL,
  `UserTypeName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_usertype`
--

INSERT INTO `master_usertype` (`UserTypeID`, `UserTypeName`) VALUES
(1, 'Admin'),
(2, 'Kasir');

-- --------------------------------------------------------

--
-- Table structure for table `reset_history`
--

CREATE TABLE `reset_history` (
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

CREATE TABLE `restore_history` (
  `RestoreHistoryID` bigint(20) NOT NULL,
  `RestoreDate` date DEFAULT NULL,
  `FilePath` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_booking`
--

CREATE TABLE `transaction_booking` (
  `BookingID` bigint(20) NOT NULL,
  `BookingNumber` varchar(100) DEFAULT NULL,
  `RetailFlag` bit(1) NOT NULL,
  `CustomerID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `PaymentTypeID` smallint(6) DEFAULT NULL,
  `Payment` double DEFAULT NULL,
  `PrintCount` smallint(6) DEFAULT NULL,
  `PrintedDate` datetime DEFAULT NULL,
  `FinishFlag` bit(1) DEFAULT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_bookingdetails`
--

CREATE TABLE `transaction_bookingdetails` (
  `BookingDetailsID` bigint(20) NOT NULL,
  `BookingID` bigint(20) DEFAULT NULL,
  `ItemID` bigint(20) NOT NULL,
  `ItemDetailsID` bigint(20) DEFAULT NULL,
  `BranchID` int(11) DEFAULT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `BookingPrice` double DEFAULT NULL,
  `Discount` double DEFAULT NULL,
  `PrintCount` smallint(6) DEFAULT NULL,
  `PrintedDate` datetime DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_firstbalance`
--

CREATE TABLE `transaction_firstbalance` (
  `FirstBalanceID` bigint(20) NOT NULL,
  `UserID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `FirstBalanceAmount` double DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_firststock`
--

CREATE TABLE `transaction_firststock` (
  `FirstStockID` bigint(20) NOT NULL,
  `FirstStockNumber` varchar(100) DEFAULT NULL,
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
-- Table structure for table `transaction_firststockdetails`
--

CREATE TABLE `transaction_firststockdetails` (
  `FirstStockDetailsID` bigint(20) NOT NULL,
  `FirstStockID` bigint(20) DEFAULT NULL,
  `ItemID` bigint(20) NOT NULL,
  `ItemDetailsID` bigint(20) DEFAULT NULL,
  `BranchID` int(11) DEFAULT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `RetailPrice` double DEFAULT NULL,
  `Price1` double DEFAULT NULL,
  `Price2` double DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_paymentdetails`
--

CREATE TABLE `transaction_paymentdetails` (
  `PaymentDetailsID` bigint(20) NOT NULL,
  `TransactionID` bigint(20) DEFAULT NULL,
  `TransactionType` varchar(1) DEFAULT NULL,
  `PaymentDate` datetime DEFAULT NULL,
  `Amount` double DEFAULT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_pick`
--

CREATE TABLE `transaction_pick` (
  `PickID` bigint(20) NOT NULL,
  `BookingID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_pickdetails`
--

CREATE TABLE `transaction_pickdetails` (
  `PickDetailsID` bigint(20) NOT NULL,
  `PickID` bigint(20) DEFAULT NULL,
  `BookingDetailsID` bigint(20) DEFAULT NULL,
  `ItemID` bigint(20) NOT NULL,
  `ItemDetailsID` bigint(20) DEFAULT NULL,
  `BranchID` int(11) DEFAULT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `Discount` double DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_purchase`
--

CREATE TABLE `transaction_purchase` (
  `PurchaseID` bigint(20) NOT NULL,
  `PurchaseNumber` varchar(100) DEFAULT NULL,
  `SupplierID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `PaymentTypeID` smallint(6) DEFAULT NULL,
  `Deadline` datetime DEFAULT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction_purchase`
--

INSERT INTO `transaction_purchase` (`PurchaseID`, `PurchaseNumber`, `SupplierID`, `TransactionDate`, `PaymentTypeID`, `Deadline`, `Remarks`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(2, 'MBL.SI18E-0874 SUG', 4, '2018-06-11 00:00:00', 1, NULL, NULL, '2018-06-10 20:54:23', 'Admin1', '2018-06-11 16:21:29', 'Admin1'),
(4, 'MBL.SI18E-0335 SUG', 4, '2018-06-11 00:00:00', 1, NULL, NULL, '2018-06-10 23:06:46', 'Admin1', '2018-06-11 16:20:57', 'Admin1'),
(5, '00456', 3, '2018-03-24 00:00:00', 1, NULL, NULL, '2018-06-11 02:10:53', 'Admin1', '2018-07-12 13:13:34', 'Admin1'),
(7, '005075', 3, '2018-06-04 00:00:00', 1, NULL, NULL, '2018-06-12 02:12:59', 'Admin1', '2018-07-12 13:13:57', 'Admin1'),
(10, '004772', 3, '2018-05-02 00:00:00', 1, NULL, NULL, '2018-07-03 00:51:41', 'Admin1', '2018-07-03 14:52:43', 'Admin1'),
(11, '005271', 3, '2018-07-03 00:00:00', 1, NULL, NULL, '2018-07-03 02:39:27', 'Admin1', '2018-07-04 14:07:08', 'Admin1'),
(12, '25618', 7, '2018-06-25 00:00:00', 1, NULL, NULL, '2018-07-04 00:08:33', 'Admin1', '2018-07-04 14:11:17', 'Admin1'),
(14, '4003584874', 9, '2018-06-13 00:00:00', 1, NULL, NULL, '2018-07-06 21:00:03', 'Admin1', '2018-07-07 11:01:27', 'Admin1'),
(18, '12180600746', 8, '2018-07-07 00:00:00', 1, NULL, NULL, '2018-07-07 02:06:49', 'Admin1', '2018-07-07 16:07:33', 'Admin1'),
(19, '11180601343', 8, '2018-07-07 00:00:00', 1, NULL, NULL, '2018-07-07 02:08:18', 'Admin1', NULL, NULL),
(20, '13180600617', 8, '2018-07-07 00:00:00', 1, NULL, NULL, '2018-07-07 02:09:48', 'Admin1', NULL, NULL),
(21, '4003632480', 9, '2018-07-07 00:00:00', 1, NULL, NULL, '2018-07-07 02:14:23', 'Admin1', NULL, NULL),
(22, '4003632500', 9, '2018-07-07 00:00:00', 1, NULL, NULL, '2018-07-07 02:18:00', 'Admin1', '2018-07-07 16:18:42', 'Admin1'),
(24, '0144074', 5, '2018-07-07 00:00:00', 1, NULL, NULL, '2018-07-07 02:27:22', 'Admin1', NULL, NULL),
(25, '0144075', 5, '2018-07-07 00:00:00', 1, NULL, NULL, '2018-07-07 02:28:08', 'Admin1', '2018-07-07 16:28:18', 'Admin1'),
(26, '001408', 2, '2018-07-09 00:00:00', 1, NULL, NULL, '2018-07-09 02:32:06', 'Admin1', '2018-07-09 16:32:18', 'Admin1'),
(27, '001409', 2, '2018-07-09 00:00:00', 1, NULL, NULL, '2018-07-09 02:36:30', 'Admin1', '2018-07-09 16:36:40', 'Admin1'),
(28, '01358318', 6, '2018-06-11 00:00:00', 1, NULL, NULL, '2018-07-11 00:46:37', 'Admin1', '2018-07-12 13:13:03', 'Admin1'),
(29, '1812481', 10, '2018-07-20 00:00:00', 1, NULL, NULL, '2018-07-20 23:03:51', 'Admin1', '2018-07-21 13:06:47', 'Admin1'),
(30, '005520', 3, '2018-07-27 00:00:00', 1, NULL, NULL, '2018-07-27 20:34:33', 'Admin1', NULL, NULL),
(31, 'MBL.SI18G-0680', 4, '2018-07-27 00:00:00', 1, NULL, NULL, '2018-07-27 20:37:27', 'Admin1', '2018-07-28 10:38:15', 'Admin1'),
(32, 'B18072400003', 11, '2018-07-27 00:00:00', 1, NULL, NULL, '2018-07-27 20:45:11', 'Admin1', '2018-07-28 10:46:29', 'Admin1'),
(33, 'SJ-1814729', 10, '2018-07-27 00:00:00', 1, NULL, NULL, '2018-07-27 20:56:50', 'Admin1', NULL, NULL),
(34, 'SJ-1814730', 10, '2018-07-27 00:00:00', 1, NULL, NULL, '2018-07-27 20:59:24', 'Admin1', NULL, NULL),
(35, '18072500008', 11, '2018-07-28 00:00:00', 1, NULL, NULL, '2018-07-27 23:49:44', 'Admin1', '2018-07-28 16:14:01', 'Admin1'),
(36, 'FJ1807-005327-F', 5, '2018-08-03 00:00:00', 1, NULL, NULL, '2018-08-03 23:31:38', 'Admin1', '2018-08-04 14:47:21', 'Admin1'),
(37, 'VK-1806544', 12, '2018-08-10 00:00:00', 1, NULL, NULL, '2018-08-10 20:21:05', 'Admin1', '2018-08-11 10:23:07', 'Admin1'),
(38, 'ajm19918', 7, '2018-09-19 00:00:00', 1, '2018-09-19 00:00:00', NULL, '2018-09-19 07:36:25', 'Admin1', '2018-09-19 14:38:31', 'Admin1');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_purchasedetails`
--

CREATE TABLE `transaction_purchasedetails` (
  `PurchaseDetailsID` bigint(20) NOT NULL,
  `PurchaseID` bigint(20) DEFAULT NULL,
  `ItemID` bigint(20) NOT NULL,
  `ItemDetailsID` bigint(20) DEFAULT NULL,
  `BranchID` int(11) DEFAULT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `RetailPrice` double DEFAULT NULL,
  `Price1` double DEFAULT NULL,
  `Price2` double DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction_purchasedetails`
--

INSERT INTO `transaction_purchasedetails` (`PurchaseDetailsID`, `PurchaseID`, `ItemID`, `ItemDetailsID`, `BranchID`, `Quantity`, `BuyPrice`, `RetailPrice`, `Price1`, `Price2`, `CreatedDate`, `CreatedBy`, `ModifiedDate`, `ModifiedBy`) VALUES
(25, 2, 25, NULL, 1, 2, 98000, 142000, 0, 0, '2018-06-10 20:54:23', 'Admin1', '2018-06-11 10:54:45', 'Admin1'),
(26, 2, 26, NULL, 1, 2, 98000, 142000, 0, 0, '2018-06-10 20:55:53', 'Admin1', NULL, NULL),
(27, 2, 27, NULL, 1, 5, 22500, 32000, 0, 0, '2018-06-10 20:56:53', 'Admin1', NULL, NULL),
(28, 2, 28, NULL, 1, 2, 98000, 142000, 0, 0, '2018-06-10 20:58:00', 'Admin1', NULL, NULL),
(32, 4, 32, NULL, 1, 3, 98000, 142000, 0, 0, '2018-06-10 23:06:46', 'Admin1', NULL, NULL),
(33, 4, 33, NULL, 1, 3, 98000, 142000, 0, 0, '2018-06-10 23:07:28', 'Admin1', NULL, NULL),
(34, 4, 34, NULL, 1, 3, 68500, 100000, 0, 0, '2018-06-10 23:08:12', 'Admin1', NULL, NULL),
(35, 4, 35, NULL, 1, 3, 29500, 43000, 0, 0, '2018-06-10 23:09:00', 'Admin1', NULL, NULL),
(36, 4, 36, NULL, 1, 2, 300000, 435000, 0, 0, '2018-06-10 23:09:49', 'Admin1', NULL, NULL),
(37, 5, 37, NULL, 1, 12, 29200, 34000, 0, 0, '2018-06-11 02:10:53', 'Admin1', NULL, NULL),
(38, 5, 38, NULL, 1, 12, 34875, 44000, 0, 0, '2018-06-11 02:11:32', 'Admin1', NULL, NULL),
(39, 5, 39, NULL, 1, 12, 30000, 35000, 0, 0, '2018-06-11 02:12:15', 'Admin1', NULL, NULL),
(43, 7, 40, NULL, 1, 6, 37200, 44000, 0, 0, '2018-06-12 02:12:59', 'Admin1', NULL, NULL),
(54, 10, 47, NULL, 1, 12, 38600, 46000, 0, 0, '2018-07-03 00:51:41', 'Admin1', NULL, NULL),
(55, 10, 48, NULL, 1, 12, 30500, 36000, 0, 0, '2018-07-03 00:52:43', 'Admin1', NULL, NULL),
(56, 11, 50, NULL, 1, 6, 46500, 54000, 0, 0, '2018-07-03 02:39:27', 'Admin1', NULL, NULL),
(57, 11, 51, NULL, 1, 12, 33800, 38000, 0, 0, '2018-07-03 02:40:05', 'Admin1', NULL, NULL),
(58, 12, 49, NULL, 1, 5, 16000, 22500, 0, 0, '2018-07-04 00:08:33', 'Admin1', NULL, NULL),
(59, 12, 52, NULL, 1, 3, 14150, 20000, 0, 0, '2018-07-04 00:11:17', 'Admin1', NULL, NULL),
(60, 12, 53, NULL, 1, 5, 15750, 22000, 0, 0, '2018-07-04 00:12:11', 'Admin1', NULL, NULL),
(61, 12, 54, NULL, 1, 2, 34970, 50000, 0, 0, '2018-07-04 00:12:57', 'Admin1', NULL, NULL),
(62, 12, 55, NULL, 1, 2, 58250, 82000, 0, 0, '2018-07-04 00:13:57', 'Admin1', NULL, NULL),
(63, 12, 56, NULL, 1, 5, 36650, 52000, 0, 0, '2018-07-04 00:15:02', 'Admin1', NULL, NULL),
(64, 12, 57, NULL, 1, 2, 15650, 22000, 0, 0, '2018-07-04 00:16:06', 'Admin1', NULL, NULL),
(65, 12, 58, NULL, 1, 1, 89000, 125000, 0, 0, '2018-07-04 00:16:54', 'Admin1', NULL, NULL),
(69, 14, 62, NULL, 1, 2, 48600, 70000, 0, 0, '2018-07-06 21:00:03', 'Admin1', NULL, NULL),
(70, 14, 63, NULL, 1, 2, 49410, 70000, 0, 0, '2018-07-06 21:01:28', 'Admin1', NULL, NULL),
(71, 14, 64, NULL, 1, 20, 9720, 15000, 0, 0, '2018-07-06 21:02:38', 'Admin1', NULL, NULL),
(79, 18, 59, NULL, 1, 2, 162750, 187000, 0, 0, '2018-07-07 02:06:49', 'Admin1', NULL, NULL),
(80, 18, 60, NULL, 1, 3, 131625, 148000, 0, 0, '2018-07-07 02:06:59', 'Admin1', NULL, NULL),
(81, 18, 61, NULL, 1, 2, 141750, 163000, 0, 0, '2018-07-07 02:07:08', 'Admin1', NULL, NULL),
(82, 19, 65, NULL, 1, 5, 37200, 55000, 0, 0, '2018-07-07 02:08:18', 'Admin1', NULL, NULL),
(83, 20, 66, NULL, 1, 96, 11700, 17000, 0, 0, '2018-07-07 02:09:48', 'Admin1', NULL, NULL),
(84, 21, 67, NULL, 1, 5, 26827, 39000, 0, 0, '2018-07-07 02:14:23', 'Admin1', NULL, NULL),
(85, 22, 68, NULL, 1, 10, 22680, 33000, 0, 0, '2018-07-07 02:18:00', 'Admin1', NULL, NULL),
(86, 22, 69, NULL, 1, 5, 19375, 28000, 0, 0, '2018-07-07 02:18:42', 'Admin1', NULL, NULL),
(88, 24, 29, NULL, 1, 3, 134680, 150000, 0, 0, '2018-07-07 02:27:22', 'Admin1', NULL, NULL),
(89, 25, 30, NULL, 1, 5, 22320, 27000, 0, 0, '2018-07-07 02:28:08', 'Admin1', NULL, NULL),
(90, 25, 31, NULL, 1, 5, 25200, 30000, 0, 0, '2018-07-07 02:28:18', 'Admin1', NULL, NULL),
(91, 26, 1, NULL, 1, 1, 24000, 36000, 0, 0, '2018-07-09 02:32:06', 'Admin1', NULL, NULL),
(92, 26, 2, NULL, 1, 1, 24000, 36000, 0, 0, '2018-07-09 02:32:18', 'Admin1', NULL, NULL),
(93, 26, 3, NULL, 1, 1, 24000, 36000, 0, 0, '2018-07-09 02:32:27', 'Admin1', NULL, NULL),
(94, 26, 4, NULL, 1, 3, 10000, 16000, 0, 0, '2018-07-09 02:32:36', 'Admin1', NULL, NULL),
(95, 26, 5, NULL, 1, 3, 10000, 16000, 0, 0, '2018-07-09 02:32:49', 'Admin1', NULL, NULL),
(96, 26, 6, NULL, 1, 9, 12000, 20000, 0, 0, '2018-07-09 02:32:58', 'Admin1', NULL, NULL),
(97, 26, 7, NULL, 1, 1, 47500, 70000, 0, 0, '2018-07-09 02:33:14', 'Admin1', NULL, NULL),
(98, 26, 8, NULL, 1, 4, 14500, 22000, 0, 0, '2018-07-09 02:33:24', 'Admin1', NULL, NULL),
(99, 26, 9, NULL, 1, 1, 17000, 26000, 0, 0, '2018-07-09 02:33:55', 'Admin1', NULL, NULL),
(100, 26, 10, NULL, 1, 3, 14500, 22000, 0, 0, '2018-07-09 02:34:06', 'Admin1', NULL, NULL),
(101, 26, 11, NULL, 1, 3, 14000, 22000, 0, 0, '2018-07-09 02:34:17', 'Admin1', NULL, NULL),
(102, 26, 12, NULL, 1, 2, 15000, 23000, 0, 0, '2018-07-09 02:34:29', 'Admin1', NULL, NULL),
(103, 26, 13, NULL, 1, 3, 16500, 25000, 0, 0, '2018-07-09 02:34:46', 'Admin1', NULL, NULL),
(104, 26, 14, NULL, 1, 4, 21000, 32000, 0, 0, '2018-07-09 02:34:55', 'Admin1', NULL, NULL),
(105, 26, 15, NULL, 1, 10, 8000, 14000, 0, 0, '2018-07-09 02:35:04', 'Admin1', NULL, NULL),
(106, 26, 16, NULL, 1, 3, 7000, 12500, 0, 0, '2018-07-09 02:35:20', 'Admin1', NULL, NULL),
(107, 27, 17, NULL, 1, 20, 10000, 15000, 0, 0, '2018-07-09 02:36:30', 'Admin1', NULL, NULL),
(108, 27, 18, NULL, 1, 5, 27000, 40000, 0, 0, '2018-07-09 02:36:40', 'Admin1', NULL, NULL),
(109, 27, 19, NULL, 1, 5, 10000, 16000, 0, 0, '2018-07-09 02:36:48', 'Admin1', NULL, NULL),
(110, 27, 20, NULL, 1, 6, 12500, 20000, 0, 0, '2018-07-09 02:36:57', 'Admin1', NULL, NULL),
(111, 27, 21, NULL, 1, 10, 6000, 10000, 0, 0, '2018-07-09 02:37:06', 'Admin1', NULL, NULL),
(112, 27, 22, NULL, 1, 10, 8500, 15000, 0, 0, '2018-07-09 02:37:19', 'Admin1', NULL, NULL),
(113, 27, 23, NULL, 1, 2, 100000, 150000, 0, 0, '2018-07-09 02:37:27', 'Admin1', NULL, NULL),
(114, 27, 24, NULL, 1, 2, 90000, 135000, 0, 0, '2018-07-09 02:37:34', 'Admin1', NULL, NULL),
(115, 28, 46, NULL, 1, 5, 11025, 22000, 0, 0, '2018-07-11 00:46:37', 'Admin1', NULL, NULL),
(116, 28, 42, NULL, 1, 10, 2100, 6000, 0, 0, '2018-07-11 00:46:49', 'Admin1', NULL, NULL),
(117, 28, 43, NULL, 1, 10, 2100, 6000, 0, 0, '2018-07-11 00:47:06', 'Admin1', NULL, NULL),
(118, 28, 44, NULL, 1, 5, 16275, 25000, 0, 0, '2018-07-11 00:47:26', 'Admin1', NULL, NULL),
(119, 28, 45, NULL, 1, 3, 15225, 25000, 0, 0, '2018-07-11 00:47:38', 'Admin1', NULL, NULL),
(120, 29, 70, NULL, 1, 5, 109440, 125000, 0, 0, '2018-07-20 23:03:51', 'Admin1', NULL, NULL),
(121, 29, 71, NULL, 1, 5, 131040, 150000, 0, 0, '2018-07-20 23:06:47', 'Admin1', NULL, NULL),
(122, 30, 72, NULL, 1, 12, 39600, 46000, 0, 0, '2018-07-27 20:34:33', 'Admin1', NULL, NULL),
(123, 31, 73, NULL, 1, 5, 85000, 125000, 0, 0, '2018-07-27 20:37:27', 'Admin1', NULL, NULL),
(124, 31, 74, NULL, 1, 5, 85000, 125000, 0, 0, '2018-07-27 20:38:15', 'Admin1', NULL, NULL),
(125, 31, 75, NULL, 1, 10, 22500, 34000, 0, 0, '2018-07-27 20:39:53', 'Admin1', NULL, NULL),
(126, 32, 76, NULL, 1, 5, 30600, 45000, 0, 0, '2018-07-27 20:45:11', 'Admin1', NULL, NULL),
(127, 32, 77, NULL, 1, 3, 45280, 66000, 0, 0, '2018-07-27 20:46:29', 'Admin1', '2018-07-28 10:47:03', 'Admin1'),
(128, 33, 78, NULL, 1, 5, 136800, 155000, 0, 0, '2018-07-27 20:56:50', 'Admin1', NULL, NULL),
(129, 34, 79, NULL, 1, 5, 155940, 175000, 0, 0, '2018-07-27 20:59:25', 'Admin1', NULL, NULL),
(130, 35, 80, NULL, 1, 2, 30800, 45000, 0, 0, '2018-07-27 23:49:44', 'Admin1', NULL, NULL),
(131, 35, 81, NULL, 1, 2, 69200, 100000, 0, 0, '2018-07-28 02:14:01', 'Admin1', NULL, NULL),
(132, 36, 82, NULL, 1, 3, 91575, 105000, 0, 0, '2018-08-03 23:31:38', 'Admin1', '2018-08-04 14:47:21', 'Admin1'),
(133, 37, 83, NULL, 1, 3, 106700, 158000, 0, 0, '2018-08-10 20:21:05', 'Admin1', '2018-08-11 10:23:08', 'Admin1'),
(134, 37, 84, NULL, 1, 3, 106700, 158000, 0, 0, '2018-08-10 21:26:38', 'Admin1', NULL, NULL),
(135, 37, 85, NULL, 1, 3, 49470, 73000, 0, 0, '2018-08-10 21:27:49', 'Admin1', '2018-08-11 11:29:53', 'Admin1'),
(136, 37, 86, NULL, 1, 2, 60140, 89000, 0, 0, '2018-08-10 21:30:59', 'Admin1', NULL, NULL),
(137, 37, 87, NULL, 1, 5, 15035, 25000, 0, 0, '2018-08-10 21:32:29', 'Admin1', NULL, NULL),
(138, 37, 88, NULL, 1, 5, 26675, 40000, 0, 0, '2018-08-10 21:33:51', 'Admin1', NULL, NULL),
(139, 38, 89, NULL, 1, 4, 15000, 22000, 22000, 22000, '2018-09-19 07:36:25', 'Admin1', NULL, NULL),
(140, 38, 90, NULL, 1, 10, 2100, 5000, 5000, 5000, '2018-09-19 07:38:31', 'Admin1', '2018-09-19 14:38:55', 'Admin1'),
(141, 38, 91, NULL, 1, 1, 24750, 36000, 36000, 36000, '2018-09-19 07:40:33', 'Admin1', NULL, NULL),
(142, 38, 92, NULL, 1, 1, 110000, 160000, 160000, 160000, '2018-09-19 07:42:16', 'Admin1', NULL, NULL),
(143, 38, 93, NULL, 1, 2, 15250, 23000, 23000, 23000, '2018-09-19 07:44:14', 'Admin1', '2018-09-19 14:44:48', 'Admin1'),
(144, 38, 94, NULL, 1, 2, 16250, 24000, 24000, 24000, '2018-09-19 07:46:31', 'Admin1', NULL, NULL),
(145, 38, 95, NULL, 1, 2, 15250, 23000, 23000, 23000, '2018-09-19 07:48:20', 'Admin1', NULL, NULL),
(146, 38, 96, NULL, 1, 3, 37750, 55000, 55000, 55000, '2018-09-19 07:49:56', 'Admin1', '2018-09-19 14:58:19', 'Admin1'),
(147, 38, 97, NULL, 1, 5, 25500, 37000, 37000, 37000, '2018-09-19 07:52:07', 'Admin1', NULL, NULL),
(148, 38, 98, NULL, 1, 4, 28000, 42000, 42000, 42000, '2018-09-19 07:53:41', 'Admin1', NULL, NULL),
(149, 38, 99, NULL, 1, 50, 675, 1500, 1500, 1500, '2018-09-19 07:55:18', 'Admin1', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_purchasereturn`
--

CREATE TABLE `transaction_purchasereturn` (
  `PurchaseReturnID` bigint(20) NOT NULL,
  `PurchaseReturnNumber` varchar(100) DEFAULT NULL,
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
-- Table structure for table `transaction_purchasereturndetails`
--

CREATE TABLE `transaction_purchasereturndetails` (
  `PurchaseReturnDetailsID` bigint(20) NOT NULL,
  `PurchaseReturnID` bigint(20) DEFAULT NULL,
  `ItemID` bigint(20) NOT NULL,
  `ItemDetailsID` bigint(20) DEFAULT NULL,
  `BranchID` int(11) DEFAULT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_sale`
--

CREATE TABLE `transaction_sale` (
  `SaleID` bigint(20) NOT NULL,
  `SaleNumber` varchar(100) DEFAULT NULL,
  `RetailFlag` bit(1) NOT NULL,
  `CustomerID` bigint(20) DEFAULT NULL,
  `TransactionDate` datetime NOT NULL,
  `PaymentTypeID` smallint(6) DEFAULT NULL,
  `Payment` double DEFAULT NULL,
  `PrintCount` smallint(6) DEFAULT NULL,
  `PrintedDate` datetime DEFAULT NULL,
  `FinishFlag` bit(1) DEFAULT NULL,
  `Remarks` text,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_saledetails`
--

CREATE TABLE `transaction_saledetails` (
  `SaleDetailsID` bigint(20) NOT NULL,
  `SaleID` bigint(20) DEFAULT NULL,
  `ItemID` bigint(20) NOT NULL,
  `ItemDetailsID` bigint(20) DEFAULT NULL,
  `BranchID` int(11) DEFAULT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `Discount` double DEFAULT NULL,
  `PrintCount` smallint(6) DEFAULT NULL,
  `PrintedDate` datetime DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_salereturn`
--

CREATE TABLE `transaction_salereturn` (
  `SaleReturnID` bigint(20) NOT NULL,
  `SaleID` bigint(20) DEFAULT NULL,
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

CREATE TABLE `transaction_salereturndetails` (
  `SaleReturnDetailsID` bigint(20) NOT NULL,
  `SaleReturnID` bigint(20) DEFAULT NULL,
  `SaleDetailsID` bigint(20) DEFAULT NULL,
  `ItemID` bigint(20) NOT NULL,
  `ItemDetailsID` bigint(20) DEFAULT NULL,
  `BranchID` int(11) DEFAULT NULL,
  `Quantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_stockadjust`
--

CREATE TABLE `transaction_stockadjust` (
  `StockAdjustID` bigint(20) NOT NULL,
  `TransactionDate` datetime DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_stockadjustdetails`
--

CREATE TABLE `transaction_stockadjustdetails` (
  `StockAdjustDetailsID` bigint(20) NOT NULL,
  `StockAdjustID` bigint(20) DEFAULT NULL,
  `ItemID` bigint(20) NOT NULL,
  `ItemDetailsID` bigint(20) DEFAULT NULL,
  `BranchID` int(11) DEFAULT NULL,
  `Quantity` double DEFAULT NULL,
  `AdjustedQuantity` double DEFAULT NULL,
  `BuyPrice` double DEFAULT NULL,
  `SalePrice` double DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_stockmutation`
--

CREATE TABLE `transaction_stockmutation` (
  `StockMutationID` bigint(20) NOT NULL,
  `TransactionDate` datetime DEFAULT NULL,
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(255) NOT NULL,
  `ModifiedDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_stockmutationdetails`
--

CREATE TABLE `transaction_stockmutationdetails` (
  `StockMutationDetailsID` bigint(20) NOT NULL,
  `StockMutationID` bigint(20) DEFAULT NULL,
  `SourceID` int(11) DEFAULT NULL,
  `DestinationID` int(11) DEFAULT NULL,
  `ItemID` bigint(20) NOT NULL,
  `ItemDetailsID` bigint(20) DEFAULT NULL,
  `Quantity` double DEFAULT NULL,
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
  ADD PRIMARY KEY (`BackupHistoryID`),
  ADD UNIQUE KEY `BACKUPHISTORY_INDEX` (`BackupHistoryID`);

--
-- Indexes for table `master_branch`
--
ALTER TABLE `master_branch`
  ADD PRIMARY KEY (`BranchID`),
  ADD UNIQUE KEY `BRANCH_INDEX` (`BranchID`);

--
-- Indexes for table `master_category`
--
ALTER TABLE `master_category`
  ADD PRIMARY KEY (`CategoryID`),
  ADD UNIQUE KEY `CATEGORY_INDEX` (`CategoryID`);

--
-- Indexes for table `master_customer`
--
ALTER TABLE `master_customer`
  ADD PRIMARY KEY (`CustomerID`),
  ADD UNIQUE KEY `CUSTOMER_INDEX` (`CustomerID`);

--
-- Indexes for table `master_eventlog`
--
ALTER TABLE `master_eventlog`
  ADD PRIMARY KEY (`EventLogID`);

--
-- Indexes for table `master_groupmenu`
--
ALTER TABLE `master_groupmenu`
  ADD PRIMARY KEY (`GroupMenuID`),
  ADD UNIQUE KEY `GROUPMENU_INDEX` (`GroupMenuID`);

--
-- Indexes for table `master_item`
--
ALTER TABLE `master_item`
  ADD PRIMARY KEY (`ItemID`),
  ADD UNIQUE KEY `ITEM_INDEX` (`ItemID`,`UnitID`,`CategoryID`),
  ADD KEY `CategoryID` (`CategoryID`),
  ADD KEY `UnitID` (`UnitID`);

--
-- Indexes for table `master_itemdetails`
--
ALTER TABLE `master_itemdetails`
  ADD PRIMARY KEY (`ItemDetailsID`),
  ADD UNIQUE KEY `ITEMDETAILS_INDEX` (`ItemDetailsID`,`ItemID`,`UnitID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `UnitID` (`UnitID`);

--
-- Indexes for table `master_menu`
--
ALTER TABLE `master_menu`
  ADD PRIMARY KEY (`MenuID`),
  ADD UNIQUE KEY `MENU_INDEX` (`MenuID`),
  ADD KEY `GroupMenuID` (`GroupMenuID`);

--
-- Indexes for table `master_parameter`
--
ALTER TABLE `master_parameter`
  ADD PRIMARY KEY (`ParameterID`);

--
-- Indexes for table `master_paymenttype`
--
ALTER TABLE `master_paymenttype`
  ADD PRIMARY KEY (`PaymentTypeID`),
  ADD UNIQUE KEY `PAYMENTTYPE_INDEX` (`PaymentTypeID`);

--
-- Indexes for table `master_role`
--
ALTER TABLE `master_role`
  ADD PRIMARY KEY (`RoleID`),
  ADD UNIQUE KEY `ROLE_INDEX` (`RoleID`,`UserID`,`MenuID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `MenuID` (`MenuID`);

--
-- Indexes for table `master_supplier`
--
ALTER TABLE `master_supplier`
  ADD PRIMARY KEY (`SupplierID`),
  ADD UNIQUE KEY `SUPPLIER_INDEX` (`SupplierID`);

--
-- Indexes for table `master_unit`
--
ALTER TABLE `master_unit`
  ADD PRIMARY KEY (`UnitID`),
  ADD UNIQUE KEY `UNIT_INDEX` (`UnitID`);

--
-- Indexes for table `master_user`
--
ALTER TABLE `master_user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `USER_INDEX` (`UserID`),
  ADD KEY `UserTypeID` (`UserTypeID`);

--
-- Indexes for table `master_usertype`
--
ALTER TABLE `master_usertype`
  ADD PRIMARY KEY (`UserTypeID`);

--
-- Indexes for table `reset_history`
--
ALTER TABLE `reset_history`
  ADD PRIMARY KEY (`ResetHistoryID`),
  ADD UNIQUE KEY `RESETHISTORY_INDEX` (`ResetHistoryID`);

--
-- Indexes for table `restore_history`
--
ALTER TABLE `restore_history`
  ADD PRIMARY KEY (`RestoreHistoryID`),
  ADD UNIQUE KEY `RESTOREHISTORY_INDEX` (`RestoreHistoryID`);

--
-- Indexes for table `transaction_booking`
--
ALTER TABLE `transaction_booking`
  ADD PRIMARY KEY (`BookingID`),
  ADD UNIQUE KEY `SALE_INDEX` (`BookingID`,`CustomerID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `PaymentTypeID` (`PaymentTypeID`);

--
-- Indexes for table `transaction_bookingdetails`
--
ALTER TABLE `transaction_bookingdetails`
  ADD PRIMARY KEY (`BookingDetailsID`),
  ADD UNIQUE KEY `BOOKINGDETAILS_INDEX` (`BookingDetailsID`,`BookingID`,`ItemID`,`BranchID`),
  ADD KEY `BookingID` (`BookingID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `BranchID` (`BranchID`);

--
-- Indexes for table `transaction_firstbalance`
--
ALTER TABLE `transaction_firstbalance`
  ADD PRIMARY KEY (`FirstBalanceID`),
  ADD UNIQUE KEY `FIRSTBALANCE_INDEX` (`FirstBalanceID`,`UserID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `transaction_firststock`
--
ALTER TABLE `transaction_firststock`
  ADD PRIMARY KEY (`FirstStockID`),
  ADD UNIQUE KEY `FIRSTSTOCK_INDEX` (`FirstStockID`,`SupplierID`),
  ADD KEY `SupplierID` (`SupplierID`);

--
-- Indexes for table `transaction_firststockdetails`
--
ALTER TABLE `transaction_firststockdetails`
  ADD PRIMARY KEY (`FirstStockDetailsID`),
  ADD UNIQUE KEY `FIRSTSTOCKDETAILS_INDEX` (`FirstStockDetailsID`,`FirstStockID`,`ItemID`,`BranchID`),
  ADD KEY `FirstStockID` (`FirstStockID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `BranchID` (`BranchID`);

--
-- Indexes for table `transaction_paymentdetails`
--
ALTER TABLE `transaction_paymentdetails`
  ADD PRIMARY KEY (`PaymentDetailsID`),
  ADD UNIQUE KEY `PAYMENTDETAILS_INDEX` (`PaymentDetailsID`,`TransactionID`);

--
-- Indexes for table `transaction_pick`
--
ALTER TABLE `transaction_pick`
  ADD PRIMARY KEY (`PickID`),
  ADD UNIQUE KEY `PICK_INDEX` (`PickID`,`BookingID`);

--
-- Indexes for table `transaction_pickdetails`
--
ALTER TABLE `transaction_pickdetails`
  ADD PRIMARY KEY (`PickDetailsID`),
  ADD UNIQUE KEY `PICKDETAILS_INDEX` (`PickDetailsID`,`PickID`,`BookingDetailsID`,`ItemID`,`BranchID`),
  ADD KEY `PickID` (`PickID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `BranchID` (`BranchID`),
  ADD KEY `BookingDetailsID` (`BookingDetailsID`);

--
-- Indexes for table `transaction_purchase`
--
ALTER TABLE `transaction_purchase`
  ADD PRIMARY KEY (`PurchaseID`),
  ADD UNIQUE KEY `PURCHASE_INDEX` (`PurchaseID`,`SupplierID`),
  ADD KEY `SupplierID` (`SupplierID`);

--
-- Indexes for table `transaction_purchasedetails`
--
ALTER TABLE `transaction_purchasedetails`
  ADD PRIMARY KEY (`PurchaseDetailsID`),
  ADD UNIQUE KEY `PURCHASEDETAILS_INDEX` (`PurchaseDetailsID`,`PurchaseID`,`ItemID`,`BranchID`),
  ADD KEY `PurchaseID` (`PurchaseID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `BranchID` (`BranchID`);

--
-- Indexes for table `transaction_purchasereturn`
--
ALTER TABLE `transaction_purchasereturn`
  ADD PRIMARY KEY (`PurchaseReturnID`),
  ADD UNIQUE KEY `PURCHASERETURN_INDEX` (`PurchaseReturnID`,`SupplierID`);

--
-- Indexes for table `transaction_purchasereturndetails`
--
ALTER TABLE `transaction_purchasereturndetails`
  ADD PRIMARY KEY (`PurchaseReturnDetailsID`),
  ADD UNIQUE KEY `PURCHASERETURNDETAILS_INDEX` (`PurchaseReturnDetailsID`,`PurchaseReturnID`,`ItemID`,`BranchID`),
  ADD KEY `PurchaseReturnID` (`PurchaseReturnID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `BranchID` (`BranchID`);

--
-- Indexes for table `transaction_sale`
--
ALTER TABLE `transaction_sale`
  ADD PRIMARY KEY (`SaleID`),
  ADD UNIQUE KEY `SALE_INDEX` (`SaleID`,`CustomerID`,`PaymentTypeID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `PaymentTypeID` (`PaymentTypeID`);

--
-- Indexes for table `transaction_saledetails`
--
ALTER TABLE `transaction_saledetails`
  ADD PRIMARY KEY (`SaleDetailsID`),
  ADD UNIQUE KEY `SALEDETAILS_INDEX` (`SaleDetailsID`,`SaleID`,`ItemID`,`BranchID`),
  ADD KEY `SaleID` (`SaleID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `BranchID` (`BranchID`);

--
-- Indexes for table `transaction_salereturn`
--
ALTER TABLE `transaction_salereturn`
  ADD PRIMARY KEY (`SaleReturnID`),
  ADD UNIQUE KEY `SALERETURN_INDEX` (`SaleReturnID`,`SaleID`);

--
-- Indexes for table `transaction_salereturndetails`
--
ALTER TABLE `transaction_salereturndetails`
  ADD PRIMARY KEY (`SaleReturnDetailsID`),
  ADD UNIQUE KEY `SALERETURNDETAILS_INDEX` (`SaleReturnDetailsID`,`SaleReturnID`,`SaleDetailsID`,`ItemID`,`BranchID`),
  ADD KEY `SaleReturnID` (`SaleReturnID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `BranchID` (`BranchID`),
  ADD KEY `SaleDetailsID` (`SaleDetailsID`);

--
-- Indexes for table `transaction_stockadjust`
--
ALTER TABLE `transaction_stockadjust`
  ADD PRIMARY KEY (`StockAdjustID`),
  ADD UNIQUE KEY `STOCKADJUST_INDEX` (`StockAdjustID`);

--
-- Indexes for table `transaction_stockadjustdetails`
--
ALTER TABLE `transaction_stockadjustdetails`
  ADD PRIMARY KEY (`StockAdjustDetailsID`),
  ADD UNIQUE KEY `STOCKADJUSTDETAILS_INDEX` (`StockAdjustDetailsID`,`StockAdjustID`,`ItemID`,`BranchID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `StockAdjustID` (`StockAdjustID`),
  ADD KEY `BranchID` (`BranchID`);

--
-- Indexes for table `transaction_stockmutation`
--
ALTER TABLE `transaction_stockmutation`
  ADD PRIMARY KEY (`StockMutationID`),
  ADD UNIQUE KEY `STOCKMUTATION_INDEX` (`StockMutationID`);

--
-- Indexes for table `transaction_stockmutationdetails`
--
ALTER TABLE `transaction_stockmutationdetails`
  ADD PRIMARY KEY (`StockMutationDetailsID`),
  ADD UNIQUE KEY `STOCKMUTATIONDETAILS_INDEX` (`StockMutationDetailsID`,`StockMutationID`,`ItemID`,`SourceID`,`DestinationID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `SourceID` (`SourceID`),
  ADD KEY `DestinationID` (`DestinationID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `backup_history`
--
ALTER TABLE `backup_history`
  MODIFY `BackupHistoryID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_branch`
--
ALTER TABLE `master_branch`
  MODIFY `BranchID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `master_category`
--
ALTER TABLE `master_category`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `master_customer`
--
ALTER TABLE `master_customer`
  MODIFY `CustomerID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_eventlog`
--
ALTER TABLE `master_eventlog`
  MODIFY `EventLogID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `master_item`
--
ALTER TABLE `master_item`
  MODIFY `ItemID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `master_itemdetails`
--
ALTER TABLE `master_itemdetails`
  MODIFY `ItemDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_parameter`
--
ALTER TABLE `master_parameter`
  MODIFY `ParameterID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `master_paymenttype`
--
ALTER TABLE `master_paymenttype`
  MODIFY `PaymentTypeID` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `master_role`
--
ALTER TABLE `master_role`
  MODIFY `RoleID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `master_supplier`
--
ALTER TABLE `master_supplier`
  MODIFY `SupplierID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `master_unit`
--
ALTER TABLE `master_unit`
  MODIFY `UnitID` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `master_user`
--
ALTER TABLE `master_user`
  MODIFY `UserID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reset_history`
--
ALTER TABLE `reset_history`
  MODIFY `ResetHistoryID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restore_history`
--
ALTER TABLE `restore_history`
  MODIFY `RestoreHistoryID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_booking`
--
ALTER TABLE `transaction_booking`
  MODIFY `BookingID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_bookingdetails`
--
ALTER TABLE `transaction_bookingdetails`
  MODIFY `BookingDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_firstbalance`
--
ALTER TABLE `transaction_firstbalance`
  MODIFY `FirstBalanceID` bigint(20) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `transaction_paymentdetails`
--
ALTER TABLE `transaction_paymentdetails`
  MODIFY `PaymentDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_pick`
--
ALTER TABLE `transaction_pick`
  MODIFY `PickID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_pickdetails`
--
ALTER TABLE `transaction_pickdetails`
  MODIFY `PickDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_purchase`
--
ALTER TABLE `transaction_purchase`
  MODIFY `PurchaseID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `transaction_purchasedetails`
--
ALTER TABLE `transaction_purchasedetails`
  MODIFY `PurchaseDetailsID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `transaction_purchasereturn`
--
ALTER TABLE `transaction_purchasereturn`
  MODIFY `PurchaseReturnID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_purchasereturndetails`
--
ALTER TABLE `transaction_purchasereturndetails`
  MODIFY `PurchaseReturnDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_sale`
--
ALTER TABLE `transaction_sale`
  MODIFY `SaleID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_saledetails`
--
ALTER TABLE `transaction_saledetails`
  MODIFY `SaleDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `transaction_stockadjust`
--
ALTER TABLE `transaction_stockadjust`
  MODIFY `StockAdjustID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_stockadjustdetails`
--
ALTER TABLE `transaction_stockadjustdetails`
  MODIFY `StockAdjustDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_stockmutation`
--
ALTER TABLE `transaction_stockmutation`
  MODIFY `StockMutationID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_stockmutationdetails`
--
ALTER TABLE `transaction_stockmutationdetails`
  MODIFY `StockMutationDetailsID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `master_item`
--
ALTER TABLE `master_item`
  ADD CONSTRAINT `master_item_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `master_category` (`CategoryID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `master_item_ibfk_2` FOREIGN KEY (`UnitID`) REFERENCES `master_unit` (`UnitID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `master_itemdetails`
--
ALTER TABLE `master_itemdetails`
  ADD CONSTRAINT `master_itemdetails_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `master_item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `master_itemdetails_ibfk_2` FOREIGN KEY (`UnitID`) REFERENCES `master_unit` (`UnitID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `master_user`
--
ALTER TABLE `master_user`
  ADD CONSTRAINT `master_user_ibfk_1` FOREIGN KEY (`UserTypeID`) REFERENCES `master_usertype` (`UserTypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_booking`
--
ALTER TABLE `transaction_booking`
  ADD CONSTRAINT `transaction_booking_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `master_customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_booking_ibfk_2` FOREIGN KEY (`PaymentTypeID`) REFERENCES `master_paymenttype` (`PaymentTypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_bookingdetails`
--
ALTER TABLE `transaction_bookingdetails`
  ADD CONSTRAINT `transaction_bookingdetails_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `transaction_booking` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_bookingdetails_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `master_item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_bookingdetails_ibfk_3` FOREIGN KEY (`BranchID`) REFERENCES `master_branch` (`BranchID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_firstbalance`
--
ALTER TABLE `transaction_firstbalance`
  ADD CONSTRAINT `transaction_firstbalance_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `master_user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_firststock`
--
ALTER TABLE `transaction_firststock`
  ADD CONSTRAINT `transaction_firststock_ibfk_1` FOREIGN KEY (`SupplierID`) REFERENCES `master_supplier` (`SupplierID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_firststockdetails`
--
ALTER TABLE `transaction_firststockdetails`
  ADD CONSTRAINT `transaction_firststockdetails_ibfk_1` FOREIGN KEY (`FirstStockID`) REFERENCES `transaction_firststock` (`FirstStockID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_firststockdetails_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `master_item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_firststockdetails_ibfk_3` FOREIGN KEY (`BranchID`) REFERENCES `master_branch` (`BranchID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_pickdetails`
--
ALTER TABLE `transaction_pickdetails`
  ADD CONSTRAINT `transaction_pickdetails_ibfk_1` FOREIGN KEY (`PickID`) REFERENCES `transaction_pick` (`PickID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_pickdetails_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `master_item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_pickdetails_ibfk_3` FOREIGN KEY (`BranchID`) REFERENCES `master_branch` (`BranchID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_pickdetails_ibfk_4` FOREIGN KEY (`BookingDetailsID`) REFERENCES `transaction_bookingdetails` (`BookingDetailsID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_purchase`
--
ALTER TABLE `transaction_purchase`
  ADD CONSTRAINT `transaction_purchase_ibfk_1` FOREIGN KEY (`SupplierID`) REFERENCES `master_supplier` (`SupplierID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_purchasedetails`
--
ALTER TABLE `transaction_purchasedetails`
  ADD CONSTRAINT `transaction_purchasedetails_ibfk_1` FOREIGN KEY (`PurchaseID`) REFERENCES `transaction_purchase` (`PurchaseID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_purchasedetails_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `master_item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_purchasedetails_ibfk_3` FOREIGN KEY (`BranchID`) REFERENCES `master_branch` (`BranchID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_purchasereturndetails`
--
ALTER TABLE `transaction_purchasereturndetails`
  ADD CONSTRAINT `transaction_purchasereturndetails_ibfk_1` FOREIGN KEY (`PurchaseReturnID`) REFERENCES `transaction_purchasereturn` (`PurchaseReturnID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_purchasereturndetails_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `master_item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_purchasereturndetails_ibfk_3` FOREIGN KEY (`BranchID`) REFERENCES `master_branch` (`BranchID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_sale`
--
ALTER TABLE `transaction_sale`
  ADD CONSTRAINT `transaction_sale_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `master_customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_sale_ibfk_2` FOREIGN KEY (`PaymentTypeID`) REFERENCES `master_paymenttype` (`PaymentTypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_saledetails`
--
ALTER TABLE `transaction_saledetails`
  ADD CONSTRAINT `transaction_saledetails_ibfk_1` FOREIGN KEY (`SaleID`) REFERENCES `transaction_sale` (`SaleID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_saledetails_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `master_item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_saledetails_ibfk_3` FOREIGN KEY (`BranchID`) REFERENCES `master_branch` (`BranchID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_salereturndetails`
--
ALTER TABLE `transaction_salereturndetails`
  ADD CONSTRAINT `transaction_salereturndetails_ibfk_1` FOREIGN KEY (`SaleReturnID`) REFERENCES `transaction_salereturn` (`SaleReturnID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_salereturndetails_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `master_item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_salereturndetails_ibfk_3` FOREIGN KEY (`BranchID`) REFERENCES `master_branch` (`BranchID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_salereturndetails_ibfk_4` FOREIGN KEY (`SaleDetailsID`) REFERENCES `transaction_saledetails` (`SaleDetailsID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_stockadjustdetails`
--
ALTER TABLE `transaction_stockadjustdetails`
  ADD CONSTRAINT `transaction_stockadjustdetails_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `master_item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_stockadjustdetails_ibfk_2` FOREIGN KEY (`StockAdjustID`) REFERENCES `transaction_stockadjust` (`StockAdjustID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_stockadjustdetails_ibfk_3` FOREIGN KEY (`BranchID`) REFERENCES `master_branch` (`BranchID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_stockmutationdetails`
--
ALTER TABLE `transaction_stockmutationdetails`
  ADD CONSTRAINT `transaction_stockmutationdetails_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `master_item` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_stockmutationdetails_ibfk_2` FOREIGN KEY (`SourceID`) REFERENCES `master_branch` (`BranchID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_stockmutationdetails_ibfk_3` FOREIGN KEY (`DestinationID`) REFERENCES `master_branch` (`BranchID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
