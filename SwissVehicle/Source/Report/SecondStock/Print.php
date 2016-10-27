<?php
	if(isset($_POST['ddlItem'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		include '../../assets/lib/fpdf17/fpdf.php';
		include '../../assets/lib/fpdf17-add-on/pdf_js.php';
		global $ItemID;
		global $ItemName;
		global $txtFromDate;
		global $txtToDate;
		global $date;
		$ItemID = mysql_real_escape_string($_POST['ddlItem']);
		$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember');
		$tanggal = date('j');
		$tahun = date('Y');
		if((INT)strlen($tanggal) == 1) $tanggal = "0".$tanggal;
		$bulan = $array_bulan[date('n')];
		$StartDate = "";
		$EndDate = "";
		if($_POST['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
			$StartDate = "01 Januari 2000";
		}
		else {
			$txtFromDate = explode('-', mysql_real_escape_string($_POST['txtFromDate']));
			$_POST['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]";
			$StartDate = $txtFromDate[0] ." ".$array_bulan[(int)$txtFromDate[1]]." ".$txtFromDate[2]; 
			$txtFromDate = $_POST['txtFromDate'];
		}
		if($_POST['txtToDate'] == "") {
			$txtToDate = date("Y-m-d");
			$EndDate = $tanggal." ".$bulan." ".$tahun;
		}
		else {
			$txtToDate = explode('-', mysql_real_escape_string($_POST['txtToDate']));
			$_POST['txtToDate'] = "$txtToDate[2]-$txtToDate[1]-$txtToDate[0]";
			$EndDate = $txtToDate[0] ." ".$array_bulan[(int)$txtToDate[1]]." ".$txtToDate[2];
			$txtToDate = $_POST['txtToDate'];
		}
		$date = $StartDate . " - " . $EndDate;

		$sql = "SELECT
					MI.ItemName
				FROM
					master_item MI
				WHERE
					MI.ItemID = $ItemID";
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		
		$row = mysql_fetch_array($result);
		$ItemName = $row['ItemName'];
		class PDF_AutoPrint extends PDF_JavaScript
		{
			var $width;
			var $aligns;
			function SetWidths($w)
			{
				//Set the array of column widths
				$this->widths=$w;
			}

			function SetAligns($a)
			{
				//Set the array of column alignments
				$this->aligns=$a;
			}

			function Row($data)
			{
				//Calculate the height of the row
				$nb=0;
				for($i=0;$i<count($data);$i++)
					$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
				$h=5*$nb;
				//Issue a page break first if needed
				$this->CheckPageBreak($h);
				//Draw the cells of the row
				for($i=0;$i<count($data);$i++)
				{
					$w=$this->widths[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//Save the current position
					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border
					$this->Rect($x,$y,$w,$h);
					//Print the text
					$this->MultiCell($w,5,$data[$i],0,$a);
					//Put the position to the right of the cell
					$this->SetXY($x+$w,$y);
				}
				//Go to the next line
				$this->Ln($h);
			}

			function CheckPageBreak($h)
			{
				//If the height h would cause an overflow, add a new page immediately
				if($this->GetY()+$h>$this->PageBreakTrigger)
					$this->AddPage($this->CurOrientation);
			}

			function NbLines($w,$txt)
			{
				//Computes the number of lines a MultiCell of width w will take
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 and $s[$nb-1]=="\n")
					$nb--;
				$sep=-1;
				$i=0;
				$j=0;
				$l=0;
				$nl=1;
				while($i<$nb)
				{
					$c=$s[$i];
					if($c=="\n")
					{
						$i++;
						$sep=-1;
						$j=$i;
						$l=0;
						$nl++;
						continue;
					}
					if($c==' ')
						$sep=$i;
					$l+=$cw[$c];
					if($l>$wmax)
					{
						if($sep==-1)
						{
							if($i==$j)
								$i++;
						}
						else
							$i=$sep+1;
						$sep=-1;
						$j=$i;
						$l=0;
						$nl++;
					}
					else
						$i++;
				}
				return $nl;
			}

			function Header() {
				$this->SetFont('Arial','B',14);
				$this->Cell(190,5,"Swiss House",0,1,'C');
				$this->SetFont('Arial','',9);
				$this->Cell(190,5,"Jalan Karang Anyar No 14",0,1,'C');
				$this->Cell(190,5,"Banyumanik - Semarang",0,1,'C');
				$this->Line(10,27,200,27);
				$this->Line(10,26,200,26);
				$this->Ln(5);
				//Judul Kolom
				$w = Array(20,20,20,20,110);
				$this->SetTextColor(0, 0, 0);
				$this->SetFont('Arial', 'B', 14);
				$this->Ln(2);
				$this->Cell(190,0.5,"Laporan Stok Barang Bekas",0,0,'C');
				$this->Ln(8);
				$this->SetFont('Arial','',9);
				$this->Cell(25,0.5,"Nama Barang",0,0,'L');
				$this->Cell(100,0.5," : ".$GLOBALS['ItemName'],0,1,'L');
				$this->Ln(5);
				$this->Cell(25,0.5,"Tanggal",0,0,'L');
				$this->Cell(100,0.5," : ".$GLOBALS['date'],0,1,'L');
				$this->Ln(5);
				$header = array('Tanggal', 'Stok Masuk', 'Penjualan', 'Stok', 'Keterangan');
				$this->SetFont('Arial','B',9);
				for($i=0; $i<count($header); $i++) {
					$this->Cell($w[$i],5,$header[$i],1,0,'C');
				}
				$this->Ln(5);
			}
					
			function Table() {
				include "../../DBConfig.php";
				//Data
				$this->SetFont('Arial','',8);
				$sql = "SELECT
							1 GroupLevel,
							'' TransactionDate,
							'' DateNoFormat,
							'' PurchaseQty,
							'' UsageQty,
							IFNULL(SD.Quantity, 0) - IFNULL(SL.Quantity, 0) Quantity,
							'Stok Awal' Remarks
						FROM
							master_item MI
							LEFT JOIN
							(
								SELECT
									SD.ItemID,
									SUM(SD.Quantity) Quantity
								FROM
									transaction_service TS
									JOIN transaction_servicedetails SD
										ON TS.ServiceID = SD.ServiceID
								WHERE
									CAST(TS.TransactionDate AS DATE) < '".$GLOBALS['txtFromDate']."'
									AND SD.ItemID = ".$GLOBALS['ItemID']."
									AND SD.IsSecond = 1
								GROUP BY
									SD.ItemID
							)SD
								ON MI.ItemID = SD.ItemID
							LEFT JOIN
							(
								SELECT
									SD.ItemID,
									SUM(SD.Quantity) Quantity
								FROM
									transaction_sale TS
									JOIN transaction_saledetails SD
										ON TS.SaleID = SD.SaleID
								WHERE
									CAST(TS.TransactionDate AS DATE) < '".$GLOBALS['txtFromDate']."'
									AND SD.ItemID = ".$GLOBALS['ItemID']."
								GROUP BY
									SD.ItemID
							)SL
								ON MI.ItemID = SL.ItemID
						WHERE
							MI.ItemID = ".$GLOBALS['ItemID']."
							AND (IFNULL(SD.Quantity, 0) - IFNULL(SL.Quantity, 0)) > 0
						UNION ALL
						SELECT
							2,
							DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') AS TransactionDate,
							TS.TransactionDate DateNoFormat,
							SD.Quantity PurchaseQty,
							'' UsageQty,
							SD.Quantity,
							CONCAT('Mobil/Mesin : ', MM.MachineCode)
						FROM
							transaction_service TS
							JOIN master_machine MM
								ON TS.MachineID = MM.MachineID
							LEFT JOIN transaction_servicedetails SD
								ON TS.ServiceID = SD.ServiceID 
						WHERE
							CAST(TS.TransactionDate AS DATE) >= '".$GLOBALS['txtFromDate']."'
							AND CAST(TS.TransactionDate AS DATE) <= '".$GLOBALS['txtToDate']."'
							AND SD.ItemID = ".$GLOBALS['ItemID']."
							AND SD.IsSecond = 1
						UNION ALL
						SELECT
							3,
							DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') AS TransactionDate,
							TS.TransactionDate DateNoFormat,
							'' PurchaseQty,
							-SD.Quantity UsageQty,
							-SD.Quantity,
							CONCAT('Pembeli : ', TS.CustomerName)
						FROM
							transaction_sale TS
							LEFT JOIN transaction_saledetails SD
								ON TS.SaleID = SD.SaleID 
						WHERE
							CAST(TS.TransactionDate AS DATE) >= '".$GLOBALS['txtFromDate']."'
							AND CAST(TS.TransactionDate AS DATE) <= '".$GLOBALS['txtToDate']."'
							AND SD.ItemID = ".$GLOBALS['ItemID']."
						ORDER BY
							DateNoFormat, GroupLevel";
								
					if (! $result = mysql_query($sql, $dbh)) {
						echo mysql_error();
						return 0;
					}
					$Stock = 0;
					while($row = mysql_fetch_array($result)) {
						$Stock += $row['Quantity'];
						$this->Row(Array($row['TransactionDate'], $row['PurchaseQty'], $row['UsageQty'], $Stock, $row['Remarks']));
					}
			}

			function Footer() {
				$this->SetY(-15);
				$this->SetFont('Arial','',8);
				$this->Cell(0,10,"Halaman ".$this->PageNo()."/{nb}",0,0,'C');
			}

			function AutoPrint($dialog=false)
			{
				//Open the print dialog or start printing immediately on the standard printer
				$param=($dialog ? 'true' : 'false');
				$script="print($param);";
				$this->IncludeJS($script);
			}

			function AutoPrintToPrinter($server, $printer, $dialog=false)
			{
				//Print on a shared printer (requires at least Acrobat 6)
				$script = "var pp = getPrintParams();";
				if($dialog)
					$script .= "pp.interactive = pp.constants.interactionLevel.full;";
				else
					$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
				$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
				$script .= "print(pp);";
				$this->IncludeJS($script);
			}
		}
		
		$pdf = new PDF_AutoPrint('P', 'mm', 'A4');
		$pdf->AliasNbPages();
		$pdf->SetTopMargin(10);
		$pdf->SetLeftMargin(10);
		$pdf->SetRightMargin(10);
		$pdf->AddPage();
		$pdf->SetWidths(Array(20,20,20,20,110));
		$pdf->SetAligns(Array('C', 'R', 'R', 'R', 'L'));
		srand(microtime()*1000000);
		$pdf->Table();
		$filename = "Laporan Stok " . $GLOBALS['ItemName'] . " bekas " . $GLOBALS['date'] . ".pdf";
		//$pdf->AutoPrint(true);
		if($_GET['PrintType'] == "1") {
			$pdf->Output($filename, "D");
		}
		else {
			$pdf->AutoPrint(true);
			$pdf->Output($filename, "I");
		}
		
	}
?>
