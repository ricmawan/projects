<?php
	header('Content-Type: application/json');
	if(isset($_POST['itemCode'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$ItemCode = mysqli_real_escape_string($dbh, $_POST['itemCode']);
		$ItemID = 0;
		$ItemName = "";
		$BuyPrice = 0;
		$RetailPrice = 0;
		$Price1 = 0;
		$Price2 = 0;
		$FailedFlag = 0;
		$ErrorMessage = "";
		$State = 1;
		
		$sql = "CALL spSelItemDetails(".$ItemCode.", '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			$FailedFlag = 1;
			$ErrorMessage = "Terjadi kesalahan sistem.";
			logEvent(mysqli_error($dbh), '/Transaction/Purchase/CheckItem.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($ItemID, $ItemName, $BuyPrice, $RetailPrice, $Price1, $Price2, $FailedFlag, $ErrorMessage);
			return 0;
		}
		
		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$ItemID = $row['ItemID'];
			$ItemName = $row['ItemName'];
			$BuyPrice = $row['BuyPrice'];
			$RetailPrice = $row['RetailPrice'];
			$Price1 = $row['Price1'];
			$Price2 = $row['Price2'];
		}
		else {
			$FailedFlag = 1;
		}
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		
		echo returnstate($ItemID, $ItemName, $BuyPrice, $RetailPrice, $Price1, $Price2, $FailedFlag, $ErrorMessage);
	}
	
	function returnstate($ItemID, $ItemName, $BuyPrice, $RetailPrice, $Price1, $Price2, $FailedFlag, $ErrorMessage) {
		$data = array(
			"ItemID" => $ItemID, 
			"ItemName" => $ItemName, 
			"BuyPrice" => $BuyPrice,
			"RetailPrice" => $RetailPrice,
			"Price1" => $Price1,
			"Price2" => $Price2,
			"FailedFlag" => $FailedFlag,
			"ErrorMessage" => $ErrorMessage
		);
		return json_encode($data);
	}
?>
