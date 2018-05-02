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
