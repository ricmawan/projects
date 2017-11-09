<?php
	if(isset($_POST['ddlPatient'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ddlPatient = mysql_real_escape_string($_POST['ddlPatient']);
		$ddlTime = mysql_real_escape_string($_POST['ddlTime']);
		$ddlBranch = mysql_real_escape_string($_POST['hdnDDLBranch']);
		$ScheduledDate = $_POST['hdnStartDate'] . " " . $ddlTime;
		//echo $ScheduledDate;
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;		
		$State = 1;
		/*$countNumber = 0;
		$sql = "SELECT
					COUNT(1) countNumber,
					ScheduledDate
				FROM
					transaction_checkschedule
				WHERE
					ScheduledDate = '".$ScheduledDate."'
					AND $ddlBranch = 1
				GROUP BY
					ScheduledDate";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		$row = mysql_fetch_array($result);
		$countNumber += $row['countNumber'];
		
		
		$sql = "SELECT
					COUNT(1) countNumber,
					ScheduledDate
				FROM
					transaction_onlineschedule
				WHERE
					ScheduledDate  = '".$ScheduledDate."'
					AND BranchID = $ddlBranch
				GROUP BY
					ScheduledDate";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		$row = mysql_fetch_array($result);
		$countNumber += $row['countNumber'];
		
		
		if($countNumber >= $MINUTE_SCHEDULE_LIMIT) {
			$Message = "Jadwal untuk jam yang dipilih sudah penuh!";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}*/
		
		$sql = "SELECT
					1 Valid
				FROM
					transaction_checkschedule
				WHERE
					PatientID = ".$ddlPatient."
					AND DATE_FORMAT(ScheduledDate, '%Y-%m-%d') = DATE_FORMAT('".$ScheduledDate."', '%Y-%m-%d')";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		$row = mysql_fetch_array($result);
		
		if($row['Valid'] == 1) {
			$Message = "Pasien sudah terdaftar untuk tanggal yang dipilih!";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		else {
			$sql = "INSERT INTO transaction_checkschedule
					(
						PatientID,
						ScheduledDate,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						".$ddlPatient.",
						'".$ScheduledDate."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
					
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("COMMIT", $dbh);
			return 0;
		}
	}
	
	function returnstate($Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
