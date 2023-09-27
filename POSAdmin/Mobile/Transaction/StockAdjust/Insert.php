<?php
	if(isset($_POST['dataJSON'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$dataJSON = json_decode($_POST['dataJSON'], true);
		//var_dump($dataJSON);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['TransactionDate']);
		$BranchID = mysqli_real_escape_string($dbh, $_POST['BranchID']);
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$StockAdjustID = 0;
		
		$StockAdjustData = array();
		if(ISSET($dataJSON)) {
			foreach($dataJSON as $data) {
				$StockAdjustData[] = "(StockAdjustID, " . $data['ItemID'] . ", " . $BranchID . ", " . $data['Quantity'] . ", " . $data['AdjustedQuantity'] . ", " . $data['BuyPrice'] . ", " . $data['SalePrice'] . ", NOW(), UserLogin)";
			}
		}

		$sql = "CALL spInsStockAdjustMobile(".$BranchID.", '".$TransactionDate."', '".implode(",", $StockAdjustData)."', '".$_SESSION['UserLoginMobile']."')";

		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/SaleReturn/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
			echo returnstate($StockAdjustID, $Message, $MessageDetail, $FailedFlag, $State);
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
