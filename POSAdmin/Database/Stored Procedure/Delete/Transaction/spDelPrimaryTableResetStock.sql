/*=============================================================
Author: Ricmawan Adi Wijaya
Description: Stored Procedure for delete primary table of transaction
Created Date: 12 March 2021
Modified Date: 
===============================================================*/

DROP PROCEDURE IF EXISTS spDelPrimaryTableResetStock;

DELIMITER $$
CREATE PROCEDURE spDelPrimaryTableResetStock (
	pYear				INT,
	pCurrentUser		VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State 	INT;
	DECLARE pID 	BIGINT;
    
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN		
		GET DIAGNOSTICS CONDITION 1
		@MessageText = MESSAGE_TEXT, 
		@State = RETURNED_SQLSTATE, @ErrNo = MYSQL_ERRNO;
		SET @full_error = CONVERT(CONCAT("ERROR No: ", IFNULL(@ErrNo, ''), " (SQLState ", IFNULL(@State, ''), " SPState ", State, ") ",  IFNULL(@MessageText, '')) USING utf8);
		CALL spInsEventLog(@full_error, 'spDelPrimaryTableResetStock', pCurrentUser);
	END;
	
SET State = 1;

	DELETE FROM	
		transaction_firststockdetails
	WHERE
		YEAR(CreatedDate) = pYear;

SET State = 5;

	DELETE FROM
		transaction_purchasedetails
	WHERE
		YEAR(CreatedDate) = pYear;
		
SET State = 6;
		
	DELETE FROM
		transaction_salereturndetails
	WHERE
		YEAR(CreatedDate) = pYear;
			
SET State = 7;

	DELETE FROM
		transaction_sale
	WHERE
		YEAR(CreatedDate) = pYear
		AND FinishFlag = 1;
	
SET State = 8;

	DELETE FROM
		transaction_purchasereturndetails
	WHERE
		YEAR(CreatedDate) = pYear;
		
SET State = 9;

	DELETE FROM
		transaction_stockmutationdetails
	WHERE
		YEAR(CreatedDate) = pYear;
		
SET State = 10;

	DELETE FROM
		transaction_stockmutationdetails
	WHERE
		YEAR(CreatedDate) = pYear;
	
SET State = 11;

	DELETE FROM
		transaction_stockadjustdetails
	WHERE
		YEAR(CreatedDate) = pYear;
	
SET State = 12;

	DELETE FROM
		transaction_booking
	WHERE
		YEAR(CreatedDate) = pYear
		AND FinishFlag = 1;

SET State = 13;

	DELETE FROM
		transaction_pickdetails
	WHERE
		YEAR(CreatedDate) = pYear;
		
SET State = 14;

	DELETE FROM
		transaction_bookingdetails
	WHERE
		YEAR(CreatedDate) = pYear;
		
	COMMIT;
END;
$$
DELIMITER ;
