<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../DBConfig.php";
	include "../GetSession.php";
	
	$StartDate = $_GET['start'];
	$EndDate = $_GET['end'];
	$ddlBranch = $_GET['ddlBranch'];
	$ddlDoctor = $_GET['ddlDoctor'];
	$sql = "SELECT
				DATE_FORMAT(TOS.ScheduledDate, '%Y-%m-%d') ScheduledDate,
				CASE
					WHEN IFNULL(TOS.ScheduledCount, 0) = DOW.ScheduleCount
					THEN 'red'
					WHEN IFNULL(TOS.ScheduledCount, 0) > 0
					THEN 'orange'
					ELSE '#69ff8b'
				END BackgroundColor,
				CASE
					WHEN IFNULL(TOS.ScheduledCount, 0) = DOW.ScheduleCount
					THEN 0
					ELSE 1
				END IsAvailable
			FROM
				(
					SELECT
						COUNT(1) ScheduledCount,
						DATE_FORMAT(OS.ScheduledDate, '%Y-%m-%d') ScheduledDate,
						OS.DoctorID,
						OS.BranchID,
						(DAYOFWEEK(OS.ScheduledDate) - 1) DayOfWeek
					FROM
						transaction_onlineschedule OS
					WHERE
						OS.ScheduledDate BETWEEN '".$StartDate."' AND '".$EndDate."'
						AND OS.BranchID = ".$ddlBranch."
						AND OS.DoctorID = ".$ddlDoctor."
					GROUP BY
						DATE_FORMAT(OS.ScheduledDate, '%Y-%m-%d'),
						OS.DoctorID,
						OS.BranchID
				)TOS
				LEFT JOIN
				(
					SELECT
						COUNT(1) ScheduleCount,
						DayOfWeek,
						DoctorID,
						BranchID
					FROM
						master_doctorschedule
					GROUP BY
						DayOfWeek,
						DoctorID,
						BranchID
				)DOW
					ON DOW.DayOfWeek = TOS.DayOfWeek
					AND DOW.DoctorID = TOS.DoctorID
					AND DOW.BranchID = TOS.BranchID";


	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$return_arr = array();
	/*$row_array['start'] = '2020-03-11';
		$row_array['end'] = '2020-03-12';
		$row_array['overlap'] = false;
		$row_array['rendering'] = 'background';
		$row_array['color'] = 'orange';*/
	//array_push($return_arr, $row_array);
	while ($row = mysql_fetch_array($result)) {
		//$row_array['checkscheduleid']= $row['ScheduleID'];
		//$row_array['title']= $row['PatientName'];
		$row_array['start'] = $row['ScheduledDate'];
		$row_array['end'] = $row['ScheduledDate'];
		$row_array['overlap'] = false;
		$row_array['rendering'] = 'background';
		$row_array['color'] = $row['BackgroundColor'];
		$row_array['isAvailable'] = $row['IsAvailable'];
		array_push($return_arr, $row_array);
	}
	
	$json = json_encode($return_arr);
	echo $json;
?>
