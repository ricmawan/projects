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
						ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)), 2) Stock,
                        ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)), 2) PhysicalStock,
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
								SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                JOIN master_item MI
									ON MI.ItemID = BD.ItemID
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
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
						MI.ItemCode,
						MI.ItemName,
						MC.CategoryName,
						MB.BranchName,
						ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
                        ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity, 2) PhysicalStock,
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
								SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                JOIN master_item MI
									ON MI.ItemID = BD.ItemID
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
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
		ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)), 2) Stock,
        ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)), 2) StockNoConversion
	FROM
		master_item MI
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
				SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
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
	WHERE
		TRIM(MI.ItemCode) = TRIM(pItemCode)
	UNION ALL
    SELECT
		MI.ItemID,
        MID.ItemDetailsID,
		MID.ItemDetailsCode,
		MI.ItemName,
		MID.BuyPrice,
		MID.RetailPrice,
		MID.Price1,
		MID.Qty1,
		MID.Price2,
		MID.Qty2,
		MID.Weight,
        MID.UnitID,
        MID.ConversionQuantity,
		ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
        ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)), 2) StockNoConversion
	FROM
		master_itemdetails MID
        JOIN master_item MI
			ON MI.ItemID = MID.ItemID
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
				SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
			FROM
				transaction_bookingdetails BD
				LEFT JOIN master_itemdetails MID
					ON BD.ItemDetailsID = MID.ItemDetailsID
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
		MI.Weight
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
        MID.BuyPrice,
		MID.RetailPrice,
		MID.Price1,
		MID.Qty1,
		MID.Price2,
		MID.Qty2,
		MID.Weight
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
		MI.Weight
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
        MID.BuyPrice,
		MID.RetailPrice,
		MID.Price1,
		MID.Qty1,
		MID.Price2,
		MID.Qty2,
		MID.Weight
    FROM
		master_unit MU
		JOIN master_itemdetails MID
			ON MID.UnitID = MU.UnitID
		JOIN master_item MI
			ON MI.ItemID = MID.ItemID
	WHERE
        TRIM(MID.ItemDetailsCode) = TRIM(pItemCode);
        
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
						)A"
					);
						
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
                        ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)), 2) Stock,
                        ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)), 2) PhysicalStock,
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
                                LEFT JOIN master_itemdetails MID
									ON SD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SD.BranchID
									ELSE ",pBranchID,"
								END = SD.BranchID
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
								SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN BD.BranchID
									ELSE ",pBranchID,"
								END = BD.BranchID
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
					WHERE
						CASE
							WHEN ",pBranchID," = 0
							THEN MB.BranchID
							ELSE ",pBranchID,"
						END = MB.BranchID AND ", pWhere, "
					
                    UNION ALL
                    SELECT
						MI.ItemID,
                        MID.ItemDetailsCode,
						MI.ItemName,
                        MC.CategoryID,
						MC.CategoryName,
                        MID.BuyPrice,
						MID.RetailPrice,
                        MID.Price1,
                        MID.Qty1,
                        MID.Price2,
                        MID.Qty2,
                        MID.Weight,
                        MID.MinimumStock,
                        ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)) / MID.ConversionQuantity, 2) Stock,
                        ROUND((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) - IFNULL(SMM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0))  / MID.ConversionQuantity, 2) PhysicalStock,
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
                                LEFT JOIN master_itemdetails MID
									ON SD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN SD.BranchID
									ELSE ",pBranchID,"
								END = SD.BranchID
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
								SUM(BD.Quantity * IFNULL(MID.ConversionQuantity, 1)) Quantity
							FROM
								transaction_bookingdetails BD
                                LEFT JOIN master_itemdetails MID
									ON BD.ItemDetailsID = MID.ItemDetailsID
							WHERE
								CASE
									WHEN ",pBranchID," = 0
									THEN BD.BranchID
									ELSE ",pBranchID,"
								END = BD.BranchID
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
        IFNULL(MID.ItemDetailsCode, MI.ItemCode) ItemCode,
        MI.ItemName,
        SD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID) UnitID,
        MU.UnitName,
        SD.BuyPrice,
        SD.SalePrice,
		SD.Discount,
		IFNULL(MID.RetailPrice, MI.RetailPrice) RetailPrice,
        IFNULL(MID.Price1, MI.Price1) Price1,
        IFNULL(MID.Qty1, MI.Qty1) Qty1,
        IFNULL(MID.Price2, MI.Price2) Price2,
        IFNULL(MID.Qty2, MI.Qty2) Qty2,
		IFNULL(MID.Weight, MI.Weight) Weight,
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
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '", "NULL", "', MI.ItemCode, '", ', MI.BuyPrice, ', ', MI.RetailPrice, ', ', MI.Price1, ', ', MI.Price2, ', ', MI.Qty1, ', ', MI.Qty2, ']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '",', MID.ItemDetailsID, ',"', MID.ItemDetailsCode, '", ', MID.BuyPrice, ', ', MID.RetailPrice, ', ', MID.Price1, ', ', MID.Price2, ', ', MID.Qty1, ', ', MID.Qty2, ']') AvailableUnit
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
        IFNULL(MID.ItemDetailsCode, MI.ItemCode),
        MI.ItemName,
        SD.Quantity,
        IFNULL(MID.UnitID, MI.UnitID),
        SD.BuyPrice,
        SD.SalePrice,
		SD.Discount,
		IFNULL(MID.RetailPrice, MI.RetailPrice),
        IFNULL(MID.Price1, MI.Price1),
        IFNULL(MID.Qty1, MI.Qty1),
        IFNULL(MID.Price2, MI.Price2),
        IFNULL(MID.Qty2, MI.Qty2),
		IFNULL(MID.Weight, MI.Weight),
        SD.ItemDetailsID,
        IFNULL(MID.ConversionQuantity, 1)
	ORDER BY
		SD.SaleDetailsID;
        
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
				SD.BranchID,
                SD.SaleDetailsID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_sale TS
				JOIN transaction_saledetails SD
					ON TS.SaleID = SD.SaleID
			GROUP BY
				TS.SaleID,
				SD.ItemID,
				SD.BranchID,
                SD.SaleDetailsID
		)TS
			ON TSR.SaleID = TS.SaleID
			AND MI.ItemID = TS.ItemID
			AND TS.BranchID = TSRD.BranchID
			AND TSRD.SaleDetailsID = TS.SaleDetailsID
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
        (SD.Quantity * IFNULL(MID.ConversionQuantity, 1) - IFNULL(TSR.Quantity, 0)) Quantity,
        (SD.BuyPrice / IFNULL(MID.ConversionQuantity, 1)) BuyPrice,
        (SD.SalePrice / IFNULL(MID.ConversionQuantity, 1)) SalePrice,
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
        TPRD.BuyPrice,
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
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '", "NULL", "', MI.ItemCode, '", ', MI.BuyPrice, ', ', MI.RetailPrice, ', ', MI.Price1, ', ', MI.Price2 ,']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '",', MID.ItemDetailsID, ',"', MID.ItemDetailsCode, '", ', MID.BuyPrice, ', ', MID.RetailPrice, ', ', MID.Price1, ', ', MID.Price2 ,']') AvailableUnit
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
		TPRD.ItemDetailsID
	ORDER BY
		TPRD.PurchaseReturnDetailsID;
        
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
        PD.BuyPrice,
        PD.RetailPrice,
        PD.Price1,
        PD.Price2,
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
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '", "NULL", "', MI.ItemCode, '", ', MI.BuyPrice, ', ', MI.RetailPrice, ', ', MI.Price1, ', ', MI.Price2 ,']') AvailableUnit
			FROM
				master_unit MU
				JOIN master_item MI
					ON MI.UnitID = MU.UnitID
			GROUP BY
				MI.ItemID
			UNION ALL
			SELECT
				MI.ItemID,
				CONCAT('[', MU.UnitID, ',"', MU.UnitName, '",', MID.ItemDetailsID, ',"', MID.ItemDetailsCode, '", ', MID.BuyPrice, ', ', MID.RetailPrice, ', ', MID.Price1, ', ', MID.Price2 ,']') AvailableUnit
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
        PD.ItemDetailsID
	ORDER BY
		PD.PurchaseDetailsID;
        
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
					LAST_INSERT_ID()
				INTO 
					pID;

