<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "CustomerID";
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
			else $order_by = "CustomerID";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( MS.SalesName LIKE '%".$search."%' OR MC.CustomerName LIKE '%".$search."%' OR MC.City LIKE '%".$search."%' OR MC.Address1 LIKE '%".$search."%' OR MC.Address2 LIKE '%".$search."%' OR MC.Telephone LIKE '%".$search."%' ) ";
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
				master_customer MC
				JOIN master_sales MS
					ON MC.SalesID = MS.SalesID
			WHERE
				$where";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				MC.CustomerID,
				MC.CustomerName,
				MC.Address1,
				MC.Address2,
				MC.City,
				MC.Telephone,
				MS.SalesName
			FROM
				master_customer MC
				JOIN master_sales MS
					ON MC.SalesID = MS.SalesID
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
		$row_array['CustomerIDName'] = $row['CustomerID']."^".$row['CustomerName'];
		$row_array['CustomerID']= $row['CustomerID'];
		$row_array['CustomerName'] = $row['CustomerName'];
		$row_array['SalesName'] = $row['SalesName'];
		$row_array['Address1'] = $row['Address1'];
		$row_array['Address2'] = $row['Address2'];
		$row_array['City'] = $row['City'];
		$row_array['Telephone'] = $row['Telephone'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
