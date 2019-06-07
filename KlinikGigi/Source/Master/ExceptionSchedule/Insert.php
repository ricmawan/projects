<?php
	if(isset($_POST['hdnExceptionScheduleID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ExceptionScheduleID = mysql_real_escape_string($_POST['hdnExceptionScheduleID']);
		$BranchID = mysql_real_escape_string($_POST['ddlBranch']);
		$IsAdmin = mysql_real_escape_string($_POST['ddlPage']);
		$DayOfWeek = mysql_real_escape_string($_POST['ddlDayOfWeek']);
		$BusinessHour = mysql_real_escape_string($_POST['ddlHour']).":".mysql_real_escape_string($_POST['ddlMinute']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsExceptionSchedule(".$ExceptionScheduleID.", ".$BranchID.", ".$DayOfWeek.", '".$BusinessHour."', ".$IsAdmin.", ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ExceptionScheduleID, $Message, $MessageDetail, $FailedFlag, $State);
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
