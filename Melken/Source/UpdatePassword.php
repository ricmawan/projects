<?php
	include "./DBConfig.php";
	include "./GetSession.php";
	$UserID = mysql_real_escape_string($_SESSION['UserID']);
	$CurrentPassword = mysql_real_escape_string($_POST['txtCurrentPassword']);
	$NewPassword = mysql_real_escape_string($_POST['txtNewPassword']);		
	$Message = "Password berhasil diubah";
	$MessageDetail = "";
	$FailedFlag = 0;
	$State = 1;
	
	$sql = "SELECT
				UserPassword
			FROM
				master_user
			WHERE
				UserID = ".$UserID."";
	if (! $result=mysql_query($sql, $dbh)) {
		$Message = "Terjadi Kesalahan Sistem";
		$MessageDetail = mysql_error();
		$FailedFlag = 1;
		echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
		return 0;
	}
	$row = mysql_fetch_array($result);
	if($row['UserPassword'] == MD5($CurrentPassword)) {
		$sql = "CALL spUpdUserPassword(".$UserID.", '".MD5($NewPassword)."', '".$_SESSION['UserLogin']."')";
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		
		$row=mysql_fetch_array($result);
		
		if($row['FailedFlag'] == 0 && $_SESSION['UserID'] == $UserID) {
			$_SESSION['UserPassword'] = MD5($NewPassword);
		}
		
		echo returnstate($row['ID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	}
	else {
		$Message = "Password lama salah!";
		$MessageDetail = "Password lama salah!";
		$FailedFlag = 1;
		echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
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
