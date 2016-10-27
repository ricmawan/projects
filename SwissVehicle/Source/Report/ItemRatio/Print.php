<?php
	if(isset($_POST['txtDate'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		include '../../assets/lib/fpdf17/fpdf.php';
		include '../../assets/lib/fpdf17-add-on/pdf_js.php';
		global $txtDate;
		global $date;
		global $where;
		global $order_by;
		global $ItemID;
		$ItemID = mysql_real_escape_string($_POST['ddlItem']);
		$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember');
		$tanggal = date('j');
		$tahun = date('Y');
		if((INT)strlen($tanggal) == 1) $tanggal = "0".$tanggal;
		$bulan = $array_bulan[date('n')];
		$StartDate = "";
		$txtDate = explode('-', mysql_real_escape_string($_POST['txtDate']));
		$_POST['txtDate'] = "$txtDate[2]-$txtDate[1]-$txtDate[0]";
		$StartDate = $txtDate[0] ." ".$array_bulan[(int)$txtDate[1]]." ".$txtDate[2]; 
		$txtDate = $_POST['txtDate'];
		
		$date = $StartDate;
		
		$where = " 1=1 ";
		$order_by = "MM.MachineCode";
		if (ISSET($_GET['sort']) && $_GET['sort'] != "{}" )
		{
			$order_by = "";
			$_GET['sort'] = json_decode($_GET['sort'], true);
			foreach($_GET['sort'] as $key => $value) {
				if($key != 'No') $order_by .= " $key $value";
				else $order_by = "MM.MachineCode";
			}
		}
		//Handles search querystring sent from Bootgrid
		if (ISSET($_GET['searchPhrase']) )
		{
			$search = trim($_GET['searchPhrase']);
			$where .= " AND ( MM.MachineType LIKE '%".$search."%' OR MM.MachineCode LIKE '%".$search."%' OR SK.Kilometer LIKE '%".$search."%' OR EK.Kilometer LIKE '%".$search."%' OR (EK.Kilometer - SK.Kilometer) LIKE '%".$search."%' )";
		}
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
				$w = Array(30,30,20,25,20,25,35);
				$this->SetTextColor(0, 0, 0);
				$this->SetFont('Arial', 'B', 14);
				$this->Ln(2);
				$this->Cell(190,0.5,"Laporan Rasio Barang",0,0,'C');
				$this->Ln(8);
				$this->SetFont('Arial','',9);
				$this->Cell(25,0.5,"Tanggal",0,0,'L');
				$this->Cell(100,0.5," : ".$GLOBALS['date'],0,1,'L');
				$this->Ln(5);
				$header = array('Tipe', 'Plat No', 'Tanggal', 'KM Awal', 'Tanggal', 'KM Akhir', 'Selisih');
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
							MM.MachineType,
							MM.MachineCode,
							IFNULL(SK.Kilometer, '-') StartKilometer,
							SK.StartDate,
							IFNULL(EK.Kilometer, '-') EndKilometer,
							EK.EndDate,
							IFNULL((EK.Kilometer - SK.Kilometer), '-') Difference
						FROM
							master_machine MM
							JOIN 
							(
								SELECT
									TS.MachineID,
									DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') EndDate,
									MAX(TS.Kilometer) Kilometer
								FROM
									transaction_service TS
									JOIN transaction_servicedetails SD
										ON TS.ServiceID = SD.ServiceID
								WHERE
									CAST(TS.TransactionDate AS DATE) = '".$GLOBALS['txtDate']."'
									AND SD.ItemID = ".$GLOBALS['ItemID']."
								GROUP BY
									TS.MachineID
							)EK
								ON MM.MachineID = EK.MachineID
							JOIN
							(
								SELECT
									DATE_FORMAT(MAX(TS.TransactionDate), '%d-%m-%Y') StartDate,
									TS.MachineID,
									MAX(TS.Kilometer) Kilometer
								FROM
									transaction_service TS
									JOIN transaction_servicedetails SD
										ON TS.ServiceID = SD.ServiceID
								WHERE
									CAST(TS.TransactionDate AS DATE) < '".$GLOBALS['txtDate']."'
									AND SD.ItemID = ".$GLOBALS['ItemID']."
								GROUP BY
									TS.MachineID
							)SK
								ON MM.MachineID = SK.MachineID
						WHERE
							".$GLOBALS['where']."
						ORDER BY
							".$GLOBALS['order_by'];
								
					if (! $result = mysql_query($sql, $dbh)) {
						echo mysql_error();
						return 0;
					}
					while($row = mysql_fetch_array($result)) {
						$this->Row(Array($row['MachineType'], $row['MachineCode'], $row['StartDate'], number_format($row['StartKilometer'],0,".",","), $row['EndDate'], number_format($row['EndKilometer'],0,".",","), number_format($row['Difference'],0,".",",")));
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
		$pdf->SetWidths(Array(30,30,20,25,20,25,35));
		$pdf->SetAligns(Array('L', 'L', 'R', 'R', 'R'));
		srand(microtime()*1000000);
		$pdf->Table();
		$filename = "Laporan Rasio BBM " . $GLOBALS['date'] . ".pdf";
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
