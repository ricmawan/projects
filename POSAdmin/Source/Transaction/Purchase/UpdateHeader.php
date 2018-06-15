<?php
	if(ISSET($_POST['PurchaseID']) && ISSET($_POST['SupplierID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$PurchaseID = mysqli_real_escape_string($dbh, $_POST['PurchaseID']);
		$PurchaseNumber = mysqli_real_escape_string($dbh, $_POST['PurchaseNumber']);
		$SupplierID = mysqli_real_escape_string($dbh, $_POST['SupplierID']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['TransactionDate']);
		$Deadline = mysqli_real_escape_string($dbh, $_POST['Deadline']);
		$PaymentType = mysqli_real_escape_string($dbh, $_POST['PaymentType']);
		$Message = "Cabang berhasil diubah";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "CALL spUpdPurchase(".$PurchaseID.", '".$PurchaseNumber."', ".$SupplierID.", '".$TransactionDate."', '".$Deadline."', ".$PaymentType.", '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/Purchase/UpdateHeader.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($SaleDetailsID, $Message, $MessageDetail, $FailedFlag, $State);
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