SET State = 5;

				SELECT
					pID AS 'ID',
					'Pelanggan Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 6;

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

SET State = 7;

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
					LAST_INSERT_ID()
				INTO 
					pID;
			
SET State = 5;			               
				SELECT
					pID AS 'ID',
					'Supplier Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
			ELSE
SET State = 6;
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

SET State = 7;
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
        MI.UnitID
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
		MID.BuyPrice,
		MID.RetailPrice,
		MID.Price1,
		MID.Qty1,
		MID.Price2,
		MID.Qty2,
		MID.Weight,
        MID.UnitID
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
		MI.Weight
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
        MID.BuyPrice,
		MID.RetailPrice,
		MID.Price1,
		MID.Qty1,
		MID.Price2,
		MID.Qty2,
		MID.Weight
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
		MI.Weight
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
        MID.BuyPrice,
		MID.RetailPrice,
		MID.Price1,
		MID.Qty1,
		MID.Price2,
		MID.Qty2,
		MID.Weight
    FROM
		master_unit MU
		JOIN master_itemdetails MID
			ON MID.UnitID = MU.UnitID
		JOIN master_item MI
			ON MI.ItemID = MID.ItemID
	WHERE
        TRIM(MID.ItemDetailsCode) = TRIM(pItemCode);

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
									ConversionQuantity,
									BuyPrice,
									RetailPrice,
									Price1,
									Qty1,
									Price2,
									Qty2,
									Weight,
									MinimumStock
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
			(TRIM(ItemName) = TRIM(pItemName)
            OR TRIM(ItemCode) = TRIM(pItemCode))
			AND ItemID <> pID
		LIMIT 1;
        
