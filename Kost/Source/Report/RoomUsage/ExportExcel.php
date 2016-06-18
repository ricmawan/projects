<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);	
	include "../../GetPermission.php";
	$rdInterval = mysql_real_escape_string($_GET['rdInterval']);
	$ddlMonth = mysql_real_escape_string($_GET['ddlMonth']);
	$ddlYear = mysql_real_escape_string($_GET['ddlYear']);
	$RoomID = mysql_real_escape_string($_GET['ddlRoom']);
	if($RoomID == "") $RoomID = 0;
	if($_GET['txtStartDate'] == "" && $rdInterval == "Daily") {
		$txtStartDate = "2000-01-01";
	}
	else if($rdInterval == "Daily") {
		$txtStartDate = explode('-', mysql_real_escape_string($_GET['txtStartDate']));
		$_GET['txtStartDate'] = "$txtStartDate[2]-$txtStartDate[1]-$txtStartDate[0]"; 
		$txtStartDate = $_GET['txtStartDate'];
	}
	else {
		$txtStartDate = $ddlYear."-".$ddlMonth."-01";
	}
	if($_GET['txtEndDate'] == "" && $rdInterval == "Daily") {
		$txtEndDate = date("Y-m-d");
	}
	else if($rdInterval == "Daily") {
		$txtEndDate = explode('-', mysql_real_escape_string($_GET['txtEndDate']));
		$_GET['txtEndDate'] = "$txtEndDate[2]-$txtEndDate[1]-$txtEndDate[0]"; 
		$txtEndDate = $_GET['txtEndDate'];
	}
	else {
		$txtEndDate = date('Y-m-t', strtotime($txtStartDate));
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
								 ->setTitle("Laporan Pemakaian Kamar")
								 ->setSubject("Laporan")
								 ->setDescription("Laporan Pemakaian Kamar")
								 ->setKeywords("Generate By PHPExcel")
								 ->setCategory("Laporan");
	//Header
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', "LAPORAN PEMAKAIAN KAMAR");
	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
	
	//set bold
	$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
	
	$rowExcel = 4;
	$col = 0;
	//set color
	//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
	$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
	$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Tanggal");
	$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Nomor Kamar");
	$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Durasi");
	$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Dari");
	$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Sampai");
	$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Total");
	$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "Pembayaran");
	$rowExcel++;
	
	$sql = "SELECT
				DATE_FORMAT(CI.TransactionDate, '%d-%m-%Y') TransactionDate,
				R.RoomNumber,
				CASE
					WHEN CI.RateType = 'Daily'
					THEN CONCAT(DATEDIFF(CI.EndDate, CI.StartDate), ' hari')
					ELSE CONCAT(TIMESTAMPDIFF(HOUR, CI.EndDate, CI.StartDate), ' jam')
				END Duration,
				DATE_FORMAT(CI.StartDate, '%d-%m-%Y %H:%i') StartDate,
				DATE_FORMAT(CI.EndDate, '%d-%m-%Y %H:%i') EndDate,
				CASE
					WHEN CI.RateType = 'Daily'
					THEN DATEDIFF(CI.EndDate, CI.StartDate) * CI.DailyRate
					ELSE TIMESTAMPDIFF(HOUR, CI.EndDate, CI.StartDate) * CI.HourlyRate
				END Total,
				CI.DownPaymentAmount + CI.PaymentAmount Payment
			FROM
				transaction_checkin CI
				JOIN master_room R
					ON R.RoomID = CI.RoomID
			WHERE
				CAST(CI.TransactionDate AS DATE) >= '".$txtStartDate."'
				AND CAST(CI.TransactionDate AS DATE) <= '".$txtEndDate."'
				AND CASE
						WHEN $RoomID = 0
						THEN R.RoomID
						ELSE $RoomID
					END = R.RoomID
			ORDER BY
				TransactionDate ASC";
				
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$RowNumber = 1;
	while($row = mysql_fetch_array($result)) {
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['TransactionDate']);
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['RoomNumber']);
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['Duration']);
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['StartDate']);
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['EndDate']);
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $row['Total']);
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, $row['Payment']);
		$RowNumber++;
		$rowExcel++;
	}
	//merge title
	$objPHPExcel->getActiveSheet()->mergeCells("A1:H2");
	$objPHPExcel->getActiveSheet()->getStyle("A4:H4")->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle("A1:H2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle("A4:H4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c4bd97');
	$objPHPExcel->getActiveSheet()->getStyle("G5:H".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		
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
	
	$title = "Laporan Pemakaian Kamar";
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
?>