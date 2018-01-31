<?php
	if(isset($_POST['hdnSaleID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$SaleID = mysqli_real_escape_string($dbh, $_POST['hdnSaleID']);
		$SaleDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnSaleDetailsID']);
		$SaleNumber = "";
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['hdnTransactionDate']);
		$BranchID = mysqli_real_escape_string($dbh, $_POST['hdnBranchID']);
		$RetailFlag = mysqli_real_escape_string($dbh, $_POST['hdnIsRetail']);
		$CustomerID = mysqli_real_escape_string($dbh, $_POST['ddlCustomer']);
		$ItemID = mysqli_real_escape_string($dbh, $_POST['hdnItemID']);
		$Qty = mysqli_real_escape_string($dbh, $_POST['txtQTY']);
		$BuyPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['hdnBuyPrice']));
		$SalePrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtSalePrice']));
		$Discount = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtDiscount']));
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsSale(".$SaleID.", ".$RetailFlag.", ".$CustomerID.", '".$TransactionDate."', ".$SaleDetailsID.", ".$BranchID.", ".$ItemID.", ".$Qty.", ".$BuyPrice.", ".$SalePrice.", ".$Discount.", ".$_SESSION['UserID'].", '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/Sale/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($SaleID, $SaleDetailsID, $SaleNumber, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['SaleDetailsID'], $row['SaleNumber'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	}
	
	function returnstate($ID, $SaleDetailsID, $SaleNumber, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"SaleDetailsID" => $SaleDetailsID,
			"SaleNumber" => $SaleNumber,
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
