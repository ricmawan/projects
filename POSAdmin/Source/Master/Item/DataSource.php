<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";

	$requestData= $_REQUEST;
	//kolom di table
	$columns = array(
					0 => "CategoryID", //unorderable
					1 => "RowNumber", //unorderable
					2 => "MI.ItemCode",
					3 => "MI.ItemName",
					4 => "MC.CategoryName",
					5 => "MI.BuyPrice",
					6 => "MI.RetailPrice",
					7 => "MI.Price1",
					8 => "MI.Qty1",
					9 => "MI.Price2",
					10 => "MI.Qty2",
					11 => "MI.Weight",
					12 => "MI.MinimumStock"
				);

	$where = " 1=1 ";
	$order_by = "MI.ItemID";
	$limit_s = $requestData['start'];
	$limit_l = $requestData['length'];
	
	//Handles Sort querystring sent from Bootgrid
	$order_by = $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
	$order_by .= ", MI.ItemID ASC";
	//Handles search querystring sent from Bootgrid
	if (!empty($requestData['search']['value']))
	{
		$search = mysqli_escape_string($dbh, trim($requestData['search']['value']));
		$where .= " AND ( MI.ItemCode LIKE '%".$search."%'";
		$where .= " OR MI.ItemName LIKE '%".$search."%'";
		$where .= " OR MC.CategoryName LIKE '%".$search."%'";
		$where .= " OR MI.BuyPrice LIKE '%".$search."%'";
		$where .= " OR MI.RetailPrice LIKE '%".$search."%'";
		$where .= " OR MI.Price1 LIKE '%".$search."%'";
		$where .= " OR MI.Qty1 LIKE '%".$search."%'";
		$where .= " OR MI.Price2 LIKE '%".$search."%'";
		$where .= " OR MI.Qty2 LIKE '%".$search."%'";
		$where .= " OR MI.Weight LIKE '%".$search."%'";
		$where .= " OR MI.MinimumStock LIKE '%".$search."%' )";
	}
	$sql = "CALL spSelItem(\"$where\", '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLogin']."')";
	if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Master/Item/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		echo "<script>$('#loading').hide();</script>";
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
		$row_array[] = "<input type='checkbox' name='select' value='".$row['ItemID']."^".$row['ItemName']."' />";
		$row_array[] = $RowNumber;
		$row_array[] = $row['ItemCode'];
		$row_array[] = $row['ItemName'];
		$row_array[] = $row['CategoryName'];
		$row_array[] = number_format($row['BuyPrice'],0,".",",");
		$row_array[] = number_format($row['RetailPrice'],0,".",",");
		$row_array[] = number_format($row['Price1'],0,".",",");
		$row_array[] = $row['Qty1'];
		$row_array[] = number_format($row['Price2'],0,".",",");
		$row_array[] = $row['Qty2'];
		$row_array[] = number_format($row['Weight'],0,".",",");
		$row_array[] = $row['MinimumStock'];
		$row_array[] = $row['ItemID'];
		$row_array[] = $row['CategoryID'];
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
