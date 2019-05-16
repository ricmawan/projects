<?php
	if(isset($_POST['BranchID']) && isset($_POST['dayOfWeek'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../DBConfig.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$BranchID = mysql_real_escape_string($_POST['BranchID']);
		$DayOfWeek = mysql_real_escape_string($_POST['dayOfWeek']);
		$DoctorID = mysql_real_escape_string($_POST['DoctorID']);
		$Message = "";
		$ScheduleDetails = "";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "SELECT
					BusinessHour
				FROM
					master_doctorschedule
				WHERE
					BranchID = ".$BranchID."
					AND DoctorID = ".$DoctorID."
					AND DayOfWeek = ".$DayOfWeek."
					AND IsAdmin = 0
				ORDER BY
					BusinessHour";

		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$return_arr = array();
		while ($row = mysql_fetch_array($result)) {
			$row_array['BusinessHour']= $row['BusinessHour'];
			array_push($return_arr, $row_array);
		}
		$json = json_encode($return_arr);
		echo $json;
	}
?>