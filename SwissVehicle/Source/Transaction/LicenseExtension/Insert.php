<?php
	if(isset($_POST['hdnLicenseExtensionID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ID = mysql_real_escape_string($_POST['hdnLicenseExtensionID']);
		$TransactionDate = explode('-', mysql_real_escape_string($_POST['txtTransactionDate']));
		$TransactionDate = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]";
		$MachineID = mysql_real_escape_string($_POST['ddlMachine']);
		$DueDate = explode('-', mysql_real_escape_string($_POST['txtDueDate']));
		$DueDate = "$DueDate[2]-$DueDate[1]-$DueDate[0]";
		$Remarks = mysql_real_escape_string($_POST['txtRemarks']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_licenseextension
					(
						TransactionDate,
						MachineID,
						DueDate,
						Remarks,
						IsExtended,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'".$TransactionDate."',
						".$MachineID.",
						'".$DueDate."',
						'".$Remarks."',
						0,
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_licenseextension
					SET
						TransactionDate = '".$TransactionDate."',
						MachineID = ".$MachineID.",
						DueDate = '".$DueDate."',
						Remarks = '".$Remarks."',
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						LicenseExtensionID = $ID";
		}
		
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
			"Id" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
