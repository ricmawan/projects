<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 AND CASE
							WHEN '".$_SESSION['UserLogin']."' = 'Admin'
							THEN 1
							WHEN '".$_SESSION['UserLogin']."' = LE.CreatedBy
							THEN 1
							ELSE 0
						END = 1 ";
	$order_by = "LE.LicenseExtensionID DESC";
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
			else $order_by = "LE.LicenseExtensionID DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND (DATE_FORMAT(LE.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' OR DATE_FORMAT(LE.DueDate, '%d-%m-%Y') LIKE '%".$search."%' OR MM.MachineKind LIKE '%".$search."%' OR MM.MachineType LIKE '%".$search."%' OR MM.MachineCode LIKE '%".$search."%' OR MM.BrandName LIKE '%".$search."%' OR LE.Remarks LIKE '%".$search."%' )";
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
				transaction_licenseextension LE
				JOIN master_machine MM
					ON MM.MachineID = LE.MachineID
			WHERE
				$where";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				LE.LicenseExtensionID,
				DATE_FORMAT(LE.TransactionDate, '%d-%m-%Y') AS TransactionDate,
				DATE_FORMAT(LE.DueDate, '%d-%m-%Y') AS DueDate,
				MM.MachineKind,
				MM.BrandName,
				MM.MachineType,
				MM.MachineCode,
				LE.Remarks,
				LE.IsExtended,
				DATE_FORMAT(LE.ExtensionDate, '%d-%m-%Y') ExtensionDate,
				LE.ExtensionCost
			FROM
				transaction_licenseextension LE
				JOIN master_machine MM
					ON MM.MachineID = LE.MachineID
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
		$row_array['LicenseExtensionID']= $row['LicenseExtensionID'];
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['DueDate'] = $row['DueDate'];
		$row_array['MachineKind'] = $row['MachineKind'];
		$row_array['IsExtended'] = $row['IsExtended'];
		$row_array['BrandName'] = $row['BrandName'];
		$row_array['MachineType'] = $row['MachineType'];
		$row_array['MachineCode'] = $row['MachineCode'];
		$row_array['ExtensionDate'] = $row['ExtensionDate'];
		$row_array['ExtensionCost'] = number_format($row['ExtensionCost'],2,".",",");
		$row_array['Remarks'] = $row['Remarks'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
