<?php
	if(isset($_POST['hdnOutgoingModelID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ID = mysql_real_escape_string($_POST['hdnOutgoingModelID']);
		$TransactionDate = mysql_real_escape_string($_POST['hdnTransactionDate']);
		$ReceiptNumber = mysql_real_escape_string($_POST['txtReceiptNumber']);
		$Remarks = mysql_real_escape_string($_POST['txtRemarks']);
		$ExaminationName = mysql_real_escape_string($_POST['txtExaminationName']);
		$DoctorID = mysql_real_escape_string($_POST['ddlDoctor']);
		$PatientID = mysql_real_escape_string($_POST['ddlPatient']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;		
		$State = 1;
		if($ID == 0) {
			$sql = "INSERT INTO transaction_outgoingmodel
					(
						TransactionDate,
						ReceiptNumber,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'".$TransactionDate."',
						'".$ReceiptNumber."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";

			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}

			$ID = mysql_insert_id();
		}

		$State = 2;
		$sql = "INSERT INTO transaction_outgoingmodeldetails
				(	
					OutgoingModelID,
					DoctorID,
					PatientID,
					ExaminationName,
					Remarks,
					CreatedDate,
					CreatedBy
				)
				VALUES
				(
					".$ID.",
					".$DoctorID.",
					".$PatientID.",
					'".$ExaminationName."',
					'".$Remarks."',
					NOW(),
					'".$_SESSION['UserLogin']."'
				)";
				
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}

		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
		mysql_query("COMMIT", $dbh);
		return 0;
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
