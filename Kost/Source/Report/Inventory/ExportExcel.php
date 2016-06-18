<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);	
	include "../../GetPermission.php";
	$rdInterval = mysql_real_escape_string($_GET['rdInterval']);
	$ddlMonth = mysql_real_escape_string($_GET['ddlMonth']);
	$ddlYear = mysql_real_escape_string($_GET['ddlYear']);
	$InventoryID = mysql_real_escape_string($_GET['ddlInventory']);
	if($InventoryID == "") $InventoryID = 0;
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
								 ->setTitle("Laporan Inventaris")
								 ->setSubject("Laporan")
								 ->setDescription("Laporan Inventaris")
								 ->setKeywords("Generate By PHPExcel")
								 ->setCategory("Laporan");
	//Header
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', "LAPORAN INVENTARIS");
	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
	
	//set bold
	$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
	
	$rowExcel = 4;
	$col = 0;
	//set color
	//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
	$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
	$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Tanggal");
	$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Nama Barang");
	$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Qty");
	$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Stok");
	$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Catatan");
	$rowExcel++;
	
	$sql = "SELECT
				DATE_FORMAT(II.TransactionDate, '%d-%m-%Y') TransactionDate,
				I.InventoryName,
				IID.Quantity,
				IID.Remarks Notes
			FROM
				transaction_incominginventory II
				JOIN transaction_incominginventorydetails IID
					ON II.IncomingInventoryID = IID.IncomingInventoryID
				JOIN master_inventory I
					ON I.InventoryID = IID.InventoryID
			WHERE
				CAST(II.TransactionDate AS DATE) >= '".$txtStartDate."'
				AND CAST(II.TransactionDate AS DATE) <= '".$txtEndDate."'
				AND CASE
						WHEN $InventoryID = 0
						THEN I.InventoryID
						ELSE $InventoryID
					END = I.InventoryID
			UNION
			SELECT
				DATE_FORMAT(OI.TransactionDate, '%d-%m-%Y') TransactionDate,
				I.InventoryName,
				-OID.Quantity,
				OID.Remarks Notes
			FROM
				transaction_outgoinginventory OI
				JOIN transaction_outgoinginventorydetails OID
					ON OI.OutgoingInventoryID = OID.OutgoingInventoryID
				JOIN master_inventory I
					ON I.InventoryID = OID.InventoryID
			WHERE
				CAST(OI.TransactionDate AS DATE) >= '".$txtStartDate."'
				AND CAST(OI.TransactionDate AS DATE) <= '".$txtEndDate."'
				AND CASE
						WHEN $InventoryID = 0
						THEN I.InventoryID
						ELSE $InventoryID
					END = I.InventoryID
			ORDER BY
				InventoryName ASC,
				TransactionDate ASC";
				
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$RowNumber = 1;
	$Stock = 0;
	$InventoryName = "";
	while($row = mysql_fetch_array($result)) {
		if($InventoryName == $row['InventoryName']) {
			$Stock += $row['Quantity'];
		}
		else {
			$Stock = $row['Quantity'];
		}
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['TransactionDate']);
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['InventoryName']);
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['Quantity']);
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $Stock);
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['Notes']);
		$InventoryName = $row['InventoryName'];
		$RowNumber++;
		$rowExcel++;
	}
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
	
	$title = "Laporan Inventaris";
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