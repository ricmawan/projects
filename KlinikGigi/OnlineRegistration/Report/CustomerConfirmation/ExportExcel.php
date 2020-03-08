<?php
	if(ISSET($_GET['IsReceived'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../DBConfig.php";
		include "../../GetSession.php";
		//echo $_SERVER['REQUEST_URI'];
		$IsReceived = mysql_real_escape_string($_GET['IsReceived']);
		
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
		$objPHPExcel->getProperties()->setCreator("User")
									 ->setLastModifiedBy("User")
									 ->setTitle("Konfirmasi Pelanggan")
									 ->setSubject("Konfirmasi Pelanggan")
									 ->setDescription("Konfirmasi Pelanggan")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "Konfirmasi Pelanggan");
		
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
		
		/*$objPHPExcel->getActiveSheet()->setCellValue("A4", "ID Pasien :");
		$objPHPExcel->getActiveSheet()->mergeCells("A4:B4");
		$objPHPExcel->getActiveSheet()->getStyle("C4")->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
		$objPHPExcel->getActiveSheet()->setCellValue("A5", "Nama Pasien :");
		$objPHPExcel->getActiveSheet()->mergeCells("A5:B5");*/
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Cabang");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Dokter");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Pasien");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Status");
		$rowExcel++;
		
		$sql = "SELECT
					MB.BranchName,
					MU.UserName DoctorName,
					TOS.PatientName,
					DATE_FORMAT(TOS.ScheduledDate, '%d-%m-%Y') ScheduledDate,
					CASE
						WHEN TOS.CustomerConfirmation = 'Y'
						THEN 'Hadir'
						WHEN TOS.CustomerConfirmation = 'N'
						THEN 'Batal'
						ELSE 'Tidak Konfirmasi'
					END CustomerConfirmation
				FROM
					transaction_onlineschedule TOS
					JOIN master_branch MB
						ON MB.BranchID = TOS.BranchID
					JOIN master_user MU
						ON MU.UserID = TOS.DoctorID
				WHERE
					CASE
						WHEN '".$Status."' = 'A'
						THEN TOS.CustomerConfirmation
						ELSE '".$Status."'
					END = IFNULL(TOS.CustomerConfirmation, '')
					AND CAST(TOS.ScheduledDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TOS.ScheduledDate AS DATE) <= '".$txtToDate."'
					AND TOS.CustomerSelfRegFlag = 1
				ORDER BY 
					TOS.ScheduledDate";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		$PatientNumber = "";
		$PatientName = "";
		while($row = mysql_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['BranchName']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['DoctorName']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['PatientName']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['ScheduledDate']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['CustomerConfirmation']);

			$RowNumber++;
			$rowExcel++;
		}
		
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:F2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:F2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:F4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');

		//set all width 
		$fromCol='A';
		$toCol= 'G';
		for($j = $fromCol; $j !== $toCol; $j++) {
			//$calculatedWidth = $objPHPExcel->getActiveSheet()->getColumnDimension($i)->getWidth();
			$objPHPExcel->getActiveSheet()->getColumnDimension($j)->setAutoSize(true);
		}
		//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		
		$styleArray = array(
			'borders' => array(
			  'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
			  )
			)
		);		
		$objPHPExcel->getActiveSheet()->getStyle("A4:F".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Status Model";
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