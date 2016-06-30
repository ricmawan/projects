<?php
	if(ISSET($_GET['BrandID']) && ISSET($_GET['TypeID'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		$BrandID = mysql_real_escape_string($_GET['BrandID']);
		$TypeID = mysql_real_escape_string($_GET['TypeID']);
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
		mysql_query("SET @row:=0;", $dbh);
		
		$sql = "SELECT
					'Stok Awal' TransactionType,
					'' TransactionNumber,
					'' TransactionDate,
					'2000-01-01' DateNoFormat,
					'' CustomerName,
					FS.BatchNumber,
					(IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(BO.Quantity, 0) + IFNULL(SO.Quantity, 0)) Quantity,
					'' Remarks
				FROM
					master_type MT
					JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
					JOIN master_unit MU
						ON MU.UnitID = MT.UnitID
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
								transaction_firststock FS
								JOIN transaction_firststockdetails FSD
									ON FS.FirstStockID = FSD.FirstStockID
							WHERE
								CAST(FS.TransactionDate AS DATE) < '".$txtFromDate."'
								AND FSD.TypeID = ".$TypeID."
							GROUP BY
								TypeID,
								BatchNumber
							UNION
							SELECT
								TypeID,
								TRIM(BatchNumber) BatchNumber,
								SUM(Quantity) Quantity
							FROM
								transaction_incoming TI
								JOIN transaction_incomingdetails TID
									ON TI.IncomingID = TID.IncomingID
							WHERE
								CAST(TI.TransactionDate AS DATE) < '".$txtFromDate."'
								AND TID.TypeID = ".$TypeID."
								AND TI.IsCancelled = 0
							GROUP BY
								TypeID,
								BatchNumber
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
							AND CAST(OT.TransactionDate AS DATE) < '".$txtFromDate."'
							AND OTD.TypeID = ".$TypeID."
						GROUP BY
							OTD.TypeID,
							OTD.BatchNumber
					)TOD
						ON TOD.TypeID = MT.TypeID
						AND TOD.BatchNumber = FS.BatchNumber
					LEFT JOIN
					(
						SELECT
							TypeID,
							TRIM(BatchNumber) BatchNumber,
							SUM(Quantity) Quantity
						FROM
							transaction_buyreturn BR
							JOIN transaction_buyreturndetails BRD
								ON BR.BuyReturnID = BRD.BuyReturnID
						WHERE
							CAST(BR.TransactionDate AS DATE) < '".$txtFromDate."'
							AND BRD.TypeID = ".$TypeID."
							AND BR.IsCancelled = 0
						GROUP BY
							TypeID,
							BatchNumber
					)BR
						ON BR.TypeID = MT.TypeID
						AND BR.BatchNumber = FS.BatchNumber
					LEFT JOIN
					(
						SELECT
							TypeID,
							TRIM(BatchNumber) BatchNumber,
							SUM(Quantity) Quantity
						FROM
							transaction_salereturn SR
							JOIN transaction_salereturndetails SRD
								ON SRD.SaleReturnID = SR.SaleReturnID
						WHERE
							CAST(SR.TransactionDate AS DATE) < '".$txtFromDate."'
							AND SRD.TypeID = ".$TypeID."
							AND SR.IsCancelled = 0
						GROUP BY
							TypeID,
							BatchNumber
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
							AND CAST(BO.TransactionDate AS DATE) < '".$txtFromDate."'
							AND BOD.TypeID = ".$TypeID."
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
						WHERE
							CAST(SO.TransactionDate AS DATE) < '".$txtFromDate."'
							AND SOD.TypeID = ".$TypeID."
						GROUP BY
							SOD.TypeID,
							SOD.BatchNumber
					)SO
						ON SO.TypeID = MT.TypeID
						AND SO.BatchNumber = FS.BatchNumber
				WHERE
					MT.TypeID = ".$TypeID."
					AND (IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(BO.Quantity, 0) + IFNULL(SO.Quantity, 0)) > 0
				UNION ALL
				SELECT
					'Stok Awal' TransactionType,
					FS.FirstStockNumber TransactionNumber,
					DATE_FORMAT(FS.TransactionDate, '%d/%c%/%y') AS TransactionDate,
					FS.TransactionDate DateNoFormat,
					'' CustomerName,
					FSD.BatchNumber,
					FSD.Quantity,
					'' Remarks
				FROM
					transaction_firststock FS
					LEFT JOIN transaction_firststockdetails FSD
						ON FSD.FirstStockID = FS.FirstStockID
					LEFT JOIN master_type MT
						ON MT.TypeID = FSD.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(FS.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(FS.TransactionDate AS DATE) <= '".$txtToDate."'
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				UNION ALL
				SELECT
					'Pembelian',
					TI.IncomingNumber,
					DATE_FORMAT(TI.TransactionDate, '%d/%c%/%y') AS TransactionDate,
					TI.TransactionDate DateNoFormat,
					MS.SupplierName,
					TID.BatchNumber,
					TID.Quantity,
					TI.Remarks
				FROM
					transaction_incoming TI
					JOIN master_supplier MS
						ON MS.SupplierID = TI.SupplierID
					LEFT JOIN transaction_incomingdetails TID
						ON TI.IncomingID = TID.IncomingID
					LEFT JOIN master_type MT
						ON MT.TypeID = TID.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(TI.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TI.TransactionDate AS DATE) <= '".$txtToDate."'
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				UNION ALL
				SELECT
					'Penjualan',
					OT.OutgoingNumber,
					DATE_FORMAT(OT.TransactionDate, '%d/%c%/%y') AS TransactionDate,
					OT.TransactionDate DateNoFormat,
					MC.CustomerName,
					TOD.BatchNumber,
					-TOD.Quantity,
					TOD.Remarks
				FROM
					transaction_outgoing OT
					JOIN master_sales MS
						ON MS.SalesID = OT.SalesID
					JOIN master_customer MC
						ON MC.CustomerID = OT.CustomerID
					LEFT JOIN transaction_outgoingdetails TOD
						ON TOD.OutgoingID = OT.OutgoingID
					LEFT JOIN master_type MT
						ON MT.TypeID = TOD.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(OT.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(OT.TransactionDate AS DATE) <= '".$txtToDate."'
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				UNION ALL
				SELECT
					'Retur Jual',
					SR.SaleReturnNumber,
					DATE_FORMAT(SR.TransactionDate, '%d/%c%/%y') AS TransactionDate,
					SR.TransactionDate DateNoFormat,
					MC.CustomerName,
					SRD.BatchNumber,
					SRD.Quantity,
					SR.Remarks
				FROM
					transaction_salereturn SR
					JOIN master_customer MC
						ON MC.CustomerID = SR.CustomerID
					LEFT JOIN transaction_salereturndetails SRD
						ON SRD.SaleReturnID = SR.SaleReturnID
					LEFT JOIN master_type MT
						ON MT.TypeID = SRD.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(SR.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(SR.TransactionDate AS DATE) <= '".$txtToDate."'
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				UNION ALL
				SELECT
					'Retur Beli',
					BR.BuyReturnNumber,
					DATE_FORMAT(BR.TransactionDate, '%d/%c%/%y') AS TransactionDate,
					BR.TransactionDate DateNoFormat,
					MS.SupplierName,
					BRD.BatchNumber,
					-BRD.Quantity,
					BR.Remarks
				FROM
					transaction_buyreturn BR
					JOIN master_supplier MS
						ON MS.SupplierID = BR.SupplierID
					LEFT JOIN transaction_buyreturndetails BRD
						ON BRD.BuyReturnID = BR.BuyReturnID
					LEFT JOIN master_type MT
						ON MT.TypeID = BRD.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(BR.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(BR.TransactionDate AS DATE) <= '".$txtToDate."'
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				UNION ALL
				SELECT
					'Booking',
					BO.BookingNumber,
					DATE_FORMAT(BO.TransactionDate, '%d/%c%/%y') AS TransactionDate,
					BO.TransactionDate DateNoFormat,
					MC.CustomerName,
					BOD.BatchNumber,
					-BOD.Quantity,
					BO.Remarks
				FROM
					transaction_booking BO
					JOIN master_customer MC
						ON MC.CustomerID = BO.CustomerID
					LEFT JOIN transaction_bookingdetails BOD
						ON BOD.BookingID = BO.BookingID
					LEFT JOIN master_type MT
						ON MT.TypeID = BOD.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(BO.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(BO.TransactionDate AS DATE) <= '".$txtToDate."'
					AND BO.BookingStatusID = 1
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				UNION ALL
				SELECT
					'Penyesuaian',
					'',
					DATE_FORMAT(SO.TransactionDate, '%d/%c%/%y') AS TransactionDate,
					SO.TransactionDate DateNoFormat,
					'',
					SOD.BatchNumber,
					CASE
						WHEN SOD.FromQty > SOD.ToQty
						THEN -(SOD.FromQty - SOD.ToQty)
						ELSE (SOD.ToQty - SOD.FromQty)
					END,
					SO.Remarks
				FROM
					transaction_stockopname SO
					LEFT JOIN transaction_stockopnamedetails SOD
						ON SOD.StockOpnameID = SO.StockOpnameID
					LEFT JOIN master_type MT
						ON MT.TypeID = SOD.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(SO.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(SO.TransactionDate AS DATE) <= '".$txtToDate."'
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				UNION ALL
				SELECT
					'Pembatalan',
					OT.OutgoingNumber,
					DATE_FORMAT(OT.ModifiedDate, '%d/%c%/%y') AS TransactionDate,
					OT.ModifiedDate DateNoFormat,
					MC.CustomerName,
					TOD.BatchNumber,
					TOD.Quantity,
					TOD.Remarks
				FROM
					transaction_outgoing OT
					JOIN master_sales MS
						ON MS.SalesID = OT.SalesID
					JOIN master_customer MC
						ON MC.CustomerID = OT.CustomerID
					LEFT JOIN transaction_outgoingdetails TOD
						ON TOD.OutgoingID = OT.OutgoingID
					LEFT JOIN master_type MT
						ON MT.TypeID = TOD.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(OT.ModifiedDate AS DATE) >= '".$txtFromDate."'
					AND CAST(OT.ModifiedDate AS DATE) <= '".$txtToDate."'
					AND OT.IsCancelled = 1
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				UNION ALL
				SELECT
					'Pembatalan',
					TI.IncomingNumber,
					DATE_FORMAT(TI.ModifiedDate, '%d/%c%/%y') AS TransactionDate,
					TI.ModifiedDate DateNoFormat,
					MS.SupplierName,
					TID.BatchNumber,
					-TID.Quantity,
					TI.Remarks
				FROM
					transaction_incoming TI
					JOIN master_supplier MS
						ON MS.SupplierID = TI.SupplierID
					LEFT JOIN transaction_incomingdetails TID
						ON TI.IncomingID = TID.IncomingID
					LEFT JOIN master_type MT
						ON MT.TypeID = TID.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(TI.ModifiedDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TI.ModifiedDate AS DATE) <= '".$txtToDate."'
					AND TI.IsCancelled = 1
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				UNION ALL
				SELECT
					'Pembatalan',
					SR.SaleReturnNumber,
					DATE_FORMAT(SR.ModifiedDate, '%d/%c%/%y') AS TransactionDate,
					SR.ModifiedDate DateNoFormat,
					MC.CustomerName,
					SRD.BatchNumber,
					-SRD.Quantity,
					SR.Remarks
				FROM
					transaction_salereturn SR
					JOIN master_customer MC
						ON MC.CustomerID = SR.CustomerID
					LEFT JOIN transaction_salereturndetails SRD
						ON SRD.SaleReturnID = SR.SaleReturnID
					LEFT JOIN master_type MT
						ON MT.TypeID = SRD.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(SR.ModifiedDate AS DATE) >= '".$txtFromDate."'
					AND CAST(SR.ModifiedDate AS DATE) <= '".$txtToDate."'
					AND SR.IsCancelled = 1
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				UNION ALL
				SELECT
					'Pembatalan',
					BR.BuyReturnNumber,
					DATE_FORMAT(BR.ModifiedDate, '%d/%c%/%y') AS TransactionDate,
					BR.ModifiedDate DateNoFormat,
					MS.SupplierName,
					BRD.BatchNumber,
					BRD.Quantity,
					BR.Remarks
				FROM
					transaction_buyreturn BR
					JOIN master_supplier MS
						ON MS.SupplierID = BR.SupplierID
					LEFT JOIN transaction_buyreturndetails BRD
						ON BRD.BuyReturnID = BR.BuyReturnID
					LEFT JOIN master_type MT
						ON MT.TypeID = BRD.TypeID
					LEFT JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					CAST(BR.ModifiedDate AS DATE) >= '".$txtFromDate."'
					AND CAST(BR.ModifiedDate AS DATE) <= '".$txtToDate."'
					AND BR.IsCancelled = 1
					AND CASE
							WHEN ".$BrandID." = 0
							THEN MB.BrandID
							ELSE ".$BrandID."
						END = MB.BrandID
					AND CASE
							WHEN ".$TypeID." = 0
							THEN MT.TypeID
							ELSE ".$TypeID."
						END = MT.TypeID
				ORDER BY	
					BatchNumber,DateNoFormat ASC";
		
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
			if($BatchNumber == $row['BatchNumber']) {
				$Stock += $row['Quantity'];
			}
			else {
				$Stock = $row['Quantity'];
			}
			
			$row_array['RowNumber'] = $RowNumber;
			$row_array['TransactionNumber']= $row['TransactionNumber'];
			$row_array['TransactionType']= $row['TransactionType'];
			$row_array['TransactionDate'] = $row['TransactionDate'];
			//$row_array['SalesName'] = $row['SalesName'];
			$row_array['Stock'] = $Stock;
			$row_array['CustomerName'] = $row['CustomerName'];
			$row_array['Quantity'] = $row['Quantity'];
			$row_array['BatchNumber'] = $row['BatchNumber'];
			$row_array['Remarks'] = $row['Remarks'];
			$BatchNumber = $row['BatchNumber'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		$GrandTotal = number_format($GrandTotal,2,".",",");
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows, \"GrandTotal\": \"$GrandTotal\" }";
	}
?>
