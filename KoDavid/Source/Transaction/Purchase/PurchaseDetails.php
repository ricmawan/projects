<?php
	if(ISSET($_POST['PurchaseID'])) {
		header('Content-Type: application/json');
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$PurchaseID = mysqli_real_escape_string($dbh, $_POST['PurchaseID']);
		$sql = "CALL spSelPurchaseDetails(".$PurchaseID.", '".$_SESSION['UserLogin']."')";
		$FailedFlag = 0;

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/Purchase/PurchaseDetails.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			$FailedFlag = 1;
			$json_data = array(
							"FailedFlag" => $FailedFlag
						);
		
			echo json_encode($json_data);
			return 0;
		}
		
		$return_arr = array();
		while ($row = mysqli_fetch_array($result)) {
			$row_array = array();
			//data yang dikirim ke table
			$row_array[] = $row['PurchaseDetailsID'];
			$row_array[] = $row['ItemID'];
			$row_array[] = $row['BranchID'];
			$row_array[] = $row['BranchName'];
			$row_array[] = $row['ItemCode'];
			$row_array[] = $row['ItemName'];
			$row_array[] = $row['Quantity'];
			$row_array[] = $row['UnitName'];
			$row_array[] = number_format($row['BuyPrice'],0,".",",");
			$row_array[] = number_format($row['RetailPrice'],0,".",",");
			$row_array[] = number_format($row['Price1'],0,".",",");
			$row_array[] = number_format($row['Price2'],0,".",",");
			$row_array[] = number_format($row['BuyPrice'] * $row['Quantity'],0,".",",");
			$row_array[] = $row['AvailableUnit'];
			$row_array[] = $row['UnitID'];
			$row_array[] = $row['ItemDetailsID'];
			$row_array[] = $row['ConversionQuantity'];
			array_push($return_arr, $row_array);
		}
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);

		$json_data = array(
						"FailedFlag" => $FailedFlag,
						"data"				=> $return_arr
					);
		
		echo json_encode($json_data);
	}
?>
