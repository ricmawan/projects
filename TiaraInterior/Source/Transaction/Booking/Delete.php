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
				$BookingData = explode("^", mysql_real_escape_string($Data[$i]));
				$BookingID = $BookingData[0];
				$BookingNumber = $BookingData[1];
				$sql = "DELETE FROM transaction_booking WHERE BookingID = '".$BookingID."'";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($BookingNumber);
				}
				$MessageSuccessDelete .= "$BookingNumber, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "No Nota " .$MessageSuccessDelete. " Berhasil Dihapus";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "No Nota " .$MessageFailedDelete. " Gagal Dihapus";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>
