<?php
	if(ISSET($_GET['BranchID']) && ISSET($_GET['CategoryID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$CategoryID = $_GET['CategoryID'];
		$BranchName = $_GET['BranchName'];
		$CategoryName = $_GET['CategoryName'];
		$BranchID = $_GET['BranchID'];
				
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		ini_set('max_execution_time', 300);
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
					->setCellValue('A1', "LAPORAN STOK KOSONG");
					
		//set margin
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.393701);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.787402);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);    
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$monthName = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");

		$period = date("d") . " " . $monthName[date("m") - 1] . " " .  date("Y");
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(16);
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:H2");
		//center title
		$objPHPExcel->getActiveSheet()->getStyle("A1:H2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B3', "Kategori:");
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('C3', str_replace(" --", "", str_replace("-- ", "", $CategoryName)));
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B4', "Cabang:");
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('C4', $BranchName);
		$objPHPExcel->getActiveSheet()->getStyle("B3:C4")->getFont()->setBold(true);

		//bold title
		$objPHPExcel->getActiveSheet()->getStyle("A6:H6")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A6:H6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');

		

		$rowExcel = 6;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Kode");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Nama");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Satuan");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Kategori");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Stok");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Fisik");
		$rowExcel++;
		
		$sql = "CALL spSelExportNoStockReport(".$CategoryID.", ".$BranchID.", '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/NoStock/ExportExcel.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		$RowNumber = 1;
		while($row = mysqli_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("B".$rowExcel, $row['ItemCode'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("C".$rowExcel, htmlspecialchars_decode($row['ItemName'], ENT_QUOTES), PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("D".$rowExcel, $row['UnitName'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, $row['CategoryName'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['Stock']);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $row['PhysicalStock']);
			$RowNumber++;
			$rowExcel++;
		}
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		
		$objPHPExcel->getActiveSheet()->getStyle("B7:E".$rowExcel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$objPHPExcel->getActiveSheet()->getStyle("F6:G".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		
		//set all width 
		$fromCol='A';
		$toCol= 'H';
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
		$objPHPExcel->getActiveSheet()->getStyle("A6:G".($rowExcel-1))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->setSelectedCells('A1');

		$title = "Laporan Stok Kosong " . $CategoryName . " " . $period;
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
		setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
?>