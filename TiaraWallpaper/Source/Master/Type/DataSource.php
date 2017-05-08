<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "TypeID";
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
			else $order_by = "MI.TypeID ASC";
		}
		$order_by .= ", MI.TypeID ASC";
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( MU.UnitName LIKE '%".$search."%' OR MI.TypeName LIKE '%".$search."%' OR MB.BrandName LIKE '%".$search."%' OR MI.ReminderCount LIKE '%".$search."%' OR MI.BuyPrice LIKE '%".$search."%' OR MI.SalePrice LIKE '%".$search."%' OR CONCAT(MB.BrandName, ' ', MI.TypeName) LIKE '%".$search."%' )";
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
				COUNT(*) AS nRows
			FROM
				master_type MI
				JOIN master_brand MB
					ON MB.BrandID = MI.BrandID
				JOIN master_unit MU
					ON MU.UnitID = MI.UnitID
			WHERE
				$where";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				MI.TypeID,
				MI.TypeName,
				MB.BrandName,
				MI.ReminderCount,
				MI.BuyPrice,
				MI.SalePrice,
				MU.UnitName
			FROM
				master_type MI
				JOIN master_brand MB
					ON MB.BrandID = MI.BrandID
				JOIN master_unit MU
					ON MU.UnitID = MI.UnitID
			WHERE
				$where
			GROUP BY
				MI.TypeID,
				MI.TypeName,
				MB.BrandName,
				MI.ReminderCount,
				MI.BuyPrice,
				MI.SalePrice,
				MU.UnitName
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
		$row_array['TypeIDName'] = $row['TypeID']."^Merek ".$row['BrandName']." tipe ".$row['TypeName'];
		$row_array['TypeID']= $row['TypeID'];
		$row_array['TypeName'] = $row['TypeName'];
		$row_array['BrandName'] = $row['BrandName'];
		$row_array['ReminderCount'] = $row['ReminderCount'];
		//$row_array['Stock'] = $row['Stock'];
		$row_array['UnitName'] = $row['UnitName'];
		$row_array['BuyPrice'] = number_format($row['BuyPrice'],2,".",",");
		$row_array['SalePrice'] = number_format($row['SalePrice'],2,".",",");
		$row_array['SalePrice2'] = number_format((100/110) * $row['SalePrice'],2,".",",");
		array_push($return_arr, $row_array);
	}
	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	
?>
