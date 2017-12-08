<?php
	if(isset($_POST['hdnMedicationID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$MedicationID = mysql_real_escape_string($_POST['hdnMedicationID']);
		$PatientID = mysql_real_escape_string($_POST['hdnPatientID']);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "UPDATE transaction_medication
				SET
					Cash = '".str_replace(",", "", $_POST['txtCash'])."',
					Debit = '".str_replace(",", "", $_POST['txtDebit'])."'
				WHERE
					MedicationID = ".$MedicationID."";
		
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($MedicationID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		
		/*if($_POST['txtNextSchedule'] != "") {
			$Date = explode(', ', mysql_real_escape_string($_POST['txtNextSchedule']));
			$ScheduledDate = explode('-', $Date[1]);
			$ScheduledDate = "$ScheduledDate[2]-$ScheduledDate[1]-$ScheduledDate[0]";
			
			$sql = "UPDATE transaction_checkschedule
					SET
						ScheduledDate = '".$ScheduledDate."',
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						MedicationID = ".$MedicationID."";
			
			if (! $result=mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($MedicationID, $Message, $MessageDetail, $FailedFlag, $State);
				return 0;
			}
			
			$sql = "INSERT INTO transaction_checkschedule
					(
						MedicationID,
						PatientID,
						ScheduledDate,
						CreatedDate,
						CreatedBy
					)
					SELECT
						".$MedicationID.",
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
								CS.MedicationID = ".$MedicationID."
								AND CS.ScheduledDate = '".$ScheduledDate."'
						)";
		
			if (! $result=mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($MedicationID, $Message, $MessageDetail, $FailedFlag, $State);
				return 0;
			}
		}*/
		echo returnstate($MedicationID, $Message, $MessageDetail, $FailedFlag, $State);
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
