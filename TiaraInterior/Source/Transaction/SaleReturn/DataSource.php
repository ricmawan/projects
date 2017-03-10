<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "SR.SaleReturnID DESC";
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
			else $order_by = "SR.SaleReturnID DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( SR.Remarks LIKE '%".$search."%' OR RIGHT(SR.SaleReturnNumber, 3) LIKE '%".$search."%' OR MC.CustomerName LIKE '%".$search."%' OR DATE_FORMAT(SR.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' ) ";
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
				transaction_salereturn SR
				LEFT JOIN master_customer MC
					ON SR.CustomerID = MC.CustomerID
			WHERE
				$where
				AND SR.IsCancelled = 0";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				SR.SaleReturnID,
				MC.CustomerName,
				DATE_FORMAT(SR.TransactionDate, '%d-%m-%Y') AS TransactionDate,
				IFNULL(SUM(CASE
								WHEN SRD.IsPercentage = 1
								THEN SRD.Quantity * (SRD.SalePrice - ((SRD.SalePrice * SRD.Discount)/100))
								ELSE SRD.Quantity * (SRD.SalePrice - SRD.Discount)
							END), 0) AS TotalAmount,
				SR.Remarks,
				SR.SaleReturnNumber
			FROM
				transaction_salereturn SR
				LEFT JOIN master_customer MC
					ON SR.CustomerID = MC.CustomerID
				LEFT JOIN transaction_salereturndetails SRD
					ON SRD.SaleReturnID = SR.SaleReturnID
			WHERE
				$where
				AND SR.IsCancelled = 0
			GROUP BY
				SR.SaleReturnID,
				MC.CustomerName,
				SR.TransactionDate,
				SR.Remarks,
				SR.SaleReturnNumber
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
		$row_array['SaleReturnIDNo'] = $row['SaleReturnID']."^".$row['SaleReturnNumber'];
		$row_array['SaleReturnID'] = $row['SaleReturnID'];
		$row_array['SaleReturnNumber'] = $row['SaleReturnNumber'];
		$row_array['CustomerName'] = $row['CustomerName'];
		$row_array['TotalAmount'] =  number_format($row['TotalAmount'],2,".",",");
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['Remarks'] = $row['Remarks'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
