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
