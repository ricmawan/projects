<?php
	if(ISSET($_GET['SupplierID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$SupplierID = mysql_real_escape_string($_GET['SupplierID']);
				
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
									 ->setTitle("Laporan Pembelian")
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Pembelian")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "LAPORAN PEMBELIAN");
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "No Nota");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Nama Supplier");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Total");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Keterangan");
		$rowExcel++;
		
		mysql_query("SET @row:=0;", $dbh);
		$sql = "SELECT
					DATE_FORMAT(TI.TransactionDate, '%d/%c/%y') AS TransactionDate,
					MS.SupplierName AS SupplierName,
					TI.IncomingNumber,
					TI.Remarks,
					IFNULL(SUM(TID.Quantity * (TID.BuyPrice - ((TID.BuyPrice * TID.Discount)/100))), 0) AS Total
				FROM
					transaction_incoming TI
					JOIN master_supplier MS
						ON MS.SupplierID = TI.SupplierID
					LEFT JOIN transaction_incomingdetails TID
						ON TID.IncomingID = TI.IncomingID
				WHERE
					TI.TransactionDate >= '".$txtFromDate."'
					AND TI.TransactionDate <= '".$txtToDate."'
					AND CASE
							WHEN ".$SupplierID." = 0
							THEN MS.SupplierID
							ELSE ".$SupplierID."
						END = MS.SupplierID
				GROUP BY
					TI.IncomingNumber,
					TI.TransactionDate,
					MS.SupplierName
				UNION ALL
				SELECT
					DATE_FORMAT(BR.TransactionDate, '%d/%c/%y') AS TransactionDate,
					MS.SupplierName AS SupplierName,
					BR.BuyReturnNumber,
					BR.Remarks,
					-IFNULL(SUM(BRD.Quantity * BRD.BuyPrice), 0) AS Total
				FROM
					transaction_buyreturn BR
					JOIN master_supplier MS
						ON MS.SupplierID = BR.SupplierID
					LEFT JOIN transaction_buyreturndetails BRD
						ON BRD.BuyReturnID = BR.BuyReturnID
				WHERE
					BR.TransactionDate >= '".$txtFromDate."'
					AND BR.TransactionDate <= '".$txtToDate."'
					AND CASE
							WHEN ".$SupplierID." = 0
							THEN MS.SupplierID
							ELSE ".$SupplierID."
						END = MS.SupplierID
				GROUP BY
					BR.BuyReturnNumber,
					BR.TransactionDate,
					MS.SupplierName
				ORDER BY	
					TransactionDate ASC";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		$Stock = 0;
		while($row = mysql_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['IncomingNumber']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['TransactionDate']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['SupplierName']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['Total']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['Remarks']);
			$RowNumber++;
			$rowExcel++;
		}
		$objPHPExcel->getActiveSheet()->getStyle("E5:E".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "=SUM(E5:E".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Grand Total");
		$objPHPExcel->getActiveSheet()->mergeCells("A".$rowExcel.":D".$rowExcel);
		$rowExcel++;
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:F2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:F2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c4bd97');

		//set all width 
		$fromCol='A';
		$toCol= 'G';
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
		$objPHPExcel->getActiveSheet()->getStyle("A4:F".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Laporan Pembelian $FromDate - $ToDate";
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