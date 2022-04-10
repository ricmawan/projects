<?php
	if(isset($_POST['hdnStockMutationID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$StockMutationID = mysqli_real_escape_string($dbh, $_POST['hdnStockMutationID']);
		$StockMutationDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnStockMutationDetailsID']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['hdnTransactionDate']);
		$SourceID = mysqli_real_escape_string($dbh, $_POST['ddlSourceBranch']);
		$DestinationID = mysqli_real_escape_string($dbh, $_POST['ddlDestinationBranch']);
		$ItemID = mysqli_real_escape_string($dbh, $_POST['hdnItemID']);
		$ItemDetailsID = mysqli_real_escape_string($dbh, $_POST['hdnItemDetailsID']);
		if($ItemDetailsID == "") $ItemDetailsID = "NULL";
		$Qty = mysqli_real_escape_string($dbh, $_POST['txtQTY']);
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsStockMutation(".$StockMutationID.", ".$SourceID.", ".$DestinationID.", '".$TransactionDate."', ".$StockMutationDetailsID.", ".$ItemID.", ".$ItemDetailsID.", ".$Qty.", ".$_SESSION['UserID'].", '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/StockMutation/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($StockMutationID, $StockMutationDetailsID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['StockMutationDetailsID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	}
	
	function returnstate($ID, $StockMutationDetailsID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"StockMutationDetailsID" => $StockMutationDetailsID,
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
