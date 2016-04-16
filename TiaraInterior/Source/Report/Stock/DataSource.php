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
					'' CustomerName,
					SUM(STC.Quantity) Quantity,
					0 Price,
					0 DiscountAmount,
					'' Discount,
					0 Total,
					'' Remarks
				FROM
					(
						SELECT
							'Stok Awal' TransactionType,
							FS.FirstStockNumber TransactionNumber,
							DATE_FORMAT(FS.TransactionDate, '%d/%c%/%y') AS TransactionDate,
							'' CustomerName,
							FSD.Quantity,
							FSD.BuyPrice Price,
							CASE
								WHEN FSD.IsPercentage = 1
								THEN (FSD.BuyPrice * FSD.Discount)/100
								ELSE FSD.Discount
							END DiscountAmount,
							CASE
								WHEN FSD.IsPercentage = 1 AND FSD.Discount <> 0
								THEN CONCAT('(', FSD.Discount, '%)')
								ELSE ''
							END Discount,
							-CASE
								WHEN FSD.IsPercentage = 1
								THEN IFNULL(FSD.Quantity * (FSD.BuyPrice - ((FSD.BuyPrice * FSD.Discount)/100)), 0)
								ELSE IFNULL(FSD.Quantity * (FSD.BuyPrice - FSD.Discount), 0)
							END AS Total,
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
							FS.TransactionDate < '".$txtFromDate."'
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
							MS.SupplierName,
							TID.Quantity,
							TID.BuyPrice,
							CASE
								WHEN TID.IsPercentage = 1
								THEN (TID.BuyPrice * TID.Discount)/100
								ELSE TID.Discount
							END DiscountAmount,
							CASE
								WHEN TID.IsPercentage = 1 AND TID.Discount <> 0
								THEN CONCAT('(', TID.Discount, '%)')
								ELSE ''
							END Discount,
							-CASE
								WHEN TID.IsPercentage = 1
								THEN IFNULL(TID.Quantity * (TID.BuyPrice - ((TID.BuyPrice * TID.Discount)/100)), 0)
								ELSE IFNULL(TID.Quantity * (TID.BuyPrice - TID.Discount), 0)
							END AS Total,
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
							TI.TransactionDate < '".$txtFromDate."'
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
							MC.CustomerName,
							-TOD.Quantity,
							TOD.SalePrice,
							CASE
								WHEN TOD.IsPercentage = 1
								THEN (TOD.BuyPrice * TOD.Discount)/100
								ELSE TOD.Discount
							END DiscountAmount,
							CASE
								WHEN TOD.IsPercentage = 1 AND TOD.Discount <> 0
								THEN CONCAT('(', TOD.Discount, '%)')
								ELSE ''
							END Discount,
							CASE
								WHEN TOD.IsPercentage = 1
								THEN IFNULL(TOD.Quantity * (TOD.SalePrice - ((TOD.SalePrice * TOD.Discount)/100)), 0)
								ELSE IFNULL(TOD.Quantity * (TOD.SalePrice - TOD.Discount), 0)
							END AS Total,
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
							OT.TransactionDate < '".$txtFromDate."'
							AND OT.IsCancelled = 0
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
							MC.CustomerName,
							SRD.Quantity,
							SRD.SalePrice,
							CASE
								WHEN SRD.IsPercentage = 1
								THEN (SRD.SalePrice * SRD.Discount)/100
								ELSE SRD.Discount
							END DiscountAmount,
							CASE
								WHEN SRD.IsPercentage = 1 AND SRD.Discount <> 0
								THEN CONCAT('(', SRD.Discount, '%)')
								ELSE ''
							END Discount,
							-CASE
								WHEN SRD.IsPercentage = 1
								THEN IFNULL(SRD.Quantity * (SRD.SalePrice - ((SRD.SalePrice * SRD.Discount)/100)), 0)
								ELSE IFNULL(SRD.Quantity * (SRD.SalePrice - SRD.Discount), 0)
							 END AS Total,
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
							SR.TransactionDate < '".$txtFromDate."'
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
							MS.SupplierName,
							-BRD.Quantity,
							BRD.BuyPrice,
							CASE
								WHEN BRD.IsPercentage = 1
								THEN (BRD.BuyPrice * BRD.Discount)/100
								ELSE BRD.Discount
							END DiscountAmount,
							CASE
								WHEN BRD.IsPercentage = 1 AND BRD.Discount <> 0
								THEN CONCAT('(', BRD.Discount, '%)')
								ELSE ''
							END Discount,
							CASE
								WHEN BRD.IsPercentage = 1
								THEN IFNULL(BRD.Quantity * (BRD.BuyPrice - ((BRD.BuyPrice * BRD.Discount)/100)), 0)
								ELSE IFNULL(BRD.Quantity * (BRD.BuyPrice - BRD.Discount), 0)
							 END AS Total,
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
							BR.TransactionDate < '".$txtFromDate."'
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
							MC.CustomerName,
							-BOD.Quantity,
							BOD.SalePrice,
							CASE
								WHEN BOD.IsPercentage = 1
								THEN (BOD.SalePrice * BOD.Discount)/100
								ELSE BOD.Discount
							END DiscountAmount,
							CASE
								WHEN BOD.IsPercentage = 1 AND BOD.Discount <> 0
								THEN CONCAT('(', BOD.Discount, '%)')
								ELSE ''
							END Discount,
							CASE
								WHEN BOD.IsPercentage = 1
								THEN IFNULL(BOD.Quantity * (BOD.SalePrice - ((BOD.SalePrice * BOD.Discount)/100)), 0)
								ELSE IFNULL(BOD.Quantity * (BOD.SalePrice - BOD.Discount), 0)
							 END AS Total,
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
							BO.TransactionDate < '".$txtFromDate."'
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
							'',
							CASE
								WHEN SOD.FromQty > SOD.ToQty
								THEN -(SOD.FromQty - SOD.ToQty)
								ELSE (SOD.ToQty - SOD.FromQty)
							END,
							CASE
								WHEN SOD.FromQty > SOD.ToQty
								THEN SOD.SalePrice
								ELSE SOD.BuyPrice
							END,
							'',
							'',
							IFNULL(CASE
									WHEN SOD.FromQty > SOD.ToQty
									THEN (SOD.FromQty - SOD.ToQty) * SOD.SalePrice
									ELSE -(SOD.ToQty - SOD.FromQty) * SOD.BuyPrice
								   END, 0) Total,
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
							SO.TransactionDate < '".$txtFromDate."'
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
							DATE_FORMAT(OT.TransactionDate, '%d/%c%/%y') AS TransactionDate,
							MC.CustomerName,
							TOD.Quantity,
							TOD.SalePrice,
							CASE
								WHEN TOD.IsPercentage = 1
								THEN (TOD.BuyPrice * TOD.Discount)/100
								ELSE TOD.Discount
							END DiscountAmount,
							CASE
								WHEN TOD.IsPercentage = 1 AND TOD.Discount <> 0
								THEN CONCAT('(', TOD.Discount, '%)')
								ELSE ''
							END Discount,
							CASE
								WHEN TOD.IsPercentage = 1
								THEN IFNULL(TOD.Quantity * (TOD.SalePrice - ((TOD.SalePrice * TOD.Discount)/100)), 0)
								ELSE IFNULL(TOD.Quantity * (TOD.SalePrice - TOD.Discount), 0)
							END AS Total,
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
							OT.TransactionDate < '".$txtFromDate."'
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
					)STC
				UNION ALL
				SELECT
					'Stok Awal' TransactionType,
					FS.FirstStockNumber TransactionNumber,
					DATE_FORMAT(FS.TransactionDate, '%d/%c%/%y') AS TransactionDate,
					'' CustomerName,
					FSD.Quantity,
					FSD.BuyPrice Price,
					CASE
						WHEN FSD.IsPercentage = 1
						THEN (FSD.BuyPrice * FSD.Discount)/100
						ELSE FSD.Discount
					END DiscountAmount,
					CASE
						WHEN FSD.IsPercentage = 1 AND FSD.Discount <> 0
						THEN CONCAT('(', FSD.Discount, '%)')
						ELSE ''
					END Discount,
					-CASE
						WHEN FSD.IsPercentage = 1
						THEN IFNULL(FSD.Quantity * (FSD.BuyPrice - ((FSD.BuyPrice * FSD.Discount)/100)), 0)
						ELSE IFNULL(FSD.Quantity * (FSD.BuyPrice - FSD.Discount), 0)
					END AS Total,
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
					FS.TransactionDate >= '".$txtFromDate."'
					AND FS.TransactionDate <= '".$txtToDate."'
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
					MS.SupplierName,
					TID.Quantity,
					TID.BuyPrice,
					CASE
						WHEN TID.IsPercentage = 1
						THEN (TID.BuyPrice * TID.Discount)/100
						ELSE TID.Discount
					END DiscountAmount,
					CASE
						WHEN TID.IsPercentage = 1 AND TID.Discount <> 0
						THEN CONCAT('(', TID.Discount, '%)')
						ELSE ''
					END Discount,
					-CASE
						WHEN TID.IsPercentage = 1
						THEN IFNULL(TID.Quantity * (TID.BuyPrice - ((TID.BuyPrice * TID.Discount)/100)), 0)
						ELSE IFNULL(TID.Quantity * (TID.BuyPrice - TID.Discount), 0)
					END AS Total,
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
					TI.TransactionDate >= '".$txtFromDate."'
					AND TI.TransactionDate <= '".$txtToDate."'
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
					MC.CustomerName,
					-TOD.Quantity,
					TOD.SalePrice,
					CASE
						WHEN TOD.IsPercentage = 1
						THEN (TOD.BuyPrice * TOD.Discount)/100
						ELSE TOD.Discount
					END DiscountAmount,
					CASE
						WHEN TOD.IsPercentage = 1 AND TOD.Discount <> 0
						THEN CONCAT('(', TOD.Discount, '%)')
						ELSE ''
					END Discount,
					CASE
						WHEN TOD.IsPercentage = 1
						THEN IFNULL(TOD.Quantity * (TOD.SalePrice - ((TOD.SalePrice * TOD.Discount)/100)), 0)
						ELSE IFNULL(TOD.Quantity * (TOD.SalePrice - TOD.Discount), 0)
					END AS Total,
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
					OT.TransactionDate >= '".$txtFromDate."'
					AND OT.TransactionDate <= '".$txtToDate."'
					AND OT.IsCancelled = 0
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
					MC.CustomerName,
					SRD.Quantity,
					SRD.SalePrice,
					CASE
						WHEN SRD.IsPercentage = 1
						THEN (SRD.SalePrice * SRD.Discount)/100
						ELSE SRD.Discount
					END DiscountAmount,
					CASE
						WHEN SRD.IsPercentage = 1 AND SRD.Discount <> 0
						THEN CONCAT('(', SRD.Discount, '%)')
						ELSE ''
					END Discount,
					-CASE
						WHEN SRD.IsPercentage = 1
						THEN IFNULL(SRD.Quantity * (SRD.SalePrice - ((SRD.SalePrice * SRD.Discount)/100)), 0)
						ELSE IFNULL(SRD.Quantity * (SRD.SalePrice - SRD.Discount), 0)
					 END AS Total,
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
					SR.TransactionDate >= '".$txtFromDate."'
					AND SR.TransactionDate <= '".$txtToDate."'
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
					MS.SupplierName,
					-BRD.Quantity,
					BRD.BuyPrice,
					CASE
						WHEN BRD.IsPercentage = 1
						THEN (BRD.BuyPrice * BRD.Discount)/100
						ELSE BRD.Discount
					END DiscountAmount,
					CASE
						WHEN BRD.IsPercentage = 1 AND BRD.Discount <> 0
						THEN CONCAT('(', BRD.Discount, '%)')
						ELSE ''
					END Discount,
					CASE
						WHEN BRD.IsPercentage = 1
						THEN IFNULL(BRD.Quantity * (BRD.BuyPrice - ((BRD.BuyPrice * BRD.Discount)/100)), 0)
						ELSE IFNULL(BRD.Quantity * (BRD.BuyPrice - BRD.Discount), 0)
					 END AS Total,
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
					BR.TransactionDate >= '".$txtFromDate."'
					AND BR.TransactionDate <= '".$txtToDate."'
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
					MC.CustomerName,
					-BOD.Quantity,
					BOD.SalePrice,
					CASE
						WHEN BOD.IsPercentage = 1
						THEN (BOD.SalePrice * BOD.Discount)/100
						ELSE BOD.Discount
					END DiscountAmount,
					CASE
						WHEN BOD.IsPercentage = 1 AND BOD.Discount <> 0
						THEN CONCAT('(', BOD.Discount, '%)')
						ELSE ''
					END Discount,
					CASE
						WHEN BOD.IsPercentage = 1
						THEN IFNULL(BOD.Quantity * (BOD.SalePrice - ((BOD.SalePrice * BOD.Discount)/100)), 0)
						ELSE IFNULL(BOD.Quantity * (BOD.SalePrice - BOD.Discount), 0)
					 END AS Total,
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
					BO.TransactionDate >= '".$txtFromDate."'
					AND BO.TransactionDate <= '".$txtToDate."'
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
					'',
					CASE
						WHEN SOD.FromQty > SOD.ToQty
						THEN -(SOD.FromQty - SOD.ToQty)
						ELSE (SOD.ToQty - SOD.FromQty)
					END,
					CASE
						WHEN SOD.FromQty > SOD.ToQty
						THEN SOD.SalePrice
						ELSE SOD.BuyPrice
					END,
					'',
					'',
					IFNULL(CASE
							WHEN SOD.FromQty > SOD.ToQty
							THEN (SOD.FromQty - SOD.ToQty) * SOD.SalePrice
							ELSE -(SOD.ToQty - SOD.FromQty) * SOD.BuyPrice
						   END, 0) Total,
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
					SO.TransactionDate >= '".$txtFromDate."'
					AND SO.TransactionDate <= '".$txtToDate."'
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
					DATE_FORMAT(OT.TransactionDate, '%d/%c%/%y') AS TransactionDate,
					MC.CustomerName,
					TOD.Quantity,
					TOD.SalePrice,
					CASE
						WHEN TOD.IsPercentage = 1
						THEN (TOD.BuyPrice * TOD.Discount)/100
						ELSE TOD.Discount
					END DiscountAmount,
					CASE
						WHEN TOD.IsPercentage = 1 AND TOD.Discount <> 0
						THEN CONCAT('(', TOD.Discount, '%)')
						ELSE ''
					END Discount,
					CASE
						WHEN TOD.IsPercentage = 1
						THEN IFNULL(TOD.Quantity * (TOD.SalePrice - ((TOD.SalePrice * TOD.Discount)/100)), 0)
						ELSE IFNULL(TOD.Quantity * (TOD.SalePrice - TOD.Discount), 0)
					END AS Total,
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
					OT.TransactionDate >= '".$txtFromDate."'
					AND OT.TransactionDate <= '".$txtToDate."'
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
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			$row_array['RowNumber'] = $RowNumber;
			$row_array['TransactionNumber']= $row['TransactionNumber'];
			$row_array['TransactionType']= $row['TransactionType'];
			$row_array['TransactionDate'] = $row['TransactionDate'];
			//$row_array['SalesName'] = $row['SalesName'];
			$row_array['CustomerName'] = $row['CustomerName'];
			$row_array['Quantity'] = $row['Quantity'];
			$row_array['Price'] = number_format($row['Price'],2,".",",");
			$row_array['Discount'] = number_format($row['DiscountAmount'],2,".",",").$row['Discount'];
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
