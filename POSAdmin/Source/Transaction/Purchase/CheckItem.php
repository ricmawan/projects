<?php
	header('Content-Type: application/json');
	if(isset($_POST['itemCode'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$ItemCode = mysqli_real_escape_string($dbh, $_POST['itemCode']);
		$ItemID = 0;
		$BuyPrice = 0;
		$RetailPrice = 0;
		$Price1 = 0;
		$Price2 = 0;
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "CALL spSelItemDetails(".$ItemCode.", '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/Purchase/CheckItem.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		
		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$FailedFlag = 0;
			$ItemID = $row['ItemID'];
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
		
		echo returnstate($ItemID, $BuyPrice, $RetailPrice, $Price1, $Price2, $FailedFlag);
	}
	
	function returnstate($ItemID, $BuyPrice, $RetailPrice, $Price1, $Price2, $FailedFlag) {
		$data = array(
			"ItemID" => $ItemID, 
			"BuyPrice" => $BuyPrice,
			"RetailPrice" => $RetailPrice,
			"Price1" => $Price1,
			"Price2" => $Price2,
			"FailedFlag" => $FailedFlag
		);
		return json_encode($data);
	}
?>
