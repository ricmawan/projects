<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 AND MUT.UserTypeID = 1 ";
	$order_by = "MU.UserID";
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
			else $order_by = "MU.UserID";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( MU.UserName LIKE '%".$search."%' OR MUT.UserTypeName LIKE '%".$search."%' OR MU.UserLogin LIKE '%".$search."%' OR CASE
																								WHEN MU.IsActive = 0
																								THEN 'Tidak Aktif'
																								ELSE 'Aktif'
																							 END LIKE '%".$search."%'																							 ) ";
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
				master_user MU
				JOIN master_usertype MUT
					ON MU.UserTypeID = MUT.UserTypeID
			WHERE
				$where";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				MU.UserID,
				MU.UserName,
				MU.UserLogin,
				CASE
					WHEN MU.IsActive = 0
					THEN 'Tidak Aktif'
					ELSE 'Aktif'
				END AS Status,
				MU.UserPassword,
				MUT.UserTypeName
			FROM
				master_user MU
				JOIN master_usertype MUT
					ON MU.UserTypeID = MUT.UserTypeID
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
		$row_array['UserIDName'] = $row['UserID']."^".$row['UserName'];
		$row_array['UserID']= $row['UserID'];
		$row_array['Status']= $row['Status'];
		$row_array['UserName'] = $row['UserName'];
		$row_array['UserTypeName'] = $row['UserTypeName'];
		$row_array['UserLogin'] = $row['UserLogin'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
