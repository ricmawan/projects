<?php
	if(isset($_POST['OutgoingID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ID = mysql_real_escape_string($_POST['OutgoingID']);
		$txtDeliveryCost = str_replace(",", "", $_POST['txtDeliveryCost']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		$InvoiceNumber = 0;
		$State = 2;
		$sql = "UPDATE transaction_outgoing
				SET
					DeliveryCost = ".$txtDeliveryCost.",
					ModifiedBy = '".$_SESSION['UserLogin']."'
				WHERE
					OutgoingID = $ID";	

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber);
		mysql_query("COMMIT", $dbh);
		return 0;
	}
	
	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State,
			"InvoiceNumber" => $InvoiceNumber
		);
		return json_encode($data);
	}
?>
