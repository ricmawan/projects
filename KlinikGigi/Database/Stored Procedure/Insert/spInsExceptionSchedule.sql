DROP PROCEDURE IF EXISTS spInsExceptionSchedule;

DELIMITER $$
CREATE PROCEDURE spInsExceptionSchedule (
	pID 			BIGINT,
	pBranchID		SMALLINT,
	pDayOfWeek 		SMALLINT,
	pBusinessHour 	VARCHAR(10),
	pIsAdmin 		SMALLINT,
	pIsEdit			SMALLINT,
    pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE Message VARCHAR(255);
	DECLARE MessageDetail VARCHAR(255);
	DECLARE FailedFlag INT;
	DECLARE State INT;
	DECLARE RowCount INT;

	DECLARE PassValidate INT;
	
	/*DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO, @DBName = SCHEMA_NAME, @TBLName = TABLE_NAME;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR ", IFNULL(@ErrNo, ''), " (", IFNULL(@State, ''), "): ", IFNULL(@MessageText, ''), ', ', IFNULL(@DBName, ''), ', ', IFNULL(@TableName, '')) USING utf8);
		SELECT 
			pId AS 'ID', 
			'Terjadi Kesalahan Sistem' AS 'Message', 
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag', 
			State AS 'State';
	END;*/
	
	SET PassValidate = 1;
	
	START TRANSACTION;	

SET State = 1;

		IF(pIsEdit = 0)	THEN /*Tambah baru*/
			INSERT INTO master_exceptionschedule
			(
				BranchID,
				DayOfWeek,
				BusinessHour,
				IsAdmin,
				CreatedDate,
				CreatedBy
			)
			VALUES (
				pBranchID,
				pDayOfWeek,
				pBusinessHour,
				pIsAdmin,
				NOW(),
				pCurrentUser
			);
		
SET State = 4;			               
			SELECT
				MAX(ExceptionScheduleID) AS 'ID',
				'Pengecualian Berhasil Ditambahkan' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State'
			FROM
				master_exceptionschedule;
				
		ELSE
SET State = 5;
			UPDATE
				master_exceptionschedule
			SET
				BranchID = pBranchID,
				DayOfWeek = pDayOfWeek,
				BusinessHour = pBusinessHour,
				IsAdmin = pIsAdmin,
				ModifiedBy = pCurrentUser
			WHERE
				ExceptionScheduleID = pID;
				
SET State = 6;
			SELECT
				pID AS 'ID',
				'Pengecualian Berhasil Diubah' AS 'Message',
				'' AS 'MessageDetail',
				0 AS 'FailedFlag',
				State AS 'State';
	
		END IF;	
	COMMIT;
END;
$$
DELIMITER ;
