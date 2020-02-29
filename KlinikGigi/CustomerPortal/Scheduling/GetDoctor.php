<?php
	if(isset($_POST['BranchID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../DBConfig.php";
		include "../GetSession.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$BranchID = mysql_real_escape_string($_POST['BranchID']);
		$Message = "";
		$ScheduleDetails = "";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "SELECT
					MDS.DoctorID,
					MU.UserName DoctorName
				FROM
					master_doctorschedule MDS
					JOIN master_user MU
						ON MU.UserID = MDS.DoctorID
				WHERE
					MDS.BranchID = ".$BranchID."
				GROUP BY
					MDS.DoctorID
				ORDER BY
					MU.UserName";

		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$return_arr = array();
		while ($row = mysql_fetch_array($result)) {
			$row_array['DoctorID']= $row['DoctorID'];
			$row_array['DoctorName']= $row['DoctorName'];
			array_push($return_arr, $row_array);
		}
		$json = json_encode($return_arr);
		echo $json;
	}
?>