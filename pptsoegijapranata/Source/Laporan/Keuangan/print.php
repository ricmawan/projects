<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);	
	include "../../GetPermission.php";
	//echo $_SERVER['REQUEST_URI'];
	
		require_once '../../assets/lib/fpdf17/fpdf.php';

	
	$Content = "";
	$EditFlag = "";
	$DeleteFlag = "";
	if($cek==0) {
		$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
	} 

		class PDF_Print extends FPDF
		{
			//INITIALIZE HEADER
			function Header()
			{
				//Select Arial bold 15
				$this->SetFont('Helvetica','B',12);

					if(isset($_POST['hdnPostBack'])) {
						$_SESSION['startDate']=$_POST['startDate'];
						$_SESSION['endDate']=$_POST['endDate'];
					}

				
				
				//Framed title	
				$header='LAPORAN KEUANGAN';
				$judul='PPT SOEGIJAPRANATA';
				$judul1='Dari Tanggal '.date("d-M-Y",strtotime($_SESSION['startDate'])).' Sampai Tanggal '.date("d-M-Y",strtotime($_SESSION['endDate'])).'';

				
				
				
				$this->Cell(270,5,$header,0,0,'C');
				$this->Ln();
				$this->Cell(270,5,$judul,0,0,'C');
				$this->Ln();
				$this->Cell(270,5,$judul1,0,0,'C');
				

				//Line break
				$this->Ln(20);

				//Colors, line width and bold font
				$this->SetFillColor(255,255,255);
				$this->SetTextColor(0);
				$this->SetLineWidth(0.3);
				$this->SetFont('','B');

				//move to center
				//$this->Cell(50);
				
				//Column titles
				$header=array('No','Kode Client','Nama Client','Tanggal','JmlPsrt','Total','No Kwitansi','Keterangan');	

				//Header
				$w=array(30,30,80,20,20,30,45,30);
				for($i=0;$i<count($header);$i++){
					$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
				}
				$this->Ln();

				//move to center
				//$this->Cell(30);
				
				//bottom line
				
			}

			//INITIALIZE TABLE
			//Colored table
			function tabel_color()
			{
			
				//width table
				$w=array(30,30,80,20,20,30,45,30);
				
				//Color and font restoration
				$this->SetFillColor(255,255,255);
				$this->SetTextColor(0);
				$this->SetFont('');
				$this->SetLineWidth(0.3);
				
				//Data
				$fill=false;
				if(isset($_POST['hdnPostBack'])) {
					$_SESSION['startDate']=$_POST['startDate'];
					$_SESSION['endDate']=$_POST['endDate'];
				}
				
				//no,kode client,nama,kode psikotest,tanggal transaksi,jumlah peserta,jumlah,no kwitansi,lunas keterangan
				$sql = "SELECT
							MK.KlienID,
							MK.Nama,
							TC.TransaksiID,
							DATE_FORMAT(TC.Tanggal, '%d-%m-%Y'),
							COUNT(TRC.TransaksiID),
							TC.Pembayaran,
							TC.UangMuka
						
						FROM
							transaksi_customerservice TC
							
						JOIN 
							transaksi_rincicustomerservice TRC
							
						ON 
							TRC.TransaksiID = TC.TransaksiID
							AND TC.Tanggal BETWEEN '".$_SESSION['startDate']."' AND '".$_SESSION['endDate']."'
						JOIN 
							master_klien MK
						ON
							MK.KlienID = TC.KlienID
							
						GROUP BY TC.TransaksiID";
											 
					if (! $result=mysql_query($sql)) {
							echo mysql_error();
							return 0;			
					}
													
					$RowNumber = 0;
					$total= 0;
					$peserta=0;
					$No = "";
					$Keterangan="";
								
						  
					while($row2 = mysql_fetch_array($result)) { 
					//move to center
					//$this->Cell(50);
					$RowNumber++;
					$jumlah=$row2[5]+$row2[6];
					if ($row2[6]<=$row2[5]){
						$Keterangan="Lunas";
					}else{
						$Keterangan="Belum Lunas";
					}

						$this->Cell($w[0],7,$RowNumber,1,0,'C',$fill); 
						$this->Cell($w[1],7,$row2[0],1,0,'L',$fill); 
						$this->Cell($w[2],7,$row2[1],1,0,'L',$fill); 
						$this->Cell($w[3],7,$row2[3],1,0,'L',$fill); 
						$this->Cell($w[4],7,$row2[4],1,0,'C',$fill); 
						$this->Cell($w[5],7,number_format(($jumlah),2,",","."),1,0,'R',$fill); 
						$this->Cell($w[6],7,$No.$row2[2]."/".$row2[2].date('m').date('Y')."/PPT/".date('m').date('Y'),1,0,'L',$fill);
						$this->Cell($w[7],7,$Keterangan,1,0,'C',$fill); 
						$this->Ln();
						
						$total=$total+$jumlah;
						$peserta=$peserta+$row2[4];
					}
				
				
				$this->Ln(10);
				
				//Data
				$fill=false;
				
				//move to center
				//$this->Cell(50);
				$this->SetFont('','B');
				$this->Cell(100,7,"Grand Total : ",1,0,'L',$fill); 
				$this->Cell($w[0],7,number_format(($total),2,",","."),1,0,'R',$fill); 
				$this->Ln();
				
				//$this->Cell(50);
				$this->SetFont('','B');
				$this->Cell(100,7,"Jumlah Peserta :",1,0,'L',$fill); 
				$this->Cell($w[0],7,$peserta,1,0,'R',$fill); 
				$this->Ln(10);
				date_default_timezone_set('Asia/Jakarta');
				$curDate=date('Y')."-".date('F')."-".date('d');
				
				
		
							
				//$this->Cell(50);
				$this->Cell(150,7,"Semarang,".date("d F Y")."",0,0,'L'); 
				$this->Ln();
				//$this->Cell(50);
				$this->Cell(150,7,"PPT Soegijapranata",0,0,'L'); 
				$this->Ln();
				//$this->Cell(50);
				$this->Cell(150,7,"Direktur, ",0,0,'L');
				$this->Ln(20);
				//$this->Cell(50);
				$this->Cell(150,7,"Ferdinand Hindiarto, S.Psi.,M.Si",0,0,'L');
				
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
			
			function Footer() {
					$this->SetY(-2);
					$this->SetFont('Times','I',8);
					$this->Cell(0,1,$this->PageNo().'/{nb}',0,0,'C');
			}

		}

		/* for saving as PDF
		$pdf=new PDF_Print();
		//$title='Nota Transaksi';
		//$pdf->SetTitle($title);
		//$pdf->SetAuthor('Admin');

		$pdf->SetFont('Arial','',10);
		$pdf->AddPage('L');
		//memanggil fungsi table
		$pdf->tabel_color();
		$pdf->Output("Nota Grosir.pdf","D");
		*/


		/* for autoprint using add on */
		$pdf=new PDF_Print();
		//$title='Sales Invoice';
		//$pdf->SetTitle($title);
		$pdf->SetFont('Arial','',10);
		$pdf->AddPage('L');
		$pdf->tabel_color();

		//Open the print dialog
		$pdf->Output("Keuangan.pdf","D");
		$pdf->AutoPrint(true);

?> 