<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1  AND CASE
							WHEN '".$_SESSION['UserLogin']."' = 'Admin'
							THEN 1
							WHEN '".$_SESSION['UserLogin']."' = MM.CreatedBy
							THEN 1
							ELSE 0
						END = 1 ";
	$order_by = "MM.MachineID DESC";
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
			else $order_by = "MM.MachineID DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( MM.MachineKind LIKE '%".$search."%' OR MM.MachineYear LIKE '%".$search."%' OR MM.MachineType LIKE '%".$search."%' OR MM.MachineCode LIKE '%".$search."%' OR MM.BrandName LIKE '%".$search."%' OR MM.Remarks LIKE '%".$search."%')";
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
	//echo "$limit_l $limit_h";
	$sql = "SELECT
				COUNT(1) AS nRows
			FROM
				master_machine MM
			WHERE
				$where";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				MM.MachineID,
				MM.MachineKind,
				MM.MachineType,
				MM.MachineYear,
				MM.MachineCode,
				MM.BrandName,
				MM.Remarks
			FROM
				master_machine MM
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
		$row_array['MachineIDCode'] = $row['MachineID']."^".$row['MachineCode'];
		$row_array['MachineID'] = $row['MachineID'];
		$row_array['MachineKind'] = $row['MachineKind'];
		$row_array['MachineType'] = $row['MachineType'];
		$row_array['MachineYear'] = $row['MachineYear'];
		$row_array['MachineCode'] = $row['MachineCode'];
		$row_array['BrandName'] = $row['BrandName'];
		$row_array['Remarks'] = $row['Remarks'];
		array_push($return_arr, $row_array);
	}
	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	
?>
