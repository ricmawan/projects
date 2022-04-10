<?php
	if(ISSET($_POST['TransactionID']) && ISSET($_POST['Payment'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$TransactionID = mysqli_real_escape_string($dbh, $_POST['TransactionID']);
		$TransactionType = mysqli_real_escape_string($dbh, $_POST['TransactionType']);
		$Payment = mysqli_real_escape_string($dbh, $_POST['Payment']);
		$Message = "Pembayaran berhasil";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "CALL spUpdPayment(".$TransactionID.", ".$Payment.", '".$TransactionType."', '".$_SESSION['UserLoginKasir']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/Payment/UpdatePayment.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
			echo returnstate($TransactionID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		
		$row=mysqli_fetch_array($result);
		
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
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
