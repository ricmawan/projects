<?php
	if(isset($_POST['hdnUserID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$UserID = mysqli_real_escape_string($dbh, $_POST['hdnUserID']);
		$UserName = mysqli_real_escape_string($dbh, $_POST['txtUserName']);
		$UserLogin = mysqli_real_escape_string($dbh, $_POST['txtUserLogin']);
		$Password = mysqli_real_escape_string($dbh, $_POST['txtPassword']);		
		$hdnMenuID = explode(",", mysqli_real_escape_string($dbh, $_POST['hdnMenuID']));
		$hdnEditMenuID = explode(",", mysqli_real_escape_string($dbh, $_POST['hdnEditMenuID']));
		$hdnDeleteMenuID = explode(",", mysqli_real_escape_string($dbh, $_POST['hdnDeleteMenuID']));
		$IsActive = mysqli_real_escape_string($dbh, $_POST['ddlStatus']);
		$UserTypeID = mysqli_real_escape_string($dbh, $_POST['ddlUserType']);
		$hdnIsEdit = mysqli_real_escape_string($dbh, $_POST['hdnIsEdit']);
		
		if($IsActive == 1) $IsActive = true;
		else $IsActive = false;
		
		$Message = "Terjadi Kesalahan Sistem!";
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
			if (! $result=mysqli_query($dbh, $sql)) {
				$MessageDetail = mysqli_error($dbh);
				$FailedFlag = 1;
				echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
				return 0;
			}
			$row = mysqli_fetch_array($result);
			$Password = $row['UserPassword'];
		}
		else $Password = MD5($Password);
		
		$roleValues = array();
		for($i=0;$i<count($hdnMenuID);$i++) {
			if($hdnMenuID[$i] == "") $roleValues[] = "";
			else $roleValues[] = "(".$UserID.", ".$hdnMenuID[$i].", ".$hdnEditMenuID[$i].", ".$hdnDeleteMenuID[$i].")";
		}
		$sql = "CALL spInsUser(".$UserID.", '".$UserName."', ".$UserTypeID.", '".$UserLogin."', '".$Password."', '".$IsActive."', '".implode(",", $roleValues)."', ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Master/User/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}				
		$row=mysqli_fetch_array($result);
		
		if($row['FailedFlag'] == 0 && $_SESSION['UserID'] == $UserID) {
			$_SESSION['UserPassword'] = $Password;
			$_SESSION['UserLogin'] = $UserLogin;
		}
		mysqli_free_result($result);
		mysqli_next_result($dbh);
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
