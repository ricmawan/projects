<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "OT.OutgoingTransactionID";
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
			else $order_by = "OT.OutgoingTransactionID";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( OT.OutgoingTransactionID LIKE '%".$search."%' OR MP.ProjectName LIKE '%".$search."%' OR DATE_FORMAT(OT.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' ) ";
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
				transaction_outgoingtransaction OT
				JOIN master_project MP
					ON OT.ProjectID = MP.ProjectID
			WHERE
				$where
				AND MP.IsDone = 0";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				OT.OutgoingTransactionID,
				MP.ProjectName,
				DATE_FORMAT(OT.TransactionDate, '%d-%m-%Y') AS TransactionDate,
				IFNULL(SUM(OTD.Quantity * OTD.Price), 0) AS TotalAmount
			FROM
				transaction_outgoingtransaction OT
				JOIN master_project MP
					ON OT.ProjectID = MP.ProjectID
				LEFT JOIN transaction_outgoingtransactiondetails OTD
					ON OTD.OutgoingTransactionID = OT.OutgoingTransactionID
			WHERE
				$where
				AND MP.IsDone = 0
			GROUP BY
				OT.OutgoingTransactionID,
				MP.ProjectName,
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
		$row_array['OutgoingTransactionID']= $row['OutgoingTransactionID'];
		$row_array['ProjectName'] = $row['ProjectName'];
		$row_array['TotalAmount'] =  number_format($row['TotalAmount'],2,".",",");
		$row_array['TransactionDate'] = $row['TransactionDate'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
