<?php
	if(ISSET($_GET['Status'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../DBConfig.php";
		date_default_timezone_set("Asia/Jakarta");
		$Status = mysql_real_escape_string($_GET['Status']);
		if($_GET['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
		}
		else {
			$txtFromDate = explode('-', mysql_real_escape_string($_GET['txtFromDate']));
			$_GET['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_GET['txtFromDate'];
		}
		if($_GET['txtToDate'] == "") {
			$txtToDate = date("Y-m-d");
		}
		else {
			$txtToDate = explode('-', mysql_real_escape_string($_GET['txtToDate']));
			$_GET['txtToDate'] = "$txtToDate[2]-$txtToDate[1]-$txtToDate[0]"; 
			$txtToDate = $_GET['txtToDate'];
		}

		$sql = "SELECT
					MB.BranchName,
					MU.UserName DoctorName,
					TOS.PatientName,
					DATE_FORMAT(TOS.ScheduledDate, '%d-%m-%Y') ScheduledDate,
					CASE
						WHEN TOS.CustomerConfirmation = 'Y'
						THEN 'Hadir'
						WHEN TOS.CustomerConfirmation = 'N'
						THEN 'Batal'
						ELSE 'Tidak Konfirmasi'
					END CustomerConfirmation
				FROM
					transaction_onlineschedule TOS
					JOIN master_branch MB
						ON MB.BranchID = TOS.BranchID
					JOIN master_user MU
						ON MU.UserID = TOS.DoctorID
				WHERE
					CASE
						WHEN '".$Status."' = 'A'
						THEN TOS.CustomerConfirmation
						ELSE '".$Status."'
					END = IFNULL(TOS.CustomerConfirmation, '')
					AND CAST(TOS.ScheduledDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TOS.ScheduledDate AS DATE) <= '".$txtToDate."'
					AND TOS.CustomerSelfRegFlag = 1
				ORDER BY 
					TOS.ScheduledDate";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$nRows = mysql_num_rows($result);		
		$return_arr = array();
		$RowNumber = 0;
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			$row_array['RowNumber'] = $RowNumber;
			$row_array['BranchName'] = $row['BranchName'];
			$row_array['DoctorName'] = $row['DoctorName'];
			$row_array['PatientName'] = $row['PatientName'];
			$row_array['ScheduledDate'] = $row['ScheduledDate'];
			$row_array['CustomerConfirmation'] = $row['CustomerConfirmation'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
