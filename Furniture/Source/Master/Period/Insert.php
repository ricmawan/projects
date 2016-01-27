<?php
	if(isset($_POST['hdnPeriodID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$PeriodID = mysql_real_escape_string($_POST['hdnPeriodID']);
			
		$StartDate = explode('-', $_POST['txtStartDate']);
		$_POST['txtStartDate'] = "$StartDate[2]-$StartDate[1]-$StartDate[0]"; 
		$StartDate = $_POST['txtStartDate'];
		
		$EndDate = explode('-', $_POST['txtEndDate']);
		$_POST['txtEndDate'] = "$EndDate[2]-$EndDate[1]-$EndDate[0]"; 
		$EndDate = $_POST['txtEndDate'];
		
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "CALL spInsPeriod(".$PeriodID.", '".$StartDate."', '".$EndDate."', ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
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
