<?php
	if(ISSET($_POST['BookingNumber'])) {
		header('Content-Type: application/json');
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$BookingNumber = mysqli_real_escape_string($dbh, $_POST['BookingNumber']);
		$sql = "CALL spSelBookingDetailsByNumber('".$BookingNumber."', '".$_SESSION['UserLogin']."')";
		$FailedFlag = 0;

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/PickUp/BookingDetails.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
			if($row['Quantity'] == 0) $disabled = "disabled";
			else $disabled = "";
			$row_array[] = $row['BookingDetailsID'];
			$row_array[] = $row['ItemID'];
			$row_array[] = $row['BranchID'];
			$row_array[] = "<input type='checkbox' name='chkBookingDetails[]' class='chkBookingDetails' value=" . $row['BookingDetailsID'] . " onclick='Calculate();'".$disabled." />";
			$row_array[] = "<div id='toggle-branch-" . $row['BookingDetailsID'] . "' class='div-center toggle-modern' onclick='updateBranch(this.id)' ></div>
				<input id='hdnBranchID". $row['BookingDetailsID'] ."' name='hdnBranchID". $row['BookingDetailsID'] ."' type='hidden' value=".$row['BranchID']." />
				<input id='hdnItemID". $row['BookingDetailsID'] ."' name='hdnItemID". $row['BookingDetailsID'] ."' type='hidden' value=".$row['ItemID']." />
				<input id='hdnItemDetailsID". $row['BookingDetailsID'] ."' name='hdnItemDetailsID". $row['BookingDetailsID'] ."' type='hidden' value=".$row['ItemDetailsID']." />
				<input id='hdnBuyPrice". $row['BookingDetailsID'] ."' name='hdnBuyPrice". $row['BookingDetailsID'] ."' type='hidden' value=".$row['BuyPrice']." />
				<input id='hdnBookingPrice". $row['BookingDetailsID'] ."' name='hdnBookingPrice". $row['BookingDetailsID'] ."' type='hidden' value=".$row['BookingPrice']." />
				<input id='hdnBookingDetailsID". $row['BookingDetailsID'] ."' name='hdnBookingDetailsID". $row['BookingDetailsID'] ."' type='hidden' value=".$row['BookingDetailsID']." />";
			$row_array[] = $row['ItemCode'];
			$row_array[] = $row['ItemName'];
			$row_array[] = '<input id="txtQty'. $row['BookingDetailsID'] .'" name="txtQty'. $row['BookingDetailsID'] .'" type="number" class="form-control-custom text-right" value='.$row['Quantity'].' max='.$row['Quantity'].' min=1 onfocus="this.select();" style="height:20px !important;" autocomplete=off placeholder="Qty Grosir 1" onpaste="return false;" onchange="validateQTY2(this)" '.$disabled.' />';
			$row_array[] = $row['UnitName'];
			$row_array[] = "<label style='font-weight:normal !important;'>".number_format($row['BookingPrice'],0,".",",")."</label>";
			$row_array[] = 0;
			$row_array[] = $row['BuyPrice'];
			$row_array[] = $row['CustomerName'];
			$row_array[] = $row['BookingID'];
			$row_array[] = $row['Quantity'];
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
