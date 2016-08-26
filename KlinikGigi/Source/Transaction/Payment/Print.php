<?php
	if(isset($_POST['hdnMedicationID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		include '../../assets/lib/fpdf17/fpdf.php';
		include '../../assets/lib/fpdf17-add-on/pdf_js.php';
		global $ID;
		global $PatientName;
		global $PatientNumber;
		global $date;
		$ID = mysql_real_escape_string($_POST['hdnMedicationID']);
		$tanggal = date('j');
		$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember');
		$bulan = $array_bulan[date('n')];
		$tahun = date('Y');
		if((INT)strlen($tanggal) == 1) $tanggal = "0".$tanggal;
		$date = $tanggal." ".$bulan." ".$tahun;
		$sql = "SELECT
					MP.PatientNumber,
					MP.PatientName
				FROM
					transaction_medication TM
					JOIN master_patient MP
						ON MP.PatientID = TM.PatientID
				WHERE
					TM.MedicationID = $ID";
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		
		$row = mysql_fetch_array($result);
		$PatientName = $row['PatientName'];
		$PatientNumber = $row['PatientNumber'];
		class PDF_AutoPrint extends PDF_JavaScript
		{
			function Header() {
				$this->Image('../../assets/img/logo.png', 0.8, 0.5, -700);
				$this->SetFont('Arial','B',14);
				$this->SetY(1);
				$this->Cell(5);
				$this->Cell(20,0.5,"IM Dental Specialist",0,1,'L');
				$this->Cell(5);
				$this->SetFont('Arial','',9);
				$this->Cell(30,0.5,"Jalan Kawi No 41",0,1,'L');
				$this->Cell(5);
				$this->Cell(30,0.5,"Semarang",0,1,'L');
				$this->Cell(5);
				$this->Cell(30,0.5,"Hp: 0851 0091 9191",0,1,'L');
				$this->Line(1,3.1,13.8,3.1);
				$this->Line(1,3.2,13.8,3.2);
				$this->Ln();
				//Judul Kolom
				$this->SetTextColor(0, 0, 0);
				$this->Cell(2,0.5,"ID Pasien",0,0,'L');
				$this->Cell(10,0.5," : ".$GLOBALS['PatientNumber'],0,1,'L');
				$this->Cell(2,0.5,"Nama Pasien",0,0,'L');
				$this->Cell(10,0.5," : ".$GLOBALS['PatientName'],0,1,'L');
				$this->Cell(2,0.5,"Tanggal",0,0,'L');
				$this->Cell(10,0.5," : ".$GLOBALS['date'],0,1,'L');
				$this->Ln();
				$header = array('No', 'Tindakan', 'Jumlah', 'Harga', 'Sub Total', 'Keterangan');
				$w=array(0.8,3,1.5,2.25,2.25,3);
				$this->SetX(1);
				$this->SetFont('Arial','B',9);
				for($i=0; $i<count($header); $i++) {
					$this->Cell($w[$i],1,$header[$i],1,0,'C');
				}
				$this->Ln();
			}
					
			function Table() {
				include "../../DBConfig.php";
				//Data
				$this->SetFont('Arial','',8);
				//Lebar kolom
				$w=array(0.8,3,1.5,2.25,2.25,3);
				$this->SetX(1);
				$sql = "SELECT
							ME.ExaminationName,
							TMD.Quantity,
							TMD.Price,
							TM.Cash,
							TM.Debit,
							(TMD.Quantity * TMD.Price) SubTotal,
							TMD.Remarks
						FROM
							transaction_medication TM
							JOIN transaction_medicationdetails TMD
								ON TM.MedicationID = TMD.MedicationID
							JOIN master_examination ME
								ON ME.ExaminationID = TMD.ExaminationID
						WHERE
							TM.MedicationID = ".$GLOBALS['ID'];
							
				if (! $result = mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}
				$RowNumber = 1;
				$GrandTotal = 0;
				$Cash = 0;
				$Debit = 0;
				while($row = mysql_fetch_array($result)) {
					$GrandTotal += $row['SubTotal'];
					$Cash = $row['Cash'];
					$Debit = $row['Debit'];
					$this->Cell($w[0],0.7,$RowNumber,1,0,'C');
					$this->Cell($w[1],0.7,$row['ExaminationName'],1,0,'L');
					$this->Cell($w[2],0.7,$row['Quantity'],1,0,'R');
					$this->Cell($w[3],0.7,number_format($row['Price'],2,".",","),1,0,'R');
					$this->Cell($w[4],0.7,number_format($row['SubTotal'],2,".",","),1,0,'R');
					$this->Cell($w[5],0.7,$row['Remarks'],1,0,'L');
					$this->Ln();
					$this->SetX(1);
					$RowNumber++;
				}
				$this->Cell(7.55,0.7,'Total',1,0,'L');
				$this->Cell(2.25,0.7,number_format($GrandTotal,2,".",","),1,0,'R');
				$this->Cell(3,0.7,'',1,0,'L');
				$this->Ln();
				$this->SetX(1);
					
				$this->Cell(7.55,0.7,'Cash',1,0,'L');
				$this->Cell(2.25,0.7,number_format($Cash,2,".",","),1,0,'R');
				$this->Cell(3,0.7,'',1,0,'L');
				
				$this->Ln();
				$this->SetX(1);
					
				$this->Cell(7.55,0.7,'Debit',1,0,'L');
				$this->Cell(2.25,0.7,number_format($Debit,2,".",","),1,0,'R');
				$this->Cell(3,0.7,'',1,0,'L');
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

		$pdf = new PDF_AutoPrint('P', 'cm', 'A5');
		$pdf->AliasNbPages();
		$pdf->SetTopMargin(0.5);
		$pdf->SetLeftMargin(1);
		$pdf->SetRightMargin(1);
		$pdf->AddPage();
		$pdf->Table();
		
		$filename = "Invoice.pdf";
		$pdf->AutoPrint(true);
		
		$pdf->Output($filename, "I");
		//$printcmd = "java -classpath C:/xampp/htdocs/Projects/KlinikGigi/Source/assets/lib/pdfbox-app-2.0.2.jar org.apache.pdfbox.PrintPDF -silentPrint -printerName 'EPSON ME 340 Series' C:/xampp/htdocs/Projects/KlinikGigi/Source/Transaction/Payment/Print.php";
		//exec($printcmd);
	}
?>
