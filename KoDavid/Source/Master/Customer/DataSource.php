<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";

	$requestData= $_REQUEST;
	//kolom di table
	$columns = array(
					0 => "CustomerID", //unorderable
					1 => "RowNumber", //unorderable
					2 => "MC.CustomerCode",
					3 => "MC.CustomerName",
					4 => "MC.Telephone",
					5 => "MC.Address",
					6 => "MC.City",
					7 => "MC.Remarks",
					8 => "CP.CustomerPriceName"
				);

	$where = " 1=1 ";
	$order_by = "MC.CustomerID DESC";
	$limit_s = $requestData['start'];
	$limit_l = $requestData['length'];
	
	//Handles Sort querystring sent from Bootgrid
	if(ISSET($requestData['order'])) {
		$order_by = $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
		$order_by .= ", MC.CustomerID ASC";
	}
	//Handles search querystring sent from Bootgrid
	if (!empty($requestData['search']['value']))
	{
		$search = mysqli_escape_string($dbh, trim($requestData['search']['value']));
		$where .= " AND ( MC.CustomerCode LIKE '%".$search."%'";
		$where .= " OR MC.CustomerName LIKE '%".$search."%'";
		$where .= " OR MC.Telephone LIKE '%".$search."%'";
		$where .= " OR MC.Address LIKE '%".$search."%'";
		$where .= " OR MC.City LIKE '%".$search."%'";
		$where .= " OR CP.CustomerPriceName LIKE '%".$search."%'";
		$where .= " OR MC.Remarks LIKE '%".$search."%' )";
	}
	$sql = "CALL spSelCustomer(\"$where\", '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLogin']."')";

	if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Master/Customer/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
		$row_array[] = "<input type='checkbox' name='select' value='".$row['CustomerID']."^".$row['CustomerName']."' />";
		$row_array[] = $RowNumber;
		$row_array[] = $row['CustomerCode'];
		$row_array[] = $row['CustomerName'];
		$row_array[] = $row['Telephone'];
		$row_array[] = $row['Address'];
		$row_array[] = $row['City'];
		$row_array[] = $row['Remarks'];
		$row_array[] = $row['CustomerPriceName'];
		$row_array[] = $row['CustomerID'];
		$row_array[] = $row['CustomerPriceID'];
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
