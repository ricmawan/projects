<?php
	if(isset($_POST['hdnFirstStockID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$FirstStockID = mysqli_real_escape_string($dbh, $_POST['hdnFirstStockID']);
		$FirstStockDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnFirstStockDetailsID']);
		$FirstStockNumber = mysqli_real_escape_string($dbh, $_POST['txtFirstStockNumber']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['hdnTransactionDate']);
		$BranchID = mysqli_real_escape_string($dbh, $_POST['ddlBranch']);
		$ItemID = mysqli_real_escape_string($dbh, $_POST['hdnItemID']);
		$ItemDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnItemDetailsID']);
		if($ItemDetailsID == "") $ItemDetailsID = "NULL";
		$Qty = mysqli_real_escape_string($dbh, $_POST['txtQTY']);
		$BuyPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['hdnBuyPrice']));
		$RetailPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['hdnRetailPrice']));
		$Price1 = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['hdnPrice1']));
		$Price2 = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['hdnPrice2']));
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsFirstStock(".$FirstStockID.", '".$FirstStockNumber."', '".$TransactionDate."', ".$FirstStockDetailsID.", ".$BranchID.", ".$ItemID.", ".$ItemDetailsID.", ".$Qty.", ".$BuyPrice.", ".$RetailPrice.", ".$Price1.", ".$Price2.", '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/FirstStock/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($FirstStockID, $FirstStockDetailsID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['FirstStockDetailsID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	}
	
	function returnstate($ID, $FirstStockDetailsID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"FirstStockDetailsID" => $FirstStockDetailsID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
