<?php
	if(isset($_POST['ddlPatient'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$PatientID = mysql_real_escape_string($_POST['ddlPatient']);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$Date = explode(', ', mysql_real_escape_string($_POST['txtNextSchedule']));
		$ScheduledDate = explode('-', $Date[1]);
		$ScheduledDate = "$ScheduledDate[2]-$ScheduledDate[1]-$ScheduledDate[0]";
			
		$sql = "INSERT INTO transaction_checkschedule
				(
					PatientID,
					ScheduledDate,
					CreatedDate,
					CreatedBy
				)
				SELECT
					".$PatientID.",
					'".$ScheduledDate."',
					NOW(),
					'".$_SESSION['UserLogin']."'
				FROM
					tbl_temp
				WHERE
					NOT EXISTS
					(
						SELECT
							1
						FROM
							transaction_checkschedule CS
						WHERE
							CS.PatientID = ".$PatientID."
							AND CS.ScheduledDate = '".$ScheduledDate."'
					)";
	
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($PatientID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		
		echo returnstate($PatientID, $Message, $MessageDetail, $FailedFlag, $State);
	}
	
	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
