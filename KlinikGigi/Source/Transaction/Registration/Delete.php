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
				$MedicationData = explode("^", mysql_real_escape_string($Data[$i]));
				$MedicationID = $MedicationData[0];
				$MedicationNumber = $MedicationData[1];
				$sql = "UPDATE transaction_medication SET IsCancelled = 1, ModifiedBy = '".$_SESSION['UserLogin']."' WHERE MedicationID = '".$MedicationID."'";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($MedicationNumber);
				}
				$MessageSuccessDelete .= "$MedicationNumber, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "No Urut " .$MessageSuccessDelete. " Berhasil Dibatalkan";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "No Urut " .$MessageFailedDelete. " Gagal Dibatalkan";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>
