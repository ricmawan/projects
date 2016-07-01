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
				$TransactionType = $CancellationData[2];
				if($TransactionType == 1) {
					$sql = "UPDATE 
								transaction_outgoing OT
								JOIN transaction_cancellation TC
									ON TC.OutgoingID = OT.OutgoingID
							SET
								OT.IsCancelled = 0
							WHERE
								TC.CancellationID = '".$CancellationID."'";
				}
				else if($TransactionType == 2) {
					$sql = "UPDATE 
								transaction_incoming TI
								JOIN transaction_cancellation TC
									ON TC.IncomingID = TI.IncomingID
							SET
								TI.IsCancelled = 0
							WHERE
								TC.CancellationID = '".$CancellationID."'";
				}
				else if($TransactionType == 3) {
					$sql = "UPDATE 
								transaction_salereturn SR
								JOIN transaction_cancellation TC
									ON TC.SaleReturnID = SR.SaleReturnID
							SET
								SR.IsCancelled = 0
							WHERE
								TC.CancellationID = '".$CancellationID."'";
				}
				else if($TransactionType == 4) {
					$sql = "UPDATE 
								transaction_buyreturn BR
								JOIN transaction_cancellation TC
									ON TC.SaleReturnID = BR.SaleReturnID
							SET
								BR.IsCancelled = 0
							WHERE
								TC.CancellationID = '".$CancellationID."'";
				}
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
