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
			$where .= " AND ( DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' OR MM.MachineType LIKE '%".$search."%' OR MM.MachineCode LIKE '%".$search."%' OR SD.Quantity LIKE '%".$search."%' OR SD.Price LIKE '%".$search."%' OR SD.Remarks LIKE '%".$search."%' )";
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
					SUM(SD.Price * SD.Quantity) AS GrandTotal
				FROM
					transaction_service TS
					JOIN master_machine MM
						ON TS.MachineID = MM.MachineID
					LEFT JOIN transaction_servicedetails SD
						ON TS.ServiceID = SD.ServiceID 
				WHERE
					$where
					AND CAST(TS.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TS.TransactionDate AS DATE) <= '".$txtToDate."'
					AND SD.ItemID = ".$ItemID;

		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$row = mysql_fetch_array($result);
		$nRows = $row['nRows'];
		$GrandTotal = $row['GrandTotal'];

		$sql = "SELECT
					DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') AS TransactionDate,
					TS.TransactionDate DateNoFormat,
					MM.MachineType,
					MM.MachineCode,
					SD.Quantity,
					SD.Price,
					(SD.Price * SD.Quantity) TotalAmount,
					SD.Remarks
				FROM
					transaction_service TS
					JOIN master_machine MM
						ON TS.MachineID = MM.MachineID
					LEFT JOIN transaction_servicedetails SD
						ON TS.ServiceID = SD.ServiceID 
				WHERE
					$where
					AND CAST(TS.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TS.TransactionDate AS DATE) <= '".$txtToDate."'
					AND SD.ItemID = ".$ItemID."
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
			$row_array['MachineType'] = $row['MachineType'];
			$row_array['MachineCode'] = $row['MachineCode'];
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
