<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../DBConfig.php";
	include "../GetSession.php";

	$requestData= $_REQUEST;
	//kolom di table
	$columns = array(
					0 => "RowNumber", //unorderable
					1 => "PurhcaseNumber",
					2 => "TransactionDate",
					3 => "Deadline",
					4 => "SupplierName",
					5 => "Total",
					6 => "TotalPayment",
					7 => "Debt"
				);

	$where = " 1=1 ";	
	$order_by = "TransactionDate";
	$limit_s = $requestData['start'];
	$limit_l = $requestData['length'];
	
	//Handles Sort querystring sent from Bootgrid
	if(ISSET($requestData['order'])) {
		$order_by = $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
		$order_by .= ", PurchaseID ASC";
	}
	//Handles search querystring sent from Bootgrid
	if (!empty($requestData['search']['value']))
	{
		$search = mysqli_escape_string($dbh, trim($requestData['search']['value']));
		$where .= " AND ( TP.PurchaseNumber LIKE '%".$search."%'";
		$where .= " OR DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%'";
		$where .= " OR DATE_FORMAT(TP.Deadline, '%d-%m-%Y') LIKE '%".$search."%'";
		$where .= " OR MS.SupplierName LIKE '%".$search."%')";
	}
	$sql = "CALL spSelDeadlinePurchase(\"$where\", '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLogin']."')";
	if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Notification/DebtDataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
		$row_array[] = $RowNumber;
		$row_array[] = $row['PurchaseNumber'];
		$row_array[] = $row['TransactionDate'];
		$row_array[] = $row['Deadline'];
		$row_array[] = $row['SupplierName'];
		$row_array[] = number_format($row['Total'],0,".",",");
		$row_array[] = number_format($row['TotalPay'],0,".",",");
		$row_array[] = number_format($row['Debt'],0,".",",");
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

