<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	
	$StartDate = $_GET['start'];
	$EndDate = $_GET['end'];
	$sql = "SELECT
				CS.CheckScheduleID,
				MP.PatientName,
				DATE_FORMAT(CS.ScheduledDate, '%Y-%m-%dT%T+07:00') ScheduledDate,
				MP.Telephone,
				CASE
					WHEN CN.CountNumber < $DAILY_SCHEDULE_LIMIT
					THEN '#3198cb'
					ELSE '#FF0000'
				END BackgroundColor,
				CASE
					WHEN CN.CountNumber < $DAILY_SCHEDULE_LIMIT
					THEN 1
					ELSE 0
				END IsAvailable
			FROM
				transaction_checkschedule CS
				JOIN master_patient MP
					ON MP.PatientID = CS.PatientID
				LEFT JOIN
				(
					SELECT
						COUNT(1) countNumber,
						ScheduledDate
					FROM
						transaction_checkschedule
					WHERE
						ScheduledDate BETWEEN '".$StartDate."' AND '".$EndDate."'
					GROUP BY
						DATE_FORMAT(ScheduledDate, '%Y-%m-%d')
				)CN
					ON DATE_FORMAT(CN.ScheduledDate, '%Y-%m-%d')  = DATE_FORMAT(CS.ScheduledDate, '%Y-%m-%d')
			WHERE
				CS.ScheduledDate BETWEEN '".$StartDate."' AND '".$EndDate."' 
			UNION ALL
			SELECT
				OS.OnlineScheduleID,
				OS.PatientName,
				DATE_FORMAT(OS.ScheduledDate, '%Y-%m-%dT%T+07:00') ScheduledDate,
				OS.PhoneNumber,
				CASE
					WHEN CNT.CountNumber < $DAILY_SCHEDULE_LIMIT
					THEN '#3198cb'
					ELSE '#FF0000'
				END BackgroundColor,
				CASE
					WHEN CNT.CountNumber < $DAILY_SCHEDULE_LIMIT
					THEN 1
					ELSE 0
				END IsAvailable
			FROM
				transaction_onlineschedule OS
				LEFT JOIN
				(
					SELECT
						COUNT(1) countNumber,
						ScheduledDate
					FROM
						transaction_onlineschedule
					WHERE
						ScheduledDate BETWEEN '".$StartDate."' AND '".$EndDate."'
					GROUP BY
						DATE_FORMAT(ScheduledDate, '%Y-%m-%d')
				)CNT
					ON DATE_FORMAT(CNT.ScheduledDate, '%Y-%m-%d')  = DATE_FORMAT(OS.ScheduledDate, '%Y-%m-%d')
			WHERE
				OS.ScheduledDate BETWEEN '".$StartDate."' AND '".$EndDate."' ";
				
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$return_arr = array();
	while ($row = mysql_fetch_array($result)) {
		$row_array['checkscheduleid']= $row['CheckScheduleID'];
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
