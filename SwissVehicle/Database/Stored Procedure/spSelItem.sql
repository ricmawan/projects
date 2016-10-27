DROP PROCEDURE IF EXISTS spSelItem;

DELIMITER $$
CREATE PROCEDURE spSelItem (
	pCurrentUser	VARCHAR(255)
)
StoredProcedure:BEGIN

	DECLARE State INT;
	
SET State = 1;
	DECLARE Prev_ID BIGINT;
	SET Prev_ID = 0;
	DECLARE Prev_Price DOUBLE;
	SET Prev_Price = 0;

SET State = 2;
	SELECT
		MI.ItemID,
		MI.ItemName,
		MI.ItemCode,
		MI.Price,
		IF(IsSecond = 0, 'Tidak', 'Ya') IsSecond,
		IFNULL(PD.Quantity, 0) - IFNULL(SD.Quantity, 0) Stock,
		IFNULL(TSD.Quantity, 0) - IFNULL(TSL.Quantity, 0) SecondStock 
	FROM
		master_item MI
		LEFT JOIN
		(
			SELECT
				PD.ItemID,
				PD.Price,
				SUM(PD.Quantity) Quantity
			FROM
				transaction_purchasedetails PD
			GROUP BY
				PD.ItemID,
				PD.Price
		)PD
			ON PD.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SD.ItemID,
				SD.Price,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_servicedetails SD
			GROUP BY
				SD.ItemID,
				SD.Price
		)SD
			ON SD.ItemID = MI.ItemID
			AND PD.Price = SD.Price
		LEFT JOIN
		(
			SELECT
				SD.ItemID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_servicedetails SD
			WHERE
				SD.IsSecond = 1
			GROUP BY
				SD.ItemID,
				SD.Price
		)TSD
			ON TSD.ItemID = MI.ItemID
		LEFT JOIN
		(
			SELECT
				SD.ItemID,
				SUM(SD.Quantity) Quantity
			FROM
				transaction_saledetails SD
			GROUP BY
				SD.ItemID
		)TSL
			ON TSL.ItemID = MI.ItemID;

END;
$$
DELIMITER ;