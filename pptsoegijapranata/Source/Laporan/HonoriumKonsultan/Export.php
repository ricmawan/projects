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
									 ->setTitle("Laporan Honorium Konsultan")
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Honorium Konsultan")
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
					->setCellValue('A1', "HONORIUM KONSULTAN PPT SOEGIJAPRANATA\n BULAN ".$Bulan." ".$Year);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		
		//set bold
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		
		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, "NO");
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, "NAMA KONSULTAN");
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, "CALON KARYAWAN");
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, "ANAK/DEWASA");
		$col++;
		
		//$TableColumn = "<td>No</td><td>Konsultan</td><td>Cakar</td>";
		$sql = "SELECT
					UPPER(L.Jenis),
					L.LayananID,
					L.Harga
				FROM
					master_layanan L
				ORDER BY
					L.LayananID";
				
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		while ($rows=mysql_fetch_row($result)) {
			//$TableColumn .= "<td>".$row5[0]."</td><td>Total</td>";
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $rows[0]);
			$col++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, "JUMLAH");
			$col++;
		}
		//$TableColumn .= "<td>Piket</td><td>JML @".$CONSULTANT_PICKET."</td>";
		//header table
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, "PIKET");
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, "@".$CONSULTANT_PICKET );
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, "TOTAL HONOR");
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, "PPH (%)");
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, "TOTAL HONOR SESUDAH PAJAK");
		$col++;
		$sql = "SELECT KonsultanID, Nama, PPH FROM master_konsultan";
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 0;
		//looping konsultan
		while ($row=mysql_fetch_row($result)) {
			$RowNumber ++;
			$rowExcel++;
			$col = 0;
			$SumPrice = 0;
			//$TableBody .= "<tr><td>".$RowNumber."</td><td>".$row[1]."</td>";
			//header table
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $RowNumber);
			$col++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $row[1]);
			$col++;		

			//query buat jumlah cakar per konsultan
			$sql2 = "SELECT
						COUNT(A.Nama)
					FROM
						(
							SELECT
								RCS.Nama,
								RCS.TransaksiID
							FROM
								master_konsultan MK
								LEFT JOIN transaksi_rincicustomerservice RCS
									ON RCS.KonsultanID = MK.KonsultanID
								LEFT JOIN transaksi_customerservice CS
									ON RCS.TransaksiID = CS.TransaksiID
								LEFT JOIN master_klien K
									ON K.KlienID = CS.KlienID
							WHERE
								MK.KonsultanID = ".$row[0]."
								AND MONTH(CS.Tanggal) = $Month
								AND YEAR(CS.Tanggal) = $Year
								AND CS.Report = 1
								AND (K.JenisKlien = 1 OR K.JenisKlien = 3)
							GROUP BY
								MK.KonsultanID,
								RCS.TransaksiID,
								RCS.Nama    
							ORDER BY
								MK.KonsultanID
						)A";
		
			if (! $result2=mysql_query($sql2, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$cekrow = mysql_num_rows($result2);
			if($cekrow == 0) {
				//$TableBody .= "<td>0</td>";
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, 0);
				$col++;
			}
			else {
				while ($row2=mysql_fetch_row($result2)) {
					//$TableBody .= "<td>".$row2[0]."</td>";
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $row2[0]);
					$col++;
				}
			}
			
			//query jumlah anak/dewasa
			$sql2 = "SELECT
						COUNT(A.TransaksiID)
					FROM
						(
							SELECT
								CS.TransaksiID
							FROM
								master_konsultan MK
								LEFT JOIN transaksi_rincicustomerservice RCS
									ON RCS.KonsultanID = MK.KonsultanID
								LEFT JOIN transaksi_customerservice CS
									ON RCS.TransaksiID = CS.TransaksiID
								LEFT JOIN master_klien K
									ON K.KlienID = CS.KlienID
							WHERE
								MK.KonsultanID = ".$row[0]."
								AND MONTH(CS.Tanggal) = $Month
								AND YEAR(CS.Tanggal) = $Year
								AND CS.Report = 1
								AND (K.JenisKlien = 2 OR K.JenisKlien = 4)
							GROUP BY
								MK.KonsultanID,
								CS.TransaksiID
							ORDER BY
								MK.KonsultanID
						)A";
		
			if (! $result2=mysql_query($sql2, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$cekrow = mysql_num_rows($result2);
			if($cekrow == 0) {
				//$TableBody .= "<td>0</td>";
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, 0);
				$col++;
			}
			else {
				while ($row2=mysql_fetch_row($result2)) {
					//$TableBody .= "<td>".$row2[0]."</td>";
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $row2[0]);
					$col++;
				}
			}
			//query semua layanan
			$sql3 = "SELECT
						L.LayananID						
					FROM
						master_layanan L
					ORDER BY
						L.LayananID";
					
			if (! $result3=mysql_query($sql3, $dbh)) {
				echo mysql_error();
				return 0;
			}
			while ($row3=mysql_fetch_row($result3)) {
				//$TableColumn .= "<td>".$row2[0]."</td>";
				//query buat semua layanan yang dihandle per konsultan
				$sql4 = "SELECT
							MK.KonsultanID,
							MK.Nama,
							CS.TransaksiID,
							L.Jenis,
							L.LayananID,
							(SELECT 
								COUNT(1)
							 FROM
								transaksi_rincicustomerservice RCS
								JOIN transaksi_customerservice CS
									ON CS.TransaksiID = RCS.TransaksiID
							 WHERE
								RCS.LayananID = L.LayananID
								AND RCS.KonsultanID = MK.KonsultanID
								AND MONTH(CS.Tanggal) = $Month
								AND YEAR(CS.Tanggal) = $Year
							) as Count,
							SUM(RCS2.Harga)
						FROM
							master_konsultan MK
							LEFT JOIN transaksi_rincicustomerservice RCS2
								ON RCS2.KonsultanID = MK.KonsultanID
							LEFT JOIN transaksi_customerservice CS
								ON RCS2.TransaksiID = CS.TransaksiID
							LEFT JOIN master_layanan L
								ON RCS2.LayananID = L.LayananID
						WHERE
							MK.KonsultanID = ".$row[0]."
							AND L.LayananID = ".$row3[0]."
							AND MONTH(CS.Tanggal) = $Month
							AND YEAR(CS.Tanggal) = $Year
							AND CS.Report = 1
						GROUP BY
							MK.KonsultanID,
							L.LayananID
						ORDER BY
							MK.KonsultanID";
			
				if (! $result4=mysql_query($sql4, $dbh)) {
					echo mysql_error();
					return 0;
				}
				$cekrow = mysql_num_rows($result4);
				if($cekrow == 0) {
					//$TableBody .= "<td>0</td><td align='right'>0,00</td>";
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, 0);
					$col++;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, 0);
					$objPHPExcel->getActiveSheet()->getStyle("".PHPExcel_Cell::stringFromColumnIndex($col).$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
					$col++;
				}
				else {
					$Count = 0;
					$Harga = 0;
					while ($row4=mysql_fetch_row($result4)) {
						$Count += $row4[5];
						$Harga = $row4[6];
					}
					$SumPrice += $Harga;
					//$TableBody .= "<td>".$Count."</td><td align='right'>".number_format($Harga,2,",",".")."</td>";
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $Count);
					$col++;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $Harga);
					$objPHPExcel->getActiveSheet()->getStyle("".PHPExcel_Cell::stringFromColumnIndex($col).$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
					$col++;
				}
			}
			$sql6 = "SELECT
						IFNULL(COUNT(PiketID), 0)
					FROM
						master_konsultan MK
						LEFT JOIN piket_konsultan PK
							ON MK.KonsultanID = PK.KonsultanID
					 WHERE
						MK.KonsultanID = ".$row[0]."
						AND MONTH(PK.TanggalPiket) = $Month
						AND YEAR(PK.TanggalPiket) = $Year
					 GROUP BY
						MK.KonsultanID";
			if (! $result6=mysql_query($sql6, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$cekrow = mysql_num_rows($result6);
			if($cekrow == 0) {
				//$TableBody .= "<td>0</td><td align='right'>0,00</td>";
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, 0);
				$col++;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, 0);
				$objPHPExcel->getActiveSheet()->getStyle("".PHPExcel_Cell::stringFromColumnIndex($col).$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$col++;
			}
			else {
				while ($row6=mysql_fetch_row($result6)) {
					$SumPrice += ($row6[0] * $CONSULTANT_PICKET);
					//$TableBody .= "<td>".$row6[0]."</td><td align='right'>".number_format(($row6[0] * $CONSULTANT_PICKET),2,",",".")."</td>";
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $row6[0]);
					$col++;
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $row6[0] * $CONSULTANT_PICKET);
					$objPHPExcel->getActiveSheet()->getStyle("".PHPExcel_Cell::stringFromColumnIndex($col).$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
					$col++;
				}
			}
			//Total Honor
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $SumPrice);
			$objPHPExcel->getActiveSheet()->getStyle("".PHPExcel_Cell::stringFromColumnIndex($col).$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$col++;
			//pph
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $row[2]);
			//$objPHPExcel->getActiveSheet()->getStyle("".PHPExcel_Cell::stringFromColumnIndex($col).$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$col++;
			//SESUDAH PPH
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowExcel, $SumPrice - (($row[2] * $SumPrice) / 100));
			$objPHPExcel->getActiveSheet()->getStyle("".PHPExcel_Cell::stringFromColumnIndex($col).$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$col++;
			//$TableBody .= "</tr>";
		}
		
		$rowExcel += 4;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col-1, $rowExcel, "=SUM(".PHPExcel_Cell::stringFromColumnIndex($col-1)."5:".PHPExcel_Cell::stringFromColumnIndex($col-1).($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->getStyle("".PHPExcel_Cell::stringFromColumnIndex($col-1).$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col-3, $rowExcel, "=SUM(".PHPExcel_Cell::stringFromColumnIndex($col-3)."5:".PHPExcel_Cell::stringFromColumnIndex($col-3).($rowExcel-1).")");
		$objPHPExcel->getActiveSheet()->getStyle("".PHPExcel_Cell::stringFromColumnIndex($col-3).$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		for($i=$col-4;$i>1;$i--) {
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $rowExcel, "=SUM(".PHPExcel_Cell::stringFromColumnIndex($i)."5:".PHPExcel_Cell::stringFromColumnIndex($i).($rowExcel-1).")");
			if(($i % 2) == 1 && $i != 3) $objPHPExcel->getActiveSheet()->getStyle("".PHPExcel_Cell::stringFromColumnIndex($i).$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		}
		
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:".PHPExcel_Cell::stringFromColumnIndex($col-1)."2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:".PHPExcel_Cell::stringFromColumnIndex($col-1)."4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1:".PHPExcel_Cell::stringFromColumnIndex($col-1)."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle("A4:".PHPExcel_Cell::stringFromColumnIndex($col-1)."4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('c4bd97');
	
		//set all width 
		$fromCol='A';
		$toCol= ''.PHPExcel_Cell::stringFromColumnIndex($col);
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
		$objPHPExcel->getActiveSheet()->getStyle("A4:".PHPExcel_Cell::stringFromColumnIndex($col-1).$rowExcel)->applyFromArray($styleArray);		

		$title = "Laporan Honorium Konsultan Bulan $Bulan $Year";
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