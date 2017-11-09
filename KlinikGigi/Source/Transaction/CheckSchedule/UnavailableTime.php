<?php
	if(isset($_POST['StartDate'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../DBConfig.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$StartDate = $_POST['StartDate'];
		$Message = "";
		$ScheduleDetails = "";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "SELECT
					DATE_FORMAT(OS.ScheduledDate, '%k:%i') unavailableTime,
					COUNT(1)
				FROM
					transaction_onlineschedule OS
				WHERE
					DATE_FORMAT(ScheduledDate, '%Y-%m-%e') = '".$StartDate."'
				GROUP BY
					ScheduledDate
				HAVING
					COUNT(1) > 2";

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