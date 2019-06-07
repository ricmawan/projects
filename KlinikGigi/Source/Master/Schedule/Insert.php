<?php
	if(isset($_POST['hdnScheduleID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ScheduleID = mysql_real_escape_string($_POST['hdnScheduleID']);
		$BranchID = mysql_real_escape_string($_POST['ddlBranch']);
		$IsAdmin = mysql_real_escape_string($_POST['ddlPage']);
		$DayOfWeek = mysql_real_escape_string($_POST['ddlDayOfWeek']);
		$StartHour = mysql_real_escape_string($_POST['ddlStartHour']);
		$EndHour = mysql_real_escape_string($_POST['ddlEndHour']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsSchedule(".$ScheduleID.", ".$BranchID.", ".$DayOfWeek.", ".$StartHour.", ".$EndHour.", ".$IsAdmin.", ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ScheduleID, $Message, $MessageDetail, $FailedFlag, $State);
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
