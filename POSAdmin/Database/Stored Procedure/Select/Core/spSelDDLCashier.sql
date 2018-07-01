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
