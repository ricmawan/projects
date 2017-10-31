<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 AND TM.IsDone = 1 AND TM.IsCancelled = 0 AND DATE_FORMAT(TM.TransactionDate, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y') ";
	$order_by = "TM.OrderNumber";
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
			else $order_by = "TM.OrderNumber";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( TM.OrderNumber LIKE '%".$search."%' OR MP.Allergy LIKE '%".$search."%' OR MP.PatientName LIKE '%".$search."%' OR MP.PatientNumber LIKE '%".$search."%' ) ";
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
				transaction_medication TM
				JOIN master_patient MP
					ON MP.PatientID = TM.PatientID
			WHERE
				$where";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				TM.MedicationID,
				TM.OrderNumber,
				MP.PatientNumber,
				TM.PatientID,
				MP.PatientName,
				MP.Allergy,
				IFNULL(SUM(MD.Total), 0) + SUM(TMD.Price * TMD.Quantity) Total
			FROM
				transaction_medication TM
				JOIN master_patient MP
					ON MP.PatientID = TM.PatientID
				LEFT JOIN transaction_medicationdetails TMD
					ON TM.MedicationID = TMD.MedicationID
				LEFT JOIN
				(
					SELECT
						MD.MedicationDetailsID,
						MD.SessionID,
						SUM(MD.SalePrice * MD.Quantity) Total
					FROM
						transaction_materialdetails MD
					GROUP BY
						MD.MedicationDetailsID
				)MD
					ON MD.MedicationDetailsID = TMD.MedicationDetailsID
			WHERE
				$where
			GROUP BY
				TM.MedicationID,
				TM.OrderNumber,
				MP.PatientNumber,
				MP.PatientName,
				MP.Allergy
			ORDER BY
				$order_by
			$limit";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$return_arr = array();
	while ($row = mysql_fetch_array($result)) {
		$row_array['OrderNumber'] = $row['OrderNumber'];
		$row_array['PatientNumber'] = $row['PatientNumber'];
		$row_array['PatientID'] = $row['PatientID'];
		$row_array['MedicationID'] = $row['MedicationID'];
		$row_array['PatientName'] = $row['PatientName'];
		$row_array['Allergy'] = $row['Allergy'];
		$row_array['Total'] = number_format($row['Total'],2,".",",");
		$row_array['MedicationIDNo']= $row['MedicationID']."^".$row['OrderNumber'];
		
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
