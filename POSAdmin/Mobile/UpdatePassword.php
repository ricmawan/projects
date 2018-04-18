<?php
	include "./DBConfig.php";
	include "./GetSession.php";
	$UserID = mysqli_real_escape_string($dbh, $_SESSION['UserID']);
	$CurrentPassword = mysqli_real_escape_string($dbh, $_POST['txtCurrentPassword']);
	$NewPassword = mysqli_real_escape_string($dbh, $_POST['txtNewPassword']);		
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
	if (! $result=mysqli_query($dbh, $sql)) {
		$Message = "Terjadi Kesalahan Sistem";
		$MessageDetail = mysqli_error($dbh);
		$FailedFlag = 1;
		logEvent(mysqli_error($dbh), '/UpdatePassword.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
		return 0;
	}
	$row = mysqli_fetch_array($result);
	mysqli_free_result($result);
	mysqli_next_result($dbh);
	
	if($row['UserPassword'] == MD5($CurrentPassword)) {
		$sql = "CALL spUpdUserPassword(".$UserID.", '".MD5($NewPassword)."', '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/UpdatePassword.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		
		$row=mysqli_fetch_array($result);
		
		if($row['FailedFlag'] == 0 && $_SESSION['UserID'] == $UserID) {
			$_SESSION['UserPassword'] = MD5($NewPassword);
		}
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
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
