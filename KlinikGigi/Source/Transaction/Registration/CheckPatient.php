<?php
	if(isset($_POST['PatientNumber'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ID = 0;
		$PatientNumber = mysql_real_escape_string($_POST['PatientNumber']);
		$State = 1;
		$Message = "ID pasien belum terdaftar, silahkan lengkapi form!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$PatientName = "";
		$IsExists = 0;
		$sql = "SELECT
					PatientName,
					PatientID
				FROM
					master_patient
				WHERE
					TRIM(PatientNumber) = TRIM('".$PatientNumber."')";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $IsExists, $PatientName);
			return 0;
		}
		if(mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result);
			$PatientName = $row['PatientName'];
			$ID = $row['PatientID'];
			$Message = "Pasien sudah terdaftar.";
			$IsExists = 1;
		}
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $IsExists, $PatientName);
		return 0;
	}
	
	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $IsExists, $PatientName) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State,
			"IsExists" => $IsExists,
			"PatientName" => $PatientName
		);
		return json_encode($data);
	}
?>
