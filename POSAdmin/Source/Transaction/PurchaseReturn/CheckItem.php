<?php
	header('Content-Type: application/json');
	if(isset($_POST['itemCode'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$ItemCode = mysqli_real_escape_string($dbh, $_POST['itemCode']);
		$ItemID = 0;
		$ItemDetailsID = 0;
		$ItemName = "";
		$BuyPrice = 0;
		$RetailPrice = 0;
		$Price1 = 0;
		$Price2 = 0;
		$UnitID = 0;
		$AvailableUnit = "";
		$FailedFlag = 0;
		$ErrorMessage = "";
		$ConversionQuantity = 1;
		$State = 1;
		
		$sql = "CALL spSelItemDetails('".$ItemCode."', '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			$FailedFlag = 1;
			$ErrorMessage = "Terjadi kesalahan sistem.";
			logEvent(mysqli_error($dbh), '/Transaction/Purchase/CheckItem.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($ItemID, $ItemDetailsID, $ItemName, $BuyPrice, $UnitID, $AvailableUnit, $ConversionQuantity, $FailedFlag, $ErrorMessage);
			return 0;
		}
		
		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$ItemID = $row['ItemID'];
			$ItemDetailsID = $row['ItemDetailsID'];
			$ItemName = $row['ItemName'];
			$BuyPrice = $row['BuyPrice'];
			$UnitID = $row['UnitID'];
			$ConversionQuantity = $row['ConversionQuantity'];
		}
		else {
			$FailedFlag = 1;
		}
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);

		if($FailedFlag == 0) {
			$result2 = mysqli_use_result($dbh);
			$AvailableUnit = array();
			while ($row = mysqli_fetch_array($result2)) {
				$row_unit = array();
				$row_unit[] = $row['UnitID'];
				$row_unit[] = $row['UnitName'];
				$row_unit[] = $row['ItemDetailsID'];
				$row_unit[] = $row['ItemCode'];
				$row_unit[] = $row['BuyPrice'];
				$row_unit[] = $row['ConversionQuantity'];
				array_push($AvailableUnit, $row_unit);
			}

			mysqli_free_result($result2);
			mysqli_next_result($dbh);
		}
		
		echo returnstate($ItemID, $ItemDetailsID, $ItemName, $BuyPrice, $UnitID, $AvailableUnit, $ConversionQuantity, $FailedFlag, $ErrorMessage);
	}
	
	function returnstate($ItemID, $ItemDetailsID, $ItemName, $BuyPrice, $UnitID, $AvailableUnit, $ConversionQuantity, $FailedFlag, $ErrorMessage) {
		$data = array(
			"ItemID" => $ItemID, 
			"ItemName" => $ItemName, 
			"ItemDetailsID" => $ItemDetailsID,
			"BuyPrice" => $BuyPrice,
			"AvailableUnit" => $AvailableUnit,
			"UnitID" => $UnitID,
			"ConversionQuantity" => $ConversionQuantity,
			"FailedFlag" => $FailedFlag,
			"ErrorMessage" => $ErrorMessage
		);
		return json_encode($data);
	}
?>
