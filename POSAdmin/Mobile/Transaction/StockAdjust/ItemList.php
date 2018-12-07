<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";

	$requestData= $_REQUEST;

	$BranchID = mysqli_real_escape_string($dbh, $requestData['BranchID']);
	$CategoryID = mysqli_real_escape_string($dbh, $requestData['CategoryID']);
	if($CategoryID == "") $CategoryID = 0;
	$limit_s = $requestData['start'];
	$limit_l = 100;
	//kolom di table

	$sql = "CALL spSelItemListStockAdjust(".$BranchID.", ".$CategoryID.", ".$limit_s.", ".$limit_l.", '".$_SESSION['UserLogin']."')";

	if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Transaction/StockAdjust/ItemList.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		return 0;
	}

	$row = mysqli_fetch_array($result);
	$totalData = $row['nRows'];
	$totalFiltered = $totalData;
	mysqli_free_result($result);
	mysqli_next_result($dbh);
	
	$result2 = mysqli_use_result($dbh);
	$return_arr = array();
	$RowNumber = 0;
	while ($row = mysqli_fetch_array($result2)) {
		$row_array = array();
		$RowNumber++;
		//data yang dikirim ke table
		$row_array[] = $row['ItemID'];
		$row_array[] = $row['ItemCode'];
		$row_array[] = $row['ItemName'];
		$row_array[] = number_format($row['Stock'],2,".",",");
		$row_array[] = number_format($row['PhysicalStock'],2,".",",");
		$row_array[] = "<input type='tel' id='txtAdjustedQty". $row['ItemID']."' class='form-control-custom' style='text-align:right;' value='". number_format($row['PhysicalStock'],2,".",",") ."' onkeypress='return isNumberKey(event, this.id, this.value)' onfocus='clearFormat(this.id, this.value);this.select();' onblur='convertWeight(this.id, this.value);' onchange='addData(".$row['ItemID'].", ". $row['PhysicalStock'] .", this.value, " . $row['BuyPrice'] . ", " . $row['RetailPrice'] . " )' onpaste='return false;'  />";
		array_push($return_arr, $row_array);
	}
	
	mysqli_free_result($result2);
	mysqli_next_result($dbh);

	$json_data = array(
					"draw"				=> intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"		=> intval( $totalData ),  // total number of records
					"recordsFiltered"	=> intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"				=> $return_arr
				);
	
	echo json_encode($json_data);
?>
