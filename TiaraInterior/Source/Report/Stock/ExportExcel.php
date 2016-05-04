<?php
	if(ISSET($_GET['BrandID']) && ISSET($_GET['TypeID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$BrandID = mysql_real_escape_string($_GET['BrandID']);
		$TypeID = mysql_real_escape_string($_GET['TypeID']);
				
		if($_GET['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
			$FromDate = "01-01-2000";
		}
		else {
			$FromDate = $_GET['txtFromDate'];
			$txtFromDate = explode('-', mysql_real_escape_string($_GET['txtFromDate']));
			$_GET['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_GET['txtFromDate'];
		}
		if($_GET['txtToDate'] == "") {
			$txtToDate = date("Y-m-d");
			$ToDate = date("d-m-Y");
		}
		else {
			$ToDate = $_GET['txtToDate'];
			$txtToDate = explode('-', mysql_real_escape_string($_GET['txtToDate']));
			$_GET['txtToDate'] = "$txtToDate[2]-$txtToDate[1]-$txtToDate[0]"; 
			$txtToDate = $_GET['txtToDate'];
		}

		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set("Asia/Jakarta");

		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');

		/** Include PHPExcel */
		require_once '../../assets/lib/PHPExcel.php';


		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator($_SESSION['UserLogin'])
									 ->setLastModifiedBy($_SESSION['UserLogin'])
									 ->setTitle("Laporan Stok")
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Stok")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "LAPORAN STOK");
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Batch");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "No Nota");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Tipe Transaksi");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Pelanggan/Supplier");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Qty");
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "Stok");
		$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, "Keterangan");
		$rowExcel++;
		
		$sql = "SELECT
					'Stok Awal' TransactionType,
					'' TransactionNumber,
					'' TransactionDate,
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
					DATE_FORMAT(OT.TransactionDate, '%d/%c%/%y') AS TransactionDate,
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
					CAST(OT.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(OT.TransactionDate AS DATE) <= '".$txtToDate."'
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
					BatchNumber,TransactionDate ASC";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		$Stock = 0;
		$BatchNumber = "";
		while($row = mysql_fetch_array($result)) {
			if($BatchNumber == $row['BatchNumber']) {
				$Stock += $row['Quantity'];
			}
			else {
				$Stock = $row['Quantity'];
			}
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['BatchNumber']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['TransactionNumber']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['TransactionType']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['TransactionDate']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['CustomerName']);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $row['Quantity']);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, $Stock);
			$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, $row['Remarks']);
			$RowNumber++;
			$rowExcel++;
		}
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:I2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:I4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:I2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:I4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c4bd97');

		//set all width 
		$fromCol='A';
		$toCol= 'J';
		for($j = $fromCol; $j !== $toCol; $j++) {
			//$calculatedWidth = $objPHPExcel->getActiveSheet()->getColumnDimension($i)->getWidth();
			$objPHPExcel->getActiveSheet()->getColumnDimension($j)->setAutoSize(true);
		}
		$styleArray = array(
			'borders' => array(
			  'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
			  )
			)
		);		
		$objPHPExcel->getActiveSheet()->getStyle("A4:I".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Laporan Stok $FromDate - $ToDate";
		// Rename worksheet
		//$objPHPExcel->getActiveSheet()->setTitle($title);
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');

		//output file name
		header('Content-Disposition: attachment;filename="'.$title.'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
?>