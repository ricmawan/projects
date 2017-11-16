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
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUserLogin', pCurrentUser);
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
