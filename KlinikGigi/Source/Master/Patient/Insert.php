<?php
	if(isset($_POST['hdnPatientID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$PatientID = mysql_real_escape_string($_POST['hdnPatientID']);
		$Telephone = mysql_real_escape_string($_POST['txtTelephone']);
		$PatientName = mysql_real_escape_string($_POST['txtPatientName']);
		$PatientNumber = mysql_real_escape_string($_POST['txtPatientNumber']);
		$Address = mysql_real_escape_string($_POST['txtAddress']);
		$Email = mysql_real_escape_string($_POST['txtEmail']);
		$City = mysql_real_escape_string($_POST['txtCity']);
		$Allergy = mysql_real_escape_string($_POST['txtAllergy']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$BirthDate = explode('-', mysql_real_escape_string($_POST['txtBirthDate']));
		$BirthDate = "$BirthDate[2]-$BirthDate[1]-$BirthDate[0]";
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsPatient(".$PatientID.", '".$PatientNumber."', '".$PatientName."', '".$BirthDate."', '".$Address."', '".$Allergy."', '".$City."', '".$Telephone."', '".$Email."', ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($PatientID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}				
		$row=mysql_fetch_array($result);
		
		if($row['FailedFlag'] == 0) {
			$RootFolder = '../../assets/photos/';
			$FolderName = $RootFolder . $PatientNumber . "_" . str_replace(" ", "_", $PatientName);
			if(!is_dir($FolderName)) {
				mkdir($FolderName, true);
				mkdir($FolderName . "/Sebelum", true);
				mkdir($FolderName . "/Proses", true);
				mkdir($FolderName . "/Setelah", true);
			}
		}
		echo returnstate($row['ID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
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