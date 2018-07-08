<?php
	if(ISSET($_GET['BranchID']) ) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$BranchID = $_GET['BranchID'];
		$BranchName = $_GET['BranchName'];
		if($_GET['FromDate'] == "") {
			$txtFromDate = "2000-01-01";
		}
		else {
			$txtFromDate = explode('-', mysql_real_escape_string($_GET['FromDate']));
			$_GET['FromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_GET['FromDate'];
		}
		if($_GET['ToDate'] == "") {
			$txtToDate = date("Y-m-d");
		}
		else {
			$txtToDate = explode('-', mysql_real_escape_string($_GET['ToDate']));
			$_GET['ToDate'] = "$txtToDate[2]-$txtToDate[1]-$txtToDate[0]"; 
			$txtToDate = $_GET['ToDate'];
		}
				
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
									 ->setTitle("Laporan Penjualan")
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Penjualan")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "LAPORAN PENJUALAN");
					
		//set margin
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.393701);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.787402);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);    
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$monthName = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");

		$period = date("d", strtotime($txtFromDate)) . " " . $monthName[date("m", strtotime($txtFromDate)) - 1] . " " .  date("Y", strtotime($txtFromDate)) . " - " . date("d", strtotime($txtToDate)) . " " . $monthName[date("m", strtotime($txtToDate)) - 1] . " " .  date("Y", strtotime($txtToDate));
		
		//bold title
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(16);
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:K2");
		//center title
		$objPHPExcel->getActiveSheet()->getStyle("A1:K2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B3', "Cabang:");
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('C3', $BranchName);
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B4', "Tanggal:");
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('C4', $period );
		$objPHPExcel->getActiveSheet()->getStyle("B3:C4")->getFont()->setBold(true);

		//bold title
		$objPHPExcel->getActiveSheet()->getStyle("A6:K6")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A6:K6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');

		$rowExcel = 6;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "No. Invoice");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Nama Pelanggan");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Kode Barang");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Nama Barang");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Quantity");
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "Satuan");
		$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, "Harga Jual");
		$objPHPExcel->getActiveSheet()->setCellValue("J".$rowExcel, "Diskon");
		$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, "Sub Total");
		$rowExcel++;
		
		$sql = "CALL spSelExportSaleReport(".$BranchID.", '".$txtFromDate."', '".$txtToDate."', '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/Sale/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		$RowNumber = 1;
		$SaleNumber = "";
		$MergeStart = 0;
		$Total = 0;
		$DetailsCounter = 0;
		$GrandTotal = 0;
		while($row = mysqli_fetch_array($result)) {
			if($SaleNumber != $row['SaleNumber']) {
				$DetailsCounter = 0;
				if ($MergeStart != $rowExcel && $RowNumber != 1) {
					//merge header
					$objPHPExcel->getActiveSheet()->mergeCells("A".($MergeStart - 1).":A".$rowExcel);
					$objPHPExcel->getActiveSheet()->mergeCells("B".($MergeStart - 1).":B".$rowExcel);
					$objPHPExcel->getActiveSheet()->mergeCells("C".($MergeStart - 1).":C".$rowExcel);
					$objPHPExcel->getActiveSheet()->mergeCells("D".($MergeStart - 1).":D".$rowExcel);
					$objPHPExcel->getActiveSheet()->getStyle("A".($MergeStart - 1).":D".$rowExcel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

					$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Total");
					$objPHPExcel->getActiveSheet()->getStyle("E".$rowExcel.":K".$rowExcel)->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->mergeCells(	"E".$rowExcel.":J".$rowExcel);
					$objPHPExcel->getActiveSheet()->getStyle("E".$rowExcel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

					$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, $Total);
					$objPHPExcel->getActiveSheet()->getStyle("E".$rowExcel.":K".$rowExcel)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('fffa00');
					$rowExcel++;
				}
				$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit("B".$rowExcel, $row['SaleNumber'], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['TransactionDate']);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit("D".$rowExcel, $row['CustomerName'], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":D".$rowExcel)->getFont()->setBold(true);
				$RowNumber++;
				$Total = 0;
				$MergeStart = $rowExcel + 1;
			}
			$DetailsCounter++;
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, $row['ItemCode'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("F".$rowExcel, $row['ItemName'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $row['Quantity']);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, $row['UnitName']);
			$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, $row['SalePrice']);
			$objPHPExcel->getActiveSheet()->setCellValue("J".$rowExcel, $row['Discount']);
			$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, $row['SubTotal']);
			$Total += $row['SubTotal'];
			$GrandTotal += $row['SubTotal'];
			$rowExcel++;
			$SaleNumber = $row['SaleNumber'];
		}
		if($DetailsCounter > 1) {
			//merge the header
			$objPHPExcel->getActiveSheet()->mergeCells("A".($MergeStart - 1).":A".$rowExcel);
			$objPHPExcel->getActiveSheet()->mergeCells("B".($MergeStart - 1).":B".$rowExcel);
			$objPHPExcel->getActiveSheet()->mergeCells("C".($MergeStart - 1).":C".$rowExcel);
			$objPHPExcel->getActiveSheet()->mergeCells("D".($MergeStart - 1).":D".$rowExcel);
			$objPHPExcel->getActiveSheet()->getStyle("A".($MergeStart - 1).":D".$rowExcel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
					
			//merge sub total
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Total");
			$objPHPExcel->getActiveSheet()->getStyle("E".$rowExcel.":J".$rowExcel)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells(	"E".$rowExcel.":J".$rowExcel);
			$objPHPExcel->getActiveSheet()->getStyle("E".$rowExcel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, $Total);
			$objPHPExcel->getActiveSheet()->getStyle("E".$rowExcel.":K".$rowExcel)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('fffa00');
			$rowExcel++;
		}

		//merge grand total
		$objPHPExcel->getActiveSheet()->mergeCells("A".$rowExcel.":J".$rowExcel);
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Grand Total");
		$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":K".$rowExcel)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, $GrandTotal);
		$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":K".$rowExcel)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ff0000');

		$styleArray = array(
	    'font'  => array(
	        'color' => array('rgb' => 'FFFFFF'),
	        'size'  => 14,
	    ));

	    $objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":K".$rowExcel)->applyFromArray($styleArray);

		$rowExcel++;

		mysqli_free_result($result);
		mysqli_next_result($dbh);

		//$objPHPExcel->getActiveSheet()->getStyle("B6:F".$rowExcel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle("G6:K".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		//set all width 
		$fromCol='A';
		$toCol= 'L';
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
		$objPHPExcel->getActiveSheet()->getStyle("A6:K".($rowExcel-1))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->setSelectedCells('A1');

		$title = "Laporan Penjualan " . $BranchName . " " . $period;
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