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
		$ItemName = "";
		$Quantity = 0;
		$FailedFlag = 0;
		$ErrorMessage = "";
		$State = 1;
		
		$sql = "CALL spSelItemQtyDetails('".$ItemCode."', ".$BranchID.", '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			$FailedFlag = 1;
			$ErrorMessage = "Terjadi kesalahan sistem.";
			logEvent(mysqli_error($dbh), '/Transaction/StockMutation/CheckItem.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($ItemID, $ItemName, $Quantity, $FailedFlag, $ErrorMessage);
			return 0;
		}
		
		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$ItemID = $row['ItemID'];
			$ItemName = $row['ItemName'];
			$Quantity = $row['Quantity'];
		}
		else {
			$FailedFlag = 1;
		}
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		
		echo returnstate($ItemID, $ItemName, $Quantity, $FailedFlag, $ErrorMessage);
	}
	
	function returnstate($ItemID, $ItemName, $Quantity, $FailedFlag, $ErrorMessage) {
		$data = array(
			"ItemID" => $ItemID, 
			"ItemName" => $ItemName,
			"Quantity" => $Quantity,
			"FailedFlag" => $FailedFlag,
			"ErrorMessage" => $ErrorMessage
		);
		return json_encode($data);
	}
?>