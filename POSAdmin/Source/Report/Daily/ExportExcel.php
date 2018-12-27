<?php
	if(ISSET($_GET['UserID']) ) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$UserID = $_GET['UserID'];
		if($_GET['TransactionDate'] == "") {
			$TransactionDate = date("Y-m-d");
		}
		else {
			$TransactionDate = explode('-', mysql_real_escape_string($_GET['TransactionDate']));
			$_GET['TransactionDate'] = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]"; 
			$TransactionDate = $_GET['TransactionDate'];
		}
				
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		ini_set('max_execution_time', 300);
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
									 ->setTitle("Laporan Harian")
									 ->setSubject("Laporan")
									 ->setDescription("Laporan Harian")
									 ->setKeywords("Generate By PHPExcel")
									 ->setCategory("Laporan");
		//Header
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', "LAPORAN HARIAN");
					
		//set margin
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.787402);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.393701);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.787402);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);    
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
		$monthName = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");

		$period = date("d", strtotime($TransactionDate)) . " " . $monthName[date("m", strtotime($TransactionDate)) - 1] . " " .  date("Y", strtotime($TransactionDate));

		$cashierStyle = array(
			'font'  => array(
	        	'color' => array('rgb' => 'FF0000'),
	        	'size'  => 14,
	        	'bold'  => true
	    	)
		);

		$transactionNameStyle = array(
			'font'  => array(
	        	'color'  => array('rgb' => '000000'),
	        	'size'   => 14,
	        	'bold'   => true,
	        	'underline' => true
	    	)
		);

		$grandTotalStyle = array(
			'font'  => array(
	        	'color'  => array('rgb' => '00ff50'),
	        	'size'   => 24,
	        	'bold'   => true,
	        	'underline' => true
	    	),
	    	'fill' => array(
	    		'type' => 'solid',
	    		'startcolor' => array('rgb' => '000000')
	    	)
		);

		$unionTotalStyle = array(
	    	'fill' => array(
	    		'type' => 'solid',
	    		'startcolor' => array('rgb' => 'fff600')
	    	)
		);

		$totalKasirStyle = array(
			'font'  => array(
	        	'color'  => array('rgb' => '000000')
	    	),
	    	'fill' => array(
	    		'type' => 'solid',
	    		'startcolor' => array('rgb' => 'ff0000')
	    	)
		);
		
		//bold title
		$objPHPExcel->getActiveSheet()->getStyle("A1:A2")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(16);
		//merge title
		$objPHPExcel->getActiveSheet()->mergeCells("A1:F2");
		//center title
		$objPHPExcel->getActiveSheet()->getStyle("A1:F2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A3', "Tanggal:");
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B3', $period );
		$objPHPExcel->getActiveSheet()->getStyle("B3:C3")->getFont()->setBold(true);

		//bold title
		/*$objPHPExcel->getActiveSheet()->getStyle("A6:K6")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle("A6:K6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8d8d8');*/

		$rowExcel = 4;
		$col = 0;
		//set color
		//$objPHPExcel->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
		/*$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, "No. Invoice");
		$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, "Tanggal");
		$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, "Nama Pelanggan");
		$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, "Kode Barang");
		$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, "Nama Barang");
		$objPHPExcel->getActiveSheet()->setCellValue("G".$rowExcel, "Quantity");
		$objPHPExcel->getActiveSheet()->setCellValue("H".$rowExcel, "Satuan");
		$objPHPExcel->getActiveSheet()->setCellValue("I".$rowExcel, "Harga Jual");
		$objPHPExcel->getActiveSheet()->setCellValue("J".$rowExcel, "Diskon");
		$objPHPExcel->getActiveSheet()->setCellValue("K".$rowExcel, "Sub Total");
		$rowExcel++;*/
		
		$sql = "CALL spSelDailyReport(".$UserID.", '".$TransactionDate."', '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/Daily/ExportExcel.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		$GrandTotal = 0;
		$Data = "";
		$Kasir = "";
		$UnionLevel = 0;
		$TransactionNumber = "";
		$TransactionName = "";
		$UnionTotal = 0;
		$SubTotal = 0;
		$GrandTotal = 0;
		$TotalKasir = 0;
		$Payment = 0;
		$DiscountTotal = 0;
		$UnionDiscountTotal = 0;
		$KasirDiscountTotal = 0;
		$GrandDiscountTotal = 0;
		while ($row = mysqli_fetch_array($result)) {
			if($Kasir != $row['UserName']) {
				if($Kasir != "") {
					if($SubTotal > 0 || ($UnionLevel == 3 && $SubTotal < 0)) {
						if($UnionLevel == 1 || $UnionLevel == 2) {
							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));

							$UnionDiscountTotal += $row['DiscountTotal'];
							$KasirDiscountTotal += $row['DiscountTotal'];
							$GrandDiscountTotal += $row['DiscountTotal'];
						}
						else if($UnionLevel == 4 || $UnionLevel == 5) {
							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));
						}
						else {
							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);
						}
						$SubTotal = 0;
					} 
					if($UnionTotal > 0 || ($UnionLevel == 3 && $UnionTotal < 0)) {
						$rowExcel++;
						//TransactionName
						$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Total ". $TransactionName);
						$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($UnionTotal - $UnionDiscountTotal));
						$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
						$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":F".$rowExcel)->applyFromArray($unionTotalStyle);
						$UnionTotal = 0;
						$UnionDiscountTotal = 0;
						$rowExcel++;
					}
					$rowExcel++;
					//TransactionName
					$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Total ". $Kasir);
					$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($TotalKasir - $KasirDiscountTotal));
					$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
					$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel."F".$rowExcel)->applyFromArray($totalKasirStyle);
					$TotalKasir = 0;
					$KasirDiscountTotal = 0;
					$rowExcel++;
				}
				//cashier
				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $row['UserName']);
				$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($cashierStyle);
			}
			if($row['UnionLevel'] == '0') {
				//TransactionName
				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $row['TransactionName']);
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['SubTotal']);
				$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
				$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":F".$rowExcel)->applyFromArray($unionTotalStyle);
				$TotalKasir += $row['SubTotal'];
				$GrandTotal += $row['SubTotal'];
			}
			else if($row['UnionLevel'] > 0 && $row['UnionLevel'] < 4) {
				if($UnionLevel != $row['UnionLevel']) {
					if($row['UnionLevel'] > 1) {
						if($SubTotal > 0) {
							if($UnionLevel == 1 || $UnionLevel == 2) {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));

								$UnionDiscountTotal += $row['DiscountTotal'];
								$KasirDiscountTotal += $row['DiscountTotal'];
								$GrandDiscountTotal += $row['DiscountTotal'];
							}
							else {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);
							}
							$SubTotal = 0;
						}
						if($UnionTotal > 0) {
							//TransactionName
							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Total ". $TransactionName);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($UnionTotal - $UnionDiscountTotal));
							$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
							$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":F".$rowExcel)->applyFromArray($unionTotalStyle);
							$UnionTotal = 0;
							$UnionDiscountTotal = 0;
							$rowExcel++;
						}
					}
					//TransactionName
					$rowExcel++;
					$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $row['TransactionName']);
					$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
				}
				if($TransactionNumber != $row['TransactionNumber']) {
					if($UnionLevel == $row['UnionLevel']) {
						if($SubTotal > 0) { 
							if($UnionLevel == 1 || $UnionLevel == 2) {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));

								$UnionDiscountTotal += $row['DiscountTotal'];
								$KasirDiscountTotal += $row['DiscountTotal'];
								$GrandDiscountTotal += $row['DiscountTotal'];
							}
							else {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);
							}
							$SubTotal = 0;
						}
					}
					$rowExcel++;
					$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['CustomerName'] . " (". $row['TransactionNumber'] .")");
				}
				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['ItemName']);
				$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['SalePrice']);
				if(strpos($row['Quantity'], ".")) $Quantity = number_format(round($row['Quantity'], 2),2,".",",");	    		
		    	else $Quantity = number_format($row['Quantity'],0,".",",");
				$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $Quantity . " ". $row['UnitName']);
				$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, number_format($row['Discount'],0,".",","));
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['SubTotal']);
				$UnionTotal += $row['SubTotal'];
				$SubTotal += $row['SubTotal'];
				$TotalKasir += $row['SubTotal'];
				$GrandTotal += $row['SubTotal'];
			}
			else if($row['UnionLevel'] > 3 && $row['UnionLevel'] < 6) {
				if($UnionLevel != $row['UnionLevel']) {
					if($row['UnionLevel'] > 1) {
						if($SubTotal > 0 || ($UnionLevel == 3 && $SubTotal < 0)) { 
							if($UnionLevel == 1 || $UnionLevel == 2) {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));

								$UnionDiscountTotal += $row['DiscountTotal'];
								$KasirDiscountTotal += $row['DiscountTotal'];
								$GrandDiscountTotal += $row['DiscountTotal'];
							}
							else if($UnionLevel == 4 || $UnionLevel == 5) {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));
							}
							else {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);
							}

							if($UnionLevel > 3) {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "DP", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $Payment);
								$UnionTotal += $Payment;
								$TotalKasir += $Payment;
								$GrandTotal += $Payment;
							}
							$SubTotal = 0;
						}
						if($UnionTotal > 0 || ($UnionLevel == 3 && $UnionTotal < 0)) {
							//TransactionName
							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Total ". $TransactionName);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($UnionTotal - $UnionDiscountTotal));
							$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
							$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":F".$rowExcel)->applyFromArray($unionTotalStyle);
							$UnionTotal = 0;
							$UnionDiscountTotal = 0;
							$rowExcel++;
						}
					}
					//TransactionName
					$rowExcel++;
					$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $row['TransactionName']);
					$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
				}
				if($TransactionNumber != $row['TransactionNumber']) {
					if($UnionLevel == $row['UnionLevel'] && $SubTotal > 0) {
						if($UnionLevel == 1 || $UnionLevel == 2) {
							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));

							$UnionDiscountTotal += $row['DiscountTotal'];
							$KasirDiscountTotal += $row['DiscountTotal'];
							$GrandDiscountTotal += $row['DiscountTotal'];
						}
						else if($UnionLevel == 4 || $UnionLevel == 5) {
							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));
						}
						else {
							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);
						}
						$UnionTotal += $Payment;
						$TotalKasir += $Payment;
						$GrandTotal += $Payment;
						$rowExcel++;
						$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "DP", PHPExcel_Cell_DataType::TYPE_STRING);
						$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $Payment);
						$SubTotal = 0;
					}
					$rowExcel++;
					$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['CustomerName'] . " (". $row['TransactionNumber'] .")");
				}
				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['ItemName']);
				$objPHPExcel->getActiveSheet()->setCellValue("C".$rowExcel, $row['SalePrice']);
				if(strpos($row['Quantity'], ".")) $Quantity = number_format(round($row['Quantity'], 2),2,".",",");	    		
		    	else $Quantity = number_format($row['Quantity'],0,".",",");
				$objPHPExcel->getActiveSheet()->setCellValue("D".$rowExcel, $Quantity . " ". $row['UnitName']);
				$objPHPExcel->getActiveSheet()->setCellValue("E".$rowExcel, number_format($row['Discount'],0,".",","));
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['SubTotal']);
				$SubTotal += $row['SubTotal'];
			}
			else {
				if($UnionLevel != $row['UnionLevel']) {
					if($row['UnionLevel'] > 1) {
						if($SubTotal > 0) { 
							if($UnionLevel == 1 || $UnionLevel == 2) {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));

								$UnionDiscountTotal += $row['DiscountTotal'];
								$KasirDiscountTotal += $row['DiscountTotal'];
								$GrandDiscountTotal += $row['DiscountTotal'];
							}
							else if($UnionLevel == 4 || $UnionLevel == 5) {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));
							}
							else {
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);
							}

							$SubTotal = 0;
							if($UnionLevel > 3 && $UnionLevel < 6) {
								$UnionTotal += $Payment;
								$TotalKasir += $Payment;
								$GrandTotal += $Payment;
								$rowExcel++;
								$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "DP", PHPExcel_Cell_DataType::TYPE_STRING);
								$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $Payment);
							}
						}
						if($UnionTotal > 0) {
							//TransactionName
							$rowExcel++;
							$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Total ". $TransactionName);
							$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($UnionTotal - $UnionDiscountTotal));
							$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
							$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":F".$rowExcel)->applyFromArray($unionTotalStyle);
							$UnionTotal = 0;
							$UnionDiscountTotal = 0;
							$rowExcel++;
						}
					}
					//TransactionName
					$rowExcel++;
					$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, $row['TransactionName']);
					$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
				}
				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("B".$rowExcel, $row['CustomerName'] . " (". $row['TransactionNumber'] .")");
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $row['SubTotal']);
				$UnionTotal += $row['SubTotal'];
				$TotalKasir += $row['SubTotal'];
				$GrandTotal += $row['SubTotal'];
			}
			$Payment = $row['Payment'];
			$UnionLevel = $row['UnionLevel'];
			$Kasir = $row['UserName'];
			$TransactionNumber = $row['TransactionNumber'];
			$TransactionName = $row['TransactionName'];
			$DiscountTotal = $row['DiscountTotal'];
		}
		if($SubTotal > 0 || ($UnionLevel == 3 && $SubTotal < 0)) {
			if($UnionLevel == 1 || $UnionLevel == 2) {
				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));

				$UnionDiscountTotal += $row['DiscountTotal'];
				$KasirDiscountTotal += $row['DiscountTotal'];
				$GrandDiscountTotal += $row['DiscountTotal'];
			}
			else if($UnionLevel == 4 || $UnionLevel == 5) {
				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);

				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Diskon", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $DiscountTotal);

				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Total", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($SubTotal - $DiscountTotal));
			}
			else {
				$rowExcel++;
				$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "Sub Total", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $SubTotal);
			}
		}

		if($Payment > 0) {
			$rowExcel++;
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("E".$rowExcel, "DP", PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $Payment);
			$UnionTotal += $Payment;
			$TotalKasir += $Payment;
			$GrandTotal += $Payment;
		}

		if($UnionTotal > 0 || $UnionTotal < 0 || ($UnionLevel == 3 && $UnionTotal < 0)) {
			$rowExcel++;
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Total ". $TransactionName);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, $UnionTotal);
			$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
			$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":F".$rowExcel)->applyFromArray($unionTotalStyle);
		}
		if($TotalKasir > 0 || $TotalKasir < 0) {
			//TransactionName
			$rowExcel++;
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Total ". $Kasir);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($TotalKasir - $KasirDiscountTotal));
			$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel)->applyFromArray($transactionNameStyle);
			$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":F".$rowExcel)->applyFromArray($totalKasirStyle);
		}
		if($GrandTotal > 0 || $GrandTotal < 0) {
			$rowExcel++;
			$rowExcel++;
			$objPHPExcel->getActiveSheet()->setCellValue("A".$rowExcel, "Grand Total");
			$objPHPExcel->getActiveSheet()->setCellValue("F".$rowExcel, ($GrandTotal - $GrandDiscountTotal));
			$objPHPExcel->getActiveSheet()->getStyle("A".$rowExcel.":F".$rowExcel)->applyFromArray($grandTotalStyle);
		}
		$objPHPExcel->getActiveSheet()->getStyle('A2:F'.$rowExcel)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);

		$rowExcel++;

		mysqli_free_result($result);
		mysqli_next_result($dbh);

		//$objPHPExcel->getActiveSheet()->getStyle("B6:F".$rowExcel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle("F5:F".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$objPHPExcel->getActiveSheet()->getStyle("C5:C".$rowExcel)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		//set all width 
		$fromCol='A';
		$toCol= 'G';
		for($j = $fromCol; $j !== $toCol; $j++) {
			//$calculatedWidth = $objPHPExcel->getActiveSheet()->getColumnDimension($i)->getWidth();
			$objPHPExcel->getActiveSheet()->getColumnDimension($j)->setAutoSize(true);
		}
		$objPHPExcel->getActiveSheet()->setSelectedCells('A1');

		$title = "Laporan Harian " . $period;
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
		setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
?>