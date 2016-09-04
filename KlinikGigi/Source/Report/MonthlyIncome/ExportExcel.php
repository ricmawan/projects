<?php
	if(ISSET($_GET['ddlMonth']) && ISSET($_GET['ddlYear'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$ddlMonth = mysql_real_escape_string($_GET['ddlMonth']);
		$ddlYear = mysql_real_escape_string($_GET['ddlYear']);

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
									 ->setTitle("Laporan Pendapatan Bulanan")
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Pendapatan Bulanan")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "LAPORAN PENDAPATAN BULANAN");
		
		//set margin
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.393701);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.787402);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$monthName = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(16);
	
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Bulan");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Total Pendapatan");
		$rowExcel++;
		
		$sql = "SELECT
					CASE MONTH(TM.TransactionDate)
						WHEN 1
						THEN 'Januari'
						WHEN 2
						THEN 'Februari'
						WHEN 3
						THEN 'Maret'
						WHEN 4
						THEN 'April'
						WHEN 5
						THEN 'Mei'
						WHEN 6
						THEN 'Juni'
						WHEN 7
						THEN 'Juli'
						WHEN 8
						THEN 'Agustus'
						WHEN 9
						THEN 'September'
						WHEN 10
						THEN 'Oktober'
						WHEN 11
						THEN 'November'
						WHEN 12
						THEN 'Desember'
					END	MonthName,
					SUM(TMD.Quantity * TMD.Price) AS TotalIncome
				FROM
					transaction_medication TM
					JOIN transaction_medicationdetails TMD
						ON TM.MedicationID = TMD.MedicationID
				WHERE
					MONTH(TM.TransactionDate) = ".$ddlMonth."
					AND YEAR(TM.TransactionDate) = ".$ddlYear."
					AND TM.IsCancelled = 0
				GROUP BY
					MONTH(TM.TransactionDate)
				ORDER BY	
					MONTH(TM.TransactionDate) ASC";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		while($row = mysql_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['MonthName']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['TotalIncome']);
			$RowNumber++;
			$rowExcel++;
		}
		
		$objPHPExcel->getActiveSheet()->getStyle("C5:C".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:C2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:C4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:C2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:C4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');

		//set all width 
		$fromCol='A';
		$toCol= 'D';
		for($j = $fromCol; $j !== $toCol; $j++) {
			//$calculatedWidth = $objPHPExcel->getActiveSheet()->getColumnDimension($i)->getWidth();
			$objPHPExcel->getActiveSheet()->getColumnDimension($j)->setAutoSize(true);
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		
		$styleArray = array(
			'borders' => array(
			  'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
			  )
			)
		);		
		$objPHPExcel->getActiveSheet()->getStyle("A4:C".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Laporan Pendapatan Bulanan - ".$monthName[$ddlMonth - 1]." ".$ddlYear;
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