<?php
	if(ISSET($_GET['txtFromDate']) && ISSET($_GET['txtToDate'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
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
					OT.OutgoingNumber InvoiceNumber,
					DATE_FORMAT(OT.TransactionDate, '%d/%c/%y') AS TransactionDate,
					OT.TransactionDate DateNoFormat,
					IFNULL(CASE
								WHEN TOD.IsPercentage = 1
								THEN TOD.Quantity * ((TOD.SalePrice - ((TOD.SalePrice * TOD.Discount)/100)) - TOD.BuyPrice)
								ELSE TOD.Quantity * ((TOD.SalePrice - TOD.Discount) - TOD.BuyPrice)
							END, 0) AS Total,
					CONCAT(MB.BrandName, ' ', MT.TypeName, ' x ', TOD.Quantity) Remarks
				FROM
					transaction_outgoing OT
					JOIN transaction_outgoingdetails TOD
						ON TOD.OutgoingID = OT.OutgoingID
					JOIN master_type MT
						ON TOD.TypeID = MT.TypeID
					JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(OT.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(OT.TransactionDate AS DATE) <= '".$txtToDate."'
					AND OT.IsCancelled = 0
				UNION ALL
				SELECT
					SR.SaleReturnNumber,
					DATE_FORMAT(SR.TransactionDate, '%d/%c/%y') AS TransactionDate,
					SR.TransactionDate DateNoFormat,
					-IFNULL(CASE
								WHEN SRD.IsPercentage = 1
								THEN SRD.Quantity * ((SRD.SalePrice - ((SRD.SalePrice * SRD.Discount)/100)) - SRD.BuyPrice)
								ELSE SRD.Quantity * ((SRD.SalePrice - SRD.Discount) - SRD.BuyPrice)
							END, 0) AS Total,
					CONCAT(MB.BrandName, ' ', MT.TypeName, ' x ', SRD.Quantity) Remarks
				FROM
					transaction_salereturn SR
					JOIN transaction_salereturndetails SRD
						ON SRD.SaleReturnID = SR.SaleReturnID
					JOIN master_type MT
						ON SRD.TypeID = MT.TypeID
					JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(SR.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(SR.TransactionDate AS DATE) <= '".$txtToDate."'					
					AND SR.IsCancelled = 0
				UNION ALL
				SELECT
					'Operasional',
					DATE_FORMAT(OP.TransactionDate, '%d/%c/%y') AS TransactionDate,
					OP.TransactionDate,
					-OPD.Amount,
					OPD.Remarks
				FROM
					transaction_operational OP
					JOIN transaction_operationaldetails OPD
						ON OP.OperationalID = OPD.OperationalID
				WHERE
					CAST(OP.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(OP.TransactionDate AS DATE) <= '".$txtToDate."'	
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
			$row_array['InvoiceNumber']= $row['InvoiceNumber'];
			$row_array['TransactionDate'] = $row['TransactionDate'];
			$row_array['Remarks'] = $row['Remarks'];
			$row_array['Total'] = number_format($row['Total'],2,".",",");
			$GrandTotal += $row['Total'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		//$GrandTotal = number_format($GrandTotal,2,".",",");
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows, \"GrandTotal\": \"$GrandTotal\" }";
	}
?>
