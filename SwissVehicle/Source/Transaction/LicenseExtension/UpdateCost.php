<?php
	if(isset($_POST['hdnLicenseExtensionID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$LicenseExtensionID = mysql_real_escape_string($_POST['hdnLicenseExtensionID']);
		$ExtensionDate = explode('-', mysql_real_escape_string($_POST['txtExtensionDate']));
		$ExtensionDate = "$ExtensionDate[2]-$ExtensionDate[1]-$ExtensionDate[0]";
		$ExtensionCost = str_replace(",", "", $_POST['txtExtensionCost']);
		$Message = "Data berhasil disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "UPDATE transaction_licenseextension
				SET
					ExtensionDate = '".$ExtensionDate."',
					ExtensionCost = ".$ExtensionCost.",
					IsExtended = 1,
					ModifiedBy = '".$_SESSION['UserLogin']."'
				WHERE
					LicenseExtensionID = $LicenseExtensionID";
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($LicenseExtensionID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		
		echo returnstate($LicenseExtensionID, $Message, $MessageDetail, $FailedFlag, $State);
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
