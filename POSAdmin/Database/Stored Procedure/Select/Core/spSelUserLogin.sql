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
