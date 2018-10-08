<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";
	$requestData= $_REQUEST;
	if(ISSET($requestData['BranchID']) && ISSET($requestData['CategoryID']) && ISSET($requestData['FirstPass']) && $requestData['FirstPass'] == "0")
	{
		$CategoryID = $requestData['CategoryID'];
		$BranchID = $requestData['BranchID'];
		//kolom di table
		$columns = array(
						0 => "ItemCode", //unorderable
						1 => "ItemName", //unorderable
						2 => "UnitName", //unorderable
						3 => "CategoryName",
						4 => "BranchName"
					);

		$where = " 1=1 ";
		$order_by = "ItemCode";
		$limit_s = $requestData['start'];
		$limit_l = $requestData['length'];
		
		//Handles Sort querystring sent from Bootgrid
		if(ISSET($requestData['order'])) {
			$order_by = $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
			$order_by .= ", ItemCode ASC";
		}
		//Handles search querystring sent from Bootgrid
		if (!empty($requestData['search']['value']))
		{
			$search = mysqli_escape_string($dbh, trim($requestData['search']['value']));
			$where .= " AND ( MI.ItemCode LIKE '%".$search."%'";
			$where .= " OR MI.ItemName LIKE '%".$search."%'";
			$where .= " OR MB.BranchName LIKE '%".$search."%'";
			$where .= " OR MC.CategoryName LIKE '%".$search."%' )";
		}
		$sql = "CALL spSelStockReport(".$CategoryID.", ".$BranchID.", \"$where\", '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/Stock/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
			//data yang dikirim ke table
			$row_array[] = $row['BranchName'];
			$row_array[] = $row['ItemCode'];
			$row_array[] = $row['ItemName'];
			$row_array[] = $row['UnitName'];
			$row_array[] = $row['CategoryName'];
			$row_array[] = number_format($row['Stock'],2,".",",");
			$row_array[] = number_format($row['PhysicalStock'],2,".",",");
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
