/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for select sale transaction
Created Date: 3 January 2018
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spSelBookingHeader;

DELIMITER $$
CREATE PROCEDURE spSelBookingHeader (
	pBookingID		BIGINT,
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
		CALL spInsEventLog(@full_error, 'spSelBookingHeader', pCurrentUser);
	END;
	
SET State = 1;

	SELECT
		TB.BookingNumber,
		DATE_FORMAT(TB.TransactionDate, '%d-%m-%Y') TransactionDate,
		TB.CreatedDate,
		TB.CreatedBy,
		MC.CustomerName,
		MC.Address,
		MC.City,
		MC.Telephone
	FROM
		transaction_booking TB
		JOIN master_customer MC
			ON MC.CustomerID = TB.CustomerID
	WHERE
		TB.BookingID = pBookingID;

END;
$$
DELIMITER ;
