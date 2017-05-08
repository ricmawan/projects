<?php
	if(isset($_POST['BrandID'])) {
		include "../../DBConfig.php";
		$BrandID = mysql_real_escape_string($_POST['BrandID']);
		$CustomerID = mysql_real_escape_string($_POST['CustomerID']);
		$State = 1;
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		
		$sql = "SELECT
					MT.TypeID,
					MT.TypeName,
					MB.BrandID,
					MB.BrandName,
					TOD.BatchNumber,
					MT.BuyPrice,
					MT.SalePrice,
					IFNULL(TOD.Quantity, 0) - IFNULL(SR.Quantity, 0) Stock,
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
							SUM(Quantity) Quantity
						FROM
							transaction_outgoing OT
							JOIN transaction_outgoingdetails OTD
								ON OT.OutgoingID = OTD.OutgoingID
						WHERE
							OT.CustomerID = ".$CustomerID."
							AND OT.IsCancelled = 0
						GROUP BY
							TypeID,
							BatchNumber
					)TOD
						ON TOD.TypeID = MT.TypeID
					LEFT JOIN
					(
						SELECT
							SRD.TypeID,
							TRIM(SRD.BatchNumber) BatchNumber,
							SUM(SRD.Quantity) Quantity
						FROM
							transaction_salereturn SR
							JOIN transaction_salereturndetails SRD
								ON SR.SaleReturnID = SRD.SaleReturnID
						WHERE
							SR.CustomerID = ".$CustomerID."
							AND SR.IsCancelled = 0
						GROUP BY
							SRD.TypeID,
							SRD.BatchNumber
					)SR
						ON SR.TypeID = MT.TypeID
						AND SR.BatchNumber = TOD.BatchNumber
				WHERE
					MB.BrandID = ".$BrandID."
					AND IFNULL(TOD.Quantity, 0) - IFNULL(SR.Quantity, 0) > 0
				ORDER BY
					MT.TypeName,
					TOD.BatchNumber";
		
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
