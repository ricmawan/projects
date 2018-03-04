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
				$DataID = mysqli_real_escape_string($dbh, $ArrayData[$i]);
				$sql = "CALL spDelStockMutationDetails($DataID, '".$_SESSION['UserLogin']."')";
				if (!$result = mysqli_query($dbh, $sql)) {
					logEvent(mysqli_error($dbh), '/Transaction/StockMutation/Delete.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
					throw new Exception($DataID);
				}
				$row=mysqli_fetch_array($result);
				
				if($row['FailedFlag'] == 1) {
					throw new Exception($DataID);
				}
				mysqli_free_result($result);
				mysqli_next_result($dbh);
				$MessageSuccessDelete .= "$DataID, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "Data Berhasil Dihapus";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "Data Gagal Dihapus";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>