<?php
	if(isset($_POST['SelectedDate']) && isset($_POST['InvoiceNumberType'])) {
		include "../../DBConfig.php";
		$Date = str_replace("-", "", mysql_real_escape_string($_POST['SelectedDate']));
		$SelectedDate = explode('-', mysql_real_escape_string($_POST['SelectedDate']));
		$SelectedDate = "$SelectedDate[2]-$SelectedDate[1]-$SelectedDate[0]";
		$InvoiceNumberType = mysql_real_escape_string($_POST['InvoiceNumberType']);
		$InvoiceNumber = "0";
		$State = 1;
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		
		$sql = "SELECT
					CONCAT('".$InvoiceNumberType."-', '".$Date."', RIGHT(CONCAT('0000', COUNT(1) + 1), 4)) AS InvoiceNumber
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
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		$row = mysql_fetch_array($result);
		$InvoiceNumber = $row['InvoiceNumber'];
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
