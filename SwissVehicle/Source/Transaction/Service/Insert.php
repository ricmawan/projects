<?php
	if(isset($_POST['hdnServiceID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnServiceID']);
		$TransactionDate = explode('-', mysql_real_escape_string($_POST['txtTransactionDate']));
		$TransactionDate = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]";
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$MachineID = mysql_real_escape_string($_POST['ddlMachine']);
		$IsSelfWorkshop = mysql_real_escape_string($_POST['hdnIsSelfWorkshop']);
		$WorkshopName = mysql_real_escape_string($_POST['txtWorkshopName']);
		$Kilometer = str_replace(",", "", $_POST['txtKilometer']);
		$txtRemarks = mysql_real_escape_string($_POST['txtRemarks']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		for($i=1;$i<=$RecordNew;$i++) {
			$DetailsID .= mysql_real_escape_string($_POST['hdnServiceDetailsID'.$i]).",";
		}
		$DetailsID = substr($DetailsID, 0, -1);
		//echo $DetailID;

		$State = 3;
		$sql = "SELECT
					ServiceID
				FROM
					transaction_service
				WHERE
					TransactionDate <= '".$TransactionDate."'
					AND Kilometer >= ".$Kilometer."
					AND MachineID = ".$MachineID."
					AND ServiceID <> ".$ID."
					AND IsSelfWorkshop = ".$IsSelfWorkshop;

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}

		if(mysql_num_rows($result) > 0) {
			$Message = "Kilometer lebih kecil dari tanggal sebelumnya";
			$MessageDetail = "Kilometer lebih kecil dari tanggal sebelumnya";
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}

		$State = 4;
		$sql = "SELECT
					ServiceID
				FROM
					transaction_service
				WHERE
					TransactionDate >= '".$TransactionDate."'
					AND Kilometer <= ".$Kilometer."
					AND MachineID = ".$MachineID."
					AND ServiceID <> ".$ID."
					AND IsSelfWorkshop = ".$IsSelfWorkshop;

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}

		if(mysql_num_rows($result) > 0) {
			$Message = "Kilometer lebih besar dari tanggal berikutnya";
			$MessageDetail = "Kilometer lebih besar dari tanggal berikutnya";
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}

		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_service
					(
						TransactionDate,
						MachineID,
						IsSelfWorkshop,
						WorkshopName,
						Kilometer,
						Remarks,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'".$TransactionDate."',
						".$MachineID.",
						".$IsSelfWorkshop.",
						'".$WorkshopName."',
						".$Kilometer.",
						'".$txtRemarks."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_service
					SET
						TransactionDate = '".$TransactionDate."',
						MachineID = ".$MachineID.",
						Remarks = '".$txtRemarks."',
						IsSelfWorkshop = ".$IsSelfWorkshop.",
						WorkshopName = '".$WorkshopName."',
						Kilometer = ".$Kilometer.",
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						ServiceID = $ID";
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
						MAX(ServiceID) AS ServiceID
					FROM 
						transaction_service";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['ServiceID'];
		}
		
		$State = 4;
		$sql = "DELETE 
				FROM 
					transaction_servicedetails 
				WHERE
					ServiceDetailsID NOT IN($DetailsID)			 
					AND ServiceID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		for($j=1;$j<=$RecordNew;$j++) {
			if($_POST['hdnServiceDetailsID'.$j] == "0") {
				$State = 5;
				$sql = "INSERT INTO transaction_servicedetails
						(
							ServiceID,
							ItemID,
							ItemName,
							Quantity,
							Price,
							IsSecond,
							Remarks,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(
							".$ID.",
							".mysql_real_escape_string($_POST['hdnItemID'.$j]).",
							'".mysql_real_escape_string($_POST['txtItemName'.$j])."',
							".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							".str_replace(",", "", $_POST['txtPrice'.$j]).",
							".mysql_real_escape_string($_POST['hdnIsSecond'.$j]).",
							'".mysql_real_escape_string($_POST['txtRemarksDetail'.$j])."',
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 6;
				$sql = "UPDATE 
							transaction_servicedetails
						SET
							ItemID = ".mysql_real_escape_string($_POST['hdnItemID'.$j]).",
							ItemName = '".mysql_real_escape_string($_POST['txtItemName'.$j])."',
							Quantity = ".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							Price = ".str_replace(",", "", $_POST['txtPrice'.$j]).",
							IsSecond = '".mysql_real_escape_string($_POST['hdnIsSecond'.$j])."',
							Remarks = '".mysql_real_escape_string($_POST['txtRemarksDetail'.$j])."',
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							ServiceDetailsID = ".mysql_real_escape_string($_POST['hdnServiceDetailsID'.$j]);
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
