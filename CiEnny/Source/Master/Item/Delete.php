<?php
	if(isset($_POST['ID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$MessageSuccessDelete = "";
		$MessageFailedDelete = "";
	
		$ArrayData = $_POST['ID'];
		
		for($i=0; $i<count($ArrayData); $i++) {
			try
			{
				$Data = explode("^", $ArrayData[$i]);
				$DataID = mysqli_real_escape_string($dbh, $Data[0]);
				$DataName = $Data[1];
				$sql = "CALL spDelItem($DataID, '".$_SESSION['UserLogin']."')";
				if (!$result = mysqli_query($dbh, $sql)) {
					logEvent(mysqli_error($dbh), '/Master/Item/Delete.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
					throw new Exception($DataName);
				}
				$row=mysqli_fetch_array($result);
				
				if($row['FailedFlag'] == 1) {
					throw new Exception($DataName);
				}
				mysqli_free_result($result);
				mysqli_next_result($dbh);
				$MessageSuccessDelete .= "$DataName, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "Barang " .$MessageSuccessDelete. " Berhasil Dihapus";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "Barang " .$MessageFailedDelete. " Gagal Dihapus";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>