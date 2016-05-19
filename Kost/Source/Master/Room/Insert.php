<?php
	if(isset($_POST['hdnRoomID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$RoomID = mysql_real_escape_string($_POST['hdnRoomID']);
		$RoomNumber = mysql_real_escape_string($_POST['txtRoomNumber']);
		$DailyRate = str_replace(",", "", $_POST['txtDailyRate']);
		$HourlyRate = str_replace(",", "", $_POST['txtHourlyRate']);
		$RoomInfo = mysql_real_escape_string($_POST['txtRoomInfo']);
		
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "CALL spInsRoom(".$RoomID.", '".$RoomNumber."', 1, ".$DailyRate.", ".$HourlyRate.", '".$RoomInfo."', ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($RoomID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}				
		$row=mysql_fetch_array($result);
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
