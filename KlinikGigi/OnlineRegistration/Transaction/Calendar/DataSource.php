<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../DBConfig.php";
	
	$StartDate = $_GET['start'];
	$EndDate = $_GET['end'];
	$ddlBranch = $_GET['ddlBranch'];
	$sql = "SELECT
				CSO.ScheduleID,
				CSO.PatientName,
				CSO.ScheduledDate,
				CASE
					WHEN (IFNULL(CN.countNumber, 0) + IFNULL(CNT.countNumber, 0)) < CSO.DailyLimit
					THEN '#3198cb'
					ELSE '#FF0000'
				END BackgroundColor,
				CASE
					WHEN (IFNULL(CN.countNumber, 0) + IFNULL(CNT.countNumber, 0)) < CSO.DailyLimit
					THEN 1
					ELSE 0
				END IsAvailable
			FROM
				(
					SELECT
						OS.OnlineScheduleID ScheduleID,
						OS.PatientName,
						DATE_FORMAT(OS.ScheduledDate, '%Y-%m-%dT%T+07:00') ScheduledDate,
						MB.DailyLimit
					FROM
						transaction_onlineschedule OS
						JOIN master_branch MB
							ON OS.BranchID = MB.BranchID
					WHERE
						OS.ScheduledDate BETWEEN '".$StartDate."' AND '".$EndDate."'
						AND OS.BranchID = $ddlBranch
					UNION ALL
					SELECT
						CS.CheckScheduleID,
						MP.PatientName,
						DATE_FORMAT(CS.ScheduledDate, '%Y-%m-%dT%T+07:00') ScheduledDate,
						$DAILY_SCHEDULE_LIMIT AS DailyLimit
					FROM
						transaction_checkschedule CS
						JOIN master_patient MP
							ON MP.PatientID = CS.PatientID
					WHERE
						CS.ScheduledDate BETWEEN '".$StartDate."' AND '".$EndDate."'
						AND $ddlBranch = 1
				)CSO
				LEFT JOIN
				(
					SELECT
						COUNT(1) countNumber,
						ScheduledDate
					FROM
						transaction_checkschedule
					WHERE
						ScheduledDate BETWEEN '".$StartDate."' AND '".$EndDate."'
						AND $ddlBranch = 1
					GROUP BY
						DATE_FORMAT(ScheduledDate, '%Y-%m-%d')
				)CN
					ON DATE_FORMAT(CN.ScheduledDate, '%Y-%m-%d')  = DATE_FORMAT(CSO.ScheduledDate, '%Y-%m-%d')
				LEFT JOIN
				(
					SELECT
						COUNT(1) countNumber,
						ScheduledDate
					FROM
						transaction_onlineschedule
					WHERE
						ScheduledDate BETWEEN '".$StartDate."' AND '".$EndDate."'
						AND BranchID = $ddlBranch
					GROUP BY
						DATE_FORMAT(ScheduledDate, '%Y-%m-%d')
				)CNT
					ON DATE_FORMAT(CNT.ScheduledDate, '%Y-%m-%d')  = DATE_FORMAT(CSO.ScheduledDate, '%Y-%m-%d')";

	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$return_arr = array();
	while ($row = mysql_fetch_array($result)) {
		$row_array['checkscheduleid']= $row['ScheduleID'];
		$row_array['title']= $row['PatientName'];
		$row_array['start'] = $row['ScheduledDate'];
		$row_array['end'] = $row['ScheduledDate'];
		$row_array['backgroundColor'] = $row['BackgroundColor'];
		$row_array['isavailable'] = $row['IsAvailable'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo $json;
?>
