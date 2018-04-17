<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";
	$requestData= $_REQUEST;
	if(ISSET($requestData['BranchID']) && ISSET($requestData['FirstPass']) && $requestData['FirstPass'] == "0")
	{
		$BranchID = $requestData['BranchID'];
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
		$columns = array(
						0 => "PurchaseNumber", //unorderable
						1 => "TransactionDate", //unorderable
						2 => "SupplierName"
					);

		$where = " 1=1 ";
		$where2 = " 1=1 ";
		$order_by = "PurchaseNumber";
		$limit_s = $requestData['start'];
		$limit_l = $requestData['length'];
		
		//Handles Sort querystring sent from Bootgrid
		$order_by = $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
		//Handles search querystring sent from Bootgrid
		$order_by .= ", PurchaseNumber ASC";
		if (!empty($requestData['search']['value']))
		{
			$search = mysqli_escape_string($dbh, trim($requestData['search']['value']));
			$where .= " AND ( TP.PurchaseNumber LIKE '%".$search."%'";
			$where .= " OR DATE_FORMAT(TP.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%'";
			$where .= " OR MS.SupplierName LIKE '%".$search."%' )";

			$where2 .= " AND ( TPR.PurchaseReturnNumber LIKE '%".$search."%'";
			$where2 .= " OR DATE_FORMAT(TPR.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%'";
			$where2 .= " OR MS.SupplierName LIKE '%".$search."%' )";
		}
		$sql = "CALL spSelPurchaseReport(".$BranchID.", '".$txtFromDate."', '".$txtToDate."', \"$where\", \"$where2\", '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/Purchase/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		$row = mysqli_fetch_array($result);
		$totalData = $row['nRows'];
		$GrandTotal = $row['GrandTotal'];
		$totalFiltered = $totalData;
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		
		$result2 = mysqli_use_result($dbh);
		$return_arr = array();
		$RowNumber = $requestData['start'];
		$SubTotal = 0;
		while ($row = mysqli_fetch_array($result2)) {
			$row_array = array();
			//data yang dikirim ke table
			$row_array["PurchaseNumber"] = $row['PurchaseNumber'];
			$row_array["TransactionDate"] = $row['TransactionDate'];
			$row_array["SupplierName"] = $row['SupplierName'];
			$row_array["Total"] = number_format($row['Total'],0,".",",");
			$row_array["PurchaseID"] = $row['PurchaseID'];
			$row_array["TransactionType"] = $row['TransactionType'];
			$SubTotal += $row['Total'];
			array_push($return_arr, $row_array);
		}
		
		mysqli_free_result($result2);
		mysqli_next_result($dbh);

		$json_data = array(
						"draw"				=> intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
						"recordsTotal"		=> intval( $totalData ),  // total number of records
						"recordsFiltered"	=> intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
						"data"				=> $return_arr,
						"SubTotal" => number_format($SubTotal,0,".",","),
						"GrandTotal" => number_format($GrandTotal,0,".",",")
					);
	}
	
	else {
		$json_data = array(
						"draw"				=> intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
						"recordsTotal"		=> 0,  // total number of records
						"recordsFiltered"	=> 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
						"data"				=> "",
						"SubTotal" => 0,
						"GrandTotal" => 0
					);
	}
	
	echo json_encode($json_data);
?>
