<?php
	if(isset($_POST['hdnPaymentDetailsID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$TransactionID = mysqli_real_escape_string($dbh, $_POST['hdnTransactionID']);
		$PaymentDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnPaymentDetailsID']);
		$PaymentDate = mysqli_real_escape_string($dbh, $_POST['hdnPaymentDate']);
		$TransactionType = mysqli_real_escape_string($dbh, $_POST['hdnTransactionType']);
		$Remarks = mysqli_real_escape_string($dbh, $_POST['txtRemarks']);
		$Amount = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtAmount']));
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsPayment(".$TransactionID.", '".$PaymentDate."', '".$TransactionType."', ".$PaymentDetailsID.", ".$Amount.", '".$Remarks."', '".$_SESSION['UserLoginMobile']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/Payment/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
			echo returnstate($PaymentID, $PaymentDetailsID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['PaymentDetailsID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	}
	
	function returnstate($ID, $PaymentDetailsID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"PaymentDetailsID" => $PaymentDetailsID,
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
