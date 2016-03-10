<?php
	if(ISSET($_GET['ProjectID'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ID = mysql_real_escape_string($_GET['ProjectID']);
		$where = " 1=1 ";
		$order_by = "DATA.TransactionDate";
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
			$where .= " AND ( DATE_FORMAT(DATA.TransactionDate, '%d%b%y') LIKE '%".$search."%' OR DATA.Name LIKE '%".$search."%' OR DATA.ItemName LIKE '%".$search."%' OR DATA.Quantity LIKE '%".$search."%' OR DATA.UnitName LIKE '%".$search."%' OR DATA.Price LIKE '%".$search."%' OR DATA.Debit LIKE '%".$search."%' OR DATA.Credit LIKE '%".$search."%' OR DATA.Remarks LIKE '%".$search."%' )";
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
		mysql_query("SET @Balance:=0;", $dbh);
		mysql_query("SET @row:=0;", $dbh);
		$sql = "SELECT
					DATE_FORMAT(DATA.TransactionDate, '%d%b%y') AS TransactionDate,
					DATA.Name,
					DATA.ItemName,
					DATA.Quantity,
					DATA.UnitName,
					DATA.Price,
					DATA.Debit,
					DATA.Credit,
					@Balance:= @Balance + DATA.Debit - DATA.Credit AS Balance,
					DATA.Remarks
				FROM
					(
						SELECT
							PP.ProjectTransactionDate AS TransactionDate,
							'' AS Name,
							'' AS ItemName,
							'' AS Quantity,
							'' AS UnitName,
							'' AS Price,
							PP.Amount AS Debit,
							'-' AS Credit,
							CONCAT('Pembayaran ', PP.Remarks) AS Remarks,
							1 AS UnionLevel
						FROM
							transaction_projectpayment PP
						WHERE
							PP.ProjectID = $ID
						UNION ALL
						SELECT
							OT.TransactionDate,
							OTD.Name,
							CONCAT(MC.CategoryName, ' ', I.ItemName) AS ItemName, 
							OTD.Quantity,
							U.UnitName,
							OTD.Price,
							'-' AS Debit,
							(OTD.Quantity * OTD.Price) AS Credit,
							OTD.Remarks,
							2 AS UnionLevel
						FROM
							transaction_outgoingtransaction OT
							JOIN transaction_outgoingtransactiondetails OTD
								ON OT.OutgoingTransactionID = OTD.OutgoingTransactionID
							JOIN master_item I
								ON I.ItemID = OTD.ItemID
							JOIN master_category MC
								ON MC.CategoryID = I.CategoryID
							JOIN master_unit U
								ON U.UnitID = I.UnitID
						WHERE
							OT.ProjectID = $ID
						UNION ALL
						SELECT
							RT.TransactionDate,
							'',
							CONCAT(MC.CategoryName, ' ', I.ItemName) AS ItemName, 
							RT.Quantity,
							U.UnitName,
							RT.Price,
							(RT.Quantity * RT.Price) AS Debit,
							'-' AS Credit,
							'Retur' AS Remarks,
							3 AS UnionLevel
						FROM
							transaction_returntransaction RT
							JOIN master_item I
								ON I.ItemID = RT.ItemID
							JOIN master_category MC
								ON MC.CategoryID = I.CategoryID
							JOIN master_unit U
								ON U.UnitID = I.UnitID
						WHERE
							RT.ProjectID = $ID
						UNION ALL
						SELECT
							PT.ProjectTransactionDate,
							'',
							'',
							'',
							'',
							'',
							'-' AS Debit,
							PT.Amount AS Credit,
							CONCAT('Operasional ', PT.Remarks),
							4 AS UnionLevel
						FROM
							transaction_projecttransaction PT
						WHERE
							PT.ProjectID = $ID
					)DATA
				WHERE
					$where
				ORDER BY 
					$order_by,
					DATA.TransactionDate,
					DATA.UnionLevel";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$nRows = mysql_num_rows($result);		
		$return_arr = array();
		$RowNumber = 0;
		//$nRows = mysql_num_rows($result);
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			$Debit = "-";
			if($row['Debit'] != "-") $Debit = number_format($row['Debit'],2,".",",");
			$Credit = "-";
			if($row['Credit'] != "-") $Credit = number_format($row['Credit'],2,".",",");
			$Balance = "-";
			if($row['Balance'] != "-") $Balance = number_format($row['Balance'],2,".",",");
			$Price = "-";
			if($row['Price'] != "") $Price = number_format($row['Price'],2,".",",");
			$row_array['RowNumber'] = $RowNumber;
			$row_array['TransactionDate'] = $row['TransactionDate'];
			$row_array['Name']= $row['Name'];
			$row_array['ItemName'] = $row['ItemName'];
			$row_array['Quantity'] = $row['Quantity'];
			$row_array['UnitName'] = $row['UnitName'];
			$row_array['Price'] = $Price;
			$row_array['Debit'] = $Debit;
			$row_array['Credit'] = $Credit;
			$row_array['Balance'] = $Balance;
			$row_array['Remarks'] = $row['Remarks'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
