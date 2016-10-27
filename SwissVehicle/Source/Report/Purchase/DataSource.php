<?php
	if(ISSET($_GET['ItemID']) && ISSET($_GET['SupplierID'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		$ItemID = mysql_real_escape_string($_GET['ItemID']);
		$SupplierID = mysql_real_escape_string($_GET['SupplierID']);
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
		
		$where = " 1=1 ";
		$order_by = "DateNoFormat ASC";
		$rows = 10;
		$current = 1;
		$limit_l = ($current * $rows) - ($rows);
		$limit_h = $limit_l + $rows ;
		//Handles Sort querystring sent from Bootgrid
		if (ISSET($_REQUEST['sort']) && is_array($_REQUEST['sort']) )
		{
			$order_by = "";
			foreach($_REQUEST['sort'] as $key => $value) {
				if($key != 'No') $order_by .= " $key $value";
				else $order_by = "DateNoFormat ASC";
			}
		}
		//Handles search querystring sent from Bootgrid
		if (ISSET($_REQUEST['searchPhrase']) )
		{
			$search = trim($_REQUEST['searchPhrase']);
			$where .= " AND ( DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' OR MS.SupplierName LIKE '%".$search."%' OR MI.ItemName LIKE '%".$search."%' OR PD.Quantity LIKE '%".$search."%' OR PD.Price LIKE '%".$search."%' OR PD.Remarks LIKE '%".$search."%' )";
		}
		//Handles determines where in the paging count this result set falls in
		if (ISSET($_REQUEST['rowCount']) ) $rows = $_REQUEST['rowCount'];
		//calculate the low and high limits for the SQL LIMIT x,y clause
		if (ISSET($_REQUEST['current']) )
		{
			$current = $_REQUEST['current'];
			$limit_l = ($current * $rows) - ($rows);
			$limit_h = $rows ;
		}
		if ($rows == -1) $limit = ""; //no limit
		else $limit = " LIMIT $limit_l, $limit_h ";

		$sql = "SELECT
					COUNT(1) AS nRows,
					SUM(PD.Price * PD.Quantity) GrandTotal
				FROM
					transaction_purchase TP
					JOIN master_supplier MS
						ON MS.SupplierID = TP.SupplierID
					LEFT JOIN transaction_purchasedetails PD
						ON TP.PurchaseID = PD.PurchaseID
					LEFT JOIN master_item MI
						ON MI.ItemID = PD.ItemID 
				WHERE
					$where
					AND CAST(TP.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TP.TransactionDate AS DATE) <= '".$txtToDate."'
					AND CASE
						WHEN ".$SupplierID." = 0
						THEN MS.SupplierID
						ELSE ".$SupplierID."
					END = MS.SupplierID
					AND CASE
						WHEN ".$ItemID." = 0
						THEN MI.ItemID
						ELSE ".$ItemID."
					END = MI.ItemID";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$row = mysql_fetch_array($result);
		$nRows = $row['nRows'];
		$GrandTotal = $row['GrandTotal'];
		
		$sql = "SELECT
					DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') AS TransactionDate,
					TP.TransactionDate DateNoFormat,
					MS.SupplierName,
					MI.ItemName,
					PD.Quantity,
					PD.Price,
					(PD.Price * PD.Quantity) TotalAmount,
					PD.Remarks
				FROM
					transaction_purchase TP
					JOIN master_supplier MS
						ON MS.SupplierID = TP.SupplierID
					LEFT JOIN transaction_purchasedetails PD
						ON TP.PurchaseID = PD.PurchaseID
					LEFT JOIN master_item MI
						ON MI.ItemID = PD.ItemID 
				WHERE
					$where
					AND CAST(TP.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TP.TransactionDate AS DATE) <= '".$txtToDate."'
					AND CASE
						WHEN ".$SupplierID." = 0
						THEN MS.SupplierID
						ELSE ".$SupplierID."
					END = MS.SupplierID
					AND CASE
						WHEN ".$ItemID." = 0
						THEN MI.ItemID
						ELSE ".$ItemID."
					END = MI.ItemID
				ORDER BY
					$order_by
				$limit";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$return_arr = array();
		while ($row = mysql_fetch_array($result)) {
			$row_array['TransactionDate'] = $row['TransactionDate'];
			$row_array['SupplierName'] = $row['SupplierName'];
			$row_array['ItemName'] = $row['ItemName'];
			$row_array['Quantity'] = $row['Quantity'];
			$row_array['Price'] = number_format($row['Price'],2,".",",");
			$row_array['TotalAmount'] = number_format($row['TotalAmount'],2,".",",");
			$row_array['Remarks'] = $row['Remarks'];
			array_push($return_arr, $row_array);
		}
		$json = json_encode($return_arr);
		$GrandTotal = number_format($GrandTotal,2,".",",");
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows, \"GrandTotal\": \"$GrandTotal\" }";
	}
?>
