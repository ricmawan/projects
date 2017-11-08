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
					DATA.ScheduleID,
					DATA.PatientName,
					DATA.ScheduledDate,
					DATA.PhoneNumber,
					DATA.Email,
					DATA.DailyLimit,
					DATA.BranchName,
					DATA.CreatedDate,
					DATA.ScheduledDatePlain
				FROM
				(
					SELECT
						OS.OnlineScheduleID ScheduleID,
						OS.PatientName,
						DATE_FORMAT(OS.ScheduledDate, '%d-%m-%Y %H:%i:%S') ScheduledDate,
						OS.PhoneNumber,
						OS.Email,
						MB.DailyLimit,
						MB.BranchName,
						OS.CreatedDate,
						OS.ScheduledDate ScheduledDatePlain
					FROM
						transaction_onlineschedule OS
						JOIN master_branch MB
							ON OS.BranchID = MB.BranchID
					WHERE
						DATE_FORMAT(OS.ScheduledDate, '%Y-%m-%d') = '".$StartDate."'
					UNION ALL
					SELECT
						CS.CheckScheduleID,
						MP.PatientName,
						DATE_FORMAT(CS.ScheduledDate, '%d-%m-%Y %H:%i:%S') ScheduledDate,
						MP.Telephone,
						MP.Email,
						$DAILY_SCHEDULE_LIMIT AS DailyLimit,
						'Kawi',
						CS.CreatedDate,
						CS.ScheduledDate
					FROM
						transaction_checkschedule CS
						JOIN master_patient MP
							ON MP.PatientID = CS.PatientID
					WHERE
						DATE_FORMAT(CS.ScheduledDate, '%Y-%m-%d') = '".$StartDate."'
				)DATA
				ORDER BY
					ScheduledDatePlain ASC,
					CreatedDate ASC";
					
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($Message, $MessageDetail, $ScheduleDetails, $FailedFlag, $State);
			return 0;
		}
		$RowNumber = 1;
		while ($row = mysql_fetch_array($result)) {
			$ScheduleDetails .= "<tr>";
			$ScheduleDetails .= "<td align='center' style='width: 35px;' >$RowNumber</td>";
			$ScheduleDetails .= "<td align='left' style='width: 200px;' >".$row['PatientName']."</td>";
			$ScheduleDetails .= "<td align='right' style='width: 200px;' >".$row['PhoneNumber']."</td>";
			$ScheduleDetails .= "<td align='right' style='width: 250px;' >".$row['Email']."</td>";
			$ScheduleDetails .= "<td align='right' style='width: 200px;' >".$row['ScheduledDate']."</td>";
			$ScheduleDetails .= "<td align='right' style='width: 200px;' >".$row['BranchName']."</td>";
			$ScheduleDetails .= "</tr>";
			$RowNumber++;
		}
		echo returnstate($Message, $MessageDetail, $ScheduleDetails, $FailedFlag, $State);
		return 0;
	}
	
	function returnstate($Message, $MessageDetail, $ScheduleDetails, $FailedFlag, $State) {
		$data = array(
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"ScheduleDetails" => $ScheduleDetails,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>