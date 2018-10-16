<?php
	if(isset($_POST['hdnBookingID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$BookingID = mysqli_real_escape_string($dbh, $_POST['hdnBookingID']);
		$BookingDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnBookingDetailsID']);
		$BookingNumber = mysqli_real_escape_string($dbh, $_POST['hdnBookingNumber']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['hdnTransactionDate']);
		$BranchID = mysqli_real_escape_string($dbh, $_POST['hdnBranchID']);
		$RetailFlag = mysqli_real_escape_string($dbh, $_POST['hdnIsRetail']);
		$CustomerID = mysqli_real_escape_string($dbh, $_POST['ddlCustomer']);
		$ItemID = mysqli_real_escape_string($dbh, $_POST['hdnItemID']);
		$ItemDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnItemDetailsID']);
		if($ItemDetailsID == "") $ItemDetailsID = "NULL";
		$Qty = mysqli_real_escape_string($dbh, $_POST['txtQTY']);
		$BuyPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['hdnBuyPrice']));
		$BookingPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['hdnBookingPrice']));
		$Discount = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtDiscount']));
		$FinishFlag = $FINSH_DEFAULT;
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsBooking(".$BookingID.", '".$BookingNumber."', ".$RetailFlag.", ".$FinishFlag.", ".$CustomerID.", '".$TransactionDate."', ".$BookingDetailsID.", ".$BranchID.", ".$ItemID.", ".$ItemDetailsID.", ".$Qty.", ".$BuyPrice.", ".$BookingPrice.", ".$Discount.", ".$_SESSION['UserID'].", '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/Booking/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($BookingID, $BookingDetailsID, $BookingNumber, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['BookingDetailsID'], $row['BookingNumber'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	}
	
	function returnstate($ID, $BookingDetailsID, $BookingNumber, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"BookingDetailsID" => $BookingDetailsID,
			"BookingNumber" => $BookingNumber,
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
