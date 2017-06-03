<?php
	if(ISSET($_GET['txtFromDate'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		if($_GET['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
		}
		else {
			$txtFromDate = explode('-', mysql_real_escape_string($_GET['txtFromDate']));
			$_GET['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_GET['txtFromDate'];
		}
		$where = " 1=1 ";
		$order_by = "DateNoFormat";
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
				else $order_by = "DateNoFormat";
			}
		}
		//Handles search querystring sent from Bootgrid
		if (ISSET($_REQUEST['searchPhrase']) )
		{
			$search = trim($_REQUEST['searchPhrase']);
			$where .= " AND ( MP.PatientName LIKE '%".$search."%' OR DATE_FORMAT(CS.ScheduledDate, '%Y-%m-%d') LIKE '%".$search."%' OR MP.Email LIKE '%".$search."%' )";
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
					COUNT(1) AS nRows
				FROM
					transaction_checkschedule CS
					JOIN master_patient MP
						ON MP.PatientID = CS.PatientID
				WHERE
					DATE_FORMAT(CS.ScheduledDate, '%Y-%m-%d') = '$txtFromDate'
					AND IFNULL(CS.EmailStatus, '') <> 'Sent'
					AND $where";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$row = mysql_fetch_array($result);
		$nRows = $row['nRows'];
		
		$sql = "SELECT
				CS.CheckScheduleID,
				DATE_FORMAT(CS.ScheduledDate, '%d-%m-%Y') ScheduledDate,
				MP.PatientName,
				MP.Email,
				CS.EmailStatus
			FROM
				transaction_checkschedule CS
				JOIN master_patient MP
					ON MP.PatientID = CS.PatientID
			WHERE
				DATE_FORMAT(CS.ScheduledDate, '%Y-%m-%d') = '$txtFromDate'
				AND IFNULL(CS.EmailStatus, '') <> 'Sent'
				AND $where";
				
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$return_arr = array();
		$RowNumber = $limit_l;
		
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			$row_array['RowNumber'] = $RowNumber;
			$row_array['CheckScheduleID']= $row['CheckScheduleID'];
			$row_array['PatientName']= $row['PatientName'];
			$row_array['ScheduledDate']= $row['ScheduledDate'];
			$row_array['Email']= $row['Email'];
			$row_array['EmailStatus']= $row['EmailStatus'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
