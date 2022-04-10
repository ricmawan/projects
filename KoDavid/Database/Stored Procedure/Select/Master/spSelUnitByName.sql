/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item details by itemCode
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelUnitByName;

DELIMITER $$
CREATE PROCEDURE spSelUnitByName (
	pUnitName		VARCHAR(100),
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelUnitByName', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		MU.UnitID,
        MU.UnitName
	FROM
		master_unit MU
	WHERE
		TRIM(MU.UnitName) = TRIM(pUnitName);
        
END;
$$
DELIMITER ;
