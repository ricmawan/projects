<?php
	if(isset($_POST['StartDate'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../DBConfig.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$StartDate = $_POST['StartDate'];
		$Message = "";
		$ScheduleDetails = "";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$BranchID = mysql_real_escape_string($_POST['BranchID']);
		$DoctorID = mysql_real_escape_string($_POST['DoctorID']);
		$DayOfWeek = mysql_real_escape_string($_POST['dayOfWeek']);
		
		$sql = "SELECT
					DATE_FORMAT(OS.ScheduledDate, '%k:%i') unavailableTime,
					COUNT(1)
				FROM
					transaction_onlineschedule OS
				WHERE
					DATE_FORMAT(OS.ScheduledDate, '%Y-%c-%e') = '".$StartDate."'
					AND OS.DoctorID = ".$DoctorID."
				GROUP BY
					OS.ScheduledDate
				/*HAVING
					COUNT(1) >= $MINUTE_SCHEDULE_LIMIT*/
				UNION ALL
				SELECT
					BusinessHour unavailableTime,
					1
				FROM
					master_exceptionschedule
				WHERE
					DayOfWeek = ".$DayOfWeek."
					AND BranchID = ".$BranchID;

		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$return_arr = array();
		while ($row = mysql_fetch_array($result)) {
			$row_array['unavailableTime']= $row['unavailableTime'];
			array_push($return_arr, $row_array);
		}
		$json = json_encode($return_arr);
		echo $json;
	}
?>