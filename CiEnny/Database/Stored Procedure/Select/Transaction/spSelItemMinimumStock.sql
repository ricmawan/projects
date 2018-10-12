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
