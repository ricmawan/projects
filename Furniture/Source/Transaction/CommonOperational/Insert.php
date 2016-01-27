<?php
	if(isset($_POST['hdnCommonOperationalID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnCommonOperationalID']);
		$TransactionDate = explode('-', $_POST['txtTransactionDate']);
		$_POST['txtTransactionDate'] = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]"; 
		$TransactionDate = $_POST['txtTransactionDate'];
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		for($i=1;$i<=$RecordNew;$i++) {
			$DetailID .= $_POST['hdnCommonOperationalDetailsID'.$i].",";
		}
		$DetailID = substr($DetailID, 0, -1);
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_commonoperational
					(
						CommonOperationalDate,
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
			$sql = "UPDATE transaction_commonoperational
					SET
						CommonOperationalDate = '".$TransactionDate."',
						ModifiedDate = NOW(),
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						CommonOperationalID = $ID";
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
						MAX(CommonOperationalID) AS CommonOperationalID
					FROM 
						transaction_commonoperational";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['CommonOperationalID'];
		}
		$State = 3;
		$sql = "DELETE 
				FROM 
					transaction_commonoperationaldetails 
				WHERE
					CommonOperationalDetailsID NOT IN($DetailID)			 
					AND CommonOperationalID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		for($j=1;$j<=$RecordNew;$j++) {
			if($_POST['hdnCommonOperationalDetailsID'.$j] == "0") {
				$State = 4;
				$sql = "INSERT INTO transaction_commonoperationaldetails
						(
							CommonOperationalID,
							Remarks,
							Amount,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(
							".$ID.",
							'".$_POST['txtRemarks'.$j]."',
							".str_replace(",", "", $_POST['txtTotal'.$j]).",
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 5;
				$sql = "UPDATE 
							transaction_commonoperationaldetails
						SET
							Remarks = '".$_POST['txtRemarks'.$j]."',
							Amount = ".str_replace(",", "", $_POST['txtTotal'.$j]).",
							ModifiedDate = NOW(),
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							CommonOperationalDetailsID = ".$_POST['hdnCommonOperationalDetailsID'.$j];
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
