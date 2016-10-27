<?php
	if(isset($_POST['hdnMachineID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$MachineID = mysql_real_escape_string($_POST['hdnMachineID']);
		$MachineKind = mysql_real_escape_string($_POST['ddlMachineKind']);
		$MachineType = mysql_real_escape_string($_POST['txtMachineType']);
		$MachineYear = mysql_real_escape_string($_POST['ddlYear']);
		$MachineCode = mysql_real_escape_string($_POST['txtMachineCode']);
		$BrandName = mysql_real_escape_string($_POST['txtBrandName']);
		$Remarks = mysql_real_escape_string($_POST['txtRemarks']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$Message = "Data gagal dimasukkan, silahkan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsMachine(".$MachineID.", '".$MachineKind."', '".$MachineType."', ".$MachineYear.", '".$MachineCode."', '".$BrandName."', '".$Remarks."', ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($MachineID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}				
		$row=mysql_fetch_array($result);
		echo returnstate($row['ID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
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
