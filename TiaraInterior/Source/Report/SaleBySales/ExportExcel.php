<?php
	if(ISSET($_GET['ddlMonth']) && ISSET($_GET['ddlYear'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$ddlMonth = mysql_real_escape_string($_GET['ddlMonth']);
		$ddlYear = mysql_real_escape_string($_GET['ddlYear']);
		if(strlen($ddlMonth) == 1) $month = "0$ddlMonth";
		else $month = $ddlMonth;
		$SelectedDate = "$ddlYear-$month-01";
		$MonthList = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
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
									 ->setTitle("Laporan Arus Kas ".strtoupper($MonthList[$ddlMonth-1])." $ddlYear")
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Arus Kas ".strtoupper($MonthList[$ddlMonth-1])." $ddlYear")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "LAPORAN ARUS KAS ".strtoupper($MonthList[$ddlMonth-1])." $ddlYear");
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Bahan");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "QTY");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Harga");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Debit");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Kredit");
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "Saldo");
		$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, "Keterangan");
		$rowExcel++;
		
		mysql_query("SET @Balance:=0;", $dbh);
					
		$sql = "SELECT
					DATE_FORMAT('".$SelectedDate."', '%d%b%y') AS TransactionDate,
					'' AS ItemName,
					'' AS Quantity,
					'-' AS Price,
					'-' AS Debit,
					'-' AS Credit,
					SUM(DATA.Debit) - SUM(DATA.Credit) AS Balance,
					'Saldo Awal' AS Remarks,
					0 AS UnionLevel
				FROM
					(
						SELECT
							A.TransactionDate AS TransactionDate,
							'' AS ItemName,
							'' AS Quantity,
							'-' AS Price,
							A.Amount AS Debit,
							'-' AS Credit,
							A.Remarks AS Remarks,
							0 AS UnionLevel
						FROM
							transaction_asset A
						WHERE
							A.TransactionDate < '$SelectedDate'
						UNION ALL
						SELECT
							PP.ProjectTransactionDate AS TransactionDate,
							'' AS ItemName,
							'' AS Quantity,
							'-' AS Price,
							PP.Amount AS Debit,
							'-' AS Credit,
							CONCAT('Pembayaran ', PP.Remarks, ' Proyek ', P.ProjectName) AS Remarks,
							1 AS UnionLevel
						FROM
							transaction_projectpayment PP
							JOIN master_project P
								ON P.ProjectID = PP.ProjectID
						WHERE
							PP.ProjectTransactionDate < '$SelectedDate'
						UNION ALL
						SELECT
							IT.TransactionDate,
							CONCAT(MC.CategoryName, ' ', I.ItemName) AS ItemName, 
							ITD.Quantity,
							ITD.Price,
							'-' AS Debit,
							(ITD.Quantity * ITD.Price) AS Credit,
							'',
							3 AS UnionLevel
						FROM
							transaction_incomingtransaction IT
							JOIN transaction_incomingtransactiondetails ITD
								ON IT.IncomingTransactionID = ITD.IncomingTransactionID
							JOIN master_item I
								ON I.ItemID = ITD.ItemID
							JOIN master_category MC
								ON MC.CategoryID = I.CategoryID
							JOIN master_unit U
								ON U.UnitID = I.UnitID
						WHERE
							IT.TransactionDate < '$SelectedDate'
						UNION ALL
						SELECT
							PT.ProjectTransactionDate,
							'',
							'',
							'-',
							'-' AS Debit,
							PT.Amount AS Credit,
							CONCAT('Operasional ', PT.Remarks, ' Proyek ', P.ProjectName),
							4 AS UnionLevel
						FROM
							transaction_projecttransaction PT
							JOIN master_project P
								ON PT.ProjectID = P.ProjectID
						WHERE
							PT.ProjectTransactionDate < '$SelectedDate'
						UNION ALL
						SELECT
							OP.CommonOperationalDate,
							'',
							'',
							'-',
							'-' AS Debit,
							OPD.Amount AS Credit,
							CONCAT('Operasional ', OPD.Remarks),
							4 AS UnionLevel
						FROM
							transaction_commonoperational OP
							JOIN transaction_commonoperationaldetails OPD
								ON OP.CommonOperationalID = OPD.CommonOperationalID
						WHERE
							OP.CommonOperationalDate < '$SelectedDate'
					)DATA
				UNION ALL
				SELECT
					DATE_FORMAT(DATA.TransactionDate, '%d%b%y') AS TransactionDate,
					DATA.ItemName,
					DATA.Quantity,
					DATA.Price,
					DATA.Debit,
					DATA.Credit,
					(DATA.Debit - DATA.Credit) AS Balance,
					DATA.Remarks,
					DATA.UnionLevel
				FROM
					(
						SELECT
							A.TransactionDate AS TransactionDate,
							'' AS ItemName,
							'' AS Quantity,
							'-' AS Price,
							A.Amount AS Debit,
							'-' AS Credit,
							A.Remarks AS Remarks,
							0 AS UnionLevel
						FROM
							transaction_asset A
						WHERE
							MONTH(A.TransactionDate) = ".$ddlMonth."
							AND YEAR(A.TransactionDate) = ".$ddlYear."
						UNION ALL
						SELECT
							PP.ProjectTransactionDate AS TransactionDate,
							'' AS ItemName,
							'' AS Quantity,
							'-' AS Price,
							PP.Amount AS Debit,
							'-' AS Credit,
							CONCAT('Pembayaran ', PP.Remarks, ' Proyek ', P.ProjectName) AS Remarks,
							1 AS UnionLevel
						FROM
							transaction_projectpayment PP
							JOIN master_project P
								ON P.ProjectID = PP.ProjectID
						WHERE
							MONTH(PP.ProjectTransactionDate) = ".$ddlMonth."
							AND YEAR(PP.ProjectTransactionDate) = ".$ddlYear."
						UNION ALL
						SELECT
							IT.TransactionDate,
							CONCAT(MC.CategoryName, ' ', I.ItemName) AS ItemName, 
							ITD.Quantity,
							ITD.Price,
							'-' AS Debit,
							(ITD.Quantity * ITD.Price) AS Credit,
							'',
							3 AS UnionLevel
						FROM
							transaction_incomingtransaction IT
							JOIN transaction_incomingtransactiondetails ITD
								ON IT.IncomingTransactionID = ITD.IncomingTransactionID
							JOIN master_item I
								ON I.ItemID = ITD.ItemID
							JOIN master_category MC
								ON MC.CategoryID = I.CategoryID
							JOIN master_unit U
								ON U.UnitID = I.UnitID
						WHERE
							MONTH(IT.TransactionDate) = ".$ddlMonth."
							AND YEAR(IT.TransactionDate) = ".$ddlYear."
						UNION ALL
						SELECT
							PT.ProjectTransactionDate,
							'',
							'',
							'-',
							'-' AS Debit,
							PT.Amount AS Credit,
							CONCAT('Operasional ', PT.Remarks, ' Proyek ', P.ProjectName),
							4 AS UnionLevel
						FROM
							transaction_projecttransaction PT
							JOIN master_project P
								ON PT.ProjectID = P.ProjectID
						WHERE
							MONTH(PT.ProjectTransactionDate) = ".$ddlMonth."
							AND YEAR(PT.ProjectTransactionDate) = ".$ddlYear."
						UNION ALL
						SELECT
							OP.CommonOperationalDate,
							'',
							'',
							'-',
							'-' AS Debit,
							OPD.Amount AS Credit,
							CONCAT('Operasional ', OPD.Remarks),
							4 AS UnionLevel
						FROM
							transaction_commonoperational OP
							JOIN transaction_commonoperationaldetails OPD
								ON OP.CommonOperationalID = OPD.CommonOperationalID
						WHERE
							MONTH(OP.CommonOperationalDate) = ".$ddlMonth."
							AND YEAR(OP.CommonOperationalDate) = ".$ddlYear."
					)DATA
				ORDER BY 
					TransactionDate,
					UnionLevel";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		$Balance = 0;
		while($row = mysql_fetch_array($result)) {
			$Balance += $row['Balance'];
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['TransactionDate']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['ItemName']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['Quantity']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['Price']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['Debit']);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $row['Credit']);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, $Balance);
			$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, $row['Remarks']);
			$RowNumber++;
			$rowExcel++;
		}
		$objPHPExcel->getActiveSheet()->getStyle("F5:H".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			
		
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:I2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:I4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:I2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:I4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c4bd97');

		//set all width 
		$fromCol='A';
		$toCol= 'J';
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
		$objPHPExcel->getActiveSheet()->getStyle("A4:I".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Laporan Arus Kas ".strtoupper($MonthList[$ddlMonth-1])." $ddlYear";
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