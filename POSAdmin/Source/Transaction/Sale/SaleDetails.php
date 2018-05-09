<?php
	if(ISSET($_POST['SaleID'])) {
		header('Content-Type: application/json');
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$SaleID = mysqli_real_escape_string($dbh, $_POST['SaleID']);
		$sql = "CALL spSelSaleDetails(".$SaleID.", '".$_SESSION['UserLogin']."')";
		$FailedFlag = 0;

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/Sale/SaleDetails.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
			$row_array[] = "<input type='checkbox' style='margin:0;' name='select' value='".$row['SaleDetailsID']."' />";
			$row_array[] = $row['SaleDetailsID'];
			$row_array[] = $row['ItemID'];
			$row_array[] = $row['BranchID'];
			$row_array[] = "<div id='toggle-branch-" . $row['SaleDetailsID'] . "' onclick='updateBranch(this.id)' class='div-center toggle-modern' ></div>";
			$row_array[] = $row['ItemCode'];
			$row_array[] = $row['ItemName'];
			$row_array[] = $row['Quantity'];
			$row_array[] = $row['UnitName'];
			$row_array[] = number_format($row['SalePrice'],0,".",",");
			$row_array[] = number_format($row['Discount'],0,".",",");
			$row_array[] = number_format($row['SalePrice'] * $row['Quantity'],0,".",",");
			$row_array[] = $row['BuyPrice'];
			$row_array[] = $row['Price1'];
			$row_array[] = $row['Qty1'];
			$row_array[] = $row['Price2'];
			$row_array[] = $row['Qty2'];
			$row_array[] = $row['Weight'];
			$row_array[] = $row['RetailPrice'];
			$row_array[] = $row['AvailableUnit'];
			$row_array[] = $row['UnitID'];
			$row_array[] = $row['ItemDetailsID'];
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
