<?php
	if(ISSET($_POST['FirstStockID']) && ISSET($_POST['SupplierID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$FirstStockID = mysqli_real_escape_string($dbh, $_POST['FirstStockID']);
		$FirstStockNumber = mysqli_real_escape_string($dbh, $_POST['FirstStockNumber']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['TransactionDate']);
		$Message = "Cabang berhasil diubah";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "CALL spUpdFirstStock(".$FirstStockID.", '".$FirstStockNumber."', '".$TransactionDate."', '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/FirstStock/UpdateHeader.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
