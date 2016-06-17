<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	date_default_timezone_set("Asia/Jakarta");
	if($_GET['txtStartDate'] == "") {
		$txtStartDate = "2000-01-01";
	}
	else {
		$txtStartDate = explode('-', mysql_real_escape_string($_GET['txtStartDate']));
		$_GET['txtStartDate'] = "$txtStartDate[2]-$txtStartDate[1]-$txtStartDate[0]"; 
		$txtStartDate = $_GET['txtStartDate'];
	}
	if($_GET['txtEndDate'] == "") {
		$txtEndDate = date("Y-m-d");
	}
	else {
		$txtEndDate = explode('-', mysql_real_escape_string($_GET['txtEndDate']));
		$_GET['txtEndDate'] = "$txtEndDate[2]-$txtEndDate[1]-$txtEndDate[0]"; 
		$txtEndDate = $_GET['txtEndDate'];
	}
	
	//echo $txtStartDate;
	//echo $txtEndDate;
	$where = " 1=1 ";
	$order_by = "";
	$rows = 10;
	$current = 1;
	$limit_l = ($current * $rows) - ($rows);
	$limit_h = $limit_l + $rows ;
	//Handles Sort querystring sent from Bootgrid
	if (ISSET($_REQUEST['sort']) && is_array($_REQUEST['sort']) )
	{
		$order_by = "";
		foreach($_REQUEST['sort'] as $key => $value) {
			if($key != 'No') $order_by .= " $key $value";
			else $order_by = "DATE_FORMAT(TO.TransactionDate, '%d-%m-%Y') ASC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		//$where .= " AND ( DATE_FORMAT(DATA.TransactionDate, '%d%b%y') LIKE '%".$search."%' OR DATA.Name LIKE '%".$search."%' OR DATA.ItemName LIKE '%".$search."%' OR DATA.Quantity LIKE '%".$search."%' OR DATA.UnitName LIKE '%".$search."%' OR DATA.Price LIKE '%".$search."%' OR DATA.Debit LIKE '%".$search."%' OR DATA.Credit LIKE '%".$search."%' OR DATA.Remarks LIKE '%".$search."%' )";
	}
	//Handles determines where in the paging count this result set falls in
	if (ISSET($_REQUEST['rowCount']) ) $rows = $_REQUEST['rowCount'];
	//calculate the low and high limits for the SQL LIMIT x,y clause
	if (ISSET($_REQUEST['current']) )
	{
		$current = $_REQUEST['current'];
		$limit_l = ($current * $rows) - ($rows);
		$limit_h = $rows ;
	}
	mysql_query("SET @row:=0;", $dbh);
	
	$sql = "SELECT
				DATE_FORMAT(II.TransactionDate, '%d-%m-%Y') TransactionDate,
				CONCAT(I.InventoryName, ' ', IID.Quantity, ' x ', FORMAT(IID.Price, 0, 'de_DE')) Remarks,
				-(IID.Quantity * IID.Price) Total,
				IID.Remarks Notes
			FROM
				transaction_incominginventory II
				JOIN transaction_incominginventorydetails IID
					ON II.IncomingInventoryID = IID.IncomingInventoryID
				JOIN master_inventory I
					ON I.InventoryID = IID.InventoryID
			WHERE
				CAST(II.TransactionDate AS DATE) >= '".$txtStartDate."'
				AND CAST(II.TransactionDate AS DATE) <= '".$txtEndDate."'
			UNION
			SELECT
				DATE_FORMAT(BO.DownPaymentDate, '%d-%m-%Y') TransactionDate,
				CONCAT('DP Booking kamar ', MR.RoomNumber, ' untuk ', CASE
																		WHEN BO.RateType = 'Daily'
																		THEN CONCAT(DATEDIFF(BO.EndDate, BO.StartDate), ' hari')
																		ELSE CONCAT(TIMESTAMPDIFF(HOUR, BO.EndDate, BO.StartDate), ' jam')
																	  END) Remarks,
				BO.DownPaymentAmount Total,
				BO.Remarks Notes
			FROM
				transaction_booking BO
				JOIN master_room MR
					ON MR.RoomID = BO.RoomID
			WHERE
				CAST(BO.DownPaymentDate AS DATE) >= '".$txtStartDate."'
				AND CAST(BO.DownPaymentDate AS DATE) <= '".$txtEndDate."'
				AND BO.CheckInFlag = 0
			UNION
			SELECT
				DATE_FORMAT(CI.DownPaymentDate, '%d-%m-%Y') TransactionDate,
				CONCAT('DP Booking kamar ', MR.RoomNumber, ' untuk ', CASE
																		WHEN CI.RateType = 'Daily'
																		THEN CONCAT(DATEDIFF(CI.EndDate, CI.StartDate), ' hari')
																		ELSE CONCAT(TIMESTAMPDIFF(HOUR, CI.EndDate, CI.StartDate), ' jam')
																	  END) Remarks,
				CI.DownPaymentAmount Total,
				CI.Remarks Notes
			FROM
				transaction_checkin CI
				JOIN master_room MR
					ON MR.RoomID = CI.RoomID
			WHERE
				CAST(CI.DownPaymentDate AS DATE) >= '".$txtStartDate."'
				AND CAST(CI.DownPaymentDate AS DATE) <= '".$txtEndDate."'
			UNION
			SELECT
				DATE_FORMAT(CI.PaymentDate, '%d-%m-%Y') TransactionDate,
				CONCAT('Pelunasan kamar ', MR.RoomNumber, ' untuk ', CASE
																		WHEN CI.RateType = 'Daily'
																		THEN CONCAT(DATEDIFF(CI.EndDate, CI.StartDate), ' hari')
																		ELSE CONCAT(TIMESTAMPDIFF(HOUR, CI.EndDate, CI.StartDate), ' jam')
																	  END) Remarks,
				CI.PaymentAmount Total,
				CI.Remarks Notes
			FROM
				transaction_checkin CI
				JOIN master_room MR
					ON MR.RoomID = CI.RoomID
			WHERE
				CAST(CI.PaymentDate AS DATE) >= '".$txtStartDate."'
				AND CAST(CI.PaymentDate AS DATE) <= '".$txtEndDate."'
			UNION
			SELECT
				DATE_FORMAT(O.TransactionDate, '%d-%m-%Y') TransactionDate,
				OD.Remarks,
				OD.Amount,
				'Biaya'
			FROM
				transaction_operational O
				JOIN transaction_operationaldetails OD
					ON O.OperationalID = OD.OperationalID
			WHERE
				CAST(O.TransactionDate AS DATE) >= '".$txtStartDate."'
				AND CAST(O.TransactionDate AS DATE) <= '".$txtEndDate."'
			ORDER BY
				TransactionDate ASC";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$nRows = mysql_num_rows($result);		
	$return_arr = array();
	//$nRows = mysql_num_rows($result);
	$Stock = 0;
	$RowNumber = 0;
	$GrandTotal = 0;
	$BatchNumber = "";
	while ($row = mysql_fetch_array($result)) {
		$RowNumber++;
		$GrandTotal += $row['Total'];
		$row_array['RowNumber'] = $RowNumber;
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['Remarks'] = $row['Remarks'];
		$row_array['Notes'] = $row['Notes'];
		$row_array['Total'] = number_format($row['Total'],2,".",",");
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	$GrandTotal = number_format($GrandTotal,2,".",",");
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows, \"GrandTotal\": \"$GrandTotal\" }";
?>
