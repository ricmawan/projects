/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete sale return
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPickUp;

DELIMITER $$
CREATE PROCEDURE spDelPickUp (
	pPickUpID		BIGINT,
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT,
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		ROLLBACK;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spDelPickUp', pCurrentUser);
        SELECT
			pPickUpID AS 'ID',
			'Terjadi kesalahan sistem!' AS 'Message',
			@full_error AS 'MessageDetail',
			1 AS 'FailedFlag',
			State AS 'State' ;
	END;
	
	START TRANSACTION;
	
SET State = 1;

		DELETE FROM
			transaction_pick
		WHERE
			PickID = pPickUpID;

    COMMIT;
    
SET State = 2;

		SELECT
			pPickUpID AS 'ID',
			'Pengambilan berhasil dihapus!' AS 'Message',
			'' AS 'MessageDetail',
			0 AS 'FailedFlag',
			State AS 'State' ;
            
END;
$$
DELIMITER ;
