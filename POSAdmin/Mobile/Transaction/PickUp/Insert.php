<?php
	if(isset($_POST['hdnBookingID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$BookingID = mysqli_real_escape_string($dbh, $_POST['hdnBookingID']);
		$PickUpID = mysqli_real_escape_string($dbh, $_POST['hdnPickUpID']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['hdnTransactionDate']);
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$hdnIsEdit = mysqli_real_escape_string($dbh, $_POST['hdnIsEdit']);
		$PickUpData = array();
		if(ISSET($_POST['chkBookingDetails'])) {
			foreach($_POST['chkBookingDetails'] as $selected){
				$ItemDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnItemDetailsID'.$selected]);
				if($ItemDetailsID == "") $ItemDetailsID = "NULL";
				$PickUpData[] = "(".$PickUpID.", ".$_POST['hdnItemID'.$selected].", ".$ItemDetailsID.", ".$_POST['hdnBranchID'.$selected].", ".$_POST['txtQty'.$selected].", ".$_POST['hdnBuyPrice'.$selected].", ".$_POST['hdnBookingPrice'.$selected].", ".$_POST['hdnDiscount'.$selected].", ".$_POST['hdnBookingDetailsID'.$selected].", NOW(), UserLogin)";
			}
		}

		$sql = "CALL spInsPickUp(".$PickUpID.", ".$BookingID.", '".$TransactionDate."', '".implode(",", $PickUpData)."', ".$hdnIsEdit.", '".$_SESSION['UserLoginMobile']."')";

		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/PickUp/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
			echo returnstate($PickUpID, $Message, $MessageDetail, $FailedFlag, $State);
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
