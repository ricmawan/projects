<?php
	if(isset($_POST['OutgoingModelDetailsID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$MessageSuccessDelete = "";
		$MessageFailedDelete = "";
	
		$OutgoingModelDetailsID = $_POST['OutgoingModelDetailsID'];
		$ReceivedDate = $_POST['ReceivedDate'];
		$IncomingReceiptNumber = $_POST['IncomingReceiptNumber'];
		
		for($i=0; $i<count($OutgoingModelDetailsID); $i++) {
			try
			{
				$ID = $OutgoingModelDetailsID[$i];
				$sql = "UPDATE transaction_outgoingmodeldetails
						SET
							ReceivedDate = '".$ReceivedDate[$i]."',
							IncomingReceiptNumber = '".$IncomingReceiptNumber[$i]."',
							IsReceived = 1,
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							OutgoingModelDetailsID = '".$ID."'";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($ID);
				}
				$MessageSuccessDelete .= "$ID, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "Data Berhasil Disimpan";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "Data Gagal Disimpan";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>
