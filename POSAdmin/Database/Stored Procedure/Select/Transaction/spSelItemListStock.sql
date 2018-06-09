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
						ROUND((IFNULL(FS.Toko, 0) + IFNULL(TP.Toko, 0) + IFNULL(SR.Toko, 0) - IFNULL(S.Toko, 0) - IFNULL(PR.Toko, 0) + IFNULL(SM.Toko, 0) - IFNULL(SMM.Toko, 0) + IFNULL(SA.Toko, 0) - IFNULL(B.Toko, 0)), 2) Toko,
						ROUND((IFNULL(FS.Gudang, 0) + IFNULL(TP.Gudang, 0) + IFNULL(SR.Gudang, 0) - IFNULL(S.Gudang, 0) - IFNULL(PR.Gudang, 0) + IFNULL(SM.Gudang, 0) - IFNULL(SMM.Gudang, 0) + IFNULL(SA.Gudang, 0) - IFNULL(B.Gudang, 0)), 2) Gudang,
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
											THEN BD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN BD.BranchID = 2
											THEN BD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_bookingdetails BD
								/*JOIN transaction_booking TB
									ON TB.BookingID = BD.BookingID*/
								LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
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
                        ROUND((IFNULL(FS.Toko, 0) + IFNULL(TP.Toko, 0) + IFNULL(SR.Toko, 0) - IFNULL(S.Toko, 0) - IFNULL(PR.Toko, 0) + IFNULL(SM.Toko, 0) - IFNULL(SMM.Toko, 0) + IFNULL(SA.Toko, 0) - IFNULL(B.Toko, 0))  / MID.ConversionQuantity, 2) Toko,
						ROUND((IFNULL(FS.Gudang, 0) + IFNULL(TP.Gudang, 0) + IFNULL(SR.Gudang, 0) - IFNULL(S.Gudang, 0) - IFNULL(PR.Gudang, 0) + IFNULL(SM.Gudang, 0) - IFNULL(SMM.Gudang, 0) + IFNULL(SA.Gudang, 0) - IFNULL(B.Gudang, 0))  / MID.ConversionQuantity, 2) Gudang,
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
											THEN BD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Toko,
								SUM(
										CASE
											WHEN BD.BranchID = 2
											THEN BD.Quantity * IFNULL(MID.ConversionQuantity, 1)
											ELSE 0
										END
									) Gudang
							FROM
								transaction_bookingdetails BD
                                /*JOIN transaction_booking TB
									ON TB.BookingID = BD.BookingID*/
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
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
