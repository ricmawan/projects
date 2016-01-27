<?php
	if(ISSET($_GET['ddlMonth']) && ISSET($_GET['ddlYear'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ddlMonth = mysql_real_escape_string($_GET['ddlMonth']);
		$ddlYear = mysql_real_escape_string($_GET['ddlYear']);
		if(strlen($ddlMonth) == 1) $month = "0$ddlMonth";
		else $month = $ddlMonth;
		$SelectedDate = "$ddlYear-$month-01";
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
		mysql_query("SET @Balance:=0;", $dbh);
		mysql_query("SET @row:=0;", $dbh);
		$sql = "SELECT
					DATE_FORMAT('".$SelectedDate."', '%d%b%y') AS TransactionDate,
					'' AS ItemName,
					'' AS Quantity,
					'-' AS Price,
					'-' AS Debit,
					'-' AS Credit,
					SUM(DATA.Debit) - SUM(DATA.Credit) AS Balance,
					'Saldo Awal' AS Remarks,
					0 AS UnionLevel
				FROM
					(
						SELECT
							A.TransactionDate AS TransactionDate,
							'' AS ItemName,
							'' AS Quantity,
							'-' AS Price,
							A.Amount AS Debit,
							'-' AS Credit,
							A.Remarks AS Remarks,
							0 AS UnionLevel
						FROM
							transaction_asset A
						WHERE
							A.TransactionDate < '$SelectedDate'
						UNION ALL
						SELECT
							PP.ProjectTransactionDate AS TransactionDate,
							'' AS ItemName,
							'' AS Quantity,
							'-' AS Price,
							PP.Amount AS Debit,
							'-' AS Credit,
							CONCAT('Pembayaran ', PP.Remarks, ' Proyek ', P.ProjectName) AS Remarks,
							1 AS UnionLevel
						FROM
							transaction_projectpayment PP
							JOIN master_project P
								ON P.ProjectID = PP.ProjectID
						WHERE
							PP.ProjectTransactionDate < '$SelectedDate'
						UNION ALL
						SELECT
							IT.TransactionDate,
							CONCAT(MC.CategoryName, ' ', I.ItemName) AS ItemName, 
							ITD.Quantity,
							ITD.Price,
							'-' AS Debit,
							(ITD.Quantity * ITD.Price) AS Credit,
							'',
							3 AS UnionLevel
						FROM
							transaction_incomingtransaction IT
							JOIN transaction_incomingtransactiondetails ITD
								ON IT.IncomingTransactionID = ITD.IncomingTransactionID
							JOIN master_item I
								ON I.ItemID = ITD.ItemID
							JOIN master_category MC
								ON MC.CategoryID = I.CategoryID
							JOIN master_unit U
								ON U.UnitID = I.UnitID
						WHERE
							IT.TransactionDate < '$SelectedDate'
						UNION ALL
						SELECT
							PT.ProjectTransactionDate,
							'',
							'',
							'-',
							'-' AS Debit,
							PT.Amount AS Credit,
							CONCAT('Operasional ', PT.Remarks, ' Proyek ', P.ProjectName),
							4 AS UnionLevel
						FROM
							transaction_projecttransaction PT
							JOIN master_project P
								ON PT.ProjectID = P.ProjectID
						WHERE
							PT.ProjectTransactionDate < '$SelectedDate'
						UNION ALL
						SELECT
							OP.CommonOperationalDate,
							'',
							'',
							'-',
							'-' AS Debit,
							OPD.Amount AS Credit,
							CONCAT('Operasional ', OPD.Remarks),
							4 AS UnionLevel
						FROM
							transaction_commonoperational OP
							JOIN transaction_commonoperationaldetails OPD
								ON OP.CommonOperationalID = OPD.CommonOperationalID
						WHERE
							OP.CommonOperationalDate < '$SelectedDate'
					)DATA
				UNION ALL
				SELECT
					DATE_FORMAT(DATA.TransactionDate, '%d%b%y') AS TransactionDate,
					DATA.ItemName,
					DATA.Quantity,
					DATA.Price,
					DATA.Debit,
					DATA.Credit,
					(DATA.Debit - DATA.Credit) AS Balance,
					DATA.Remarks,
					DATA.UnionLevel
				FROM
					(
						SELECT
							A.TransactionDate AS TransactionDate,
							'' AS ItemName,
							'' AS Quantity,
							'-' AS Price,
							A.Amount AS Debit,
							'-' AS Credit,
							A.Remarks AS Remarks,
							0 AS UnionLevel
						FROM
							transaction_asset A
						WHERE
							MONTH(A.TransactionDate) = ".$ddlMonth."
							AND YEAR(A.TransactionDate) = ".$ddlYear."
						UNION ALL
						SELECT
							PP.ProjectTransactionDate AS TransactionDate,
							'' AS ItemName,
							'' AS Quantity,
							'-' AS Price,
							PP.Amount AS Debit,
							'-' AS Credit,
							CONCAT('Pembayaran ', PP.Remarks, ' Proyek ', P.ProjectName) AS Remarks,
							1 AS UnionLevel
						FROM
							transaction_projectpayment PP
							JOIN master_project P
								ON P.ProjectID = PP.ProjectID
						WHERE
							MONTH(PP.ProjectTransactionDate) = ".$ddlMonth."
							AND YEAR(PP.ProjectTransactionDate) = ".$ddlYear."
						UNION ALL
						SELECT
							IT.TransactionDate,
							CONCAT(MC.CategoryName, ' ', I.ItemName) AS ItemName, 
							ITD.Quantity,
							ITD.Price,
							'-' AS Debit,
							(ITD.Quantity * ITD.Price) AS Credit,
							'',
							3 AS UnionLevel
						FROM
							transaction_incomingtransaction IT
							JOIN transaction_incomingtransactiondetails ITD
								ON IT.IncomingTransactionID = ITD.IncomingTransactionID
							JOIN master_item I
								ON I.ItemID = ITD.ItemID
							JOIN master_category MC
								ON MC.CategoryID = I.CategoryID
							JOIN master_unit U
								ON U.UnitID = I.UnitID
						WHERE
							MONTH(IT.TransactionDate) = ".$ddlMonth."
							AND YEAR(IT.TransactionDate) = ".$ddlYear."
						UNION ALL
						SELECT
							PT.ProjectTransactionDate,
							'',
							'',
							'-',
							'-' AS Debit,
							PT.Amount AS Credit,
							CONCAT('Operasional ', PT.Remarks, ' Proyek ', P.ProjectName),
							4 AS UnionLevel
						FROM
							transaction_projecttransaction PT
							JOIN master_project P
								ON PT.ProjectID = P.ProjectID
						WHERE
							MONTH(PT.ProjectTransactionDate) = ".$ddlMonth."
							AND YEAR(PT.ProjectTransactionDate) = ".$ddlYear."
						UNION ALL
						SELECT
							OP.CommonOperationalDate,
							'',
							'',
							'-',
							'-' AS Debit,
							OPD.Amount AS Credit,
							CONCAT('Operasional ', OPD.Remarks),
							4 AS UnionLevel
						FROM
							transaction_commonoperational OP
							JOIN transaction_commonoperationaldetails OPD
								ON OP.CommonOperationalID = OPD.CommonOperationalID
						WHERE
							MONTH(OP.CommonOperationalDate) = ".$ddlMonth."
							AND YEAR(OP.CommonOperationalDate) = ".$ddlYear."
					)DATA
				WHERE
					$where
				ORDER BY 
					TransactionDate,
					UnionLevel";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$nRows = mysql_num_rows($result);		
		$return_arr = array();
		//$nRows = mysql_num_rows($result);
		$Balance = 0;
		$RowNumber = 0;
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			$Debit = "-";
			if($row['Debit'] != "-") $Debit = number_format($row['Debit'],2,".",",");
			$Credit = "-";
			if($row['Credit'] != "-") $Credit = number_format($row['Credit'],2,".",",");
			$Balance += $row['Balance'];
			$Price = "-";
			if($row['Price'] != "-") $Price = number_format($row['Price'],2,".",",");
			$row_array['RowNumber'] = $RowNumber;
			$row_array['TransactionDate'] = $row['TransactionDate'];
			$row_array['ItemName'] = $row['ItemName'];
			$row_array['Quantity'] = $row['Quantity'];
			$row_array['Price'] = $Price;
			$row_array['Debit'] = $Debit;
			$row_array['Credit'] = $Credit;
			$row_array['Balance'] = number_format($Balance,2,".",",");
			$row_array['Remarks'] = $row['Remarks'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
