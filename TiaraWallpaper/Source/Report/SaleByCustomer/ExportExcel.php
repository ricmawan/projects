<?php
	if(ISSET($_GET['CustomerID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$CustomerID = mysql_real_escape_string($_GET['CustomerID']);
				
		if($_GET['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
			$FromDate = "01-01-2000";
		}
		else {
			$FromDate = $_GET['txtFromDate'];
			$txtFromDate = explode('-', mysql_real_escape_string($_GET['txtFromDate']));
			$_GET['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_GET['txtFromDate'];
		}
		if($_GET['txtToDate'] == "") {
			$txtToDate = date("Y-m-d");
			$ToDate = date("d-m-Y");
		}
		else {
			$ToDate = $_GET['txtToDate'];
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
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$monthName = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A4:C5")->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(16);
		$objPHPExcel->getActiveSheet()->getStyle("G4")->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle("G4")->getFont()->setBold(true);
	
		$objPHPExcel->getActiveSheet()->setCellValue("A4", "Nama Pelanggan :");
		$objPHPExcel->getActiveSheet()->setCellValue("G4", $monthName[date("m", strtotime($txtFromDate)) - 1] . " - " . date("Y", strtotime($txtFromDate)));
		$objPHPExcel->getActiveSheet()->mergeCells("A4:B4");
		$objPHPExcel->getActiveSheet()->setCellValue("A5", "Kota :");
		$objPHPExcel->getActiveSheet()->mergeCells("A5:B5");
		$rowExcel = 7;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "No Nota");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Ongkos Kirim");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Sub Total");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Total");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Keterangan");
		$rowExcel++;
		
		mysql_query("SET @row:=0;", $dbh);
		$sql = "SELECT
					OT.OutgoingNumber,
					DATE_FORMAT(OT.TransactionDate, '%d/%c/%y') AS TransactionDate,
					OT.TransactionDate DateNoFormat,
					MC.CustomerName,
					MC.City,
					OT.DeliveryCost,
					IFNULL(SUM(CASE
						WHEN TOD.IsPercentage = 1
						THEN TOD.Quantity * (TOD.SalePrice - ((TOD.SalePrice * TOD.Discount)/100))
						ELSE TOD.Quantity * (TOD.SalePrice - TOD.Discount)
					END), 0) AS SubTotal,
					IFNULL(SUM(CASE
						WHEN TOD.IsPercentage = 1
						THEN TOD.Quantity * (TOD.SalePrice - ((TOD.SalePrice * TOD.Discount)/100))
						ELSE TOD.Quantity * (TOD.SalePrice - TOD.Discount)
					END), 0) + OT.DeliveryCost AS Total,
					OT.Remarks
				FROM
					transaction_outgoing OT
					JOIN master_sales MS
						ON MS.SalesID = OT.SalesID
					JOIN master_customer MC
						ON MC.CustomerID = OT.CustomerID
					LEFT JOIN transaction_outgoingdetails TOD
						ON TOD.OutgoingID = OT.OutgoingID
				WHERE
					CAST(OT.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(OT.TransactionDate AS DATE) <= '".$txtToDate."'
					AND OT.IsCancelled = 0
					AND ".$CustomerID." = MC.CustomerID
				GROUP BY
					OT.OutgoingNumber,
					OT.TransactionDate,
					MC.CustomerName
				UNION ALL
				SELECT
					SR.SaleReturnNumber,
					DATE_FORMAT(SR.TransactionDate, '%d/%c/%y') AS TransactionDate,
					SR.TransactionDate DateNoFormat,
					MC.CustomerName,
					MC.City,
					0,
					-IFNULL(SUM(CASE
						WHEN SRD.IsPercentage = 1
						THEN SRD.Quantity * (SRD.SalePrice - ((SRD.SalePrice * SRD.Discount)/100))
						ELSE SRD.Quantity * (SRD.SalePrice - SRD.Discount)
					END), 0) AS SubTotal,
					-IFNULL(SUM(CASE
						WHEN SRD.IsPercentage = 1
						THEN SRD.Quantity * (SRD.SalePrice - ((SRD.SalePrice * SRD.Discount)/100))
						ELSE SRD.Quantity * (SRD.SalePrice - SRD.Discount)
					END), 0) AS Total,
					SR.Remarks
				FROM
					transaction_salereturn SR
					JOIN master_customer MC
						ON MC.CustomerID = SR.CustomerID
					LEFT JOIN transaction_salereturndetails SRD
						ON SRD.SaleReturnID = SR.SaleReturnID
				WHERE
					CAST(SR.TransactionDate AS DATE) >= '".$txtFromDate."'
					AND CAST(SR.TransactionDate AS DATE) <= '".$txtToDate."'
					AND SR.IsCancelled = 0
					AND ".$CustomerID." = MC.CustomerID
				GROUP BY
					SR.SaleReturnNumber,
					SR.TransactionDate,
					MC.CustomerName
				ORDER BY	
					DateNoFormat ASC";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		$Stock = 0;
		$CustomerName = "";
		$City = "";
		while($row = mysql_fetch_array($result)) {
			$CustomerName = $row['CustomerName'];
			$City = $row['City'];
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['OutgoingNumber']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['TransactionDate']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['DeliveryCost']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['SubTotal']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['Total']);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $row['Remarks']);
			$RowNumber++;
			$rowExcel++;
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue("C4", $CustomerName);
		$objPHPExcel->getActiveSheet()->setCellValue("C5", $City);
		$objPHPExcel->getActiveSheet()->getStyle("D7:F".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "=SUM(F7:F".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Grand Total");
		$objPHPExcel->getActiveSheet()->mergeCells("A".$rowExcel.":E".$rowExcel);
		$rowExcel++;
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:G2");
		$objPHPExcel->getActiveSheet()->getStyle("A7:G7")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A4:B5")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:G2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A7:G7")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');

		//set all width 
		$fromCol='A';
		$toCol= 'H';
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
		$objPHPExcel->getActiveSheet()->getStyle("A7:G".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Laporan Penjualan $FromDate - $ToDate";
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