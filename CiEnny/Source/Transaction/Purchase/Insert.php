<?php
	if(isset($_POST['hdnPurchaseID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$PurchaseID = mysqli_real_escape_string($dbh, $_POST['hdnPurchaseID']);
		$PurchaseDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnPurchaseDetailsID']);
		$PurchaseNumber = mysqli_real_escape_string($dbh, $_POST['txtPurchaseNumber']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['hdnTransactionDate']);
		$BranchID = mysqli_real_escape_string($dbh, $_POST['ddlBranch']);
		$SupplierID = mysqli_real_escape_string($dbh, $_POST['ddlSupplier']);
		$ItemID = mysqli_real_escape_string($dbh, $_POST['hdnItemID']);
		$Qty = mysqli_real_escape_string($dbh, $_POST['txtQTY']);
		$BuyPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtBuyPrice']));
		$RetailPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtRetailPrice']));
		$Price1 = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtPrice1']));
		$Price2 = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtPrice2']));
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsPurchase(".$PurchaseID.", '".$PurchaseNumber."', ".$SupplierID.", '".$TransactionDate."', ".$PurchaseDetailsID.", ".$BranchID.", ".$ItemID.", ".$Qty.", ".$BuyPrice.", ".$RetailPrice.", ".$Price1.", ".$Price2.", '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/Purchase/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($PurchaseID, $PurchaseDetailsID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['PurchaseDetailsID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	}
	
	function returnstate($ID, $PurchaseDetailsID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"PurchaseDetailsID" => $PurchaseDetailsID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>