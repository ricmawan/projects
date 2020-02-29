<?php
	if(isset($_POST['DoctorID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../DBConfig.php";
		include "../GetSession.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$DoctorID = mysql_real_escape_string($_POST['DoctorID']);
		$BranchID = mysql_real_escape_string($_POST['BranchID']);
		$Message = "";
		$ScheduleDetails = "";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "SELECT
					GROUP_CONCAT(DISTINCT DayOfWeek SEPARATOR ', ') AS dow
				FROM
					master_doctorschedule
				WHERE
					DoctorID = ".$DoctorID."
					AND BranchID = ".$BranchID."
				GROUP BY
					DoctorID";

		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$return_arr = array();
		while ($row = mysql_fetch_array($result)) {
			$row_array['dow']= $row['dow'];
			array_push($return_arr, $row_array);
		}
		$json = json_encode($return_arr);
		echo $json;
	}
?>