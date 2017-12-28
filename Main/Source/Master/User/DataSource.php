<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";

	$requestData= $_REQUEST;
	$columns = array(
					0 => "UserID",
					1 => "RowNumber",
					2 => "UserName",
					3 => "UserLogin",
					4 => "Status"
				);

	$where = " 1=1 AND MUT.UserTypeID = 1 ";
	$order_by = "MU.UserID";
	$limit_s = $requestData['start'];
	$limit_l = $requestData['length'];
	
	//Handles Sort querystring sent from Bootgrid
	$order_by = $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
	//Handles search querystring sent from Bootgrid
	if (!empty($requestData['search']['value']))
	{
		$search = trim($requestData['search']['value']);
		$where .= " AND ( MU.UserName LIKE '%".$search."%'";
		$where .= "OR MUT.UserTypeName LIKE '%".$search."%'";
		$where .= "OR MU.UserLogin LIKE '%".$search."%'";
		$where .= "OR CASE
						WHEN MU.IsActive = 0
						THEN 'Tidak Aktif'
						ELSE 'Aktif'
					  END LIKE '%".$search."%') ";
	}
	
	$sql = "CALL spSelUser('$where', '$order_by', $limit_s, $limit_l, '".$_SESSION['UserLogin']."')";		
	if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Master/User/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		echo "<script>$('#loading').hide();</script>";
		return 0;
	}
	//$result1 = mysqli_use_result($dbh);
	$row = mysqli_fetch_array($result);
	$totalData = $row['nRows'];
	$totalFiltered = $totalData;
	mysqli_free_result($result);
	mysqli_next_result($dbh);
	
	$result2 = mysqli_use_result($dbh);
	/*if (! $result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/Master/User/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		echo "<script>$('#loading').hide();</script>";
		return 0;
	}*/
	$return_arr = array();
	$RowNumber = $requestData['start'];
	while ($row = mysqli_fetch_array($result2)) {
		$row_array = array();
		$RowNumber++;
		$row_array[] = "<input type='checkbox' name='select' value='".$row['UserID']."^".$row['UserName']."' />";
		$row_array[] = $RowNumber;
		$row_array[] = $row['UserName'];
		$row_array[] = $row['UserID'];
		$row_array[] = $row['UserLogin'];
		$row_array[]= $row['Status'];
		
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
