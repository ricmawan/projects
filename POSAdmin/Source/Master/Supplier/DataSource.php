<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";

	$requestData= $_REQUEST;
	//kolom di table
	$columns = array(
					0 => "SupplierID", //unorderable
					1 => "RowNumber", //unorderable
					2 => "MS.SupplierCode",
					3 => "MS.SupplierName",
					4 => "MS.Telephone",
					5 => "MS.Address",
					6 => "MS.City",
					7 => "MS.Remarks"
				);

	$where = " 1=1 ";
	$order_by = "MS.SupplierID";
	$limit_s = $requestData['start'];
	$limit_l = $requestData['length'];
	
	//Handles Sort querystring sent from Bootgrid
	$order_by = $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
	$order_by .= ", MS.SupplierID ASC";
	//Handles search querystring sent from Bootgrid
	if (!empty($requestData['search']['value']))
	{
		$search = mysqli_escape_string($dbh, trim($requestData['search']['value']));
		$where .= " AND ( MS.SupplierCode LIKE '%".$search."%'";
		$where .= " OR MS.SupplierName LIKE '%".$search."%'";
		$where .= " OR MS.Telephone LIKE '%".$search."%'";
		$where .= " OR MS.Address LIKE '%".$search."%'";
		$where .= " OR MS.City LIKE '%".$search."%'";
		$where .= " OR MS.Remarks LIKE '%".$search."%' )";
	}
	$sql = "CALL spSelSupplier(\"$where\", '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLogin']."')";

	if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Master/Supplier/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
		$row_array[] = "<input type='checkbox' name='select' value='".$row['SupplierID']."^".$row['SupplierName']."' />";
		$row_array[] = $RowNumber;
		$row_array[] = $row['SupplierCode'];
		$row_array[] = $row['SupplierName'];
		$row_array[] = $row['Telephone'];
		$row_array[] = $row['Address'];
		$row_array[] = $row['City'];
		$row_array[] = $row['Remarks'];
		$row_array[] = $row['SupplierID'];
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
