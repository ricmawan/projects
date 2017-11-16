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
				$UserID = mysql_real_escape_string($UserData[0]);
				$UserName = $UserData[1];
				$sql = "DELETE FROM master_role WHERE UserID = $UserID";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($UserName);
				}
				$sql = "DELETE FROM master_user WHERE UserID = $UserID";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($UserName);
				}
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
