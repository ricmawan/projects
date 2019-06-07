<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	
	if($_GET['Filter'] == "1") {
		if($_GET['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
		}
		else {
			$FromDate = explode(', ', mysql_real_escape_string($_GET['txtFromDate']));
			$txtFromDate = explode('-', $FromDate[1]);
			$_GET['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_GET['txtFromDate'];
		}
		if($_GET['txtToDate'] == "") {
			$txtToDate = date("Y-m-d");
		}
		else {
			$ToDate = explode(', ', mysql_real_escape_string($_GET['txtToDate']));
			$txtToDate = explode('-', $ToDate[1]);
			$_GET['txtToDate'] = "$txtToDate[2]-$txtToDate[1]-$txtToDate[0]"; 
			$txtToDate = $_GET['txtToDate'];
		}
	}
	else {
		$txtToDate = date("Y-m-d");
		$txtFromDate = date("Y-m-d");
	}
	$where = " 1=1 AND CS.ScheduledDate BETWEEN '".$txtFromDate."' AND '".$txtToDate."' ";
	
	
	$order_by = "PatientID";
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
			else $order_by = "PatientID";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( MP.PatientName LIKE '%".$search."%' OR MP.City LIKE '%".$search."%' OR MP.Address LIKE '%".$search."%' OR MP.Telephone LIKE '%".$search."%' OR MP.Allergy LIKE '%".$search."%' ) ";
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
				transaction_checkschedule CS
				JOIN master_patient MP
					ON MP.PatientID = CS.PatientID
			WHERE
				$where";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				MP.PatientID,
				MP.PatientNumber,
				MP.PatientName,
				DATE_FORMAT(MP.BirthDate, '%d-%m-%Y') BirthDate,
				DATE_FORMAT(CS.ScheduledDate, '%d-%m-%Y') ScheduledDate,
				MP.Address,
				MP.City,
				MP.Telephone,
				MP.Allergy
			FROM
				transaction_checkschedule CS
				JOIN master_patient MP
					ON MP.PatientID = CS.PatientID
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
		$row_array['PatientNumber']= $row['PatientNumber'];
		$row_array['PatientName'] = $row['PatientName'];
		$row_array['BirthDate'] = $row['BirthDate'];
		$row_array['ScheduledDate'] = $row['ScheduledDate'];
		$row_array['Address'] = $row['Address'];
		$row_array['City'] = $row['City'];
		$row_array['Telephone'] = $row['Telephone'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
