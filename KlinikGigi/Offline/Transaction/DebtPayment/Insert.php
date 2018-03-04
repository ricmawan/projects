<?php
	if(isset($_POST['hdnMedicationID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$MedicationID = mysql_real_escape_string($_POST['hdnMedicationID']);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "UPDATE transaction_debtpayment
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
			
		$sql = "INSERT INTO transaction_debtpayment
				(
					MedicationID,
					Cash,
					Debit,
					CreatedDate,
					CreatedBy
				)
				SELECT
					".$MedicationID.",
					'".str_replace(",", "", $_POST['txtCash'])."',
					'".str_replace(",", "", $_POST['txtDebit'])."',
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
							transaction_debtpayment DP
						WHERE
							DP.MedicationID = ".$MedicationID."
					)";
	
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($MedicationID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
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
