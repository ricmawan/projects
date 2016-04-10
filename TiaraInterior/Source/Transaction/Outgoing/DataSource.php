<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 AND OT.IsCancelled = 0 ";
	$order_by = "OT.OutgoingID DESC";
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
			else $order_by = "OT.OutgoingID DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( OT.OutgoingNumber LIKE '%".$search."%' OR OT.Remarks LIKE '%".$search."%' OR MC.CustomerName LIKE '%".$search."%' OR MS.SalesName LIKE '%".$search."%' OR DATE_FORMAT(OT.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' ) ";
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
				transaction_outgoing OT
				JOIN master_sales MS
					ON OT.SalesID = MS.SalesID
				JOIN master_customer MC
					ON OT.CustomerID = MC.CustomerID
			WHERE
				$where";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				OT.OutgoingID,
				OT.OutgoingNumber,
				MS.SalesName,
				MC.CustomerName,
				DATE_FORMAT(OT.TransactionDate, '%d-%m-%Y') AS TransactionDate,
				CASE
					WHEN OTD.IsPercentage = 1
					THEN IFNULL(SUM(OTD.Quantity * (OTD.SalePrice - ((OTD.SalePrice * OTD.Discount)/100))), 0)
					ELSE IFNULL(SUM(OTD.Quantity * (OTD.SalePrice - OTD.Discount)), 0)
				END AS SubTotal,
				CASE
					WHEN OTD.IsPercentage = 1
					THEN IFNULL(SUM(OTD.Quantity * (OTD.SalePrice - ((OTD.SalePrice * OTD.Discount)/100))), 0)
					ELSE IFNULL(SUM(OTD.Quantity * (OTD.SalePrice - OTD.Discount)), 0)
				END + OT.DeliveryCost AS Total,
				OT.DeliveryCost,
				OT.Remarks
			FROM
				transaction_outgoing OT
				JOIN master_sales MS
					ON OT.SalesID = MS.SalesID
				JOIN master_customer MC
					ON OT.CustomerID = MC.CustomerID
				LEFT JOIN transaction_outgoingdetails OTD
					ON OTD.OutgoingID = OT.OutgoingID
			WHERE
				$where
			GROUP BY
				OT.OutgoingID,
				MS.SalesName,
				MC.CustomerName,
				OT.Remarks,
				OT.TransactionDate
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
		$row_array['OutgoingIDNo']= $row['OutgoingID']."^".$row['OutgoingNumber'];
		$row_array['OutgoingID']= $row['OutgoingID'];
		$row_array['OutgoingNumber']= $row['OutgoingNumber'];
		$row_array['SalesName'] = $row['SalesName'];
		$row_array['CustomerName'] = $row['CustomerName'];
		$row_array['DeliveryCost'] =  number_format($row['DeliveryCost'],2,".",",");
		$row_array['SubTotal'] =  number_format($row['SubTotal'],2,".",",");
		$row_array['Total'] =  number_format($row['Total'],2,".",",");
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['Remarks'] = $row['Remarks'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
