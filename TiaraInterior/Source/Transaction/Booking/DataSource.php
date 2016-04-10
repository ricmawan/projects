<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "BO.BookingID DESC";
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
			else $order_by = "BO.BookingID DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( BO.BookingNumber LIKE '%".$search."%' OR BO.Remarks LIKE '%".$search."%' OR MC.CustomerName LIKE '%".$search."%' OR MS.SalesName LIKE '%".$search."%' OR DATE_FORMAT(BO.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' ) ";
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
				transaction_booking BO
				JOIN master_sales MS
					ON BO.SalesID = MS.SalesID
				JOIN master_customer MC
					ON BO.CustomerID = MC.CustomerID
			WHERE
				$where";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				BO.BookingID,
				BO.BookingNumber,
				MS.SalesName,
				MC.CustomerName,
				DATE_FORMAT(BO.TransactionDate, '%d-%m-%Y') AS TransactionDate,
				CASE
					WHEN BO.DueDate = '0000-00-00 00:00:00' 
					THEN ''
					ELSE DATE_FORMAT(BO.DueDate, '%d-%m-%Y')
				END AS DueDate,
				CASE
					WHEN BO.BookingStatusID = 1
					THEN 'Belum Selesai'
					WHEN BO.BookingStatusID = 2
					THEN 'Selesai'
					WHEN BO.BookingStatusID = 3
					THEN 'Jatuh Tempo'
				END BookingStatus,
				CASE
					WHEN BOD.IsPercentage = 1
					THEN IFNULL(SUM(BOD.Quantity * (BOD.SalePrice - ((BOD.SalePrice * BOD.Discount)/100))), 0)
					ELSE IFNULL(SUM(BOD.Quantity * (BOD.SalePrice - BOD.Discount)), 0)
				END AS Total,
				BO.Remarks
			FROM
				transaction_booking BO
				JOIN master_sales MS
					ON BO.SalesID = MS.SalesID
				JOIN master_customer MC
					ON BO.CustomerID = MC.CustomerID
				LEFT JOIN transaction_bookingdetails BOD
					ON BOD.BookingID = BO.BookingID
			WHERE
				$where
			GROUP BY
				BO.BookingID,
				MS.SalesName,
				MC.CustomerName,
				BO.Remarks,
				BO.TransactionDate
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
		$row_array['BookingIDNo']= $row['BookingID']."^".$row['BookingNumber'];
		$row_array['BookingNumber']= $row['BookingNumber'];
		$row_array['BookingID']= $row['BookingID'];
		$row_array['BookingStatus']= $row['BookingStatus'];
		$row_array['SalesName'] = $row['SalesName'];
		$row_array['CustomerName'] = $row['CustomerName'];
		$row_array['Total'] =  number_format($row['Total'],2,".",",");
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['DueDate'] = $row['DueDate'];
		$row_array['Remarks'] = $row['Remarks'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
