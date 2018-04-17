<?php
	if(ISSET($_GET['CategoryID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$CategoryID = $_GET['CategoryID'];
		$CategoryName = $_GET['CategoryName'];
				
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
									 ->setTitle("Data Barang")
									 ->setSubject("Laporan")
									 ->setDescription("Data Barang")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Data Barang");
		
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
		
		//bold title
		$objPHPExcel->getActiveSheet()->getStyle("A1:J1")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:J1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');

		

		$rowExcel = 1;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "ID");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Kode");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Nama");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Kategori");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Harga Beli");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Harga Ecer");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Harga 1");
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "Qty 1");
		$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, "Harga 2");
		$objPHPExcel->getActiveSheet()->setCellValue("J".$rowExcel, "Qty 2");
		$rowExcel++;
		
		$sql = "CALL spSelExportItem(".$CategoryID.", '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Master/PriceUpdate/ExportExcel.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		$RowNumber = 1;
		while($row = mysqli_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $row['ItemID']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("B".$rowExcel, $row['ItemCode'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("C".$rowExcel, $row['ItemName'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("D".$rowExcel, $row['CategoryName'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['BuyPrice']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['RetailPrice']);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $row['Price1']);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, $row['Qty1']);
			$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, $row['Price2']);
			$objPHPExcel->getActiveSheet()->setCellValue("J".$rowExcel, $row['Qty2']);
			$rowExcel++;
		}
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		
		$objPHPExcel->getActiveSheet()->getStyle("B2:D".$rowExcel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle("E2:J".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		
		//set all width 
		$fromCol='A';
		$toCol= 'K';
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
		$objPHPExcel->getActiveSheet()->getStyle("A1:J".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Data Barang " . $CategoryName . " " . $period;
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