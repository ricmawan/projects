<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);	
	include "../../GetPermission.php";
	//echo $_SERVER['REQUEST_URI'];	
	require_once '../../assets/lib/fpdf17/fpdf.php';
	if($cek==0) {
		$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
	} 
	else {
		class PDF extends FPDF {
			function Header() {
				$this->SetY(1);
				$this->SetFont('Arial','B',12);
				$this->SetTextColor(0, 0, 0);
				$this->Cell(0,0.5,"Daftar Absen Karyawan",0,0,'C');
				$this->Ln();
				$this->Cell(0,0.5,$_POST['hdnKlien'],0,0,'C');
				$this->Ln();
				$this->Cell(0,0.5,$_POST['hdnTanggal'],0,0,'C');
				$this->Ln();
				$this->Ln();
				
				//Judul Kolom
				$this->SetTextColor(0, 0, 0);
				$header=array('No','Nama Karyawan','No Psikogram','Pendidikan','Konsultan', 'Asisten', 'Layanan', 'Keterangan');	
				$w=array(1,4,4,3,6,4,4,2);
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
				$this->SetFont('Arial','',10);
				//Lebar kolom
				$w=array(1,4,4,3,6,4,4,2);
				$this->SetX(1);
				$RowNumber = 1;
				$RecordNew = $_POST['recordnew'];
				for($j=1;$j<=$RecordNew;$j++) {
					if($_POST['ddlPendidikan'.$j] == 1) $Pendidikan = "SD";
					else if($_POST['ddlPendidikan'.$j] == 2) $Pendidikan = "SMP";
					else if($_POST['ddlPendidikan'.$j] == 3) $Pendidikan = "SMA";
					else if($_POST['ddlPendidikan'.$j] == 4) $Pendidikan = "S1";
					else if($_POST['ddlPendidikan'.$j] == 5) $Pendidikan = "S2";
					else if($_POST['ddlPendidikan'.$j] == 6) $Pendidikan = "S3";
					else $Pendidikan = "";
					$this->Cell($w[0],0.7,$RowNumber,1,0,'C');
					$this->Cell($w[1],0.7,$_POST['txtNama'.$j],1,0,'C');
					$this->Cell($w[2],0.7,$_POST['txtPsikogram'.$j],1,0,'C');
					$this->Cell($w[3],0.7,$Pendidikan,1,0,'C');
					$sql = "SELECT
							Nama
						FROM 
							master_konsultan
						WHERE
							KonsultanID = ".mysql_real_escape_string($_POST['ddlKonsultan'.$j]);
					if (! $result = mysql_query($sql, $dbh)) {
						echo mysql_error();
						return 0;
					}
					$row = mysql_fetch_row($result);
					$this->Cell($w[4],0.7,$row[0],1,0,'C');
					$sql = "SELECT
							Nama
						FROM 
							master_asisten
						WHERE
							AsistenID = ".mysql_real_escape_string($_POST['ddlAsisten'.$j]);
					if (! $result = mysql_query($sql, $dbh)) {
						echo mysql_error();
						return 0;
					}
					$row = mysql_fetch_row($result);
					$this->Cell($w[5],0.7,$row[0],1,0,'C');

					$sql = "SELECT
							Jenis
						FROM 
							master_layanan
						WHERE
							LayananID = ".mysql_real_escape_string($_POST['ddlLayanan'.$j]);
					if (! $result = mysql_query($sql, $dbh)) {
						echo mysql_error();
						return 0;
					}
					$row = mysql_fetch_row($result);
					$this->Cell($w[6],0.7,$row[0],1,0,'C');
					$this->Cell($w[7],0.7,"",1,0,'C');
					$this->Ln();
					$this->SetX(1);
					$RowNumber++;
				}
			} 
		
			function Footer() {
				$this->SetY(-2);
		
				$this->SetFont('Arial','I',8);
				$this->Cell(0,1,$this->PageNo().'/{nb}',0,0,'C');
			}
		}	
	
		$pdf = new PDF('L', 'cm', 'A4');
		$pdf->AliasNbPages();
		$pdf->SetLeftMargin(1);
		$pdf->SetRightMargin(1);
		$pdf->AddPage();
		$pdf->Table();
		
		$filename = "Absen Karyawan ".$_POST['hdnKlien']." ".$_POST['hdnTanggal'].".pdf";
		$pdf->Output($filename, "D");
	}
?> 
