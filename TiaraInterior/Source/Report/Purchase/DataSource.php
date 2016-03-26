<?php
	if(ISSET($_GET['SupplierID'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
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
		
		//echo $txtFromDate;
		//echo $txtToDate;
		$where = " 1=1 ";
		$order_by = "";
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
				else $order_by = "DATE_FORMAT(TI.TransactionDate, '%d-%m-%Y') ASC";
			}
		}
		//Handles search querystring sent from Bootgrid
		if (ISSET($_REQUEST['searchPhrase']) )
		{
			$search = trim($_REQUEST['searchPhrase']);
			//$where .= " AND ( DATE_FORMAT(DATA.TransactionDate, '%d%b%y') LIKE '%".$search."%' OR DATA.Name LIKE '%".$search."%' OR DATA.ItemName LIKE '%".$search."%' OR DATA.Quantity LIKE '%".$search."%' OR DATA.UnitName LIKE '%".$search."%' OR DATA.Price LIKE '%".$search."%' OR DATA.Debit LIKE '%".$search."%' OR DATA.Credit LIKE '%".$search."%' OR DATA.Remarks LIKE '%".$search."%' )";
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
		mysql_query("SET @row:=0;", $dbh);
		$sql = "SELECT
					DATE_FORMAT(TI.TransactionDate, '%d/%c/%y') AS TransactionDate,
					MS.SupplierName AS SupplierName,
					TI.IncomingNumber,
					TI.Remarks,
					CASE
						WHEN TID.IsPercentage = 1
						THEN IFNULL(SUM(TID.Quantity * (TID.BuyPrice - ((TID.BuyPrice * TID.Discount)/100))), 0) 
						ELSE IFNULL(SUM(TID.Quantity * (TID.BuyPrice - TID.Discount)), 0)
					END AS Total
				FROM
					transaction_incoming TI
					JOIN master_supplier MS
						ON MS.SupplierID = TI.SupplierID
					LEFT JOIN transaction_incomingdetails TID
						ON TID.IncomingID = TI.IncomingID
				WHERE
					TI.TransactionDate >= '".$txtFromDate."'
					AND TI.TransactionDate <= '".$txtToDate."'
					AND CASE
							WHEN ".$SupplierID." = 0
							THEN MS.SupplierID
							ELSE ".$SupplierID."
						END = MS.SupplierID
				GROUP BY
					TI.IncomingNumber,
					TI.TransactionDate,
					MS.SupplierName
				UNION ALL
				SELECT
					DATE_FORMAT(BR.TransactionDate, '%d/%c/%y') AS TransactionDate,
					MS.SupplierName AS SupplierName,
					BR.BuyReturnNumber,
					BR.Remarks,
					-CASE
						WHEN BRD.IsPercentage = 1
						THEN IFNULL(SUM(BRD.Quantity * (BRD.BuyPrice - ((BRD.BuyPrice * BRD.Discount)/100))), 0) 
						ELSE IFNULL(SUM(BRD.Quantity * (BRD.BuyPrice - BRD.Discount)), 0)
					 END  AS Total
				FROM
					transaction_buyreturn BR
					JOIN master_supplier MS
						ON MS.SupplierID = BR.SupplierID
					LEFT JOIN transaction_buyreturndetails BRD
						ON BRD.BuyReturnID = BR.BuyReturnID
				WHERE
					BR.TransactionDate >= '".$txtFromDate."'
					AND BR.TransactionDate <= '".$txtToDate."'
					AND CASE
							WHEN ".$SupplierID." = 0
							THEN MS.SupplierID
							ELSE ".$SupplierID."
						END = MS.SupplierID
				GROUP BY
					BR.BuyReturnNumber,
					BR.TransactionDate,
					MS.SupplierName
				ORDER BY	
					TransactionDate ASC";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$nRows = mysql_num_rows($result);		
		$return_arr = array();
		//$nRows = mysql_num_rows($result);
		$Stock = 0;
		$RowNumber = 0;
		$GrandTotal = 0;
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			$row_array['RowNumber'] = $RowNumber;
			$row_array['TransactionDate'] = $row['TransactionDate'];
			$row_array['SupplierName'] = $row['SupplierName'];
			$row_array['IncomingNumber']= $row['IncomingNumber'];
			$row_array['Total'] = number_format($row['Total'],2,".",",");
			$row_array['Remarks'] = $row['Remarks'];
			$GrandTotal += $row['Total'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		$GrandTotal = number_format($GrandTotal,2,".",",");
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows, \"GrandTotal\": \"$GrandTotal\" }";
	}
?>
