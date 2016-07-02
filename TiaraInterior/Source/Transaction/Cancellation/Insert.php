<?php
	if(isset($_POST['hdnCancellationID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnCancellationID']);
		$TransactionType = mysql_real_escape_string($_POST['hdnTransactionType']);
		if($TransactionType == "1") {
			$OutgoingID = mysql_real_escape_string($_POST['hdnOutgoingID']);
			$IncomingID = 0;
			$SaleReturnID = 0;
			$BuyReturnID = 0;
		}
		else if($TransactionType == "2") {
			$OutgoingID = 0;
			$IncomingID = mysql_real_escape_string($_POST['hdnOutgoingID']);
			$SaleReturnID = 0;
			$BuyReturnID = 0;
		}
		else if($TransactionType == "3") {
			$OutgoingID = 0;
			$IncomingID = 0;
			$SaleReturnID = mysql_real_escape_string($_POST['hdnOutgoingID']);
			$BuyReturnID = 0;
		}
		else {
			$OutgoingID = 0;
			$IncomingID = 0;
			$SaleReturnID = 0;
			$BuyReturnID = mysql_real_escape_string($_POST['hdnOutgoingID']);
		}
		
		$txtRemarks = mysql_real_escape_string($_POST['txtRemarks']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_cancellation
					(
						DeletedBy,
						OutgoingID,
						IncomingID,
						SaleReturnID,
						BuyReturnID,
						TransactionDate,
						Remarks,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						".$_SESSION['UserID'].",
						".$OutgoingID.",
						".$IncomingID.",
						".$SaleReturnID.",
						".$BuyReturnID.",
						NOW(),
						'".$txtRemarks."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
					
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$State = 2;
			if($TransactionType == 1) {
				$sql = "UPDATE 
							transaction_outgoing OT
						SET
							OT.IsCancelled = 1
						WHERE
							OT.OutgoingID = '".$OutgoingID."'";
			}
			else if($TransactionType == 2) {
				$sql = "UPDATE 
							transaction_incoming TI
						SET
							TI.IsCancelled = 1
						WHERE
							TI.IncomingID = '".$IncomingID."'";
			}
			else if($TransactionType == 3) {
				$sql = "UPDATE 
							transaction_salereturn SR
						SET
							SR.IsCancelled = 1
						WHERE
							SR.SaleReturnID = '".$SaleReturnID."'";
			}
			else if($TransactionType == 4) {
				$sql = "UPDATE 
							transaction_buyreturn BR
						SET
							BR.IsCancelled = 1
						WHERE
							BR.BuyReturnID = '".$BuyReturnID."'";
			}
						
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
		}
		else {
			$State = 3;
			if($TransactionType == 1) {
				$sql = "UPDATE 
							transaction_outgoing OT
							JOIN transaction_cancellation TC
								ON TC.OutgoingID = OT.OutgoingID
						SET
							OT.IsCancelled = 0
						WHERE
							TC.CancellationID = '".$ID."'";
			}
			else if($TransactionType == 2) {
				$sql = "UPDATE 
							transaction_incoming TI
							JOIN transaction_cancellation TC
								ON TC.IncomingID = TI.IncomingID
						SET
							TI.IsCancelled = 0
						WHERE
							TC.CancellationID = '".$ID."'";
			}
			else if($TransactionType == 3) {
				$sql = "UPDATE 
							transaction_salereturn SR
							JOIN transaction_cancellation TC
								ON TC.SaleReturnID = SR.SaleReturnID
						SET
							SR.IsCancelled = 0
						WHERE
							TC.CancellationID = '".$ID."'";
			}
			else if($TransactionType == 4) {
				$sql = "UPDATE 
							transaction_buyreturn BR
							JOIN transaction_cancellation TC
								ON TC.SaleReturnID = BR.SaleReturnID
						SET
							BR.IsCancelled = 0
						WHERE
							TC.CancellationID = '".$ID."'";
			}
						
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$State = 4;
						
			$sql = "UPDATE transaction_cancellation
					SET
						DeletedBy = ".$_SESSION['UserID'].",
						OutgoingID = ".$OutgoingID.",
						IncomingID = ".$IncomingID.",
						SaleReturnID = ".$SaleReturnID.",
						BuyReturnID = ".$BuyReturnID.",
						TransactionDate = NOW(),
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						CancellationID = $ID";
						
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
		}
		
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
		mysql_query("COMMIT", $dbh);
		return 0;
	}
	
	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
