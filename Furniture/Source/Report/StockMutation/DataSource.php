<?php
	if(ISSET($_GET['ItemID'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		$ItemID = mysql_real_escape_string($_GET['ItemID']);
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
				else $order_by = "DATE_FORMAT(DATA.TransactionDate, '%d-%m-%Y') ASC";
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
					DATE_FORMAT('".$txtFromDate."', '%d%b%y') AS TransactionDate,
					'".$txtFromDate."' AS OrderDate,
					'' AS Name,
					DATA.Incoming,
					DATA.Outgoing,
					SUM(DATA.Stock) AS Stock,
					0 AS Price,
					'Stok Awal' AS Remarks,
					1 AS UnionLevel
				FROM
					(
						SELECT
							'-' AS TransactionDate,
							'-' AS Incoming,
							'-' AS Outgoing,
							SUM(ITD.Quantity) AS Stock
						FROM
							transaction_incomingtransactiondetails ITD
							JOIN transaction_incomingtransaction IT
								ON IT.IncomingTransactionID = ITD.IncomingTransactionID
						WHERE
							ITD.ItemID = ".$ItemID."
							AND IT.TransactionDate < '".$txtFromDate."'
						GROUP BY
							ITD.ItemID
						UNION ALL
						SELECT
							OT.TransactionDate,
							'-',
							'-',
							-SUM(OTD.Quantity) AS Quantity
						FROM
							transaction_outgoingtransactiondetails OTD
							JOIN transaction_outgoingtransaction OT
								ON OT.OutgoingTransactionID = OTD.OutgoingTransactionID
						WHERE
							OTD.ItemID = ".$ItemID."
							AND OT.TransactionDate < '".$txtFromDate."'
						GROUP BY
							OTD.ItemID
						UNION ALL
						SELECT
							RT.TransactionDate,
							'-',
							'-',
							SUM(RT.Quantity) AS Quantity
						FROM
							transaction_returntransaction RT
						WHERE
							RT.ItemID = ".$ItemID."
							AND RT.TransactionDate < '".$txtFromDate."'
						GROUP BY
							RT.ItemID
					)DATA
				UNION ALL
				SELECT
					DATE_FORMAT(DATA.TransactionDate, '%d%b%y') AS TransactionDate,
					DATA.TransactionDate AS OrderDate,
					DATA.Name,
					DATA.Incoming,
					DATA.Outgoing,
					0,
					DATA.Price,
					DATA.Remarks,
					DATA.UnionLevel
				FROM
					(
						SELECT
							IT.TransactionDate AS TransactionDate,
							ITD.Quantity AS Incoming,
							0 AS Outgoing,
							'' AS Name,
							ITD.Price,
							MS.SupplierName AS Remarks,
							1 AS UnionLevel
						FROM
							transaction_incomingtransactiondetails ITD
							JOIN transaction_incomingtransaction IT
								ON IT.IncomingTransactionID = ITD.IncomingTransactionID
							LEFT JOIN master_supplier MS
								ON MS.SupplierID = IT.SupplierID
						WHERE
							ITD.ItemID = ".$ItemID."
							AND IT.TransactionDate >= '".$txtFromDate."'
							AND IT.TransactionDate <= '".$txtToDate."'
						UNION ALL
						SELECT
							OT.TransactionDate,
							0,
							OTD.Quantity,
							OTD.Name,
							OTD.Price,
							MP.ProjectName,
							2 AS UnionLevel
						FROM
							transaction_outgoingtransactiondetails OTD
							JOIN transaction_outgoingtransaction OT
								ON OT.OutgoingTransactionID = OTD.OutgoingTransactionID
							JOIN master_project MP
								ON MP.ProjectID = OT.ProjectID
						WHERE
							OTD.ItemID = ".$ItemID."
							AND OT.TransactionDate >= '".$txtFromDate."'
							AND OT.TransactionDate <= '".$txtToDate."'
						UNION ALL
						SELECT
							RT.TransactionDate,
							RT.Quantity AS Quantity,
							0,
							'',
							RT.Price,
							CONCAT('Retur ', MP.ProjectName) AS Remarks,
							3 AS UnionLevel
						FROM
							transaction_returntransaction RT
							JOIN master_project MP
								ON MP.ProjectID = RT.ProjectID
						WHERE
							RT.ItemID = ".$ItemID."
							AND RT.TransactionDate >= '".$txtFromDate."'
							AND RT.TransactionDate <= '".$txtToDate."'
					)DATA
				WHERE
					$where
				ORDER BY	
					OrderDate ASC,
					UnionLevel ASC";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$nRows = mysql_num_rows($result);		
		$return_arr = array();
		//$nRows = mysql_num_rows($result);
		$Stock = 0;
		$RowNumber = 0;
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			if($row['Incoming'] == "-" && $row['Outgoing'] == "-") $Stock += $row['Stock'];
			else $Stock += $row['Incoming'] - $row['Outgoing'];
			$row_array['RowNumber'] = $RowNumber;
			$row_array['TransactionDate'] = $row['TransactionDate'];
			$row_array['Name'] = $row['Name'];
			$row_array['Incoming']= $row['Incoming'];
			$row_array['Outgoing'] = $row['Outgoing'];
			$row_array['Price'] = number_format($row['Price'],2,".",",");
			$row_array['Stock'] = $Stock;
			$row_array['Remarks'] = $row['Remarks'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
