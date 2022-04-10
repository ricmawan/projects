/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select no stock report
Created Date: 24 May 2021
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelNoStockReport;

DELIMITER $$
CREATE PROCEDURE spSelNoStockReport (
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
		CALL spInsEventLog(@full_error, 'spSelNoStockReport', pCurrentUser);
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
										FSD.BranchID = ", pBranchID,"
									GROUP BY
										FSD.ItemID,
										FSD.BranchID
								)FS
									ON FS.ItemID = MI.ItemID
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
										TPD.BranchID = ", pBranchID,"
									GROUP BY
										TPD.ItemID,
										TPD.BranchID
								)TP
									ON TP.ItemID = MI.ItemID
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
										SRD.BranchID = ", pBranchID,"
									GROUP BY
										SRD.ItemID,
										SRD.BranchID
								)SR
									ON SR.ItemID = MI.ItemID
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
									WHERE
										SD.BranchID = ", pBranchID,"
									GROUP BY
										SD.ItemID,
										SD.BranchID
								)S
									ON S.ItemID = MI.ItemID
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
										PRD.BranchID = ", pBranchID,"
									GROUP BY
										PRD.ItemID,
										PRD.BranchID
								)PR
									ON MI.ItemID = PR.ItemID
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
										SMD.DestinationID = ", pBranchID,"
									GROUP BY
										SMD.ItemID,
										SMD.DestinationID
								)SM
									ON MI.ItemID = SM.ItemID
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
										SMD.SourceID = ", pBranchID,"
									GROUP BY
										SMD.ItemID,
										SMD.SourceID
								)SMM
									ON MI.ItemID = SMM.ItemID
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
										SAD.BranchID = ", pBranchID,"
									GROUP BY
										SAD.ItemID,
										SAD.BranchID
								)SA
									ON MI.ItemID = SA.ItemID
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
									WHERE
										BD.BranchID = ", pBranchID,"
									GROUP BY
										BD.ItemID,
										BD.BranchID
								)B
									ON B.ItemID = MI.ItemID
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
										PD.BranchID = ", pBranchID,"
									GROUP BY
										PD.ItemID,
										PD.BranchID
								)P
									ON P.ItemID = MI.ItemID
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
										PD.BranchID = ", pBranchID,"
									GROUP BY
										BD.ItemID,
										PD.BranchID
								)BN
									ON BN.ItemID = MI.ItemID
							WHERE
								CASE 
									WHEN ", pCategoryID," = 0
									THEN MC.CategoryID 
									ELSE ", pCategoryID,"
								END = MC.CategoryID
								AND ((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) <= MI.MinimumStock
								OR (IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)) <= MI.MinimumStock) AND ", pWhere, "
							UNION ALL
                            SELECT
								1
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
										FSD.BranchID,
										SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
									FROM
										transaction_firststockdetails FSD
										LEFT JOIN master_itemdetails MID
											ON FSD.ItemDetailsID = MID.ItemDetailsID
									WHERE
										FSD.BranchID = ", pBranchID,"
									GROUP BY
										FSD.ItemID,
										FSD.BranchID
								)FS
									ON FS.ItemID = MI.ItemID
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
										TPD.BranchID = ", pBranchID,"
									GROUP BY
										TPD.ItemID,
										TPD.BranchID
								)TP
									ON TP.ItemID = MI.ItemID
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
										SRD.BranchID = ", pBranchID,"
									GROUP BY
										SRD.ItemID,
										SRD.BranchID
								)SR
									ON SR.ItemID = MI.ItemID
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
									WHERE
										SD.BranchID = ", pBranchID,"
									GROUP BY
										SD.ItemID,
										SD.BranchID
								)S
									ON S.ItemID = MI.ItemID
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
										PRD.BranchID = ", pBranchID,"
									GROUP BY
										PRD.ItemID,
										PRD.BranchID
								)PR
									ON MI.ItemID = PR.ItemID
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
										SMD.DestinationID = ", pBranchID,"
									GROUP BY
										SMD.ItemID,
										SMD.DestinationID
								)SM
									ON MI.ItemID = SM.ItemID
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
										SMD.SourceID = ", pBranchID,"
									GROUP BY
										SMD.ItemID,
										SMD.SourceID
								)SMM
									ON MI.ItemID = SMM.ItemID
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
										SAD.BranchID = ", pBranchID,"
									GROUP BY
										SAD.ItemID,
										SAD.BranchID
								)SA
									ON MI.ItemID = SA.ItemID
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
									WHERE
										BD.BranchID = ", pBranchID,"
									GROUP BY
										BD.ItemID,
										BD.BranchID
								)B
									ON B.ItemID = MI.ItemID
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
										PD.BranchID = ", pBranchID,"
									GROUP BY
										PD.ItemID,
										PD.BranchID
								)P
									ON P.ItemID = MI.ItemID
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
										PD.BranchID = ", pBranchID,"
									GROUP BY
										BD.ItemID,
										PD.BranchID
								)BN
									ON BN.ItemID = MI.ItemID
							WHERE
								CASE 
									WHEN ", pCategoryID," = 0
									THEN MC.CategoryID 
									ELSE ", pCategoryID,"
								END = MC.CategoryID
								AND (((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / MID.ConversionQuantity) <= MI.MinimumStock
								OR ((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity) <= MI.MinimumStock) AND ", pWhere, "
						)ST" 
				);
					
                    
                   
                       
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
SET State = 2;

