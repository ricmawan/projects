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
				$OutgoingModelData = explode("^", $Data[$i]);
				$OutgoingModelID = mysql_real_escape_string($OutgoingModelData[0]);
				$ReceiptNumber = $OutgoingModelData[1];
				$sql = "DELETE FROM transaction_outgoingmodel WHERE OutgoingModelID = '".$OutgoingModelID."'";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($ReceiptNumber);
				}
				$MessageSuccessDelete .= "$ReceiptNumber, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "Resi " .$MessageSuccessDelete. " Berhasil Dihapus";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "Resi " .$MessageFailedDelete. " Gagal Dihapus";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>
