<?php
	if(isset($_POST['hdnMedicationDetailsID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ID = mysql_real_escape_string($_POST['hdnMedicationDetailsID']);
		$txtRemarks = mysql_real_escape_string($_POST['txtRemarks2']);
		$Quantity = mysql_real_escape_string($_POST['txtQuantity2']);
		$Price = str_replace(",", "", $_POST['txtExaminationPrice2']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;		
		$State = 1;
		$sql = "UPDATE transaction_medicationdetails
				SET
					Remarks = '".$txtRemarks."',
					Price = ".$Price.",
					Quantity = ".$Quantity.",
					ModifiedBy = '".$_SESSION['UserLogin']."'
				WHERE
					MedicationDetailsID = $ID";
				
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
