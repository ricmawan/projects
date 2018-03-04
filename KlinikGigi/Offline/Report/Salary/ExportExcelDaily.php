<?php
	if(ISSET($_GET['txtFromDate']) && ISSET($_GET['txtToDate'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		if($_GET['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
			$FromDate = "01-01-2000";
		}
		else {
			$FromDate = mysql_real_escape_string($_GET['txtFromDate']);
			$txtFromDate = explode('-', mysql_real_escape_string($_GET['txtFromDate']));
			$_GET['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_GET['txtFromDate'];
		}
		if($_GET['txtToDate'] == "") {
			$txtToDate = date("Y-m-d");
			$ToDate = date("d-m-Y");
		}
		else {
			$ToDate = mysql_real_escape_string($_GET['txtToDate']);
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
									 ->setTitle("Laporan Pendapatan Dokter")
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Pendapatan Dokter")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "LAPORAN PENDAPATAN DOKTER");
		
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
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Dokter");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Total Pemasukan");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Komisi");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Biaya Alat");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Pendapatan");
		$rowExcel++;
		
		$sql = "SELECT
					DATE_FORMAT(TM.TransactionDate, '%d-%m-%Y') TransactionDate,
					MU.UserName,
					SUM(TMD.Quantity * TMD.Price) AS TotalIncome,
					0 ToolsFee,
					CONCAT(TDC.CommisionPercentage, '%') AS Commision,
					(SUM(TMD.Quantity * TMD.Price) /100) * TDC.CommisionPercentage AS Earning
				FROM
					transaction_medication TM
					JOIN transaction_medicationdetails TMD
						ON TM.MedicationID = TMD.MedicationID
					JOIN master_user MU
						ON MU.UserID = TMD.DoctorID
					LEFT JOIN transaction_doctorcommision TDC
						ON TDC.DoctorID = TMD.DoctorID
						AND TDC.BusinessMonth = MONTH(TM.TransactionDate)
						AND TDC.BusinessYear = YEAR(TM.TransactionDate)
				WHERE
					CAST(TM.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(TM.TransactionDate AS DATE) <= '".$txtToDate."'
					AND MU.UserTypeID = 2
					AND TM.IsCancelled = 0
				GROUP BY
					DATE_FORMAT(TM.TransactionDate, '%d-%m-%Y'),
					TDC.CommisionPercentage,
					MU.UserName
				ORDER BY	
					TM.TransactionDate ASC,
					MU.UserName ASC";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		while($row = mysql_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['TransactionDate']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['UserName']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['TotalIncome']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['Commision']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['ToolsFee']);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $row['Earning']);
			$RowNumber++;
			$rowExcel++;
		}
		
		$objPHPExcel->getActiveSheet()->getStyle("D4:D".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getStyle("F4:F".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getStyle("G4:G".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:G2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:G4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:G2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:G4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');

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
		$objPHPExcel->getActiveSheet()->getStyle("A4:G".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Laporan Pendapatan Dokter ".$FromDate." Sampai ".$ToDate;
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