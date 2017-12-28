<?php
	if(isset($_POST['ID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$MessageSuccessDelete = "";
		$MessageFailedDelete = "";
	
		$Data = $_POST['ID'];
		
		for($i=0; $i<count($Data); $i++) {
			try
			{
				$UserData = explode("^", $Data[$i]);
				$UserID = mysqli_real_escape_string($dbh, $UserData[0]);
				$UserName = $UserData[1];
				$sql = "CALL spDelUser($UserID, '".$_SESSION['UserLogin']."')";
				if (!$result = mysqli_query($dbh, $sql)) {
					logEvent(mysqli_error($dbh), '/Master/User/Delete.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
					echo "<script>$('#loading').hide();</script>";
					//return 0;
					throw new Exception($UserName);
				}
				mysqli_next_result($dbh);
				//mysqli_free_result($result);
				$MessageSuccessDelete .= "$UserName, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "User " .$MessageSuccessDelete. " Berhasil Dihapus";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "User " .$MessageFailedDelete. " Gagal Dihapus";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>