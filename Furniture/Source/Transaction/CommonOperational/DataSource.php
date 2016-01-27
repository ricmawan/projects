<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "CommonOperationalID";
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
			else $order_by = "CommonOperationalID";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( CO.CommonOperationalID LIKE '%".$search."%' OR DATE_FORMAT(CO.CommonOperationalDate, '%d-%m-%Y') LIKE '%".$search."%' ) ";
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
				transaction_commonoperational CO
			WHERE
				$where";
				
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				CO.CommonOperationalID,
				DATE_FORMAT(CO.CommonOperationalDate, '%d-%m-%Y') AS TransactionDate,
				COD.Amount AS TotalAmount
			FROM
				transaction_commonoperational CO
				LEFT JOIN 
				(
					SELECT
						COD.CommonOperationalID,
						IFNULL(SUM(COD.Amount), 0) AS Amount
					FROM
						transaction_commonoperationaldetails COD
					GROUP BY
						COD.CommonOperationalID
				)COD
					ON COD.CommonOperationalID = CO.CommonOperationalID
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
		$row_array['CommonOperationalID'] = $row['CommonOperationalID'];
		$row_array['TransactionDate']= $row['TransactionDate'];
		$row_array['TotalAmount'] =  number_format($row['TotalAmount'],2,".",",");
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
