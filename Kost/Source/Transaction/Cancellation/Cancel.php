<?php
	SESSION_START();
	if(isset($_POST['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		//include "../../GetPermission.php";
		include "../../DBConfig.php";
		$BookingID = mysql_real_escape_string($_POST['ID']);
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Pembatalan Berhasil!";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;
		$State = 2;
		$sql = "UPDATE transaction_booking
				SET
					IsCancelled = 1,
					ModifiedBy = '".$_SESSION['UserLogin']."'
				WHERE
					BookingID = $BookingID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($BookingID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		echo returnstate($BookingID, $Message, $MessageDetail, $FailedFlag, $State);
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
