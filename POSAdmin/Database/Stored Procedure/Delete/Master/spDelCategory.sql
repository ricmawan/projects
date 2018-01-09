/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete category
Created Date: 1 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelCategory;

DELIMITER $$
CREATE PROCEDURE spDelCategory (
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
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (State ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spDelCategory', pCurrentUser);
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			master_category
		WHERE
			CategoryID = pCategoryID;

    COMMIT;
END;
$$
DELIMITER ;
