<?php
	if(isset($_POST['BrandID'])) {
		include "../../DBConfig.php";
		$BrandID = mysql_real_escape_string($_POST['BrandID']);
		$State = 1;
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		
		$sql = "SELECT
					MT.TypeID,
					MT.TypeName,
					MB.BrandID,
					MB.BrandName,
					FS.BatchNumber,
					MT.BuyPrice,
					MT.SalePrice,
					(IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(BO.Quantity, 0) + IFNULL(SO.Quantity, 0)) Stock,
					MU.UnitName
				FROM
					master_type MT
					JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
					JOIN master_unit MU
						ON MU.UnitID = MT.UnitID
					LEFT JOIN
					(
						SELECT
							TypeID,
							TRIM(BatchNumber) BatchNumber,
							SUM(SA.Quantity) Quantity
						FROM
						(
							SELECT
								TypeID,
								TRIM(BatchNumber) BatchNumber,
								SUM(Quantity) Quantity
							FROM
								transaction_firststockdetails
							GROUP BY
								TypeID,
								BatchNumber
							UNION
							SELECT
								TypeID,
								TRIM(BatchNumber) BatchNumber,
								SUM(Quantity) Quantity
							FROM
								transaction_incomingdetails
							GROUP BY
								TypeID,
								BatchNumber
						)SA
						GROUP BY
							TypeID,
							BatchNumber
					)FS
						ON FS.TypeID = MT.TypeID
					LEFT JOIN
					(
						SELECT
							OTD.TypeID,
							TRIM(OTD.BatchNumber) BatchNumber,
							SUM(OTD.Quantity) Quantity
						FROM
							transaction_outgoingdetails OTD
							JOIN transaction_outgoing OT
								ON OT.OutgoingID = OTD.OutgoingID
						WHERE
							OT.IsCancelled = 0
						GROUP BY
							OTD.TypeID,
							OTD.BatchNumber
					)TOD
						ON TOD.TypeID = MT.TypeID
						AND TOD.BatchNumber = FS.BatchNumber
					LEFT JOIN
					(
						SELECT
							TypeID,
							TRIM(BatchNumber) BatchNumber,
							SUM(Quantity) Quantity
						FROM
							transaction_buyreturndetails
						GROUP BY
							TypeID,
							BatchNumber
					)BR
						ON BR.TypeID = MT.TypeID
						AND BR.BatchNumber = FS.BatchNumber
					LEFT JOIN
					(
						SELECT
							TypeID,
							TRIM(BatchNumber) BatchNumber,
							SUM(Quantity) Quantity
						FROM
							transaction_salereturndetails
						GROUP BY
							TypeID,
							BatchNumber
					)SR
						ON SR.TypeID = MT.TypeID
						AND SR.BatchNumber = FS.BatchNumber
					LEFT JOIN
				(
					SELECT
						BOD.TypeID,
						TRIM(BOD.BatchNumber) BatchNumber,
						SUM(BOD.Quantity) Quantity
					FROM
						transaction_booking BO
						JOIN transaction_bookingdetails BOD
							ON BO.BookingID = BOD.BookingID
					WHERE
						BO.BookingStatusID = 1
					GROUP BY
						BOD.TypeID,
						BOD.BatchNumber
				)BO
					ON BO.TypeID = MT.TypeID
					AND BO.BatchNumber = FS.BatchNumber
				LEFT JOIN
				(
					SELECT
						SOD.TypeID,
						TRIM(SOD.BatchNumber) BatchNumber,
						SUM(
								CASE
									WHEN SOD.FromQty > SOD.ToQty
									THEN -(SOD.FromQty - SOD.ToQty)
									ELSE (SOD.ToQty - SOD.FromQty)
								END
							) Quantity
					FROM
						transaction_stockopname SO
						JOIN transaction_stockopnamedetails SOD
							ON SO.StockOpnameID = SOD.StockOpnameID
					GROUP BY
						SOD.TypeID,
						SOD.BatchNumber
				)SO
					ON SO.TypeID = MT.TypeID
					AND SO.BatchNumber = FS.BatchNumber
				WHERE
					MB.BrandID = ".$BrandID."
					AND (IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(BO.Quantity, 0) + IFNULL(SO.Quantity, 0)) > 0
				ORDER BY
					MT.TypeName,
					FS.BatchNumber";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($InvoiceNumber, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		$rows = array();
		while($row = mysql_fetch_assoc($result)) {
			$rows[] = $row;
		}
		echo json_encode($rows);
		return 0;
	}
	
	function returnstate($InvoiceNumber, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"InvoiceNumber" => $InvoiceNumber, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
