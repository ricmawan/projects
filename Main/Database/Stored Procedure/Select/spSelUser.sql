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

	SELECT
		COUNT(*) AS nRows
	FROM
		master_user MU
		JOIN master_usertype MUT
			ON MU.UserTypeID = MUT.UserTypeID
	WHERE
		pWhere;
        
SET State = 2;
	
	SELECT
		MU.UserID,
		MU.UserName,
		MU.UserLogin,
		CASE
			WHEN MU.IsActive = 0
			THEN 'Tidak Aktif'
			ELSE 'Aktif'
		END AS Status,
		MU.UserPassword,
		MUT.UserTypeName
	FROM
		master_user MU
		JOIN master_usertype MUT
			ON MU.UserTypeID = MUT.UserTypeID
	WHERE
		pWhere
	ORDER BY 
		pOrder
	LIMIT
		pLimit_s, pLimit_l;
        
END;
$$
DELIMITER ;
