<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../DBConfig.php";
	$where = " 1=1 ";
	$order_by = "MI.ItemID";
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
			else $order_by = "MI.ItemID ASC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( MI.ItemName LIKE '%".$search."%' OR MC.CategoryName LIKE '%".$search."%' OR CONCAT(MC.CategoryName, ' ', MI.ItemName) LIKE '%".$search."%' )";
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
				MI.ItemID,
				CONCAT('Stok ', MC.CategoryName, ' ', MI.ItemName, ' : ', IFNULL(SUM(Incoming.Quantity), 0)) AS Remarks
			FROM
				master_item MI
				JOIN master_category MC
					ON MC.CategoryID = MI.CategoryID
				LEFT JOIN
				(
					SELECT
						ITD.ItemID,
						SUM(ITD.Quantity) AS Quantity
					FROM
						transaction_incomingtransactiondetails ITD
					GROUP BY
						ITD.ItemID
					UNION ALL
					SELECT
						OTD.ItemID,
						-SUM(OTD.Quantity) AS Quantity
					FROM
						transaction_outgoingtransactiondetails OTD
					GROUP BY
						OTD.ItemID
					UNION ALL
					SELECT
						RT.ItemID,
						SUM(RT.Quantity) AS Quantity
					FROM
						transaction_returntransaction RT
					GROUP BY
						RT.ItemID
				)Incoming
					ON MI.ItemID = Incoming.ItemID
			WHERE
				$where
			GROUP BY
				MI.ItemID,
				MC.CategoryName,
				MI.ItemName,
				MI.ReminderCount
			HAVING
				IFNULL(SUM(Incoming.Quantity), 0) <= MI.ReminderCount
			ORDER BY 
				$order_by";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$nRows = mysql_num_rows($result);
	$sql .= " ".$limit;
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$return_arr = array();
	$RowNumber = $limit_l;
	while ($row = mysql_fetch_array($result)) {
		$RowNumber++;
		$row_array['RowNumber'] = $RowNumber;
		$row_array['Remarks']= $row['Remarks'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
