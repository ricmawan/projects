<?php
	if(ISSET($_POST['BookingID'])) {
		header('Content-Type: application/json');
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$BookingID = mysqli_real_escape_string($dbh, $_POST['BookingID']);
		$sql = "CALL spSelBookingDetails(".$BookingID.", '".$_SESSION['UserLoginKasir']."')";
		$FailedFlag = 0;

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/Booking/BookingDetails.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
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
			$row_array[] = "<input type='checkbox' class='chkBookingDetails' style='margin:0;' name='select' value='".$row['BookingDetailsID']."' />";
			$row_array[] = $row['BookingDetailsID'];
			$row_array[] = $row['ItemID'];
			$row_array[] = $row['BranchID'];
			$row_array[] = "<div id='toggle-branch-" . $row['BookingDetailsID'] . "' onclick=\"updateBranch(this.id, " . $row['Quantity'] . ", '". $row['ItemCode'] ."')\" class='div-center toggle-modern' ></div>";
			$row_array[] = $row['ItemCode'];
			$row_array[] = $row['ItemName'];
			$row_array[] = $row['Quantity'];
			$row_array[] = $row['UnitName'];
			$row_array[] = number_format($row['BookingPrice'],0,".",",");
			$row_array[] = number_format($row['Discount'],0,".",",");
			$row_array[] = number_format(($row['BookingPrice'] - $row['Discount']) * $row['Quantity'],0,".",",");
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
			$row_array[] = $row['ConversionQty'];
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
