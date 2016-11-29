<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "ML.MenuName";
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
			else $order_by = "ML.MenuName";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( ML.MenuName LIKE '%".$search."%' OR MLC.MenuListCategoryName LIKE '%".$search."%' OR ML.Price LIKE '%".$search."%' ) ";
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
				master_menulist ML
				JOIN master_menulistcategory MLC
					ON MLC.MenuListCategoryID = ML.MenuListCategoryID
			WHERE
				$where";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				ML.MenuListID,
				ML.MenuListCategoryID,
				ML.MenuName,
				MLC.MenuListCategoryName,
				ML.Price
			FROM
				master_menulist ML
				JOIN master_menulistcategory MLC
					ON MLC.MenuListCategoryID = ML.MenuListCategoryID
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
		$row_array['MenuListIDName'] = $row['MenuListID']."^".$row['MenuName'];
		$row_array['MenuListID']= $row['MenuListID'];
		$row_array['MenuName'] = $row['MenuName'];
		$row_array['MenuListCategoryName'] = $row['MenuListCategoryName'];
		$row_array['Price'] = number_format($row['Price'],2,".",",");
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
