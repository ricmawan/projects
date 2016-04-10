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
				$CancellationData = explode("^", mysql_real_escape_string($Data[$i]));
				$CancellationID = $CancellationData[0];
				$CancellationNumber = $CancellationData[1];
				$sql = "UPDATE 
							transaction_outgoing OT
							JOIN transaction_cancellation TC
								ON TC.OutgoingID = OT.OutgoingID
						SET
							IsCancelled = 0
						WHERE
							TC.CancellationID = '".$CancellationID."'";
				if (! $result=mysql_query($sql, $dbh)) {
					//throw new Exception($CancellationNumber);
					echo mysql_error();
				}
				
				$sql = "DELETE FROM transaction_cancellation WHERE CancellationID = '".$CancellationID."'";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($CancellationNumber);
				}
				$MessageSuccessDelete .= "$CancellationNumber, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "ID " .$MessageSuccessDelete. " Berhasil Dihapus";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "ID " .$MessageFailedDelete. " Gagal Dihapus";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>
