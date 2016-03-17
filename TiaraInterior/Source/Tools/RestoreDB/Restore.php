<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	date_default_timezone_set("Asia/Jakarta");
	$RestoreMethod = mysql_real_escape_string($_POST['RestoreMethod']);
	if(ISSET($_POST['select'])) {
		$BackupHistoryID = mysql_real_escape_string($_POST['select']);
	}
	$ID = 0;
	$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
	$MessageDetail = "";
	$FailedFlag = 0;
	$State = 1;
	if($RestoreMethod == 1) {
		$sql = "SELECT
					FilePath
				FROM
					backup_history
				WHERE
					BackupHistoryID = ".$BackupHistoryID."";
		
		if(!$result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
			
		$row = mysql_fetch_array($result);
		$FileName = "\"".$BACKUP_FULLPATH.basename($row['FilePath'])."\"";
		$SavedPath = $row['FilePath'];
		$command = $MYSQL_PATH." --user=".$DBUser." --password=".$DBPass." --database=".$DBName." < ".$FileName."";
	}
	
	else {
		IF(ISSET($_FILES['uploadfile'])) {
			$filename = strtolower($_FILES['uploadfile']['name']);
			$exts = explode(".", $filename) ; 
			$n = count($exts)-1; 
			$exts = $exts[$n]; 
			$date = date("dmYHis");
			$new_file_name="Psikotes ".$date." ".$filename;

			//set where you want to store files
			$path= "../../UploadedFiles/".$filename;
			if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $path))
			{
				$SavedPath = "\"".$UPLOAD_PATH.$filename."\"";
				$FileName = "\"".$UPLOAD_PATH.$filename."\"";
				$command = $MYSQL_PATH." --user=".$DBUser." --password=".$DBPass." --database=".$DBName." < ".$FileName."";
			}
			else
			{
				$Message = "File Gagal disimpan";
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				return 0;
			}
		}
	}
	$TimeStamp = date("YmdHis");
	//$command = $MYSQL_DUMP_PATH." --user=".$DBUser." --password=".$DBPass." --databases --routines --log-error=".$ERROR_LOG_PATH." ".$DBName." > \"".$BACKUP_FULLPATH.$DBName."_".$TimeStamp.".sql\"";
	exec($command);
	$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
	$MessageDetail = "";
	$FailedFlag = 0;
	$State = 1;
	
	$sql = "CALL spInsRestore('".$SavedPath."', '".$_SESSION['UserLogin']."')";
	
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
		//$data = $ID."|".$Message."|".$MessageDetail."|".$FailedFlag."|".$State;
		//return $data;
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
