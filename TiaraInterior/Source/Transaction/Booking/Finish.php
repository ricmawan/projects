<?php
	if(isset($_POST['hdnBookingID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnBookingID']);
		$State = 1;
		$Date = date("Y-m-d");
		$FriendlyDate = date("dmy");
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		$InvoiceNumber = "";
		$State = 1;
		$sql = "SELECT
					CONCAT('TJ-', '".$FriendlyDate."', RIGHT(CONCAT('000', COUNT(1) + 1), 3)) AS InvoiceNumber
				FROM 
					transaction_invoicenumber
				WHERE
					InvoiceDate = '".$Date."'
					AND InvoiceNumberType = 'TJ'";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($InvoiceNumber, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row = mysql_fetch_array($result);
		$InvoiceNumber = $row['InvoiceNumber'];
		
		$State = 2;
		$sql = "INSERT INTO transaction_outgoing
				(
					OutgoingNumber,
					SalesID,
					CustomerID,
					TransactionDate,
					DeliveryCost,
					Remarks,
					IsCancelled,
					CreatedDate,
					CreatedBy
				)
				SELECT
					'".$InvoiceNumber."',
					SalesID,
					CustomerID,
					NOW(),
					0,
					Remarks,
					0,
					NOW(),
					'".$_SESSION['UserLogin']."'
				FROM
					transaction_booking
				WHERE
					BookingID = $ID";
					
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		$State = 3;
		$sql = "INSERT INTO transaction_invoicenumber
				(
					InvoiceNumberType,
					InvoiceDate,
					InvoiceNumber,
					DeleteFlag,
					CreatedDate,
					CreatedBy
				)
				VALUES
				(
					'TJ',
					'".$Date."',
					'".$InvoiceNumber."',
					0,
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
			
		$State = 4;
		$sql = "SELECT
					MAX(OutgoingID) AS OutgoingID
				FROM 
					transaction_outgoing";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		$row = mysql_fetch_array($result);
		$OutgoingID = $row['OutgoingID'];
		
		$State = 6;
		$sql = "INSERT INTO transaction_outgoingdetails
				(
					OutgoingID,
					TypeID,
					Quantity,
					BuyPrice,
					SalePrice,
					Discount,
					IsPercentage,
					BatchNumber,
					Remarks,
					CreatedDate,
					CreatedBy
				)
				SELECT
					".$OutgoingID.",
					TypeID,
					Quantity,
					BuyPrice,
					SalePrice,
					Discount,
					IsPercentage,
					BatchNumber,
					Remarks,
					NOW(),
					'".$_SESSION['UserLogin']."'
				FROM
					transaction_bookingdetails
				WHERE
					BookingID = $ID";
					
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}

		$sql = "UPDATE transaction_booking
				SET
					BookingStatusID = 2
				WHERE
					BookingID = $ID";
					
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
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
