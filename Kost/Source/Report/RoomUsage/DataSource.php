<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	date_default_timezone_set("Asia/Jakarta");
	$rdInterval = mysql_real_escape_string($_GET['rdInterval']);
	$ddlMonth = mysql_real_escape_string($_GET['ddlMonth']);
	$ddlYear = mysql_real_escape_string($_GET['ddlYear']);
	$RoomID = mysql_real_escape_string($_GET['ddlRoom']);
	if($RoomID == "") $RoomID = 0;
	if($_GET['txtStartDate'] == "" && $rdInterval == "Daily") {
		$txtStartDate = "2000-01-01";
	}
	else if($rdInterval == "Daily") {
		$txtStartDate = explode('-', mysql_real_escape_string($_GET['txtStartDate']));
		$_GET['txtStartDate'] = "$txtStartDate[2]-$txtStartDate[1]-$txtStartDate[0]"; 
		$txtStartDate = $_GET['txtStartDate'];
	}
	else {
		$txtStartDate = $ddlYear."-".$ddlMonth."-01";
	}
	if($_GET['txtEndDate'] == "" && $rdInterval == "Daily") {
		$txtEndDate = date("Y-m-d");
	}
	else if($rdInterval == "Daily") {
		$txtEndDate = explode('-', mysql_real_escape_string($_GET['txtEndDate']));
		$_GET['txtEndDate'] = "$txtEndDate[2]-$txtEndDate[1]-$txtEndDate[0]"; 
		$txtEndDate = $_GET['txtEndDate'];
	}
	else {
		$txtEndDate = date('Y-m-t', strtotime($txtStartDate));
	}
	
	//echo $txtStartDate;
	//echo $txtEndDate;
	$where = " 1=1 ";
	$order_by = "";
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
			else $order_by = "DATE_FORMAT(TO.TransactionDate, '%d-%m-%Y') ASC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		//$where .= " AND ( DATE_FORMAT(DATA.TransactionDate, '%d%b%y') LIKE '%".$search."%' OR DATA.Name LIKE '%".$search."%' OR DATA.ItemName LIKE '%".$search."%' OR DATA.Quantity LIKE '%".$search."%' OR DATA.UnitName LIKE '%".$search."%' OR DATA.Price LIKE '%".$search."%' OR DATA.Debit LIKE '%".$search."%' OR DATA.Credit LIKE '%".$search."%' OR DATA.Remarks LIKE '%".$search."%' )";
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
	mysql_query("SET @row:=0;", $dbh);
	
	$sql = "SELECT
				DATE_FORMAT(CI.TransactionDate, '%d-%m-%Y') TransactionDate,
				R.RoomNumber,
				CASE
					WHEN CI.RateType = 'Daily'
					THEN CONCAT(DATEDIFF(CI.EndDate, CI.StartDate), ' hari')
					ELSE CONCAT(TIMESTAMPDIFF(HOUR, CI.EndDate, CI.StartDate), ' jam')
				END Duration,
				DATE_FORMAT(CI.StartDate, '%d-%m-%Y %H:%i') StartDate,
				DATE_FORMAT(CI.EndDate, '%d-%m-%Y %H:%i') EndDate,
				CASE
					WHEN CI.RateType = 'Daily'
					THEN DATEDIFF(CI.EndDate, CI.StartDate) * CI.DailyRate
					ELSE TIMESTAMPDIFF(HOUR, CI.EndDate, CI.StartDate) * CI.HourlyRate
				END Total,
				CI.DownPaymentAmount + CI.PaymentAmount Payment
			FROM
				transaction_checkin CI
				JOIN master_room R
					ON R.RoomID = CI.RoomID
			WHERE
				CAST(CI.TransactionDate AS DATE) >= '".$txtStartDate."'
				AND CAST(CI.TransactionDate AS DATE) <= '".$txtEndDate."'
				AND CASE
						WHEN $RoomID = 0
						THEN R.RoomID
						ELSE $RoomID
					END = R.RoomID
			ORDER BY
				TransactionDate ASC";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$nRows = mysql_num_rows($result);		
	$return_arr = array();
	//$nRows = mysql_num_rows($result);
	$RowNumber = 0;
	while ($row = mysql_fetch_array($result)) {
		$RowNumber++;
		$row_array['RowNumber'] = $RowNumber;
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['RoomNumber'] = $row['RoomNumber'];
		$row_array['Duration'] = $row['Duration'];
		$row_array['StartDate'] = $row['StartDate'];
		$row_array['EndDate'] = $row['EndDate'];
		$row_array['Total'] = number_format($row['Total'],2,".",",");
		$row_array['Payment'] = number_format($row['Payment'],2,".",",");
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
