<?php
	if(isset($_POST['hdnStockAdjustID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$StockAdjustID = mysqli_real_escape_string($dbh, $_POST['hdnStockAdjustID']);
		$StockAdjustDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnStockAdjustDetailsID']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['hdnTransactionDate']);
		$BranchID = mysqli_real_escape_string($dbh, $_POST['ddlBranch']);
		$ItemID = mysqli_real_escape_string($dbh, $_POST['hdnItemID']);
		$Qty = mysqli_real_escape_string($dbh, $_POST['txtQTY']);
		$AdjustedQty = mysqli_real_escape_string($dbh, $_POST['txtAdjustedQTY']);
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsStockAdjust(".$StockAdjustID.", ".$BranchID.", '".$TransactionDate."', ".$StockAdjustDetailsID.", ".$ItemID.", ".$Qty.", ".$AdjustedQty.", ".$_SESSION['UserID'].", '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/StockAdjust/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($StockAdjustID, $StockAdjustDetailsID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['StockAdjustDetailsID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	}
	
	function returnstate($ID, $StockAdjustDetailsID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"StockAdjustDetailsID" => $StockAdjustDetailsID,
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
