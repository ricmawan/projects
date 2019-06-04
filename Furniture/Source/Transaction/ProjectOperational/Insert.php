<?php
	if(isset($_POST['hdnProjectID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnProjectID']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		if($RecordNew > 0) {
			for($i=1;$i<=$RecordNew;$i++) {
				$DetailID .= $_POST['hdnProjectTransactionID'.$i].",";
			}
			$DetailID = substr($DetailID, 0, -1);
		}
		else {
			$DetailID = 0;
		}
		$State = 1;
		$sql = "DELETE 
				FROM 
					transaction_projecttransaction
				WHERE
					ProjectTransactionID NOT IN($DetailID)			 
					AND ProjectID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		if($RecordNew > 0) {
			for($j=1;$j<=$RecordNew;$j++) {
				$TransactionDate = explode('-', $_POST['txtTransactionDate'.$j]);
				$_POST['txtTransactionDate'.$j] = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]"; 
				$TransactionDate = $_POST['txtTransactionDate'.$j];
				if($_POST['hdnProjectTransactionID'.$j] == "0") {
					$State = 4;
					$sql = "INSERT INTO transaction_projecttransaction
							(
								ProjectID,
								ProjectTransactionDate,
								Remarks,
								Amount,
								CreatedDate,
								CreatedBy
							)
							VALUES
							(
								".$ID.",
								'".$TransactionDate."',
								'".$_POST['txtTransactionRemarks'.$j]."',
								".str_replace(",", "", $_POST['txtTransactionAmount'.$j]).",
								NOW(),
								'".$_SESSION['UserLogin']."'
							)";
				}
				else {
					$State = 5;
					$sql = "UPDATE 
								transaction_projecttransaction
							SET
								ProjectTransactionDate = '".$TransactionDate."',
								Remarks = '".$_POST['txtTransactionRemarks'.$j]."',
								Amount = ".str_replace(",", "", $_POST['txtTransactionAmount'.$j]).",
								ModifiedDate = NOW(),
								ModifiedBy = '".$_SESSION['UserLogin']."'
							WHERE
								ProjectTransactionID = ".$_POST['hdnProjectTransactionID'.$j];
				}

				if (! $result = mysql_query($sql, $dbh)) {
					$Message = "Terjadi Kesalahan Sistem".$j;
					$MessageDetail = mysql_error();
					$FailedFlag = 1;
					echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
					mysql_query("ROLLBACK", $dbh);
					return 0;					
				}							
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
