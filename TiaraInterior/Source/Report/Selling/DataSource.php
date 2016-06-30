<?php
	if(ISSET($_GET['SalesID']) && ISSET($_GET['CustomerID'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		$SalesID = mysql_real_escape_string($_GET['SalesID']);
		$CustomerID = mysql_real_escape_string($_GET['CustomerID']);
		if($_GET['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
		}
		else {
			$txtFromDate = explode('-', mysql_real_escape_string($_GET['txtFromDate']));
			$_GET['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_GET['txtFromDate'];
		}
		if($_GET['txtToDate'] == "") {
			$txtToDate = date("Y-m-d");
		}
		else {
			$txtToDate = explode('-', mysql_real_escape_string($_GET['txtToDate']));
			$_GET['txtToDate'] = "$txtToDate[2]-$txtToDate[1]-$txtToDate[0]"; 
			$txtToDate = $_GET['txtToDate'];
		}
		
		//echo $txtFromDate;
		//echo $txtToDate;
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
		$sql = "SELECT
					OT.OutgoingNumber,
					DATE_FORMAT(OT.TransactionDate, '%d/%c/%y') AS TransactionDate,
					OT.TransactionDate DateNoFormat,
					MS.SalesName,
					MC.CustomerName,
					OT.DeliveryCost,
					IFNULL(SUM(CASE
									WHEN TOD.IsPercentage = 1
									THEN TOD.Quantity * (TOD.SalePrice - ((TOD.SalePrice * TOD.Discount)/100))
									ELSE TOD.Quantity * (TOD.SalePrice - TOD.Discount)
								END), 0) AS SubTotal,
					IFNULL(SUM(CASE
									WHEN TOD.IsPercentage = 1
									THEN TOD.Quantity * (TOD.SalePrice - ((TOD.SalePrice * TOD.Discount)/100))
									ELSE TOD.Quantity * (TOD.SalePrice - TOD.Discount)
								END), 0) + OT.DeliveryCost AS Total,
					OT.Remarks
				FROM
					transaction_outgoing OT
					JOIN master_sales MS
						ON MS.SalesID = OT.SalesID
					JOIN master_customer MC
						ON MC.CustomerID = OT.CustomerID
					LEFT JOIN transaction_outgoingdetails TOD
						ON TOD.OutgoingID = OT.OutgoingID
				WHERE
					CAST(OT.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(OT.TransactionDate AS DATE) <= '".$txtToDate."'
					AND OT.IsCancelled = 0
					AND CASE
							WHEN ".$SalesID." = 0
							THEN MS.SalesID
							ELSE ".$SalesID."
						END = MS.SalesID
					AND CASE
							WHEN ".$CustomerID." = 0
							THEN MC.CustomerID
							ELSE ".$CustomerID."
						END = MC.CustomerID
				GROUP BY
					OT.OutgoingNumber,
					OT.TransactionDate,
					MS.SalesName,
					MC.CustomerName
				UNION
				SELECT
					SR.SaleReturnNumber,
					DATE_FORMAT(SR.TransactionDate, '%d/%c/%y') AS TransactionDate,
					SR.TransactionDate DateNoFormat,
					MS.SalesName,
					MC.CustomerName,
					0,
					-IFNULL(SUM(CASE
									WHEN SRD.IsPercentage = 1
									THEN SRD.Quantity * (SRD.SalePrice - ((SRD.SalePrice * SRD.Discount)/100))
									ELSE SRD.Quantity * (SRD.SalePrice - SRD.Discount)
								END), 0) AS SubTotal,
					-IFNULL(SUM(CASE
									WHEN SRD.IsPercentage = 1
									THEN SRD.Quantity * (SRD.SalePrice - ((SRD.SalePrice * SRD.Discount)/100))
									ELSE SRD.Quantity * (SRD.SalePrice - SRD.Discount)
								END), 0) AS Total,
					SR.Remarks
				FROM
					transaction_salereturn SR
					JOIN master_customer MC
						ON MC.CustomerID = SR.CustomerID
					JOIN master_sales MS
						ON MS.SalesID = SR.SalesID
					LEFT JOIN transaction_salereturndetails SRD
						ON SRD.SaleReturnID = SR.SaleReturnID
				WHERE
					CAST(SR.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(SR.TransactionDate AS DATE) <= '".$txtToDate."'					
					AND CASE
							WHEN ".$CustomerID." = 0
							THEN MC.CustomerID
							ELSE ".$CustomerID."
						END = MC.CustomerID
					AND SR.IsCancelled = 0
					AND CASE
							WHEN ".$SalesID." = 0
							THEN MS.SalesID
							ELSE ".$SalesID."
						END = MS.SalesID
				GROUP BY
					SR.SaleReturnNumber,
					SR.TransactionDate,
					MC.CustomerName
				ORDER BY	
					DateNoFormat ASC";
		
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
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			$row_array['RowNumber'] = $RowNumber;
			$row_array['OutgoingNumber']= $row['OutgoingNumber'];
			$row_array['TransactionDate'] = $row['TransactionDate'];
			$row_array['SalesName'] = $row['SalesName'];
			$row_array['CustomerName'] = $row['CustomerName'];
			$row_array['DeliveryCost'] = number_format($row['DeliveryCost'],2,".",",");
			$row_array['SubTotal'] = number_format($row['SubTotal'],2,".",",");
			$row_array['Total'] = number_format($row['Total'],2,".",",");
			$row_array['Remarks'] = $row['Remarks'];
			$GrandTotal += $row['Total'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		$GrandTotal = number_format($GrandTotal,2,".",",");
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows, \"GrandTotal\": \"$GrandTotal\" }";
	}
?>
