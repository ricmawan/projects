DROP PROCEDURE IF EXISTS spInsSchedule;

DELIMITER $$
CREATE PROCEDURE spInsSchedule (
	pID 			BIGINT,
	pBranchID		SMALLINT,
	pDayOfWeek 		SMALLINT,
	pStartHour 		SMALLINT,
	pEndHour 		SMALLINT,
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

		SELECT 
			0
		INTO
			PassValidate
		FROM 
			master_schedule
		WHERE
			(pStartHour BETWEEN StartHour AND EndHour
			OR pEndHour BETWEEN StartHour AND EndHour
			OR StartHour BETWEEN pStartHour AND pEndHour
			OR EndHour BETWEEN pStartHour AND pEndHour)
			AND ScheduleID <> pID
			AND IsAdmin = pIsAdmin 
			AND BranchID = pBranchID
			AND DayOfWeek = pDayOfWeek
		LIMIT 1;
			
		IF PassValidate = 0 THEN /*Data yang diinput tidak valid*/
SET State = 2;
			SELECT
				pID AS 'ID',
				'Jadwal Praktek sudah ada' AS 'Message',
				'' AS 'MessageDetail',
				1 AS 'FailedFlag',
				State AS 'State' ;
		
			LEAVE StoredProcedure;
			
		ELSE /*Data yang diinput valid*/
SET State = 3;
			IF(pIsEdit = 0)	THEN /*Tambah baru*/
				INSERT INTO master_schedule
				(
					BranchID,
					DayOfWeek,
					StartHour,
					EndHour,
					IsAdmin,
					CreatedDate,
					CreatedBy
				)
				VALUES (
					pBranchID,
					pDayOfWeek,
					pStartHour,
					pEndHour,
					pIsAdmin,
					NOW(),
					pCurrentUser
				);
			
SET State = 4;			               
				SELECT
					MAX(ScheduleID) AS 'ID',
					'Jadwal Berhasil Ditambahkan' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State'
				FROM
					master_schedule;
					
			ELSE
SET State = 5;
				UPDATE
					master_schedule
				SET
					BranchID = pBranchID,
					DayOfWeek = pDayOfWeek,
					StartHour = pStartHour,
					EndHour = pEndHour,
					IsAdmin = pIsAdmin,
					ModifiedBy = pCurrentUser
				WHERE
					ScheduleID = pID;
					
SET State = 6;
				SELECT
					pID AS 'ID',
					'Jadwal Berhasil Diubah' AS 'Message',
					'' AS 'MessageDetail',
					0 AS 'FailedFlag',
					State AS 'State';
		
			END IF;	
		END IF;
	COMMIT;
END;
$$
DELIMITER ;
