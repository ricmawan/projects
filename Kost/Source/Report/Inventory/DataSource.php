<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	date_default_timezone_set("Asia/Jakarta");
	$rdInterval = mysql_real_escape_string($_GET['rdInterval']);
	$ddlMonth = mysql_real_escape_string($_GET['ddlMonth']);
	$ddlYear = mysql_real_escape_string($_GET['ddlYear']);
	$InventoryID = mysql_real_escape_string($_GET['ddlInventory']);
	if($InventoryID == "") $InventoryID = 0;
	if($_GET['txtStartDate'] == "" && $rdInterval == "Daily") {
		$txtStartDate = "2000-01-01";
	}
	else if($rdInterval == "Daily") {
		$txtStartDate = explode('-', mysql_real_escape_string($_GET['txtStartDate']));
		$_GET['txtStartDate'] = "$txtStartDate[2]-$txtStartDate[1]-$txtStartDate[0]"; 
		$txtStartDate = $_GET['txtStartDate'];
	}
	else {
		$txtStartDate = $ddlYear."-".$ddlMonth."-01";
	}
	if($_GET['txtEndDate'] == "" && $rdInterval == "Daily") {
		$txtEndDate = date("Y-m-d");
	}
	else if($rdInterval == "Daily") {
		$txtEndDate = explode('-', mysql_real_escape_string($_GET['txtEndDate']));
		$_GET['txtEndDate'] = "$txtEndDate[2]-$txtEndDate[1]-$txtEndDate[0]"; 
		$txtEndDate = $_GET['txtEndDate'];
	}
	else {
		$txtEndDate = date('Y-m-t', strtotime($txtStartDate));
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
				I.InventoryName,
				IID.Quantity,
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
				AND CASE
						WHEN $InventoryID = 0
						THEN I.InventoryID
						ELSE $InventoryID
					END = I.InventoryID
			UNION
			SELECT
				DATE_FORMAT(OI.TransactionDate, '%d-%m-%Y') TransactionDate,
				I.InventoryName,
				-OID.Quantity,
				OID.Remarks Notes
			FROM
				transaction_outgoinginventory OI
				JOIN transaction_outgoinginventorydetails OID
					ON OI.OutgoingInventoryID = OID.OutgoingInventoryID
				JOIN master_inventory I
					ON I.InventoryID = OID.InventoryID
			WHERE
				CAST(OI.TransactionDate AS DATE) >= '".$txtStartDate."'
				AND CAST(OI.TransactionDate AS DATE) <= '".$txtEndDate."'
				AND CASE
						WHEN $InventoryID = 0
						THEN I.InventoryID
						ELSE $InventoryID
					END = I.InventoryID
			ORDER BY
				InventoryName ASC,
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
	$InventoryName = "";
	while ($row = mysql_fetch_array($result)) {
		if($InventoryName == $row['InventoryName']) {
			$Stock += $row['Quantity'];
		}
		else {
			$Stock = $row['Quantity'];
		}
		$RowNumber++;
		$row_array['RowNumber'] = $RowNumber;
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['InventoryName'] = $row['InventoryName'];
		$row_array['Notes'] = $row['Notes'];
		$row_array['Quantity'] = $row['Quantity'];
		$row_array['Stock'] = $Stock;
		$InventoryName = $row['InventoryName'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
