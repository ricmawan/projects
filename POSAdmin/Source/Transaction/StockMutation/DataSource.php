<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";

	$requestData= $_REQUEST;
	//kolom di table
	$columns = array(
					0 => "SM.StockMutationID", //unorderable
					1 => "RowNumber", //unorderable
					2 => "SM.TransactionDate",
					3 => "SB.BranchName",
					4 => "DB.BranchName",
					5 => "MI.ItemCode",
					6 => "MI.ItemName",
					7 => "SMD.Quantity",
					8 => "MU.Unitname"
				);

	$where = " 1=1 ";
	$order_by = "SM.StockMutationID DESC";
	$limit_s = $requestData['start'];
	$limit_l = $requestData['length'];
	
	//Handles Sort querystring sent from Bootgrid
	if(ISSET($requestData['order'])) {
		$order_by = $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
		$order_by .= ", SM.StockMutationID ASC";
	}
	//Handles search querystring sent from Bootgrid
	if (!empty($requestData['search']['value']))
	{
		$search = mysqli_escape_string($dbh, trim($requestData['search']['value']));
		$where .= " AND ( SB.BranchName LIKE '%".$search."%'";
		$where .= " OR DB.BranchName LIKE '%".$search."%'";
		$where .= " OR MI.ItemName LIKE '%".$search."%'";
		$where .= " OR MI.ItemCode LIKE '%".$search."%'";
		$where .= " OR MID.ItemDetailsCode LIKE '%".$search."%'";
		$where .= " OR MU.UnitName LIKE '%".$search."%'";
		$where .= " OR DATE_FORMAT(SM.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%'";
		$where .= " OR SMD.Quantity LIKE '%".$search."%' )";
	}
	$sql = "CALL spSelStockMutation(\"$where\", '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLogin']."')";

	if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Transaction/StockMutation/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
		$row_array[] = "<input name='select' type='checkbox' value='".$row['StockMutationDetailsID']."' />";
		$row_array[] = $RowNumber;
		$row_array[] = $row['TransactionDate'];
		$row_array[] = $row['SourceBranchName'];
		$row_array[] = $row['DestinationBranchName'];
		$row_array[] = $row['ItemCode'];
		$row_array[] = $row['ItemName'];
		$row_array[] = $row['Quantity'];
		$row_array[] = $row['UnitName'];
		$row_array[] = $row['StockMutationID'];
		$row_array[] = $row['StockMutationDetailsID'];
		$row_array[] = $row['PlainTransactionDate'];
		$row_array[] = $row['ItemID'];
		$row_array[] = $row['SourceID'];
		$row_array[] = $row['DestinationID'];
		$row_array[] = $row['UnitID'];
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
