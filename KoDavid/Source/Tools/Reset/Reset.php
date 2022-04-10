<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	date_default_timezone_set("Asia/Jakarta");
	$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
	$MessageDetail = "";
	$FailedFlag = 0;
	$State = 1;
	$ID = 0;
	$sql = "CALL spInsFirstStock('".$_SESSION['UserLogin']."')";
	
	if (! $result=mysql_query($sql, $dbh)) {
		$Message = "Terjadi Kesalahan Sistem";
		$MessageDetail = mysql_error();
		$FailedFlag = 1;
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
		return 0;
	}				
	$row=mysql_fetch_array($result);
	echo returnstate($row['ID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	
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
