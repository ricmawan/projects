<?php
	if(isset($_POST['hdnPurchaseReturnID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$PurchaseReturnID = mysqli_real_escape_string($dbh, $_POST['hdnPurchaseReturnID']);
		$PurchaseReturnNumber = mysqli_real_escape_string($dbh, $_POST['txtPurchaseReturnNumber']);
		$PurchaseReturnDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnPurchaseReturnDetailsID']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['hdnTransactionDate']);
		$BranchID = mysqli_real_escape_string($dbh, $_POST['ddlBranch']);
		$SupplierID = mysqli_real_escape_string($dbh, $_POST['ddlSupplier']);
		$ItemID = mysqli_real_escape_string($dbh, $_POST['hdnItemID']);
		$ItemDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnItemDetailsID']);
		if($ItemDetailsID == "") $ItemDetailsID = "NULL";
		$Qty = mysqli_real_escape_string($dbh, $_POST['txtQTY']);
		$BuyPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['hdnBuyPrice']));
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsPurchaseReturn(".$PurchaseReturnID.", '".$PurchaseReturnNumber."', ".$SupplierID.", '".$TransactionDate."', ".$PurchaseReturnDetailsID.", ".$BranchID.", ".$ItemID.", ".$ItemDetailsID.", ".$Qty.", ".$BuyPrice.", ".$_SESSION['UserID'].", '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Master/PurchaseReturn/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($PurchaseReturnID, $PurchaseReturnDetailsID, $PurchaseReturnNumber, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['PurchaseReturnDetailsID'], $row['PurchaseReturnNumber'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	}
	
	function returnstate($ID, $PurchaseReturnDetailsID, $PurchaseReturnNumber, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"PurchaseReturnDetailsID" => $PurchaseReturnDetailsID,
			"PurchaseReturnNumber" => $PurchaseReturnNumber,
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
