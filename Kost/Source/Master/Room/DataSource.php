<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "RoomID";
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
			else $order_by = "RoomID";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( MR.RoomNumber LIKE '%".$search."%' OR MR.DailyRate LIKE '%".$search."%' OR MR.HourlyRate LIKE '%".$search."%' OR MS.StatusName LIKE '%".$search."%' )";
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
				master_room MR
				JOIN master_status MS
					ON MR.StatusID = MS.StatusID
			WHERE
				$where";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				MR.RoomID,
				MR.RoomNumber,
				MR.DailyRate,
				MR.HourlyRate,
				MS.StatusName,
				MR.RoomInfo
			FROM
				master_room MR
				JOIN master_status MS
					ON MR.StatusID = MS.StatusID
			WHERE
				$where
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
		$row_array['RoomIDName'] = $row['RoomID']."^".$row['RoomNumber'];
		$row_array['RoomID']= $row['RoomID'];
		$row_array['Status']= $row['StatusName'];
		$row_array['HourlyRate'] = number_format($row['HourlyRate'],2,".",",");
		$row_array['DailyRate'] = number_format($row['DailyRate'],2,".",",");
		$row_array['RoomInfo'] = $row['RoomInfo'];
		$row_array['RoomNumber'] = $row['RoomNumber'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
