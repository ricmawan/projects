<?php
	if(isset($_POST['hdnOutgoingTransactionID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnOutgoingTransactionID']);
		$TransactionDate = explode('-', $_POST['txtTransactionDate']);
		$_POST['txtTransactionDate'] = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]"; 
		$TransactionDate = $_POST['txtTransactionDate'];
		$ProjectID = mysql_real_escape_string($_POST['ddlProject']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		for($i=1;$i<=$RecordNew;$i++) {
			$DetailID .= $_POST['hdnOutgoingTransactionDetailsID'.$i].",";
		}
		$DetailID = substr($DetailID, 0, -1);
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_outgoingtransaction
					(
						ProjectID,
						TransactionDate,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						".$ProjectID.",
						'".$TransactionDate."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_outgoingtransaction
					SET
						ProjectID = ".$ProjectID.",
						TransactionDate = '".$TransactionDate."',
						ModifiedDate = NOW(),
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						OutgoingTransactionID = $ID";
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
						MAX(OutgoingTransactionID) AS OutgoingTransactionID
					FROM 
						transaction_outgoingtransaction";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['OutgoingTransactionID'];
		}
		$State = 3;
		$sql = "DELETE 
				FROM 
					transaction_outgoingtransactiondetails 
				WHERE
					OutgoingTransactionDetailsID NOT IN($DetailID)			 
					AND OutgoingTransactionID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		for($j=1;$j<=$RecordNew;$j++) {
			if($_POST['hdnOutgoingTransactionDetailsID'.$j] == "0") {
				$State = 4;
				$sql = "INSERT INTO transaction_outgoingtransactiondetails
						(
							OutgoingTransactionID,
							ItemID,
							Quantity,
							Price,
							Name,
							Remarks,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(
							".$ID.",
							".$_POST['hdnItemID'.$j].",
							".$_POST['txtQuantity'.$j].",
							".str_replace(",", "", $_POST['txtPrice'.$j]).",
							'".$_POST['txtName'.$j]."',
							'".$_POST['txtRemarks'.$j]."',
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 5;
				$sql = "UPDATE 
							transaction_outgoingtransactiondetails
						SET
							ItemID = ".$_POST['hdnItemID'.$j].",
							Quantity = ".$_POST['txtQuantity'.$j].",
							Price = ".str_replace(",", "", $_POST['txtPrice'.$j]).",
							Name = '".$_POST['txtName'.$j]."',
							Remarks = '".$_POST['txtRemarks'.$j]."',
							ModifiedDate = NOW(),
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							OutgoingTransactionDetailsID = ".$_POST['hdnOutgoingTransactionDetailsID'.$j];
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
			"Id" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
