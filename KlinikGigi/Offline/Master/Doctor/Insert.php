<?php
	if(isset($_POST['hdnUserID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$UserID = mysql_real_escape_string($_POST['hdnUserID']);
		$UserName = mysql_real_escape_string($_POST['txtUserName']);
		$UserLogin = mysql_real_escape_string($_POST['txtUserLogin']);
		$Password = mysql_real_escape_string($_POST['txtPassword']);
		$hdnMenuID = mysql_real_escape_string($_POST['hdnMenuID']);
		$hdnEditMenuID = mysql_real_escape_string($_POST['hdnEditMenuID']);
		$hdnDeleteMenuID = mysql_real_escape_string($_POST['hdnDeleteMenuID']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		if(ISSET($_POST['chkActive'])) $chkActive = true;
		else $chkActive = false;
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
	
		$MenuID = "";
		$EditMenuID = "";
		$DeleteMenuID = "";
		
		if($Password == "") {
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
			$Password = $row['UserPassword'];
		}
		else $Password = MD5($Password);

		$sql = "CALL spInsUser(".$UserID.", '".$UserName."', 2, '".$UserLogin."', '".$Password."', '".$chkActive."', '".$hdnMenuID."', '".$hdnEditMenuID."', '".$hdnDeleteMenuID."', ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}				
		$row=mysql_fetch_array($result);
		
		if($row['FailedFlag'] == 0 && $_SESSION['UserID'] == $UserID) {
			$_SESSION['UserPassword'] = $Password;
			$_SESSION['UserLogin'] = $UserLogin;
		}
		
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
