<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	
	if($_GET['Filter'] == "1") {
		if($_GET['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
		}
		else {
			$FromDate = explode(', ', mysql_real_escape_string($_GET['txtFromDate']));
			$txtFromDate = explode('-', $FromDate[1]);
			$_GET['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_GET['txtFromDate'];
		}
		if($_GET['txtToDate'] == "") {
			$txtToDate = date("Y-m-d");
		}
		else {
			$ToDate = explode(', ', mysql_real_escape_string($_GET['txtToDate']));
			$txtToDate = explode('-', $ToDate[1]);
			$_GET['txtToDate'] = "$txtToDate[2]-$txtToDate[1]-$txtToDate[0]"; 
			$txtToDate = $_GET['txtToDate'];
		}
	}
	else {
		$txtToDate = "2099-12-31";
		$txtFromDate = "2000-01-01";
	}
	$where = " 1=1 AND IT.TransactionDate BETWEEN '".$txtFromDate."' AND '".$txtToDate."' ";
	
	$order_by = "IT.TransactionDate DESC";
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
			else $order_by = "IT.TransactionDate DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( ITD.Remarks LIKE '%".$search."%' OR MM.MaterialName LIKE '%".$search."%' OR ITD.SupplierName LIKE '%".$search."%' OR DATE_FORMAT(IT.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' ) ";
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
				COUNT(*) AS nRows
			FROM
				transaction_incoming IT
				JOIN transaction_incomingdetails ITD
					ON IT.IncomingID = ITD.IncomingID
				JOIN master_material MM
					ON MM.MaterialID = ITD.MaterialID
			WHERE
				$where";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				IT.IncomingID,
				ITD.IncomingDetailsID,
				MM.MaterialName,
				ITD.SupplierName,
				DATE_FORMAT(IT.TransactionDate, '%d-%m-%Y') AS TransactionDate,
				ITD.Remarks,
				ITD.Quantity
			FROM
				transaction_incoming IT
				JOIN transaction_incomingdetails ITD
					ON IT.IncomingID = ITD.IncomingID
				JOIN master_material MM
					ON MM.MaterialID = ITD.MaterialID
			WHERE
				$where
			ORDER BY 
				$order_by
			$limit";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$return_arr = array();
	$RowNumber = $limit_l;
	while ($row = mysql_fetch_array($result)) {
		$RowNumber++;
		$row_array['RowNumber'] = $RowNumber;
		$row_array['IncomingDetailsID']= $row['IncomingDetailsID'];
		$row_array['MaterialName'] = $row['MaterialName'];
		$row_array['SupplierName'] = $row['SupplierName'];
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['Quantity'] = $row['Quantity'];
		$row_array['Remarks'] = $row['Remarks'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
