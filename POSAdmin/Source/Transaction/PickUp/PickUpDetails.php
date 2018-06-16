<?php
	if(ISSET($_POST['PickUpID'])) {
		header('Content-Type: application/json');
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$PickUpID = mysqli_real_escape_string($dbh, $_POST['PickUpID']);
		$sql = "CALL spSelPickUpDetails(".$PickUpID.", '".$_SESSION['UserLogin']."')";
		$FailedFlag = 0;

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/PickUp/PickUpDetails.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
			$row_array[] = $row['PickDetailsID'];
			$row_array[] = $row['ItemID'];
			$row_array[] = $row['BranchID'];
			$row_array[] = "<input type='checkbox' name='chkBookingDetails[]' class='chkBookingDetails' value=" . $row['PickDetailsID'] . " onclick='Calculate();' />";
			$row_array[] = "<div id='toggle-branch-" . $row['PickDetailsID'] . "' class='div-center toggle-modern' onclick='updateBranch(this.id)' ></div>
				<input id='hdnBranchID". $row['PickDetailsID'] ."' name='hdnBranchID". $row['PickDetailsID'] ."' type='hidden' value=".$row['BranchID']." />
				<input id='hdnItemID". $row['PickDetailsID'] ."' name='hdnItemID". $row['PickDetailsID'] ."' type='hidden' value=".$row['ItemID']." />
				<input id='hdnItemDetailsID". $row['PickDetailsID'] ."' name='hdnItemDetailsID". $row['PickDetailsID'] ."' type='hidden' value='".$row['ItemDetailsID']."' />
				<input id='hdnBuyPrice". $row['PickDetailsID'] ."' name='hdnBuyPrice". $row['PickDetailsID'] ."' type='hidden' value=".$row['BuyPrice']." />
				<input id='hdnBookingPrice". $row['PickDetailsID'] ."' name='hdnBookingPrice". $row['PickDetailsID'] ."' type='hidden' value=".$row['SalePrice'] / $row['ConversionQuantity']." />
				<input id='hdnDiscount". $row['PickDetailsID'] ."' name='hdnDiscount". $row['PickDetailsID'] ."' type='hidden' value=".$row['Discount']." />
				<input id='hdnBookingDetailsID". $row['PickDetailsID'] ."' name='hdnBookingDetailsID". $row['PickDetailsID'] ."' type='hidden' value=".$row['BookingDetailsID']." />";
			$row_array[] = $row['ItemCode'];
			$row_array[] = $row['ItemName'];
			$row_array[] = '<input id="txtQty'. $row['PickDetailsID'] .'" name="txtQty'. $row['PickDetailsID'] .'" type="number" class="form-control-custom text-right" value='.$row['Quantity'].' max='.$row['Maksimum'].' min=1 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 1" onpaste="return false;" onchange="validateQTY2(this)" required />';
			$row_array[] = $row['UnitName'];
			$row_array[] = "<label style='font-weight:normal !important;'>".number_format($row['SalePrice'],0,".",",")."</label>";
			$row_array[] = 0;
			$row_array[] = $row['BuyPrice'];
			$row_array[] = $row['BookingID'];
			array_push($return_arr, $row_array);
		}

		mysqli_free_result($result);
		mysqli_next_result($dbh);

		$json_data = array(
						"FailedFlag" => $FailedFlag,
						"data"		=> $return_arr
					);
		
		echo json_encode($json_data);
	}
?>
