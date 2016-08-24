DROP PROCEDURE IF EXISTS spInsFirstStock;

DELIMITER $$
CREATE PROCEDURE spInsFirstStock (
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;
	DECLARE CurrentDate  DATETIME;
	DECLARE pFirstStockID BIGINT;
	DECLARE PassValidate INT;
	
	SET CurrentDate = NOW();
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
		INSERT INTO transaction_firststock
		(
			FirstStockNumber,
			TransactionDate,
			Remarks,
			CreatedDate,
			CreatedBy
		)
		SELECT
			CONCAT('SA', DATE_FORMAT(NOW(), '%Y%m%d'), RIGHT(CONCAT('0000', COUNT(1) + 1), 4)),
			CurrentDate,
			'Reset',
			NOW(),
			'Admin'
		FROM 
			transaction_invoicenumber
		WHERE
			InvoiceDate = DATE_FORMAT(NOW(), '%Y-%m-%d')
			AND InvoiceNumberType = 'SA';
			
SET @State = 2;
		SET pFirstStockID = (SELECT MAX(FirstStockID) FROM transaction_firststock);

SET State = 3;			               
		INSERT INTO transaction_firststockdetails
		(
			FirstStockID,
			TypeID,
			Quantity,
			BuyPrice,
			SalePrice,
			Discount,
			BatchNumber,
			CreatedDate,
			CreatedBy
		)
		SELECT
			pFirstStockID,
			MT.TypeID,
			(IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(BO.Quantity, 0) + IFNULL(SO.Quantity, 0)),
			IFNULL(FS.BuyPrice, MT.BuyPrice) BuyPrice,
			IFNULL(FS.SalePrice, MT.SalePrice) SalePrice,
			FS.Discount,
			FS.BatchNumber,
			NOW(),
			pCurrentUser
		FROM
			master_type MT
			JOIN master_brand MB
				ON MB.BrandID = MT.BrandID
			JOIN master_unit MU
				ON MU.UnitID = MT.UnitID
			LEFT JOIN
			(
				SELECT
					TypeID,
					TRIM(BatchNumber) BatchNumber,
					SUM(SA.Quantity) Quantity,
					BuyPrice,
					SalePrice,
					Discount
				FROM
				(
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(Quantity) Quantity,
						BuyPrice,
						SalePrice,
						Discount
					FROM
						transaction_firststockdetails
					GROUP BY
						TypeID,
						BatchNumber
					UNION ALL
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(Quantity) Quantity,
						BuyPrice,
						SalePrice,
						Discount
					FROM
						transaction_incomingdetails
					WHERE
						IsCancelled = 0
					GROUP BY
						TypeID,
						BatchNumber
				)SA
				GROUP BY
					TypeID,
					BatchNumber,
					BuyPrice,
					SalePrice,
					Discount
			)FS
				ON FS.TypeID = MT.TypeID
			LEFT JOIN
			(
				SELECT
					OTD.TypeID,
					TRIM(OTD.BatchNumber) BatchNumber,
					SUM(OTD.Quantity) Quantity
				FROM
					transaction_outgoingdetails OTD
					JOIN transaction_outgoing OT
						ON OT.OutgoingID = OTD.OutgoingID
				WHERE
					OT.IsCancelled = 0
				GROUP BY
					OTD.TypeID,
					OTD.BatchNumber
			)TOD
				ON TOD.TypeID = MT.TypeID
				AND TOD.BatchNumber = FS.BatchNumber
			LEFT JOIN
			(
				SELECT
					TypeID,
					TRIM(BatchNumber) BatchNumber,
					SUM(Quantity) Quantity
				FROM
					transaction_buyreturndetails
				WHERE
					IsCancelled = 0
				GROUP BY
					TypeID,
					BatchNumber
			)BR
				ON BR.TypeID = MT.TypeID
				AND BR.BatchNumber = FS.BatchNumber
			LEFT JOIN
			(
				SELECT
					TypeID,
					TRIM(BatchNumber) BatchNumber,
					SUM(Quantity) Quantity
				FROM
					transaction_salereturndetails
				WHERE
					IsCancelled = 0
				GROUP BY
					TypeID,
					BatchNumber
			)SR
				ON SR.TypeID = MT.TypeID
				AND SR.BatchNumber = FS.BatchNumber
			LEFT JOIN
			(
				SELECT
					BOD.TypeID,
					TRIM(BOD.BatchNumber) BatchNumber,
					SUM(BOD.Quantity) Quantity
				FROM
					transaction_booking BO
					JOIN transaction_bookingdetails BOD
						ON BO.BookingID = BOD.BookingID
				WHERE
					BO.BookingStatusID = 1
				GROUP BY
					BOD.TypeID,
					BOD.BatchNumber
			)BO
				ON BO.TypeID = MT.TypeID
				AND BO.BatchNumber = FS.BatchNumber
			LEFT JOIN
			(
				SELECT
					SOD.TypeID,
					TRIM(SOD.BatchNumber) BatchNumber,
					SUM(
							CASE
								WHEN SOD.FromQty > SOD.ToQty
								THEN -(SOD.FromQty - SOD.ToQty)
								ELSE (SOD.ToQty - SOD.FromQty)
							END
						) Quantity
				FROM
					transaction_stockopname SO
					JOIN transaction_stockopnamedetails SOD
						ON SO.StockOpnameID = SOD.StockOpnameID
				GROUP BY
					SOD.TypeID,
					SOD.BatchNumber
			)SO
				ON SO.TypeID = MT.TypeID
				AND SO.BatchNumber = FS.BatchNumber
		WHERE
			(IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(BO.Quantity, 0) + IFNULL(SO.Quantity, 0)) > 0;
			
SET State = 4;
		DELETE FROM transaction_firststock WHERE TransactionDate < CurrentDate;
		
SET State = 5;
		DELETE FROM transaction_incoming WHERE TransactionDate < CurrentDate;

SET State = 6;
		DELETE FROM transaction_buyreturn WHERE TransactionDate < CurrentDate;

SET State = 7;		
		DELETE FROM transaction_salereturn WHERE TransactionDate < CurrentDate;
		
SET State = 8;
		DELETE FROM transaction_outgoing WHERE TransactionDate < CurrentDate;
		
SET State = 9;
		DELETE FROM transaction_booking WHERE TransactionDate < CurrentDate;
		
SET State = 10;
		DELETE FROM transaction_stockopname WHERE TransactionDate < CurrentDate;
		
SET State = 9;
		SELECT
			0 AS 'ID',
			'Reset Berhasil' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State';
	COMMIT;
END;
$$
DELIMITER ;
