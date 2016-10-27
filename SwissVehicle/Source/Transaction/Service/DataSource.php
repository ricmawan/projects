<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 AND CASE
							WHEN '".$_SESSION['UserLogin']."' = 'Admin'
							THEN 1
							WHEN '".$_SESSION['UserLogin']."' = TS.CreatedBy
							THEN 1
							ELSE 0
						END = 1 ";
	$order_by = "TS.ServiceID DESC";
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
			else $order_by = "TS.ServiceID DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND (DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' OR MM.MachineKind LIKE '%".$search."%' OR MM.MachineType LIKE '%".$search."%' OR MM.MachineCode LIKE '%".$search."%' OR MM.BrandName LIKE '%".$search."%' OR TS.WorkshopName LIKE '%".$search."%' OR TS.Kilometer LIKE '%".$search."%' OR TS.Remarks LIKE '%".$search."%') ";
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
				transaction_service TS
				JOIN master_machine MM
					ON MM.MachineID = TS.MachineID
			WHERE
				$where";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				TS.ServiceID,
				DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') AS TransactionDate,
				CASE
					WHEN TS.IsSelfWorkshop = 1
					THEN 'Bengkel Sendiri'
					ELSE TS.WorkshopName
				END WorkshopName,
				TS.Kilometer,
				MM.MachineKind,
				MM.BrandName,
				MM.MachineType,
				MM.MachineCode,
				IFNULL(SUM(SD.Quantity * SD.Price), 0)  AS TotalAmount,
				TS.Remarks
			FROM
				transaction_service TS
				JOIN transaction_servicedetails SD
					ON TS.ServiceID = SD.ServiceID
				JOIN master_machine MM
					ON MM.MachineID = TS.MachineID
			WHERE
				$where
			GROUP BY
				TS.ServiceID,
				TS.TransactionDate,
				MM.MachineKind,
				MM.BrandName,
				MM.MachineType,
				MM.MachineCode,
				TS.Remarks
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
		$row_array['ServiceID']= $row['ServiceID'];
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['MachineKind'] = $row['MachineKind'];
		$row_array['BrandName'] = $row['BrandName'];
		$row_array['MachineType'] = $row['MachineType'];
		$row_array['MachineCode'] = $row['MachineCode'];
		$row_array['Kilometer'] = number_format($row['Kilometer'],0,".",",");
		$row_array['WorkshopName'] = $row['WorkshopName'];
		$row_array['TotalAmount'] =  number_format($row['TotalAmount'],2,".",",");
		$row_array['Remarks'] = $row['Remarks'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
