<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "IT.IncomingTransactionID";
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
			else $order_by = "IT.IncomingTransactionID";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( IT.IncomingTransactionID LIKE '%".$search."%' OR MS.SupplierName LIKE '%".$search."%' OR DATE_FORMAT(IT.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' ) ";
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
				transaction_incomingtransaction IT
				LEFT JOIN master_supplier MS
					ON IT.SupplierID = MS.SupplierID
			WHERE
				$where";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				IT.IncomingTransactionID,
				MS.SupplierName,
				DATE_FORMAT(IT.TransactionDate, '%d-%m-%Y') AS TransactionDate,
				IFNULL(SUM(ITD.Quantity * ITD.Price), 0) AS TotalAmount
			FROM
				transaction_incomingtransaction IT
				LEFT JOIN master_supplier MS
					ON IT.SupplierID = MS.SupplierID
				LEFT JOIN transaction_incomingtransactiondetails ITD
					ON ITD.IncomingTransactionID = IT.IncomingTransactionID
			WHERE
				$where
			GROUP BY
				IT.IncomingTransactionID,
				MS.SupplierName,
				IT.TransactionDate
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
		$row_array['IncomingTransactionID']= $row['IncomingTransactionID'];
		$row_array['SupplierName'] = $row['SupplierName'];
		$row_array['TotalAmount'] =  number_format($row['TotalAmount'],2,".",",");
		$row_array['TransactionDate'] = $row['TransactionDate'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
