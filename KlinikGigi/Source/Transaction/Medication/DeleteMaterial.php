<?php
	if(isset($_POST['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$MessageSuccessDelete = "";
		$MessageFailedDelete = "";
	
		$Data = $_POST['ID'];
		
		for($i=0; $i<count($Data); $i++) {
			try
			{
				$MaterialDetailsID = $Data;
				$sql = "DELETE FROM transaction_materialdetails WHERE MaterialDetailsID = '".$MaterialDetailsID."'";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($MedicationDetailsID);
				}
				$MessageSuccessDelete .= "$MaterialDetailsID, ";
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
