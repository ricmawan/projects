<?php
	if(isset($_POST['hdnIncomingInventoryID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnIncomingInventoryID']);
		$TransactionDate = explode('-', mysql_real_escape_string($_POST['txtTransactionDate']));
		$TransactionDate = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]";
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		for($i=1;$i<=$RecordNew;$i++) {
			$DetailsID .= mysql_real_escape_string($_POST['hdnIncomingInventoryDetailsID'.$i]).",";
		}
		$DetailsID = substr($DetailsID, 0, -1);
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_incominginventory
					(
						TransactionDate,						
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'".$TransactionDate."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_incominginventory
					SET
						TransactionDate = '".$TransactionDate."',
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						IncomingInventoryID = $ID";
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
			$State = 3;
			$sql = "SELECT
						MAX(IncomingInventoryID) AS IncomingInventoryID
					FROM 
						transaction_incominginventory";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['IncomingInventoryID'];
		}
		
		$State = 5;
		$sql = "DELETE 
				FROM 
					transaction_incominginventorydetails 
				WHERE
					IncomingInventoryDetailsID NOT IN($DetailsID)			 
					AND IncomingInventoryID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		for($j=1;$j<=$RecordNew;$j++) {
			if($_POST['hdnIncomingInventoryDetailsID'.$j] == "0") {
				$State = 6;
				$sql = "INSERT INTO transaction_incominginventorydetails
						(
							IncomingInventoryID,
							InventoryID,
							Quantity,
							Price,
							Remarks,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(
							".$ID.",
							".mysql_real_escape_string($_POST['hdnInventoryID'.$j]).",
							".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							".str_replace(",", "", $_POST['txtPrice'.$j]).",
							'".mysql_real_escape_string($_POST['txtRemarks'.$j])."',
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 7;
				$sql = "UPDATE 
							transaction_incominginventorydetails
						SET
							InventoryID = ".mysql_real_escape_string($_POST['hdnInventoryID'.$j]).",
							Quantity = ".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							Price = ".str_replace(",", "", $_POST['txtPrice'.$j]).",
							Remarks = '".mysql_real_escape_string($_POST['txtRemarks'.$j])."',
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							IncomingInventoryDetailsID = ".mysql_real_escape_string($_POST['hdnIncomingInventoryDetailsID'.$j]);
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
