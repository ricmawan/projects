<?php
	if(isset($_POST['SaleID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$SaleID = mysql_real_escape_string($_POST['SaleID']);
		$TableID = mysql_real_escape_string($_POST['TableID']);
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$Message = "Pembatalan Berhasil!";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;
		$State = 2;
		$sql = "UPDATE transaction_sale
				SET
					IsCancelled = 1,
					ModifiedBy = '".$_SESSION['UserLogin']."'
				WHERE
					SaleID = $SaleID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($SaleID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		$sql = "UPDATE master_table
				SET
					TableStatusID = 1,
					ModifiedBy = '".$_SESSION['UserLogin']."'
				WHERE
					TableID = $TableID";
					
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($SaleID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		echo returnstate($SaleID, $Message, $MessageDetail, $FailedFlag, $State);
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
