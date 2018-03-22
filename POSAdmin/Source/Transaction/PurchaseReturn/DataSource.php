<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";

	$requestData= $_REQUEST;
	//kolom di table
	$columns = array(
					0 => "PurchaseReturnID", //unorderable
					1 => "RowNumber", //unorderable
					2 => "TPR.PurchaseReturnNumber",
					3 => "TPR.TransactionDate",
					4 => "MS.SupplierName",
					5 => "TPRD.Total"
				);

	$where = " 1=1 ";
	$order_by = "TRP.PurchaseReturnID";
	$limit_s = $requestData['start'];
	$limit_l = $requestData['length'];
	
	//Handles Sort querystring sent from Bootgrid
	$order_by = $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
	$order_by .= ", TPR.PurchaseReturnID ASC";
	//Handles search querystring sent from Bootgrid
	if (!empty($requestData['search']['value']))
	{
		$search = mysqli_real_escape_string($dbh, trim($requestData['search']['value']));
		$where .= " AND ( DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%'";
		$where .= " OR TPR.PurchaseReturnNumber LIKE '%".$search."%'";
		$where .= " OR MS.SupplierName LIKE '%".$search."%' )";
	}
	$sql = "CALL spSelPurchaseReturn(\"$where\", '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLogin']."')";

	if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Transaction/PurchaseReturn/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
		$row_array[] = "<input name='select' type='checkbox' value='".$row['PurchaseReturnID']."^".$row['PurchaseReturnNumber']."' />";
		$row_array[] = $RowNumber;
		$row_array[] = $row['PurchaseReturnNumber'];
		$row_array[] = $row['TransactionDate'];
		$row_array[] = $row['SupplierName'];
		$row_array[] = number_format($row['Total'],0,".",",");
		$row_array[] = $row['PurchaseReturnID'];
		$row_array[] = $row['SupplierID'];
		$row_array[] = $row['PlainTransactionDate'];
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
