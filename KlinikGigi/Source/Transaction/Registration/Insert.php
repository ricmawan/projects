<?php
	if(isset($_POST['hdnPatientID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$PatientID = mysql_real_escape_string($_POST['hdnPatientID']);
		$State = 1;
		$Message = "Pendaftaran Berhasil";
		$OrderNumber = "";
		$MessageDetail = "";
		$FailedFlag = 0;

		$sql = "SELECT
					RIGHT(CONCAT('000', COUNT(1) + 1), 3) OrderNumber
				FROM
					transaction_invoicenumber
				WHERE
					DATE_FORMAT(TransactionDate, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y')";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($PatientID, $Message, $MessageDetail, $FailedFlag, $State, $OrderNumber);
			return 0;
		}
		$row = mysql_fetch_array($result);
		$OrderNumber = $row['OrderNumber'];
		
		$State = 2;
		$sql = "SELECT
					1
				FROM
					transaction_medication
				WHERE
					DATE_FORMAT(TransactionDate, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y')
					AND PatientID = $PatientID
					AND IsDone = 0
					AND IsCancelled = 0";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($PatientID, $Message, $MessageDetail, $FailedFlag, $State, $OrderNumber);
			return 0;
		}
		
		if(mysql_num_rows($result) > 0) {
			$FailedFlag = 1;
			$Message = "Pasien sudah terdaftar untuk hari ini!";
		}
		else {
			$State = 3;
			$sql = "INSERT INTO transaction_medication
					(
						PatientID,
						OrderNumber,
						TransactionDate,
						IsDone,
						IsCancelled,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						".$PatientID.",
						'".$OrderNumber."',
						NOW(),
						0,
						0,
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
						
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($PatientID, $Message, $MessageDetail, $FailedFlag, $State, $OrderNumber);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$State = 4;
			$sql = "INSERT INTO transaction_invoicenumber
					(
						TransactionDate,
						OrderNumber,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						NOW(),
						'".$OrderNumber."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
						
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($PatientID, $Message, $MessageDetail, $FailedFlag, $State, $OrderNumber);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
		}
		echo returnstate($PatientID, $Message, $MessageDetail, $FailedFlag, $State, $OrderNumber);
		mysql_query("COMMIT", $dbh);
		return 0;
	}
	
	function returnstate($PatientID, $Message, $MessageDetail, $FailedFlag, $State, $OrderNumber) {
		$data = array(
			"ID" => $PatientID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State,
			"OrderNumber" => $OrderNumber
		);
		return json_encode($data);
	}
?>
