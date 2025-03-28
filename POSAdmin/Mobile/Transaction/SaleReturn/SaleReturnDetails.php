<?php
	if(ISSET($_POST['SaleReturnID'])) {
		header('Content-Type: application/json');
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$SaleReturnID = mysqli_real_escape_string($dbh, $_POST['SaleReturnID']);
		$sql = "CALL spSelSaleReturnDetails(".$SaleReturnID.", '".$_SESSION['UserLoginMobile']."')";
		$FailedFlag = 0;

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/SaleReturn/SaleReturnDetails.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
			$FailedFlag = 1;
			$json_data = array(
							"FailedFlag" => $FailedFlag
						);
		
			echo json_encode($json_data);
			return 0;
		}
		
		$return_arr = array();
		$tabindex = 8;
		while ($row = mysqli_fetch_array($result)) {
			$row_array = array();
			//data yang dikirim ke table
			$row_array[] = $row['SaleReturnDetailsID'];
			$row_array[] = $row['ItemID'];
			$row_array[] = $row['BranchID'];
			$row_array[] = "<input type='checkbox' name='chkSaleDetails[]' class='chkSaleDetails' value=" . $row['SaleReturnDetailsID'] . " onclick='Calculate();' tabindex=".$tabindex." />";
			$row_array[] = "<div id='toggle-branch-" . $row['SaleReturnDetailsID'] . "' class='div-center toggle-modern' onclick='updateBranch(this.id)' ></div>
				<input id='hdnBranchID". $row['SaleReturnDetailsID'] ."' name='hdnBranchID". $row['SaleReturnDetailsID'] ."' type='hidden' value=".$row['BranchID']." />
				<input id='hdnItemID". $row['SaleReturnDetailsID'] ."' name='hdnItemID". $row['SaleReturnDetailsID'] ."' type='hidden' value=".$row['ItemID']." />
				<input id='hdnBuyPrice". $row['SaleReturnDetailsID'] ."' name='hdnBuyPrice". $row['SaleReturnDetailsID'] ."' type='hidden' value=".$row['BuyPrice']." />
				<input id='hdnSalePrice". $row['SaleReturnDetailsID'] ."' name='hdnSalePrice". $row['SaleReturnDetailsID'] ."' type='hidden' value=".$row['SalePrice']." />
				<input id='hdnSaleDetailsID". $row['SaleReturnDetailsID'] ."' name='hdnSaleDetailsID". $row['SaleReturnDetailsID'] ."' type='hidden' value=".$row['SaleDetailsID']." />";
			$row_array[] = $row['ItemCode'];
			$row_array[] = $row['ItemName'];
			$row_array[] = '<input id="txtQty'. $row['SaleReturnDetailsID'] .'" name="txtQty'. $row['SaleReturnDetailsID'] .'" type="number" class="form-control-custom text-right txtQTY" style="width: 100%;margin: 0;border: 0;padding-right: 22px;" value='.$row['Quantity'].' max='.$row['Maksimum'].' min=1 onfocus="this.select();" onchange="validateQTY2(this)" required tabindex='.($tabindex+1).' />';
			$row_array[] = $row['UnitName'];
			$row_array[] = "<label style='font-weight:normal !important;'>".number_format($row['SalePrice'],0,".",",")."</label>";
			$row_array[] = 0;
			$row_array[] = $row['BuyPrice'];
			$row_array[] = $row['SaleID'];
			$tabindex = $tabindex + 2;
			array_push($return_arr, $row_array);
		}

		mysqli_free_result($result);
		mysqli_next_result($dbh);

		$json_data = array(
						"FailedFlag" => $FailedFlag,
						"data"		=> $return_arr,
						"tabindex" => $tabindex
					);
		
		echo json_encode($json_data);
	}
?>
