<?php
	if(isset($_POST['hdnFuelID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ID = mysql_real_escape_string($_POST['hdnFuelID']);
		$FuelTypeID = mysql_real_escape_string($_POST['ddlFuelType']);
		$TransactionDate = explode('-', mysql_real_escape_string($_POST['txtTransactionDate']));
		$TransactionDate = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]";
		$MachineID = mysql_real_escape_string($_POST['ddlMachine']);
		$Kilometer = str_replace(",", "", $_POST['txtKilometer']);
		$Quantity = str_replace(",", "", $_POST['txtQuantity']);
		$Remarks = mysql_real_escape_string($_POST['txtRemarks']);
		$Price = str_replace(",", "", $_POST['txtPrice']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;

		$sql = "SELECT
					FuelID
				FROM
					transaction_fuel
				WHERE
					TransactionDate = '".$TransactionDate."'
					AND MachineID = ".$MachineID."
					AND FuelID <> ".$ID;
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}

		if(mysql_num_rows($result) > 0) {
			$Message = "Tanggal & Mobil yang dipilih sudah diinput";
			$MessageDetail = "Tanggal & Mobil yang dipilih sudah diinput";
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}

		$State = 3;
		$sql = "SELECT
					FuelID
				FROM
					transaction_fuel
				WHERE
					TransactionDate < '".$TransactionDate."'
					AND Kilometer >= ".$Kilometer."
					AND MachineID = ".$MachineID."
					AND FuelID <> ".$ID;

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
					FuelID
				FROM
					transaction_fuel
				WHERE
					TransactionDate > '".$TransactionDate."'
					AND Kilometer <= ".$Kilometer."
					AND MachineID = ".$MachineID."
					AND FuelID <> ".$ID;

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
			$State = 5;
			$sql = "INSERT INTO transaction_fuel
					(
						FuelTypeID,
						TransactionDate,
						MachineID,
						Kilometer,
						Quantity,
						Price,
						Remarks,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						".$FuelTypeID.",
						'".$TransactionDate."',
						".$MachineID.",
						".$Kilometer.",
						".$Quantity.",
						".$Price.",
						'".$Remarks."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 6;
			$sql = "UPDATE transaction_fuel
					SET
						FuelTypeID = ".$FuelTypeID.",
						TransactionDate = '".$TransactionDate."',
						MachineID = ".$MachineID.",
						Kilometer = ".$Kilometer.",
						Quantity = ".$Quantity.",
						Price = ".$Price.",
						Remarks = '".$Remarks."',
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						FuelID = $ID";
		}
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
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
