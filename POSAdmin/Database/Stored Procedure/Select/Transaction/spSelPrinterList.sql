/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelPrinterList;

DELIMITER $$
CREATE PROCEDURE spSelPrinterList (
	pIPAddress		VARCHAR(100),
	pCurrentUser	VARCHAR(100)
)
StoredProcedure:BEGIN

	DECLARE State INT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spSelPrinterList', pCurrentUser);
	END;
	
SET State = 1;
	
    SELECT
		IPAddress,
		SharedPrinterName
	FROM
		transaction_printerlist
	WHERE
		IPAddress = pIPAddress;
		
END;
$$
DELIMITER ;
