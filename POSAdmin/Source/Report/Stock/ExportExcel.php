<?php
	if(ISSET($_GET['BranchID']) && ISSET($_GET['CategoryID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$CategoryID = $_GET['CategoryID'];
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
		//$objPHPExcel->getActiveSheet()->getStyle("I4")->getFont()->setSize(14);
		//$objPHPExcel->getActiveSheet()->getStyle("I4")->getFont()->setBold(true);
		//$objPHPExcel->getActiveSheet()->setCellValue("I4", $monthName[date("m", strtotime($txtFromDate)) - 1] . " - " . date("Y", strtotime($txtFromDate)));
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Kode");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Nama");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Kategori");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Cabang");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Stok");
		$rowExcel++;
		
		$sql = "CALL spSelExportStockReport(".$CategoryID.", ".$BranchID.", '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/Stock/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		$RowNumber = 1;
		while($row = mysqli_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['ItemCode']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['ItemName']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['CategoryName']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['BranchName']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['Stock']);
			$RowNumber++;
			$rowExcel++;
		}
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:F2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:F2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("B4:E".$rowExcel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');

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

		$title = "Laporan Stok";
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