SET @query = CONCAT("SELECT
						MI.ItemCode,
						MI.ItemName,
						MC.CategoryName,
						ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)), 2) Stock,
                        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)), 2) PhysicalStock,
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
								FSD.BranchID,
								SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_firststockdetails FSD
								LEFT JOIN master_itemdetails MID
									ON FSD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								FSD.BranchID = ", pBranchID,"
							GROUP BY
								FSD.ItemID,
								FSD.BranchID
						)FS
							ON FS.ItemID = MI.ItemID
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
								TPD.BranchID = ", pBranchID,"
							GROUP BY
								TPD.ItemID,
								TPD.BranchID
						)TP
							ON TP.ItemID = MI.ItemID
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
								SRD.BranchID = ", pBranchID,"
							GROUP BY
								SRD.ItemID,
								SRD.BranchID
						)SR
							ON SR.ItemID = MI.ItemID
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
							WHERE
								SD.BranchID = ", pBranchID,"
							GROUP BY
								SD.ItemID,
								SD.BranchID
						)S
							ON S.ItemID = MI.ItemID
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
								PRD.BranchID = ", pBranchID,"
							GROUP BY
								PRD.ItemID,
								PRD.BranchID
						)PR
							ON MI.ItemID = PR.ItemID
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
								SMD.DestinationID = ", pBranchID,"
							GROUP BY
								SMD.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
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
								SMD.SourceID = ", pBranchID,"
							GROUP BY
								SMD.ItemID,
								SMD.SourceID
						)SMM
							ON MI.ItemID = SMM.ItemID
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
								SAD.BranchID = ", pBranchID,"
							GROUP BY
								SAD.ItemID,
								SAD.BranchID
						)SA
							ON MI.ItemID = SA.ItemID
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
							WHERE
								BD.BranchID = ", pBranchID,"
							GROUP BY
								BD.ItemID,
								BD.BranchID
						)B
							ON B.ItemID = MI.ItemID
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
								PD.BranchID = ", pBranchID,"
							GROUP BY
								PD.ItemID,
								PD.BranchID
						)P
							ON P.ItemID = MI.ItemID
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
								PD.BranchID = ", pBranchID,"
							GROUP BY
								BD.ItemID,
								PD.BranchID
						)BN
							ON BN.ItemID = MI.ItemID
					WHERE
						CASE 
							WHEN ", pCategoryID," = 0
							THEN MC.CategoryID 
							ELSE ", pCategoryID,"
						END = MC.CategoryID
						AND ((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) <= MI.MinimumStock
						OR (IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)) <= MI.MinimumStock) AND ", pWhere, "
					UNION ALL 
                    SELECT
						MID.ItemDetailsCode,
						MI.ItemName,
						MC.CategoryName,
						ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
                        ROUND((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity, 2) PhysicalStock,
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
								FSD.BranchID,
								SUM(FSD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_firststockdetails FSD
								LEFT JOIN master_itemdetails MID
									ON FSD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								FSD.BranchID = ", pBranchID,"
							GROUP BY
								FSD.ItemID,
								FSD.BranchID
						)FS
							ON FS.ItemID = MI.ItemID
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
								TPD.BranchID = ", pBranchID,"
							GROUP BY
								TPD.ItemID,
								TPD.BranchID
						)TP
							ON TP.ItemID = MI.ItemID
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
								SRD.BranchID = ", pBranchID,"
							GROUP BY
								SRD.ItemID,
								SRD.BranchID
						)SR
							ON SR.ItemID = MI.ItemID
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
							WHERE
								SD.BranchID = ", pBranchID,"
							GROUP BY
								SD.ItemID,
								SD.BranchID
						)S
							ON S.ItemID = MI.ItemID
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
								PRD.BranchID = ", pBranchID,"
							GROUP BY
								PRD.ItemID,
								PRD.BranchID
						)PR
							ON MI.ItemID = PR.ItemID
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
								SMD.DestinationID = ", pBranchID,"
							GROUP BY
								SMD.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
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
								SMD.SourceID = ", pBranchID,"
							GROUP BY
								SMD.ItemID,
								SMD.SourceID
						)SMM
							ON MI.ItemID = SMM.ItemID
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
								SAD.BranchID = ", pBranchID,"
							GROUP BY
								SAD.ItemID,
								SAD.BranchID
						)SA
							ON MI.ItemID = SA.ItemID
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
							WHERE
								BD.BranchID = ", pBranchID,"
							GROUP BY
								BD.ItemID,
								BD.BranchID
						)B
							ON B.ItemID = MI.ItemID
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
								PD.BranchID = ", pBranchID,"
							GROUP BY
								PD.ItemID,
								PD.BranchID
						)P
							ON P.ItemID = MI.ItemID
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
								PD.BranchID = ", pBranchID,"
							GROUP BY
								BD.ItemID,
								PD.BranchID
						)BN
							ON BN.ItemID = MI.ItemID
					WHERE
						CASE 
							WHEN ", pCategoryID," = 0
							THEN MC.CategoryID 
							ELSE ", pCategoryID,"
						END = MC.CategoryID
						AND (((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0) - IFNULL(BN.Quantity, 0)) / MID.ConversionQuantity) <= MI.MinimumStock
						OR ((IFNULL(FS.Quantity, 0) + IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity) <= MI.MinimumStock) AND ", pWhere, "
					ORDER BY ", pOrder,
					" LIMIT ", pLimit_s, ", ", pLimit_l);
                    
	PREPARE stmt FROM @query;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
        
END;
$$
DELIMITER ;
