<?php
	if(ISSET($_POST['BookingID']) && ISSET($_POST['CustomerID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$BookingID = mysqli_real_escape_string($dbh, $_POST['BookingID']);
		$CustomerID = mysqli_real_escape_string($dbh, $_POST['CustomerID']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['TransactionDate']);
		$Message = "Cabang berhasil diubah";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "CALL spUpdBooking(".$BookingID.", ".$CustomerID.", '".$TransactionDate."', '".$_SESSION['UserLoginMobile']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/Booking/UpdateHeader.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
			echo returnstate($BookingDetailsID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		
		$row=mysqli_fetch_array($result);
		
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
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
