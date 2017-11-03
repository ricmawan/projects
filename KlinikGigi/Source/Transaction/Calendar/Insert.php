<?php
	if(isset($_POST['txtPatientName'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$txtPatientName = mysql_real_escape_string($_POST['txtPatientName']);
		$txtPhone = mysql_real_escape_string($_POST['txtPhone']);
		$ddlTime = mysql_real_escape_string($_POST['ddlTime']);
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
		$sql = "INSERT INTO transaction_onlineschedule
				(
					ScheduledDate,
					PatientName,
					PhoneNumber,
					CreatedDate,
					CreatedBy
				)
				VALUES
				(
					'".$ScheduledDate."',
					'".$txtPatientName."',
					'".$txtPhone."',
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