SET State = 4;

		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
			SELECT
				pID AS 'ID',
				'Barang sudah ada!' AS 'Message',
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
			
		ELSE /*Data yang diinput valid*/
        
SET State = 11;

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
			
SET State = 12;

				SELECT
					LAST_INSERT_ID()
				INTO 
					pID;

SET State = 13;
				SET SQL_SAFE_UPDATES = 0;
                
				UPDATE temp_master_itemdetails
                SET ItemID = pID
                WHERE
					ItemDetailsID = 0;
				
                SET SQL_SAFE_UPDATES = 1;
                
SET State = 14;
				INSERT INTO master_itemdetails
                (
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
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
                SELECT
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
					BuyPrice,
					RetailPrice,
					Price1,
					Qty1,
					Price2,
					Qty2,
					Weight,
					MinimumStock,
                    NOW(),
                    'Admin'
				FROM
					temp_master_itemdetails;
                
SET State = 15;

				SELECT
					pID AS 'ID',
					'Barang Berhasil Ditambahkan!' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
                    
			ELSE
            
SET State = 16;

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

SET State = 17;

				UPDATE master_itemdetails MID
                JOIN temp_master_itemdetails TMID
					ON TMID.ItemDetailsID = MID.ItemDetailsID
				SET
					MID.ItemDetailsCode = TMID.ItemDetailsCode,
					MID.UnitID = TMID.UnitID,
					MID.ConversionQuantity = TMID.ConversionQuantity,
					MID.BuyPrice = TMID.BuyPrice,
					MID.RetailPrice = TMID.RetailPrice,
					MID.Price1 = TMID.Price1,
					MID.Qty1 = TMID.Qty1,
					MID.Price2 = TMID.Price2,
					MID.Qty2 = TMID.Qty2,
					MID.Weight = TMID.Weight,
					ModifiedBy = pCurrentUser;
                    
SET State = 18;
				
				DELETE FROM master_itemdetails
				WHERE 
					ItemDetailsID NOT IN(
											SELECT 
												TMID.ItemDetailsID
											FROM 
												temp_master_itemdetails TMID
										);
                                
SET State = 19;

				INSERT INTO master_itemdetails
                (
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
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
                SELECT
					ItemDetailsID,
					ItemID,
					ItemDetailsCode,
					UnitID,
					ConversionQuantity,
					BuyPrice,
					RetailPrice,
					Price1,
					Qty1,
					Price2,
					Qty2,
					Weight,
					MinimumStock,
                    NOW(),
                    'Admin'
				FROM
					temp_master_itemdetails
				WHERE
					ItemDetailsID = 0;
                    
SET State = 20;

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
DROP PROCEDURE IF EXISTS spUpdPurchase;

DELIMITER $$
CREATE PROCEDURE spUpdPurchase (
	pID 				BIGINT,
    pPurchaseNumber		VARCHAR(100),
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
		ConversionQuantity,
		BuyPrice,
		RetailPrice,
		Price1,
		Qty1,
		Price2,
		Qty2,
		Weight,
		MinimumStock
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
				CONCAT('B', RIGHT(CONCAT('00', pUserID), 2), DATE_FORMAT(NOW(), '%Y%m'), RIGHT(CONCAT('00000', (IFNULL(MAX(CAST(RIGHT(BookingNumber, 5) AS UNSIGNED)), 0) + 1)), 5))
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
								TS.SaleID,
							    IFNULL(TS.Payment, 0) Payment,
							    IFNULL(TP.Amount, 0) Amount,
							    0 PickQuantity
							FROM
								transaction_sale TS
								JOIN transaction_saledetails SD
									ON SD.SaleID = TS.SaleID
								JOIN master_customer MC
									ON MC.CustomerID = TS.CustomerID
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
								TS.Payment,
								TP.Amount
							HAVING
								SUM(SD.Quantity * SD.SalePrice  - SD.Discount) > (Amount + Payment)
							UNION ALL
		                    SELECT
								TB.BookingID,
								IFNULL(TB.Payment, 0) Payment,
								IFNULL(TP.Amount, 0) Amount,
							    IFNULL(PK.Quantity, 0) PickQuantity
							FROM
								transaction_booking TB
								JOIN transaction_bookingdetails BD
									ON BD.BookingID = TB.BookingID
								JOIN master_customer MC
									ON MC.CustomerID = TB.CustomerID
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
								LEFT JOIN
							    (
									SELECT
										TB.BookingID,
							            SUM(PD.Quantity) Quantity
									FROM
										transaction_pickdetails PD
							            JOIN transaction_bookingdetails BD
											ON PD.BookingDetailsID = BD.BookingDetailsID
										JOIN transaction_booking TB
											ON TB.BookingID = BD.BookingID
									GROUP BY
										TB.BookingID
								)PK
									ON TB.BookingID = PK.BookingID
							WHERE
								", pWhere2, "
							GROUP BY
								TB.BookingID,
								TB.Payment,
								TP.Amount,
								PK.Quantity
							HAVING
								SUM(BD.Quantity * BD.BookingPrice  - BD.Discount) > (Amount + Payment)
							    OR SUM(BD.Quantity) > PickQuantity
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
						SUM(SD.Quantity * SD.SalePrice  - SD.Discount) Total,
						IFNULL(TS.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
					    IFNULL(TS.Payment, 0) Payment,
					    IFNULL(TP.Amount, 0) Amount,
					    0 PickQuantity
					FROM
						transaction_sale TS
						JOIN transaction_saledetails SD
							ON SD.SaleID = TS.SaleID
						JOIN master_customer MC
							ON MC.CustomerID = TS.CustomerID
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
						TS.SaleID,
						TS.SaleNumber,
						TS.TransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						IFNULL(TS.Payment, 0),
					    IFNULL(TP.Amount, 0)
					HAVING
						SUM(SD.Quantity * SD.SalePrice  - SD.Discount) > (Amount + Payment)
					UNION ALL
                    SELECT
						TB.BookingID,
						TB.BookingNumber,
						DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
						TB.TransactionDate PlainTransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						SUM(BD.Quantity * BD.BookingPrice  - BD.Discount) Total,
						IFNULL(TB.Payment, 0) + IFNULL(TP.Amount, 0) TotalPayment,
						IFNULL(TB.Payment, 0) Payment,
						IFNULL(TP.Amount, 0) Amount,
					    IFNULL(PK.Quantity, 0) PickQuantity
					FROM
						transaction_booking TB
						JOIN transaction_bookingdetails BD
							ON BD.BookingID = TB.BookingID
						JOIN master_customer MC
							ON MC.CustomerID = TB.CustomerID
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
						LEFT JOIN
					    (
							SELECT
								TB.BookingID,
					            SUM(PD.Quantity) Quantity
							FROM
								transaction_pickdetails PD
					            JOIN transaction_bookingdetails BD
									ON PD.BookingDetailsID = BD.BookingDetailsID
								JOIN transaction_booking TB
									ON TB.BookingID = BD.BookingID
							GROUP BY
								TB.BookingID
						)PK
							ON TB.BookingID = PK.BookingID
					WHERE
						", pWhere2, "
					GROUP BY
						TB.BookingID,
						TB.BookingNumber,
						TB.TransactionDate,
						MC.CustomerID,
						MC.CustomerName,
						IFNULL(TB.Payment, 0),
						IFNULL(TP.Amount, 0),
					    IFNULL(PK.Quantity, 0)
					HAVING
						SUM(BD.Quantity * BD.BookingPrice  - BD.Discount) > (Amount + Payment)
					    OR SUM(BD.Quantity) > PickQuantity
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
		SUM(SD.Quantity * SD.SalePrice - SD.Discount) Total
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
								SUM(SD.Quantity * SD.SalePrice - SD.Discount) Total
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
                        SUM(SD.Quantity * SD.SalePrice - SD.Discount) Total
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
        (SD.Quantity * SD.SalePrice - SD.Discount) TotalSale,
		(SD.Quantity * SD.SalePrice - SD.Discount) - (SD.Quantity * SD.BuyPrice) Income
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
								SUM((SD.Quantity * SD.SalePrice - SD.Discount) - (SD.Quantity * SD.BuyPrice)) Total
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
								-SUM(((SRD.Quantity * SRD.SalePrice) - (SRD.Quantity * SRD.BuyPrice)))
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
                        SUM((SD.Quantity * SD.SalePrice - SD.Discount) - (SD.Quantity * SD.BuyPrice)) Total
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
                        -SUM(((SRD.Quantity * SRD.SalePrice) - (SRD.Quantity * SRD.BuyPrice))) Total
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
								SUM(SD.Quantity * SD.SalePrice  - SD.Discount) Total
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
                        SUM(SD.Quantity * SD.SalePrice - SD.Discount) Total
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
DROP PROCEDURE IF EXISTS spUpdSalePayment;

DELIMITER $$
CREATE PROCEDURE spUpdSalePayment (
	pID 			BIGINT, 
	pPayment 		DOUBLE,
	pPaymentTypeID	SMALLINT,
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
								LEFT JOIN
								(
									SELECT
										TPD.ItemID,
										TPD.BranchID,
										SUM(TPD.Quantity) Quantity
									FROM
										transaction_purchasedetails TPD							
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
										SUM(SRD.Quantity) Quantity
									FROM
										transaction_salereturndetails SRD
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
										SUM(SD.Quantity) Quantity
									FROM
										transaction_saledetails SD
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
										SUM(PRD.Quantity) Quantity
									FROM
										transaction_purchasereturndetails PRD
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
										SUM(SMD.Quantity) Quantity
									FROM
										transaction_stockmutationdetails SMD
										JOIN master_item MI
											ON MI.ItemID = SMD.ItemID
									GROUP BY
										MI.ItemID,
										SMD.DestinationID
								)SM
									ON MI.ItemID = SM.ItemID
									AND SM.DestinationID = MB.BranchID
								LEFT JOIN
								(
									SELECT
										SAD.ItemID,
										SAD.BranchID,
										SUM(SAD.AdjustedQuantity - SAD.Quantity) Quantity
									FROM
										transaction_stockadjustdetails SAD
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
										SUM(BD.Quantity) Quantity
									FROM
										transaction_bookingdetails BD
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
										SUM(PD.Quantity) Quantity
									FROM
										transaction_pickdetails PD
									GROUP BY
										PD.ItemID,
										PD.BranchID
								)P
									ON P.ItemID = MI.ItemID
									AND P.BranchID = MB.BranchID
							WHERE
								((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)) < MI.MinimumStock
								OR (IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)) < MI.MinimumStock) AND ", pWhere, "
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
                        MI.MinimumStock,
                        (IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)) Stock,
                        (IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)) PhysicalStock
					FROM
						master_item MI
                        CROSS JOIN master_branch MB
						JOIN master_category MC
							ON MC.CategoryID = MI.CategoryID
						LEFT JOIN
						(
							SELECT
								TPD.ItemID,
								TPD.BranchID,
								SUM(TPD.Quantity) Quantity
							FROM
								transaction_purchasedetails TPD							
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
								SUM(SRD.Quantity) Quantity
							FROM
								transaction_salereturndetails SRD
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
								SUM(SD.Quantity) Quantity
							FROM
								transaction_saledetails SD
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
								SUM(PRD.Quantity) Quantity
							FROM
								transaction_purchasereturndetails PRD
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
								SUM(SMD.Quantity) Quantity
							FROM
								transaction_stockmutationdetails SMD
								JOIN master_item MI
									ON MI.ItemID = SMD.ItemID
							GROUP BY
								MI.ItemID,
								SMD.DestinationID
						)SM
							ON MI.ItemID = SM.ItemID
							AND SM.DestinationID = MB.BranchID
						LEFT JOIN
						(
							SELECT
								SAD.ItemID,
								SAD.BranchID,
								SUM(SAD.AdjustedQuantity - SAD.Quantity) Quantity
							FROM
								transaction_stockadjustdetails SAD
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
								SUM(BD.Quantity) Quantity
							FROM
								transaction_bookingdetails BD
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
								SUM(PD.Quantity) Quantity
							FROM
								transaction_pickdetails PD
							GROUP BY
								PD.ItemID,
								PD.BranchID
						)P
							ON P.ItemID = MI.ItemID
							AND P.BranchID = MB.BranchID
					WHERE
						((IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(B.Quantity, 0)) < MI.MinimumStock
						OR (IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0) - IFNULL(P.Quantity, 0)) < MI.MinimumStock) AND ", pWhere, 
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
								MI.ItemName
							 FROM
								transaction_saledetails SD
								JOIN master_item MI
									ON MI.ItemID = SD.ItemID
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
								TP.PurchaseID
							UNION ALL
		                    SELECT
								-SUM(PRD.Quantity * PRD.BuyPrice) Total
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
						-SUM(PRD.Quantity * PRD.BuyPrice) Total
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
            -(PRD.Quantity * PRD.BuyPrice) SubTotal
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
			-(SRD.Quantity * SRD.SalePrice) SubTotal
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
		(IFNULL(TP.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(S.Quantity, 0) - IFNULL(PR.Quantity, 0) + IFNULL(SM.Quantity, 0) + IFNULL(SA.Quantity, 0)) Quantity,
		'0000-00-00' CreatedDate
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
		TPD.Quantity,
		TP.CreatedDate
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
		SRD.Quantity,
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
		-SD.Quantity,
		TS.CreatedDate
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
		-PRD.Quantity,
		TPR.CreatedDate
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
		SMD.Quantity,
		SM.CreatedDate
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
		(SAD.AdjustedQuantity - SAD.Quantity),
		SA.CreatedDate
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
						IFNULL(TS.Payment, 0) Payment
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
