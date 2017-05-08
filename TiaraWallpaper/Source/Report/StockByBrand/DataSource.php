<?php
	if(ISSET($_GET['BrandID']) ) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		$BrandID = mysql_real_escape_string($_GET['BrandID']);
		
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
		mysql_query("SET @row:=0;", $dbh);
		
		$sql = "SELECT
					MB.BrandName,
					SUM(IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(BO.Quantity, 0) + IFNULL(SO.Quantity, 0)) Stock
				FROM
					master_type MT
					JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
					LEFT JOIN
					(
						SELECT
							TypeID,
							TRIM(BatchNumber) BatchNumber,
							SUM(SA.Quantity) Quantity
						FROM
						(
							SELECT
								TypeID,
								TRIM(BatchNumber) BatchNumber,
								SUM(Quantity) Quantity
							FROM
								transaction_firststockdetails
							GROUP BY
								TypeID,
								BatchNumber
							UNION ALL
							SELECT
								TID.TypeID,
								TRIM(TID.BatchNumber) BatchNumber,
								SUM(TID.Quantity) Quantity
							FROM
								transaction_incoming TI
								JOIN transaction_incomingdetails TID
									ON TI.IncomingID = TID.IncomingID
							WHERE
								TI.IsCancelled = 0
							GROUP BY
								TID.TypeID,
								TID.BatchNumber
						)SA
						GROUP BY
							TypeID,
							BatchNumber
					)FS
						ON FS.TypeID = MT.TypeID
					LEFT JOIN
					(
						SELECT
							OTD.TypeID,
							TRIM(OTD.BatchNumber) BatchNumber,
							SUM(OTD.Quantity) Quantity
						FROM
							transaction_outgoingdetails OTD
							JOIN transaction_outgoing OT
								ON OT.OutgoingID = OTD.OutgoingID
						WHERE
							OT.IsCancelled = 0
						GROUP BY
							OTD.TypeID,
							OTD.BatchNumber
					)TOD
						ON TOD.TypeID = MT.TypeID
						AND TOD.BatchNumber = FS.BatchNumber
					LEFT JOIN
					(
						SELECT
							BRD.TypeID,
							TRIM(BRD.BatchNumber) BatchNumber,
							SUM(BRD.Quantity) Quantity
						FROM
							transaction_buyreturn BR
							JOIN transaction_buyreturndetails BRD
								ON BR.BuyReturnID = BRD.BuyReturnID
						WHERE
							BR.IsCancelled = 0
						GROUP BY
							BRD.TypeID,
							BRD.BatchNumber
					)BR
						ON BR.TypeID = MT.TypeID
						AND BR.BatchNumber = FS.BatchNumber
					LEFT JOIN
					(
						SELECT
							SRD.TypeID,
							TRIM(SRD.BatchNumber) BatchNumber,
							SUM(SRD.Quantity) Quantity
						FROM
							transaction_salereturn SR
							JOIN transaction_salereturndetails SRD
								ON SR.SaleReturnID = SRD.SaleReturnID
						WHERE
							SR.IsCancelled = 0 
						GROUP BY
							SRD.TypeID,
							SRD.BatchNumber
					)SR
						ON SR.TypeID = MT.TypeID
						AND SR.BatchNumber = FS.BatchNumber
					LEFT JOIN
					(
						SELECT
							BOD.TypeID,
							TRIM(BOD.BatchNumber) BatchNumber,
							SUM(BOD.Quantity) Quantity
						FROM
							transaction_booking BO
							JOIN transaction_bookingdetails BOD
								ON BO.BookingID = BOD.BookingID
						WHERE
							BO.BookingStatusID = 1
						GROUP BY
							BOD.TypeID,
							BOD.BatchNumber
					)BO
						ON BO.TypeID = MT.TypeID
						AND BO.BatchNumber = FS.BatchNumber
					LEFT JOIN
					(
						SELECT
							SOD.TypeID,
							TRIM(SOD.BatchNumber) BatchNumber,
							SUM(
									CASE
										WHEN SOD.FromQty > SOD.ToQty
										THEN -(SOD.FromQty - SOD.ToQty)
										ELSE (SOD.ToQty - SOD.FromQty)
									END
								) Quantity
						FROM
							transaction_stockopname SO
							JOIN transaction_stockopnamedetails SOD
								ON SO.StockOpnameID = SOD.StockOpnameID
						GROUP BY
							SOD.TypeID,
							SOD.BatchNumber
					)SO
						ON SO.TypeID = MT.TypeID
						AND SO.BatchNumber = FS.BatchNumber
				WHERE
					CASE
						WHEN ".$BrandID." = 0
						THEN MB.BrandID
						ELSE ".$BrandID."
					END = MB.BrandID
				GROUP BY
					MB.BrandName";
		
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
			
			
			$row_array['RowNumber'] = $RowNumber;
			$row_array['BrandName'] = $row['BrandName'];
			$row_array['Stock'] = $row['Stock'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		$GrandTotal = number_format($GrandTotal,2,".",",");
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
