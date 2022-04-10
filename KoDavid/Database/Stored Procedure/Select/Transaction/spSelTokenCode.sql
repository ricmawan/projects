/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select item details by itemCode
Created Date: 9 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelTokenCode;

DELIMITER $$
CREATE PROCEDURE spSelTokenCode (
	pTokenCode		VARCHAR(10),
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
		CALL spInsEventLog(@full_error, 'spSelTokenCode', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		1
	FROM
		transaction_tokencode
	WHERE
		TRIM(TokenCode) = TRIM(pTokenCode)
		AND IsValid = 1;
	
SET State = 2;
	
	UPDATE
		transaction_tokencode
	SET
		IsValid = 0
	WHERE
		TRIM(TokenCode) = TRIM(pTokenCode);

END;
$$
DELIMITER ;
