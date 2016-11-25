<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1  AND CASE
							WHEN '".$_SESSION['UserLogin']."' = 'Admin'
							THEN 1
							WHEN '".$_SESSION['UserLogin']."' = MI.CreatedBy
							THEN 1
							ELSE 0
						END = 1 ";
	$order_by = "MI.ItemName ASC";
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
			else $order_by = "MI.ItemName ASC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( MI.ItemName LIKE '%".$search."%' OR RC.ReportCategoryName LIKE '%".$search."%' OR IF(IsSecond = 0, 'Tidak', 'Ya') LIKE '%".$search."%' OR MI.ItemCode LIKE '%".$search."%' OR MI.Price LIKE '%".$search."%' )";
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
				master_item MI
				LEFT JOIN master_reportcategory RC
					ON RC.ReportCategoryID = MI.ReportCategoryID
			WHERE
				$where";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				MI.ItemID,
				MI.ItemName,
				MI.ItemCode,
				MI.Price,
				RC.ReportCategoryName,
				IF(IsSecond = 0, 'Tidak', 'Ya') IsSecond
			FROM
				master_item MI
				LEFT JOIN master_reportcategory RC
					ON RC.ReportCategoryID = MI.ReportCategoryID
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
		$row_array['ItemIDName'] = $row['ItemID']."^".$row['ItemName'];
		$row_array['ItemID'] = $row['ItemID'];
		$row_array['ItemName'] = $row['ItemName'];
		$row_array['ItemCode'] = $row['ItemCode'];
		$row_array['IsSecond'] = $row['IsSecond'];
		$row_array['ReportCategoryName'] = $row['ReportCategoryName'];
		$row_array['Price'] = number_format($row['Price'],2,".",",");
		array_push($return_arr, $row_array);
	}
	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	
?>
