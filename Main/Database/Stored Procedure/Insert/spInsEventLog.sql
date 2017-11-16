/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for insert event log
Created Date: 12 November 2017
Modified Date: 
===============================================================*/

ROP PROCEDURE IF EXISTS spInsEventLog;

DELIMITER $$
CREATE PROCEDURE spInsEventLog (
	pDescription	TEXT,
	pSource			VARCHAR(100),
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
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (State ", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spInsEventLog', pCurrentUser);
	END;
	
	SET PassValidate = 1;
	
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
