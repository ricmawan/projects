<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";
	$requestData= $_REQUEST;
	if(ISSET($requestData['BranchID']) && ISSET($requestData['ItemID']) && ISSET($requestData['FirstPass']) && $requestData['FirstPass'] == "0")
	{
		$ItemID = $requestData['ItemID'];
		$BranchID = $requestData['BranchID'];
		$ConversionQuantity = $requestData['conversionQuantity'];
		if($requestData['FromDate'] == "") {
			$txtFromDate = "2000-01-01";
		}
		else {
			$txtFromDate = explode('-', mysql_real_escape_string($requestData['FromDate']));
			$requestData['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $requestData['FromDate'];
		}
		if($requestData['ToDate'] == "") {
			$txtToDate = date("Y-m-d");
		}
		else {
			$txtToDate = explode('-', mysql_real_escape_string($requestData['ToDate']));
			$requestData['ToDate'] = "$txtToDate[2]-$txtToDate[1]-$txtToDate[0]"; 
			$txtToDate = $requestData['ToDate'];
		}
		//kolom di table
		
		$sql = "CALL spSelStockDetailsReport(".$ItemID.", ".$BranchID.", '".$txtFromDate."', '".$txtToDate."', ".$ConversionQuantity.", '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/StockDetails/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		
		$Stock = 0;
		$return_arr = array();
		while ($row = mysqli_fetch_array($result)) {
			$row_array = array();
			$Stock += $row['Quantity'];
			//data yang dikirim ke table
			$row_array[] = $row['TransactionType'];
			$row_array[] = $row['TransactionDate'];
			$row_array[] = $row['CustomerName'];
			$row_array[] = $row['Quantity'];
			$row_array[] = $Stock;
			array_push($return_arr, $row_array);
		}
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);

		$json_data = array(
						"draw"				=> intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
						"recordsTotal"		=> 0,  // total number of records
						"recordsFiltered"	=> 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
						"data"				=> $return_arr
					);
	}
	
	else {
		$json_data = array(
						"draw"				=> intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
						"recordsTotal"		=> 0,  // total number of records
						"recordsFiltered"	=> 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
						"data"				=> ""
					);
	}
	
	echo json_encode($json_data);
?>
