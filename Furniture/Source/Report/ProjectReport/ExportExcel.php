<?php
	if(ISSET($_GET['ProjectID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$ID = mysql_real_escape_string($_GET['ProjectID']);
		$sql = "SELECT
					ProjectID,
					ProjectName
				FROM
					master_project P
				WHERE
					P.ProjectID = $ID";
						
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$row = mysql_fetch_array($result);
		global $ProjectName;
		$ProjectName = $row['ProjectName'];

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
									 ->setTitle("Laporan Proyek ".$ProjectName)
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Proyek ".$ProjectName)
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "LAPORAN PROYEK ".strtoupper($ProjectName));
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Nama");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Bahan");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "QTY");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Satuan");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Harga");
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "Debit");
		$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, "Kredit");
		$objPHPExcel->getActiveSheet()->setCellValue("J".$rowExcel, "Saldo");
		$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, "Keterangan");
		$rowExcel++;
		
		mysql_query("SET @Balance:=0;", $dbh);
					
		$sql = "SELECT
					DATE_FORMAT(DATA.TransactionDate, '%d-%m-%Y') AS TransactionDate,
					DATA.Name,
					DATA.ItemName,
					DATA.Quantity,
					DATA.UnitName,
					DATA.Price,
					DATA.Debit,
					DATA.Credit,
					@Balance:= @Balance + DATA.Debit - DATA.Credit AS Balance,
					DATA.Remarks
				FROM
					(
						SELECT
							PP.ProjectTransactionDate AS TransactionDate,
							'' AS Name,
							'' AS ItemName,
							'' AS Quantity,
							'' AS UnitName,
							'' AS Price,
							PP.Amount AS Debit,
							'-' AS Credit,
							CONCAT('Pembayaran ', PP.Remarks) AS Remarks,
							1 AS UnionLevel
						FROM
							transaction_projectpayment PP
						WHERE
							PP.ProjectID = $ID
						UNION ALL
						SELECT
							OT.TransactionDate,
							OTD.Name,
							CONCAT(MC.CategoryName, ' ', I.ItemName) AS ItemName, 
							OTD.Quantity,
							U.UnitName,
							OTD.Price,
							'-' AS Debit,
							(OTD.Quantity * OTD.Price) AS Credit,
							OTD.Remarks,
							2 AS UnionLevel
						FROM
							transaction_outgoingtransaction OT
							JOIN transaction_outgoingtransactiondetails OTD
								ON OT.OutgoingTransactionID = OTD.OutgoingTransactionID
							JOIN master_item I
								ON I.ItemID = OTD.ItemID
							JOIN master_category MC
								ON MC.CategoryID = I.CategoryID
							JOIN master_unit U
								ON U.UnitID = I.UnitID
						WHERE
							OT.ProjectID = $ID
						UNION ALL
						SELECT
							RT.TransactionDate,
							'',
							CONCAT(MC.CategoryName, ' ', I.ItemName) AS ItemName, 
							RT.Quantity,
							U.UnitName,
							RT.Price,
							(RT.Quantity * RT.Price) AS Debit,
							'-' AS Credit,
							'Retur' AS Remarks,
							3 AS UnionLevel
						FROM
							transaction_returntransaction RT
							JOIN master_item I
								ON I.ItemID = RT.ItemID
							JOIN master_category MC
								ON MC.CategoryID = I.CategoryID
							JOIN master_unit U
								ON U.UnitID = I.UnitID
						WHERE
							RT.ProjectID = $ID
						UNION ALL
						SELECT
							PT.ProjectTransactionDate,
							'',
							'',
							'',
							'',
							'',
							'-' AS Debit,
							PT.Amount AS Credit,
							CONCAT('Operasional ', PT.Remarks),
							4 AS UnionLevel
						FROM
							transaction_projecttransaction PT
						WHERE
							PT.ProjectID = $ID
						UNION ALL
						SELECT
							S.SalaryDate,
							E.EmployeeName,
							'',
							SD.Days,
							'',
							SD.DailySalary,
							'-' AS Debit,
							(SD.DailySalary * SD.Days) AS Credit,
							'Gaji Karyawan' AS Remarks,
							5 AS UnionLevel
						FROM
							transaction_salary S
							JOIN transaction_salarydetails SD
								ON S.SalaryID = SD.SalaryID
							JOIN master_employee E
								ON SD.EmployeeID = E.EmployeeID
						WHERE
							SD.ProjectID = $ID
					)DATA
				ORDER BY
					DATA.TransactionDate,
					DATA.UnionLevel";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 1;
		while($row = mysql_fetch_array($result)) {
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $RowNumber);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['TransactionDate']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['Name']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $row['ItemName']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $row['Quantity']);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['UnitName']);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $row['Price']);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, $row['Debit']);
			$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, $row['Credit']);
			$objPHPExcel->getActiveSheet()->setCellValue("J".$rowExcel, $row['Balance']);
			$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, $row['Remarks']);
			$RowNumber++;
			$rowExcel++;
		}
		$objPHPExcel->getActiveSheet()->getStyle("G5:J".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			
		
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:K2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:K4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:K2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:K4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c4bd97');

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
		$objPHPExcel->getActiveSheet()->getStyle("A4:K".($rowExcel-1))->applyFromArray($styleArray);		

		$title = "Laporan Proyek $ProjectName";
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