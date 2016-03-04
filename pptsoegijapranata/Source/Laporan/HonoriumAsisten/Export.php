<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);	
	include "../../GetPermission.php";
	//echo $_SERVER['REQUEST_URI'];
	$Content = "";
	$EditFlag = "";
	$DeleteFlag = "";
	if($cek==0) {
		$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
	}
	else {/** Error reporting */
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
		$objPHPExcel->getProperties()->setCreator("PPT Soegijapranata")
									 ->setLastModifiedBy("PPT Soegijapranata")
									 ->setTitle("Laporan Honorium Asiste")
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Honorium Asiste")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		if(isset($_GET['hdnPostBack'])) {
			$MonthYear = explode("-", $_GET['ddlMonthYear']);
			$Month = $MonthYear[0];
			$Year = $MonthYear[1];
		}
		else {
			$Month = date('n');
			$Year = date('Y');
		}
		$array_bulan = array(1=>'JANUARI','FEBRUARI','MARET', 'APRIL', 'MEI', 'JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER', 'NOVEMBER','DESEMBER');
		$Bulan = $array_bulan[$Month];
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "HONORIUM ASISTEN PPT SOEGIJAPRANATA\n BULAN ".$Bulan." ".$Year);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		
		$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "NO");		
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "NAMA ASISTEN");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "JAGA REGULER");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "JUMLAH");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "JUMLAH KLIEN");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "JUMLAH");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "JOB LUAR");
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "JUMLAH");
		$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, "JUMLAH PIKET");
		$objPHPExcel->getActiveSheet()->setCellValue("J".$rowExcel, "JUMLAH");
		$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, "TOTAL");
		$rowExcel++;
		
		$sql = "SELECT
					A.AsistenID,
					A.Nama,
					IFNULL(JR.Count, 0) AS JobReguler,
					IFNULL(JR.Jumlah, 0) AS JmlJobReguler,
					IFNULL(JK.Count, 0) AS JmlKlien,
					IFNULL(JL.Count, 0) AS JobLuar,
					IFNULL(JL.Jumlah, 0) AS JmlJobLuar,
					IFNULL(PA.Count, 0) AS JmlPiket
				FROM
					master_asisten A
					LEFT JOIN 
					(
						SELECT
							TA.AsistenID,
							SUM(RA.Jumlah) AS Count,
							SUM(RA.Harga) AS Jumlah
						FROM
							transaksi_asisten TA
							JOIN transaksi_rinciasisten RA
								ON TA.TransaksiID = RA.TransaksiID
							JOIN master_jobdesk J
								ON J.JobdeskID = RA.JobdeskID
						WHERE
							J.Jenis = 1
							AND MONTH(TA.Tanggal) = $Month
							AND YEAR(TA.Tanggal) = $Year
						GROUP BY
							TA.AsistenID
					) JR
						ON JR.AsistenID = A.AsistenID
					LEFT JOIN 
					(
						SELECT
							RCS.AsistenID,
							COUNT(RCS.DetailID) AS Count
						FROM
							transaksi_customerservice CS
							JOIN transaksi_rincicustomerservice RCS
								ON CS.TransaksiID = RCS.TransaksiID
						WHERE
							MONTH(CS.Tanggal) = $Month
							AND YEAR(CS.Tanggal) = $Year
							AND CS.Report = 1
						GROUP BY
							RCS.AsistenID
					) JK
						ON JK.AsistenID = A.AsistenID
					LEFT JOIN 
					(
						SELECT
							TA.AsistenID,
							SUM(RA.Jumlah) AS Count,
							SUM(RA.Harga) AS Jumlah
						FROM
							transaksi_asisten TA
							JOIN transaksi_rinciasisten RA
								ON TA.TransaksiID = RA.TransaksiID
							JOIN master_jobdesk J
								ON J.JobdeskID = RA.JobdeskID
						WHERE
							J.Jenis = 2
							AND MONTH(TA.Tanggal) = $Month
							AND YEAR(TA.Tanggal) = $Year
						GROUP BY
							TA.AsistenID
					) JL
						ON JL.AsistenID = A.AsistenID
					LEFT JOIN
				    	(
						SELECT
							PA.AsistenID,
							COUNT(PA.PiketID) AS Count
						FROM
							piket_asisten PA
						WHERE
							MONTH(PA.TanggalPiket) = $Month
							AND YEAR(PA.TanggalPiket) = $Year
					) PA
						ON PA.AsistenID = A.AsistenID";
				
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$i=0;
		while ($rows=mysql_fetch_row($result)) {
			$i++;
			$Total = 0;
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $i);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $rows[1]);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $rows[2]);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $rows[3]);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, $rows[4]);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $rows[4] * $ASISTANT_CLIENT_PICKET);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, $rows[5]);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, $rows[6]);
			$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, $rows[7]);
			$objPHPExcel->getActiveSheet()->setCellValue("J".$rowExcel, $rows[7] * $ASISTANT_PICKET);

			$Total = $rows[3] + ($rows[4] * $ASISTANT_CLIENT_PICKET) + $rows[6] + ($rows[7] * $ASISTANT_PICKET);
			$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, $Total);
			$rowExcel++;
		}
		
		$rowExcel += 4;
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "=SUM(C5:C".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "=SUM(D5:D".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->getStyle("D5:D".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "=SUM(E5:E".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "=SUM(F5:F".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->getStyle("F5:F".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "=SUM(G5:G".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "=SUM(H5:H".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->getStyle("H5:H".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, "=SUM(I5:I".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue("J".$rowExcel, "=SUM(J5:J".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->getStyle("J5:J".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		

		$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, "=SUM(K5:K".($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->getStyle("K5:K".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		
		
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
		$objPHPExcel->getActiveSheet()->getStyle("A4:K".$rowExcel)->applyFromArray($styleArray);		

		$title = "Laporan Honorium Asisten Bulan $Bulan $Year";
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
