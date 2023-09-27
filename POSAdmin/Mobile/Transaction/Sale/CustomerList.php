<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";

	$requestData= $_REQUEST;

	//$BranchID = mysqli_real_escape_string($dbh, $requestData['BranchID']);
	//kolom di table

	$where = " 1=1 ";
	$order_by = "CustomerID";
	$limit_s = $requestData['start'];
	$limit_l = $requestData['length'];
	
	//Handles search querystring sent from Bootgrid
	if (!empty($requestData['search']['value']))
	{
		$search = mysqli_real_escape_string($dbh, trim($requestData['search']['value']));
		$where .= " AND ( MC.CustomerName LIKE '%".$search."%'";
		$where .= " OR MC.CustomerCode LIKE '%".$search."%'";
		$where .= " OR MC.Telephone LIKE '%".$search."%'";
		$where .= " OR MC.Address LIKE '%".$search."%'";
		$where .= " OR MC.City LIKE '%".$search."%' )";
	}
	$sql = "CALL spSelCustomer(\"$where\", '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLoginMobile']."')";

	if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Transaction/Sale/CustomerList.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
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
		$row_array[] = $row['CustomerCode'];
		$row_array[] = $row['CustomerName'];
		$row_array[] = $row['Telephone'];
		$row_array[] = $row['Address'];
		$row_array[] = $row['City'];
		$row_array[] = $row['Remarks'];
		$row_array[] = $row['CustomerID'];
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
