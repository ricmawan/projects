<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "IT.IncomingID DESC";
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
			else $order_by = "IT.IncomingID DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( IT.Remarks LIKE '%".$search."%' OR IT.IncomingNumber LIKE '%".$search."%' OR MS.SupplierName LIKE '%".$search."%' OR DATE_FORMAT(IT.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' ) ";
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
				LEFT JOIN master_supplier MS
					ON IT.SupplierID = MS.SupplierID
			WHERE
				$where
				AND IT.IsCancelled = 0";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				IT.IncomingID,
				IT.IncomingNumber,
				MS.SupplierName,
				DATE_FORMAT(IT.TransactionDate, '%d-%m-%Y') AS TransactionDate,
				IFNULL(SUM(CASE
								WHEN ITD.IsPercentage = 1
								THEN ITD.Quantity * (ITD.BuyPrice - ((ITD.BuyPrice * ITD.Discount)/100))
								ELSE ITD.Quantity * (ITD.BuyPrice - ITD.Discount)
							END), 0) + IT.DeliveryCost AS TotalAmount,
				IT.DeliveryCost,
				IT.Remarks
			FROM
				transaction_incoming IT
				LEFT JOIN master_supplier MS
					ON IT.SupplierID = MS.SupplierID
				LEFT JOIN transaction_incomingdetails ITD
					ON ITD.IncomingID = IT.IncomingID
			WHERE
				$where
				AND IT.IsCancelled = 0
			GROUP BY
				IT.IncomingID,
				MS.SupplierName,
				IT.TransactionDate,
				IT.Remarks
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
		$row_array['IncomingIDNo']= $row['IncomingID']."^".$row['IncomingNumber'];
		$row_array['IncomingID']= $row['IncomingID'];
		$row_array['IncomingNumber']= $row['IncomingNumber'];
		$row_array['SupplierName'] = $row['SupplierName'];
		$row_array['TotalAmount'] =  number_format($row['TotalAmount'],2,".",",");
		$row_array['DeliveryCost'] =  number_format($row['DeliveryCost'],2,".",",");
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['Remarks'] = $row['Remarks'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
