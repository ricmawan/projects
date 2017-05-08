<?php
	if(isset($_POST['SelectedDate']) && isset($_POST['InvoiceNumberType'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Date = str_replace("-", "", mysql_real_escape_string($_POST['SelectedDate']));
		$SelectedDate = explode('-', mysql_real_escape_string($_POST['SelectedDate']));
		$Date = $SelectedDate[0].$SelectedDate[1].substr($SelectedDate[2], -2);
		$SelectedDate = "$SelectedDate[2]-$SelectedDate[1]-$SelectedDate[0]";
		//$SelectedDate[2] = substr($SelectedDate, -2);
		$InvoiceNumberType = mysql_real_escape_string($_POST['InvoiceNumberType']);
		$InvoiceNumber = "0";
		$State = 1;
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		
		$sql = "DELETE TIN
				FROM
					transaction_invoicenumber TIN
				WHERE
					TIN.InvoiceNumberType = 'SA'
					AND TIN.DeleteFlag = 0
					AND MINUTE(TIMEDIFF(NOW(), TIN.CreatedDate)) > 60
					AND	NOT EXISTS
						(
							SELECT
								1
							FROM
								transaction_firststock FS
							WHERE
								TRIM(FS.FirstStockNumber) = TRIM(TIN.InvoiceNumber)
						)";
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($InvoiceNumber, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		$State = 2;
		$sql = "SELECT
					CONCAT('".$InvoiceNumberType."-', '".$Date."', RIGHT(CONCAT('0000', COUNT(1) + 1), 3)) AS InvoiceNumber
				FROM 
					transaction_invoicenumber
				WHERE
					InvoiceDate = '".$SelectedDate."'
					AND InvoiceNumberType = '".$InvoiceNumberType."'";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($InvoiceNumber, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row = mysql_fetch_array($result);
		$InvoiceNumber = $row['InvoiceNumber'];
		
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
					'SA',
					'".$SelectedDate."',
					'".$InvoiceNumber."',
					0,
					NOW(),
					'".$_SESSION['UserLogin']."'
				)";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($InvoiceNumber, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		echo returnstate($InvoiceNumber, $Message, $MessageDetail, $FailedFlag, $State);
		return 0;
	}
	
	function returnstate($InvoiceNumber, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"InvoiceNumber" => $InvoiceNumber, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
