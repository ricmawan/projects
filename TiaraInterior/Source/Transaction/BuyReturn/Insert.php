<?php
	if(isset($_POST['hdnIncomingTransactionID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnIncomingTransactionID']);
		$TransactionDate = explode('-', $_POST['txtTransactionDate']);
		$_POST['txtTransactionDate'] = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]"; 
		$TransactionDate = $_POST['txtTransactionDate'];
		$SupplierID = mysql_real_escape_string($_POST['ddlSupplier']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		for($i=1;$i<=$RecordNew;$i++) {
			$DetailID .= $_POST['hdnIncomingTransactionDetailsID'.$i].",";
		}
		$DetailID = substr($DetailID, 0, -1);
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_incomingtransaction
					(
						SupplierID,
						TransactionDate,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'".$SupplierID."',
						'".$TransactionDate."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_incomingtransaction
					SET
						SupplierID = '".$SupplierID."',
						TransactionDate = '".$TransactionDate."',
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						IncomingTransactionID = $ID";
		}
		

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		if($hdnIsEdit == 0) {
			$sql = "SELECT
						MAX(IncomingTransactionID) AS IncomingTransactionID
					FROM 
						transaction_incomingtransaction";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['IncomingTransactionID'];
		}
		$State = 3;
		$sql = "DELETE 
				FROM 
					transaction_incomingtransactiondetails 
				WHERE
					IncomingTransactionDetailsID NOT IN($DetailID)			 
					AND IncomingTransactionID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		for($j=1;$j<=$RecordNew;$j++) {
			if($_POST['hdnIncomingTransactionDetailsID'.$j] == "0") {
				$State = 4;
				$sql = "INSERT INTO transaction_incomingtransactiondetails
						(
							IncomingTransactionID,
							ItemID,
							Quantity,
							Price,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(
							".$ID.",
							".$_POST['hdnItemID'.$j].",
							".$_POST['txtQuantity'.$j].",
							".str_replace(",", "", $_POST['txtPrice'.$j]).",
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 5;
				$sql = "UPDATE 
							transaction_incomingtransactiondetails
						SET
							ItemID = ".$_POST['hdnItemID'.$j].",
							Quantity = ".$_POST['txtQuantity'.$j].",
							Price = ".str_replace(",", "", $_POST['txtPrice'.$j]).",
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							IncomingTransactionDetailsID = ".$_POST['hdnIncomingTransactionDetailsID'.$j];
			}

			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}

			$sql = "UPDATE 
						master_item
					SET
						Price = ".str_replace(",", "", $_POST['txtPrice'.$j]).",
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						ItemID = ".$_POST['hdnItemID'.$j];
						
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
			"Id" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
