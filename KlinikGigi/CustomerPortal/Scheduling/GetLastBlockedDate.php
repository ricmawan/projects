<?php
	if(isset($_POST['PatientName'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../DBConfig.php";
		include "../GetSession.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$PatientName = mysql_real_escape_string($_POST['PatientName']);
		$Message = "";
		$ScheduleDetails = "";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "SELECT
					DATE_FORMAT(TOS.ScheduledDate, '%Y-%m-%d') CancelledDate,
					DATE_FORMAT(DATE_ADD(TOS.ScheduledDate, INTERVAL 14 DAY), '%Y-%m-%d') LastBlockedDate
				FROM
					transaction_onlineschedule TOS
					JOIN master_patient MP
						ON MP.PatientName = TOS.PatientName
				WHERE
					TOS.PatientName = '".$PatientName."'
					AND CustomerSelfRegFlag = 1
					AND CustomerConfirmation = 'N'";

		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}

		$return_arr = array();

		$cek = mysql_num_rows($result);
		if($cek == 0) {
			$row_array['CancelledDate'] = date("Y-m-d");
			$row_array['LastBlockedDate'] = date("Y-m-d");
		}

		while ($row = mysql_fetch_array($result)) {
			$row_array['CancelledDate']= $row['CancelledDate'];
			$row_array['LastBlockedDate']= $row['LastBlockedDate'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo $json;
	}
?>