<?php
	header('Content-Type: application/json');
	if(isset($_POST['itemCode'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$ItemCode = mysqli_real_escape_string($dbh, $_POST['itemCode']);
		$BranchID = mysqli_real_escape_string($dbh, $_POST['branchID']);
		$ItemID = 0;
		$ItemDetailsID = 0;
		$ItemName = "";
		$BuyPrice = 0;
		$RetailPrice = 0;
		$Price1 = 0;
		$Qty1 = 0;
		$Price2 = 0;
		$Qty2 = 0;
		$Weight = 0;
		$Stock = 0;
		$UnitID = 0;
		$ConversionQty = 0;
		$StockNoConversion = 0;
		$AvailableUnit = "";
		$FailedFlag = 0;
		$ErrorMessage = "";
		$State = 1;
		
		$sql = "CALL spSelItemQtyDetails('".$ItemCode."', ".$BranchID.", '".$_SESSION['UserLoginMobile']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			$FailedFlag = 1;
			$ErrorMessage = "Terjadi kesalahan sistem.";
			logEvent(mysqli_error($dbh), '/Transaction/Booking/CheckItem.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
			echo returnstate($ItemID, $ItemDetailsID, $ItemName, $BuyPrice, $RetailPrice, $Price1, $Qty1, $Price2, $Qty2, $UnitID, $AvailableUnit, $Weight, $Stock, $StockNoConversion, $ConversionQty, $FailedFlag, $ErrorMessage);
			return 0;
		}
		
		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$ItemID = $row['ItemID'];
			$ItemDetailsID = $row['ItemDetailsID'];
			$ItemName = htmlspecialchars_decode($row['ItemName'], ENT_QUOTES);
			$BuyPrice = $row['BuyPrice'];
			$RetailPrice = $row['RetailPrice'];
			$Price1 = $row['Price1'];
			$Qty1 = $row['Qty1'];
			$Price2 = $row['Price2'];
			$Qty2 = $row['Qty2'];
			$Weight = $row['Weight'];
			$ConversionQty = $row['ConversionQty'];
			$Stock = $row['Stock'];
			$StockNoConversion = $row['StockNoConversion'];
			$UnitID = $row['UnitID'];
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
				$row_unit[] = $row['RetailPrice'];
				$row_unit[] = $row['Price1'];
				$row_unit[] = $row['Price2'];
				$row_unit[] = $row['Qty1'];
				$row_unit[] = $row['Qty2'];
				$row_unit[] = $row['ConversionQuantity'];
				array_push($AvailableUnit, $row_unit);
			}

			mysqli_free_result($result2);
			mysqli_next_result($dbh);
		}
		
		echo returnstate($ItemID, $ItemDetailsID, $ItemName, $BuyPrice, $RetailPrice, $Price1, $Qty1, $Price2, $Qty2, $UnitID, $AvailableUnit, $Weight, $Stock, $StockNoConversion, $ConversionQty, $FailedFlag, $ErrorMessage);
	}
	
	function returnstate($ItemID, $ItemDetailsID, $ItemName, $BuyPrice, $RetailPrice, $Price1, $Qty1, $Price2, $Qty2, $UnitID, $AvailableUnit, $Weight, $Stock, $StockNoConversion, $ConversionQty, $FailedFlag, $ErrorMessage) {
		$data = array(
			"ItemID" => $ItemID, 
			"ItemName" => $ItemName,
			"ItemDetailsID" => $ItemDetailsID,
			"BuyPrice" => $BuyPrice,
			"RetailPrice" => $RetailPrice,
			"Price1" => $Price1,
			"Qty1" => $Qty1,
			"Price2" => $Price2,
			"Qty2" => $Qty2,
			"Weight" => $Weight,
			"Stock" => $Stock,
			"AvailableUnit" => $AvailableUnit,
			"UnitID" => $UnitID,
			"ConversionQty" => $ConversionQty,
			"StockNoConversion" => $StockNoConversion,
			"FailedFlag" => $FailedFlag,
			"ErrorMessage" => $ErrorMessage
		);
		return json_encode($data);
	}
?>
