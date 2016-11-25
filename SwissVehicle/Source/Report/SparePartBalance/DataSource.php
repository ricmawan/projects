<?php
	if(ISSET($_GET['ItemID'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		$ItemID = mysql_real_escape_string($_GET['ItemID']);
		if($_GET['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
		}
		else {
			$txtFromDate = explode('-', mysql_real_escape_string($_GET['txtFromDate']));
			$_GET['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_GET['txtFromDate'];
		}
		if($_GET['txtToDate'] == "") {
			$txtToDate = date("Y-m-d");
		}
		else {
			$txtToDate = explode('-', mysql_real_escape_string($_GET['txtToDate']));
			$_GET['txtToDate'] = "$txtToDate[2]-$txtToDate[1]-$txtToDate[0]"; 
			$txtToDate = $_GET['txtToDate'];
		}
		
		$sql = "SELECT
					1 GroupLevel,
					'' TransactionDate,
					'' DateNoFormat,
					'' PurchaseQty,
					'' UsageQty,
					IFNULL(PD.Quantity, 0) - IFNULL(SD.Quantity, 0) Quantity,
					'Stok Awal' Remarks
				FROM
					master_item MI
					LEFT JOIN
					(
						SELECT
							PD.ItemID,
							SUM(PD.Quantity) Quantity
						FROM
							transaction_purchase TP
							JOIN transaction_purchasedetails PD
								ON TP.PurchaseID = PD.PurchaseID
						WHERE
							CAST(TP.TransactionDate AS DATE) < '".$txtFromDate."'
							AND PD.ItemID = ".$ItemID."
						GROUP BY
							PD.ItemID
					)PD
						ON MI.ItemID = PD.ItemID
					LEFT JOIN
					(
						SELECT
							SD.ItemID,
							SUM(SD.Quantity) Quantity
						FROM
							transaction_service TS
							JOIN transaction_servicedetails SD
								ON TS.ServiceID = SD.ServiceID
						WHERE
							CAST(TS.TransactionDate AS DATE) < '".$txtFromDate."'
							AND SD.ItemID = ".$ItemID."
						GROUP BY
							SD.ItemID
					)SD
						ON MI.ItemID = SD.ItemID
				WHERE
					MI.ItemID = ".$ItemID."
					AND (IFNULL(PD.Quantity, 0) - IFNULL(SD.Quantity, 0)) > 0
				UNION ALL
				SELECT
					2,
					DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') AS TransactionDate,
					TP.TransactionDate DateNoFormat,
					PD.Quantity PurchaseQty,
					'' UsageQty,
					PD.Quantity,
					CONCAT('Supplier : ', MS.SupplierName)
				FROM
					transaction_purchase TP
					JOIN master_supplier MS
						ON MS.SupplierID = TP.SupplierID
					LEFT JOIN transaction_purchasedetails PD
						ON TP.PurchaseID = PD.PurchaseID
				WHERE
					CAST(TP.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TP.TransactionDate AS DATE) <= '".$txtToDate."'
					AND PD.ItemID = ".$ItemID."
				UNION ALL
				SELECT
					3,
					DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') AS TransactionDate,
					TS.TransactionDate DateNoFormat,
					'' PurchaseQty,
					-SD.Quantity UsageQty,
					-SD.Quantity,
					CONCAT('Mobil/Mesin : ', MM.MachineCode)
				FROM
					transaction_service TS
					JOIN master_machine MM
						ON TS.MachineID = MM.MachineID
					LEFT JOIN transaction_servicedetails SD
						ON TS.ServiceID = SD.ServiceID 
				WHERE
					CAST(TS.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TS.TransactionDate AS DATE) <= '".$txtToDate."'
					AND SD.ItemID = ".$ItemID."
				ORDER BY
					DateNoFormat, GroupLevel";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$nRows = mysql_num_rows($result);		
		$return_arr = array();
		$Stock = 0;
		while ($row = mysql_fetch_array($result)) {
			$Stock += $row['Quantity'];
			$row_array['TransactionDate'] = $row['TransactionDate'];
			$row_array['Stock'] = $Stock;
			$row_array['PurchaseQty'] = $row['PurchaseQty'];
			$row_array['UsageQty'] = $row['UsageQty'];
			$row_array['Remarks'] = $row['Remarks'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
