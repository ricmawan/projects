<?php
	if(ISSET($_POST['ItemID'])) {
		header('Content-Type: application/json');
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$ItemID = mysqli_real_escape_string($dbh, $_POST['ItemID']);
		$sql = "CALL spSelItemUnitDetails(".$ItemID.", '".$_SESSION['UserLogin']."')";
		$FailedFlag = 0;

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Master/Item/ItemDetails.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
			$row_array[] = $row['ItemDetailsID'];
			$row_array[] = $row['ItemDetailsCode'];
			$row_array[] = $row['UnitID'];
			$row_array[] = $row['ConversionQuantity'];
			$row_array[] = number_format($row['BuyPrice'],0,".",",");
			$row_array[] = number_format($row['RetailPrice'],0,".",",");
			$row_array[] = number_format($row['Price1'],0,".",",");
			$row_array[] = $row['Qty1'];
			$row_array[] = number_format($row['Price2'],0,".",",");
			$row_array[] = $row['Qty2'];
			$row_array[] = $row['Weight'];
			$row_array[] = $row['MinimumStock'];
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
