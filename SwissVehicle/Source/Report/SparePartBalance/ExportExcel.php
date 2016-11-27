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
									 ->setTitle("Laporan Saldo Spare Part")
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Saldo Spare Part")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "LAPORAN SALDO SPARE PART");
		
		//set margin
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.393701);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.787402);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$monthName = array("JAN", "FEB", "MAR", "APR", "MEI", "JUN", "JUL", "AGS", "SEP", "OKT", "NOV", "DES");
		if($ddlMonth == 1) {
			$Month = 12;
			$Year = $ddlYear - 1;
		}
		else {
			$Month = $ddlMonth - 1;
			$Year = $ddlYear;
		}
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(16);
	
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Keterangan");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Stok Fisik \n".$monthName[$Month-1]." ".$Year);
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Pembelian \n".$monthName[$ddlMonth-1]." ".$ddlYear);
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Pemakaian \n".$monthName[$ddlMonth-1]." ".$ddlYear);
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Saldo Akhir \n".$monthName[$ddlMonth-1]." ".$ddlYear);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rowExcel.':E'.$rowExcel)->getAlignment()->setWrapText(true);
		$rowExcel++;
		
		//Spare part
		$sql = "SELECT
					RC.ReportCategoryID,
					RC.ReportCategoryName
				FROM
					master_reportcategory RC
				WHERE
					RC.ReportCategoryType = 'Spare Part'
				ORDER BY	
					RC.ReportCategoryName ASC";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		
		while($row = mysql_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $row['ReportCategoryName']);
			$sql2 = "SELECT
						IFNULL((TPD.Total - TSD.Total), 0) PreviousBalance
					FROM
						master_reportcategory RC
						LEFT JOIN 
						(
							SELECT
								MI.ReportCategoryID,
								IFNULL(SUM(TPD.Quantity * TPD.Price), 0) Total
							FROM
								transaction_purchase TP
								JOIN transaction_purchasedetails TPD
									ON TP.PurchaseID = TPD.PurchaseID
								JOIN master_item MI
									ON MI.ItemID = TPD.ItemID
							WHERE
								TP.TransactionDate < '".$ddlYear."-".$ddlMonth."-01'
								AND MI.ReportCategoryID = ".$row['ReportCategoryID']."
							GROUP BY
								MI.ReportCategoryID
						)TPD
							ON RC.ReportCategoryID = TPD.ReportCategoryID
						LEFT JOIN
						(
							SELECT
								MI.ReportCategoryID,
								IFNULL(SUM(TSD.Quantity * TSD.Price), 0) Total
							FROM
								transaction_service TS
								JOIN transaction_servicedetails TSD
									ON TS.ServiceID = TSD.ServiceID
								JOIN master_item MI
									ON MI.ItemID = TSD.ItemID
							WHERE
								TS.TransactionDate < '".$ddlYear."-".$ddlMonth."-01'
								AND MI.ReportCategoryID = ".$row['ReportCategoryID']."
							GROUP BY
								MI.ReportCategoryID 
						)TSD
							ON RC.ReportCategoryID = TSD.ReportCategoryID";
									
			
			if (! $result2 = mysql_query($sql2, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$row2 = mysql_fetch_array($result2);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row2['PreviousBalance']);
			
			$sql3 = "SELECT
						IFNULL(TPD.Total, 0) CurrentPurchase,
						IFNULL(TSD.Total, 0) CurrentUsage
					FROM
						master_reportcategory RC
						LEFT JOIN
						(
							SELECT
								MI.ReportCategoryID,
								IFNULL(SUM(TPD.Quantity * TPD.Price), 0) Total
							FROM
								transaction_purchase TP
								JOIN transaction_purchasedetails TPD
									ON TP.PurchaseID = TPD.PurchaseID
								JOIN master_item MI
									ON MI.ItemID = TPD.ItemID
							WHERE
								MONTH(TP.TransactionDate) = ".$ddlMonth."
								AND YEAR(TP.TransactionDate) = ".$ddlYear."
								AND MI.ReportCategoryID = ".$row['ReportCategoryID']."
							GROUP BY
								MI.ReportCategoryID
						)TPD
							ON RC.ReportCategoryID = TPD.ReportCategoryID
						LEFT JOIN
						(
							SELECT
								MI.ReportCategoryID,
								IFNULL(SUM(TSD.Quantity * TSD.Price), 0) Total
							FROM
								transaction_service TS
								JOIN transaction_servicedetails TSD
									ON TS.ServiceID = TSD.ServiceID
								JOIN master_item MI
									ON MI.ItemID = TSD.ItemID
							WHERE
								MONTH(TS.TransactionDate) = ".$ddlMonth."
								AND YEAR(TS.TransactionDate) = ".$ddlYear."
								AND MI.ReportCategoryID = ".$row['ReportCategoryID']."
							GROUP BY
								MI.ReportCategoryID 
						)TSD
							ON RC.ReportCategoryID = TSD.ReportCategoryID";

			if (! $result3 = mysql_query($sql3, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$row3 = mysql_fetch_array($result3);
			
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row3['CurrentPurchase']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row3['CurrentUsage']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, '=C'.$rowExcel.'-D'.$rowExcel);
			$rowExcel++;
		}
		
		$rowExcel++;
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Total Spare Part");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "=SUM(B5:B".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "=SUM(C5:C".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "=SUM(D5:D".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "=SUM(E5:E".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":E".$rowExcel)->getFont()->setBold(true);
		
		$rowTotal1 = $rowExcel;
		$rowExcel++;
		
		$rowStart = $rowExcel;
		
		//Peralatan
		$sql = "SELECT
					RC.ReportCategoryID,
					RC.ReportCategoryName
				FROM
					master_reportcategory RC
				WHERE
					RC.ReportCategoryType = 'Peralatan'
				ORDER BY	
					RC.ReportCategoryName ASC";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		
		while($row = mysql_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $row['ReportCategoryName']);
			$sql2 = "SELECT
						IFNULL((TPD.Total - TSD.Total), 0) PreviousBalance
					FROM
						master_reportcategory RC
						LEFT JOIN 
						(
							SELECT
								MI.ReportCategoryID,
								IFNULL(SUM(TPD.Quantity * TPD.Price), 0) Total
							FROM
								transaction_purchase TP
								JOIN transaction_purchasedetails TPD
									ON TP.PurchaseID = TPD.PurchaseID
								JOIN master_item MI
									ON MI.ItemID = TPD.ItemID
							WHERE
								TP.TransactionDate < '".$ddlYear."-".$ddlMonth."-01'
								AND MI.ReportCategoryID = ".$row['ReportCategoryID']."
							GROUP BY
								MI.ReportCategoryID
						)TPD
							ON RC.ReportCategoryID = TPD.ReportCategoryID
						LEFT JOIN
						(
							SELECT
								MI.ReportCategoryID,
								IFNULL(SUM(TSD.Quantity * TSD.Price), 0) Total
							FROM
								transaction_service TS
								JOIN transaction_servicedetails TSD
									ON TS.ServiceID = TSD.ServiceID
								JOIN master_item MI
									ON MI.ItemID = TSD.ItemID
							WHERE
								TS.TransactionDate < '".$ddlYear."-".$ddlMonth."-01'
								AND MI.ReportCategoryID = ".$row['ReportCategoryID']."
							GROUP BY
								MI.ReportCategoryID 
						)TSD
							ON RC.ReportCategoryID = TSD.ReportCategoryID";
									
			
			if (! $result2 = mysql_query($sql2, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$row2 = mysql_fetch_array($result2);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row2['PreviousBalance']);
			
			$sql3 = "SELECT
						IFNULL(TPD.Total, 0) CurrentPurchase,
						IFNULL(TSD.Total, 0) CurrentUsage
					FROM
						master_reportcategory RC
						LEFT JOIN
						(
							SELECT
								MI.ReportCategoryID,
								IFNULL(SUM(TPD.Quantity * TPD.Price), 0) Total
							FROM
								transaction_purchase TP
								JOIN transaction_purchasedetails TPD
									ON TP.PurchaseID = TPD.PurchaseID
								JOIN master_item MI
									ON MI.ItemID = TPD.ItemID
							WHERE
								MONTH(TP.TransactionDate) = ".$ddlMonth."
								AND YEAR(TP.TransactionDate) = ".$ddlYear."
								AND MI.ReportCategoryID = ".$row['ReportCategoryID']."
							GROUP BY
								MI.ReportCategoryID
						)TPD
							ON RC.ReportCategoryID = TPD.ReportCategoryID
						LEFT JOIN
						(
							SELECT
								MI.ReportCategoryID,
								IFNULL(SUM(TSD.Quantity * TSD.Price), 0) Total
							FROM
								transaction_service TS
								JOIN transaction_servicedetails TSD
									ON TS.ServiceID = TSD.ServiceID
								JOIN master_item MI
									ON MI.ItemID = TSD.ItemID
							WHERE
								MONTH(TS.TransactionDate) = ".$ddlMonth."
								AND YEAR(TS.TransactionDate) = ".$ddlYear."
								AND MI.ReportCategoryID = ".$row['ReportCategoryID']."
							GROUP BY
								MI.ReportCategoryID 
						)TSD
							ON RC.ReportCategoryID = TSD.ReportCategoryID";

			if (! $result3 = mysql_query($sql3, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$row3 = mysql_fetch_array($result3);
			
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row3['CurrentPurchase']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row3['CurrentUsage']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, '=C'.$rowExcel.'-D'.$rowExcel);
			$rowExcel++;
		}
		
		$rowExcel++;
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Total Peralatan");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "=SUM(B".$rowStart.":B".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "=SUM(C".$rowStart.":C".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "=SUM(D".$rowStart.":D".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "=SUM(E".$rowStart.":E".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":E".$rowExcel)->getFont()->setBold(true);
		
		$rowTotal2 = $rowExcel;
		$rowExcel++;
		
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Grand Total");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "=B".$rowTotal1."+B".$rowTotal2.")");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "=C".$rowTotal1."+C".$rowTotal2.")");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "=D".$rowTotal1."+D".$rowTotal2.")");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "=E".$rowTotal1."+E".$rowTotal2.")");
		$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":E".$rowExcel)->getFont()->setBold(true);
		
		$rowExcel++;
		
		$objPHPExcel->getActiveSheet()->getStyle("B5:E".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:E2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:E4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:E2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:E4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:E4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:E4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');

		//set all width 
		$fromCol='A';
		$toCol= 'F';
		for($j = $fromCol; $j !== $toCol; $j++) {
			//$calculatedWidth = $objPHPExcel->getActiveSheet()->getColumnDimension($i)->getWidth();
			$objPHPExcel->getActiveSheet()->getColumnDimension($j)->setAutoSize(true);
		}
		
		$styleArray = array(
			'borders' => array(
			  'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_DOUBLE
			  )
			)
		);
		
		$borderArray = array(
			'borders' => array(
			  'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THICK
			  )
			)
		);
		
		$bottomArray = array(
			'borders' => array(
			  'bottom' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THICK
			  )
			)
		);
		
		$topArray = array(
			'borders' => array(
			  'top' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THICK
			  )
			)
		);
		$objPHPExcel->getActiveSheet()->getStyle("A4:E".($rowExcel-1))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle("A".$rowTotal1.":E".$rowTotal1)->applyFromArray($borderArray);
		$objPHPExcel->getActiveSheet()->getStyle("A".($rowTotal1-1).":E".($rowTotal1-1))->applyFromArray($bottomArray);
		$objPHPExcel->getActiveSheet()->getStyle("A".($rowTotal1+1).":E".($rowTotal1+1))->applyFromArray($topArray);
		$objPHPExcel->getActiveSheet()->getStyle("A".($rowExcel-2).":E".($rowExcel-1))->applyFromArray($borderArray);
		$objPHPExcel->getActiveSheet()->getStyle("A".($rowExcel-3).":E".($rowExcel-3))->applyFromArray($bottomArray);
		

		$title = "Laporan Saldo Spare Part - ".$monthName[$ddlMonth - 1]." ".$ddlYear;
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