<?php
	header('Content-Type: application/json');
	if(isset($_POST['TokenCode'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$TokenCode = mysqli_real_escape_string($dbh, $_POST['TokenCode']);
		$FailedFlag = 0;
		$ErrorMessage = "";
		$State = 1;
		
		$sql = "CALL spSelTokenCode('".$TokenCode."', '".$_SESSION['UserLoginMobile']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			$FailedFlag = 1;
			$ErrorMessage = "Terjadi kesalahan sistem.";
			logEvent(mysqli_error($dbh), '/Transaction/Sale/CheckToken.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
			echo returnstate($ItemID, $ItemDetailsID, $ItemName, $BuyPrice, $RetailPrice, $Price1, $Qty1, $Price2, $Qty2, $UnitID, $AvailableUnit, $Weight, $Stock, $StockNoConversion, $ConversionQty, $FailedFlag, $ErrorMessage);
			return 0;
		}
		
		if(mysqli_num_rows($result) > 0) {
			//do nothing ?
		}
		else {
			$FailedFlag = 1;
		}
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		
		echo returnstate($FailedFlag, $ErrorMessage);
	}
	
	function returnstate($FailedFlag, $ErrorMessage) {
		$data = array(
			"FailedFlag" => $FailedFlag,
			"ErrorMessage" => $ErrorMessage
		);
		return json_encode($data);
	}
?>
