<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "TC.CancellationID DESC";
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
			else $order_by = "OT.OutgoingID DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( TI.IncomingNumber LIKE '%".$search."%' OR OT.OutgoingNumber LIKE '%".$search."%' OR SR.SaleReturnNumber LIKE '%".$search."%' OR BR.BuyReturnNumber LIKE '%".$search."%' OR TI.Remarks LIKE '%".$search."%' OR OT.Remarks LIKE '%".$search."%' OR SR.Remarks LIKE '%".$search."%' OR BR.Remarks LIKE '%".$search."%' OR MC.CustomerName LIKE '%".$search."%' OR MS.SupplierName LIKE '%".$search."%' OR DATE_FORMAT(TC.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' ) ";
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
	if ($rows == -1) $limit = ""; //no limit
	else $limit = " LIMIT $limit_l, $limit_h ";
	$sql = "SELECT
				COUNT(*) AS nRows
			FROM
				transaction_cancellation TC
				JOIN master_user MU
					ON MU.UserID = TC.DeletedBy
				LEFT JOIN transaction_outgoing OT
					ON OT.OutgoingID = TC.OutgoingID
				LEFT JOIN transaction_incoming TI
					ON TI.IncomingID = TC.IncomingID
				LEFT JOIN transaction_buyreturn BR
					ON BR.BuyReturnID = TC.BuyReturnID
				LEFT JOIN transaction_salereturn SR
					ON SR.SaleReturnID = TC.SaleReturnID
				LEFT JOIN master_customer MC
					ON (OT.CustomerID = MC.CustomerID
					OR SR.CustomerID = MC.CustomerID)
				LEFT JOIN master_supplier MS
					ON (MS.SupplierID = TI.SupplierID
					OR MS.SupplierID = BR.SupplierID)
			WHERE
				$where";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				TC.CancellationID,
				COALESCE(OT.OutgoingNumber, TI.IncomingNumber, SR.SaleReturnNumber, BR.BuyReturnNumber) InvoiceNumber,
				IFNULL(MC.CustomerName, MS.SupplierName) Name,
				DATE_FORMAT(TC.TransactionDate, '%d-%m-%Y') AS TransactionDate,
				CASE
					WHEN TC.OutgoingID <> 0
					THEN IFNULL(SUM(CASE
								WHEN OTD.IsPercentage = 1
								THEN OTD.Quantity * (OTD.SalePrice - ((OTD.SalePrice * OTD.Discount)/100))
								ELSE OTD.Quantity * (OTD.SalePrice - OTD.Discount)
							END), 0) + OT.DeliveryCost
					WHEN TC.IncomingID <> 0
					THEN IFNULL(SUM(CASE
								WHEN ITD.IsPercentage = 1
								THEN ITD.Quantity * (ITD.BuyPrice - ((ITD.BuyPrice * ITD.Discount)/100))
								ELSE ITD.Quantity * (ITD.BuyPrice - ITD.Discount)
							END), 0) + TI.DeliveryCost
					WHEN TC.BuyReturnID <> 0
					THEN IFNULL(SUM(CASE
								WHEN BRD.IsPercentage = 1
								THEN BRD.Quantity * (BRD.BuyPrice - ((BRD.BuyPrice * BRD.Discount)/100))
								ELSE BRD.Quantity * (BRD.BuyPrice - BRD.Discount)
							END), 0)
					ELSE IFNULL(SUM(CASE
								WHEN SRD.IsPercentage = 1
								THEN SRD.Quantity * (SRD.SalePrice - ((SRD.SalePrice * SRD.Discount)/100))
								ELSE SRD.Quantity * (SRD.SalePrice - SRD.Discount)
							END), 0)
				END AS Total,
				COALESCE(OT.Remarks, TI.Remarks, BR.Remarks, SR.Remarks) Remarks,
				MU.UserLogin DeletedBy
			FROM
				transaction_cancellation TC
				JOIN master_user MU
					ON MU.UserID = TC.DeletedBy
				LEFT JOIN transaction_outgoing OT
					ON OT.OutgoingID = TC.OutgoingID
				LEFT JOIN transaction_incoming TI
					ON TI.IncomingID = TC.IncomingID
				LEFT JOIN transaction_buyreturn BR
					ON BR.BuyReturnID = TC.BuyReturnID
				LEFT JOIN transaction_salereturn SR
					ON SR.SaleReturnID = TC.SaleReturnID
				LEFT JOIN master_customer MC
					ON (OT.CustomerID = MC.CustomerID
					OR SR.CustomerID = MC.CustomerID)
				LEFT JOIN master_supplier MS
					ON (MS.SupplierID = TI.SupplierID
					OR MS.SupplierID = BR.SupplierID)
				LEFT JOIN transaction_outgoingdetails OTD
					ON OTD.OutgoingID = OT.OutgoingID
				LEFT JOIN transaction_incomingdetails ITD
					ON TI.IncomingID = ITD.IncomingID
				LEFT JOIN transaction_buyreturndetails BRD
					ON BR.BuyReturnID = BRD.BuyReturnID
				LEFT JOIN transaction_salereturndetails SRD
					ON SR.SaleReturnID = SRD.SaleReturnID
			WHERE
				$where
			GROUP BY
				TC.CancellationID,
				OT.OutgoingNumber, 
				TI.IncomingNumber, 
				SR.SaleReturnNumber, 
				BR.BuyReturnNumber,
				MC.CustomerName, 
				MS.SupplierName,
				TC.TransactionDate,
				TC.OutgoingID,
				TC.IncomingID,
				TC.BuyReturnID,
				TC.SaleReturnID,
				OT.Remarks, 
				TI.Remarks, 
				BR.Remarks, 
				SR.Remarks,
				MU.UserLogin
			ORDER BY 
				$order_by
			$limit";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$return_arr = array();
	$RowNumber = $limit_l;
	while ($row = mysql_fetch_array($result)) {
		$RowNumber++;
		$row_array['RowNumber'] = $RowNumber;
		$row_array['CancellationIDNo']= $row['CancellationID']."^".$row['InvoiceNumber'];
		$row_array['CancellationID'] = $row['CancellationID'];
		$row_array['InvoiceNumber']= $row['InvoiceNumber'];
		$row_array['Name'] = $row['Name'];
		$row_array['Total'] =  number_format($row['Total'],2,".",",");
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['Remarks'] = $row['Remarks'];
		$row_array['DeletedBy'] = $row['DeletedBy'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
