<?php
	if(ISSET($_GET['PatientID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$PatientID = mysql_real_escape_string($_GET['PatientID']);
		
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
									 ->setTitle("Rekam Medis")
									 ->setSubject("Rekam Medis")
									 ->setDescription("Rekam Medis")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "REKAM MEDIS");
		
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
		
		$objPHPExcel->getActiveSheet()->setCellValue("A4", "ID Pasien :");
		$objPHPExcel->getActiveSheet()->mergeCells("A4:B4");
		$objPHPExcel->getActiveSheet()->getStyle("C4")->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
		$objPHPExcel->getActiveSheet()->setCellValue("A5", "Nama Pasien :");
		$objPHPExcel->getActiveSheet()->mergeCells("A5:B5");
		$rowExcel = 7;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Pemeriksaan");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Keterangan");
		$rowExcel++;
		
		$sql = "SELECT
					MP.PatientNumber,
					MP.PatientName,
					DATE_FORMAT(TM.TransactionDate, '%d-%m-%Y') TransactionDate,
					ME.ExaminationName,
					TMD.Remarks
				FROM
					transaction_medication TM
					JOIN transaction_medicationdetails TMD
						ON TM.MedicationID = TMD.MedicationID
					JOIN master_patient MP
						ON MP.PatientID = TM.PatientID
					JOIN master_examination ME
						ON ME.ExaminationID = TMD.ExaminationID
				WHERE
					MP.PatientID = ".$PatientID."
				ORDER BY	
					TM.TransactionDate ASC";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		$PatientNumber = "";
		$PatientName = "";
		while($row = mysql_fetch_array($result)) {
			$PatientName = $row['PatientName'];
			$PatientNumber = $row['PatientNumber'];
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['TransactionDate']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['ExaminationName']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['Remarks']);
			$RowNumber++;
			$rowExcel++;
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue("C4", $PatientNumber);
		$objPHPExcel->getActiveSheet()->setCellValue("C5", $PatientName);
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:D2");
		$objPHPExcel->getActiveSheet()->getStyle("A7:D7")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A7:D7")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');

		//set all width 
		$fromCol='A';
		$toCol= 'E';
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
		$objPHPExcel->getActiveSheet()->getStyle("A7:D".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Rekam Medis ".$PatientNumber." - ".$PatientName;
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