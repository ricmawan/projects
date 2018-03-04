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
		$Qty1 = 0;
		$Price2 = 0;
		$Qty2 = 0;
		$Weight = 0;
		$FailedFlag = 0;
		$ErrorMessage = "";
		$State = 1;
		
		$sql = "CALL spSelItemDetails('".$ItemCode."', '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			$FailedFlag = 1;
			$ErrorMessage = "Terjadi kesalahan sistem.";
			logEvent(mysqli_error($dbh), '/Transaction/Booking/CheckItem.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($ItemID, $ItemName, $BuyPrice, $RetailPrice, $Price1, $Qty1, $Price2, $Qty2, $Weight, $FailedFlag, $ErrorMessage);
			return 0;
		}
		
		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$ItemID = $row['ItemID'];
			$ItemName = $row['ItemName'];
			$BuyPrice = $row['BuyPrice'];
			$RetailPrice = $row['RetailPrice'];
			$Price1 = $row['Price1'];
			$Qty1 = $row['Qty1'];
			$Price2 = $row['Price2'];
			$Qty2 = $row['Qty2'];
			$Weight = $row['Weight'];
		}
		else {
			$FailedFlag = 1;
		}
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		
		echo returnstate($ItemID, $ItemName, $BuyPrice, $RetailPrice, $Price1, $Qty1, $Price2, $Qty2, $Weight, $FailedFlag, $ErrorMessage);
	}
	
	function returnstate($ItemID, $ItemName, $BuyPrice, $RetailPrice, $Price1, $Qty1, $Price2, $Qty2, $Weight, $FailedFlag, $ErrorMessage) {
		$data = array(
			"ItemID" => $ItemID, 
			"ItemName" => $ItemName, 
			"BuyPrice" => $BuyPrice,
			"RetailPrice" => $RetailPrice,
			"Price1" => $Price1,
			"Qty1" => $Qty1,
			"Price2" => $Price2,
			"Qty2" => $Qty2,
			"Weight" => $Weight,
			"FailedFlag" => $FailedFlag,
			"ErrorMessage" => $ErrorMessage
		);
		return json_encode($data);
	}
?>
