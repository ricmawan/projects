<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";

	$requestData= $_REQUEST;
	//kolom di table
	$columns = array(
					0 => "SA.StockAdjustID", //unorderable
					1 => "RowNumber", //unorderable
					2 => "SA.TransactionDate",
					3 => "MB.BranchName",
					4 => "MI.ItemCode",
					5 => "MI.ItemName",
					6 => "SAD.Quantity",
					6 => "SAD.AdjustedQuantity"
				);

	$where = " 1=1 ";
	$order_by = "SA.StockAdjustID";
	$limit_s = $requestData['start'];
	$limit_l = $requestData['length'];
	
	//Handles Sort querystring sent from Bootgrid
	$order_by = $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
	$order_by .= ", SA.StockAdjustID ASC";
	//Handles search querystring sent from Bootgrid
	if (!empty($requestData['search']['value']))
	{
		$search = mysqli_escape_string($dbh, trim($requestData['search']['value']));
		$where .= " AND ( MB.BranchName LIKE '%".$search."%'";
		$where .= " OR MI.ItemCode LIKE '%".$search."%'";
		$where .= " OR MI.ItemName LIKE '%".$search."%'";
		$where .= " OR DATE_FORMAT(SA.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%'";
		$where .= " OR SAD.AdjustedQuantity LIKE '%".$search."%'";
		$where .= " OR SAD.Quantity LIKE '%".$search."%' )";
	}
	$sql = "CALL spSelStockAdjust(\"$where\", '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLogin']."')";

	if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Transaction/StockAdjust/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		return 0;
	}
	$row = mysqli_fetch_array($result);
	$totalData = $row['nRows'];
	$totalFiltered = $totalData;
	mysqli_free_result($result);
	mysqli_next_result($dbh);
	
	$result2 = mysqli_use_result($dbh);
	$return_arr = array();
	$RowNumber = $requestData['start'];
	while ($row = mysqli_fetch_array($result2)) {
		$row_array = array();
		$RowNumber++;
		//data yang dikirim ke table
		$row_array[] = "<input name='select' type='checkbox' value='".$row['StockAdjustDetailsID']."' />";
		$row_array[] = $RowNumber;
		$row_array[] = $row['TransactionDate'];
		$row_array[] = $row['BranchName'];
		$row_array[] = $row['ItemCode'];
		$row_array[] = $row['ItemName'];
		$row_array[] = $row['Quantity'];
		$row_array[] = $row['AdjustedQuantity'];
		$row_array[] = $row['StockAdjustID'];
		$row_array[] = $row['StockAdjustDetailsID'];
		$row_array[] = $row['PlainTransactionDate'];
		$row_array[] = $row['ItemID'];
		$row_array[] = $row['BranchID'];
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
