<?php
	if(ISSET($_GET['searchPhrase']) && ISSET($_GET['sort'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		
		$where = " 1=1 ";		
		$order_by = "MB.BrandName";
		$txtToDate = date("Y-m-d");
		$ToDate = date("d-m-Y");
		
		if (ISSET($_GET['sort']) && $_GET['sort'] != "{}" )
		{
			$order_by = "";
			$_GET['sort'] = json_decode($_GET['sort'], true);
			foreach($_GET['sort'] as $key => $value) {
				if($key != 'No') $order_by .= " $key $value";
				else $order_by = "MB.BrandName ASC, MT.TypeName, FS.BatchNumber";
			}
		}
		if (ISSET($_GET['searchPhrase']) )
		{
			$search = trim($_GET['searchPhrase']);
			$where .= " AND ( FS.BatchNumber LIKE '%".$search."%' OR MU.UnitName LIKE '%".$search."%' OR MT.TypeName LIKE '%".$search."%' OR MB.BrandName LIKE '%".$search."%' OR MT.BuyPrice LIKE '%".$search."%' OR MT.SalePrice LIKE '%".$search."%' OR CONCAT(MB.BrandName, ' ', MT.TypeName) LIKE '%".$search."%' )";
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
					
		//set margin
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.393701);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.787402);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$monthName = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(16);
		$objPHPExcel->getActiveSheet()->getStyle("H4")->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle("H4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue("H4", $monthName[date("m", strtotime($txtToDate)) - 1] . " - " . date("Y", strtotime($txtToDate)));
		$rowExcel = 6;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Merek");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Tipe");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Sstuan");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Batch");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Stok");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Harga Beli");
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "Harga Jual");
		$rowExcel++;
		
		$sql = "SELECT
				MT.TypeID,
				MT.TypeName,
				MB.BrandName,
				FS.BatchNumber,
				MT.BuyPrice,
				MT.SalePrice,
				(IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0) - IFNULL(BO.Quantity, 0) + IFNULL(SO.Quantity, 0)) Stock,
				MU.UnitName
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
				$where
			ORDER BY 
				$order_by";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		$BatchNumber = "";
		while($row = mysql_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['BrandName']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['TypeName']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['UnitName']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['BatchNumber']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['Stock']);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $row['BuyPrice']);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, $row['SalePrice']);
			$RowNumber++;
			$rowExcel++;
		}
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:H2");
		$objPHPExcel->getActiveSheet()->getStyle("A6:H6")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:H2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("B6:E".$rowExcel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle("A6:H6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');
		$objPHPExcel->getActiveSheet()->getStyle("G6:H".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		

		//set all width 
		$fromCol='A';
		$toCol= 'I';
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
		$objPHPExcel->getActiveSheet()->getStyle("A6:H".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Laporan Stok $ToDate";
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