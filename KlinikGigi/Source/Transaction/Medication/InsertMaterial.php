<?php
	if(isset($_POST['hdnSessionID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ID = mysql_real_escape_string($_POST['hdnSessionID']);
		$txtRemarks = mysql_real_escape_string($_POST['txtMaterialRemarks']);
		$MaterialID = mysql_real_escape_string($_POST['ddlMaterial']);
		$Quantity = mysql_real_escape_string($_POST['txtMaterialQuantity']);
		$Price = str_replace(",", "", $_POST['txtMaterialPrice']);
		$MedicationDetailsID = mysql_real_escape_string($_POST['hdnMedicationDetailsID2']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;		
		$State = 1;
		$sql = "INSERT INTO transaction_materialdetails
				(
					MedicationDetailsID,
					MaterialID,
					Remarks,
					SalePrice,
					Quantity,
					SessionID,
					CreatedDate,
					CreatedBy
				)
				VALUES
				(
					0,
					".$MaterialID.",
					'".$txtRemarks."',
					".$Price.",
					".$Quantity.",
					'".$ID."',
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
		
		if($MedicationDetailsID != 0) {
			$sql = "UPDATE transaction_materialdetails
					SET
						MedicationDetailsID = ".$MedicationDetailsID.",
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						SessionID = '$ID'";
					
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				return 0;
			}
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
