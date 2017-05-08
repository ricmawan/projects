<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	date_default_timezone_set("Asia/Jakarta");
	$TimeStamp = date("YmdHis");
	$command = $MYSQL_DUMP_PATH." --user=".$DBUser." --password=".$DBPass." --databases --routines --log-error=".$ERROR_LOG_PATH." ".$DBName." > \"".$BACKUP_FULLPATH.$DBName."_".$TimeStamp.".sql\"";
	
	//echo $command;
	exec($command);
	$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
	$MessageDetail = "";
	$FailedFlag = 0;
	$State = 1;
	
	$sql = "CALL spInsBackup('".$BACKUP_FOLDER.$DBName."_".$TimeStamp.".sql', '".$_SESSION['UserLogin']."')";
	
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
			"Id" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
