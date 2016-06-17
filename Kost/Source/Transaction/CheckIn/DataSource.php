<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	//include "../../DBConfig.php";
	$where = " 1=1 ";
	$order_by = "CI.CheckInID DESC";
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
			else $order_by = "CI.CheckInID DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND (DATE_FORMAT(CI.BirthDate, '%d-%m-%Y') LIKE '%".$search."%' OR DATE_FORMAT(CI.StartDate, '%d-%m-%Y %H:%i') LIKE '%".$search."%' OR DATE_FORMAT(CI.EndDate, '%d-%m-%Y %H:%i') LIKE '%".$search."%' OR CI.CustomerName LIKE '%".$search."%' OR CI.Phone LIKE '%".$search."%' OR CI.Address LIKE '%".$search."%') ";
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
				transaction_checkin CI
			WHERE
				$where
				AND CI.CheckOutFlag = 0";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				CI.CheckInID,
				CI.RoomID,
				CI.CustomerName,
				CI.Address,
				CI.Phone,
				DATE_FORMAT(CI.BirthDate, '%d-%m-%Y') BirthDate,
				DATE_FORMAT(CI.StartDate, '%d-%m-%Y %H:%i') StartDate,
				DATE_FORMAT(CI.EndDate, '%d-%m-%Y %H:%i') EndDate
			FROM
				transaction_checkin CI
			WHERE
				$where
				AND CI.CheckOutFlag = 0
			ORDER BY 
				$order_by
			$limit;";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$return_arr = array();
	$RowNumber = $limit_l;
	while ($row = mysql_fetch_array($result)) {
		$RowNumber++;
		$row_array['RowNumber'] = $RowNumber;
		$row_array['CheckInID'] = $row['CheckInID'];
		$row_array['RoomID'] = $row['RoomID'];
		$row_array['CustomerName']= $row['CustomerName'];
		$row_array['Address']= $row['Address'];
		$row_array['Phone']= $row['Phone'];
		$row_array['BirthDate']= $row['BirthDate'];
		$row_array['StartDate']= $row['StartDate'];
		$row_array['EndDate']= $row['EndDate'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
