<?php
	if(ISSET($_GET['ItemID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$ItemID = mysql_real_escape_string($_GET['ItemID']);
		$sql = "SELECT
					I.ItemID,
					CONCAT(C.CategoryName, ' ', I.ItemName) AS ItemName
				FROM
					master_item I
					JOIN master_category C
						ON C.CategoryID = I.CategoryID
				WHERE
					I.ItemID = $ItemID";
						
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$row = mysql_fetch_array($result);
		$ItemName = $row['ItemName'];
		
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
									 ->setTitle("Laporan Mutasi Stok ".$ItemName)
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Mutasi Stok ".$ItemName)
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "LAPORAN MUTASI STOK ".strtoupper($ItemName));
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Nama");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Masuk");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Keluar");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Harga");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Stok");
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "Keterangan");
		$rowExcel++;
		
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
				ORDER BY	
					OrderDate ASC,
					UnionLevel ASC";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		$Stock = 0;
		while($row = mysql_fetch_array($result)) {
			if($row['Incoming'] == "-" && $row['Outgoing'] == "-") $Stock += $row['Stock'];
			else $Stock += $row['Incoming'] - $row['Outgoing'];
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['TransactionDate']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['Name']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['Incoming']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['Outgoing']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['Price']);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $Stock);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, $row['Remarks']);
			$RowNumber++;
			$rowExcel++;
		}
		$objPHPExcel->getActiveSheet()->getStyle("F5:F".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			
		
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:H2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:H4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:H2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:H4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c4bd97');

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
		$objPHPExcel->getActiveSheet()->getStyle("A4:H".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Laporan Mutasi Stok $ItemName $FromDate - $ToDate";
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