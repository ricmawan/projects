<?php
	if(ISSET($_POST['ddlProject'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);	
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];	
		require_once '../../assets/lib/fpdf17/fpdf.php';
		global $ID;
		$ID = mysql_real_escape_string($_POST['ddlProject']);
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
		
		class PDF extends FPDF {
			function Header() {
				$this->SetY(1);
				$this->SetFont('Arial','B',12);
				$this->SetTextColor(0, 0, 0);
				$this->Cell(0,0.5,"LAPORAN PROYEK ".strtoupper($GLOBALS['ProjectName']),0,0,'C');
				$this->Ln();
				$this->Ln();
				//Judul Kolom
				$this->SetTextColor(0, 0, 0);
				$header = array('No', 'Tanggal', 'Nama', 'Bahan', 'QTY', 'Satuan', 'Harga', 'Debit', 'Kredit', 'Saldo', 'Keterangan');
				$w=array(0.8,1.8,3,3,0.8,1.5,3,3,3,4,4);
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
				$this->SetFont('Arial','',9);
				//Lebar kolom
				$w=array(0.8,1.8,3,3,0.8,1.5,3,3,3,4,4);
				$this->SetX(1);
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
									PP.ProjectID = ".$GLOBALS['ID']."
								UNION ALL
								SELECT
									OT.TransactionDate,
									OTD.Name,
									I.ItemName, 
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
									JOIN master_unit U
										ON U.UnitID = I.UnitID
								WHERE
									OT.ProjectID = ".$GLOBALS['ID']."
								UNION ALL
								SELECT
									RT.TransactionDate,
									'',
									I.ItemName, 
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
									JOIN master_unit U
										ON U.UnitID = I.UnitID
								WHERE
									RT.ProjectID = ".$GLOBALS['ID']."
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
									PT.ProjectID = ".$GLOBALS['ID']."
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
					$Debit = "-";
					if($row['Debit'] != "-") $Debit = number_format($row['Debit'],2,".",",");
					$Credit = "-";
					if($row['Credit'] != "-") $Credit = number_format($row['Credit'],2,".",",");
					$Balance = "-";
					if($row['Balance'] != "-") $Balance = number_format($row['Balance'],2,".",",");
					$Price = "-";
					if($row['Price'] != "") $Price = number_format($row['Price'],2,".",",");
					/*$height = 1;
					$nName=$this->WordWrap($row['Name'],$w[2]);
					$nItemName=$this->WordWrap($row['ItemName'],$w[3]);
					$nPrice=$this->WordWrap(number_format($row['Price'],2,".",","),$w[6]);
					$nDebit=$this->WordWrap($Debit,$w[7]);
					$nCredit=$this->WordWrap($Credit,$w[8]);
					$nBalance=$this->WordWrap($Balance,$w[9]);
					$nRemarks=$this->WordWrap($row['Remarks'],$w[10]);
					
					if($height < $nName) $height = $nName;
					if($height < $nItemName) $height = $nItemName;
					if($height < $nPrice) $height = $nPrice;
					if($height < $nDebit) $height = $nDebit;
					if($height < $nCredit) $height = $nCredit;
					if($height < $nBalance) $height = $nBalance;
					if($height < $nRemarks) $height = $nRemarks;*/
					
					$this->Cell($w[0],0.7,$RowNumber,1,0,'C');
					$this->Cell($w[1],0.7,$row['TransactionDate'],1,0,'C');
					$this->Cell($w[2],0.7,$row['Name'],1,0,'L');
					$this->Cell($w[3],0.7,$row['ItemName'],1,0,'L');
					$this->Cell($w[4],0.7,$row['Quantity'],1,0,'R');
					$this->Cell($w[5],0.7,$row['UnitName'],1,0,'C');
					$this->Cell($w[6],0.7,$Price,1,0,'R');
					$this->Cell($w[7],0.7,$Debit,1,0,'R');
					$this->Cell($w[8],0.7,$Credit,1,0,'R');
					$this->Cell($w[9],0.7,$Balance,1,0,'R');
					$this->Cell($w[10],0.7,$row['Remarks'],1,0,'L');
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
			function WordWrap(&$text, $maxwidth)
			{
				$text = trim($text);
				if ($text==='')
					return 0;
				$space = $this->GetStringWidth(' ');
				$lines = explode("\n", $text);
				$text = '';
				$count = 0;

				foreach ($lines as $line)
				{
					$words = preg_split('/ +/', $line);
					$width = 0;

					foreach ($words as $word)
					{
						$wordwidth = $this->GetStringWidth($word);
						if ($wordwidth > $maxwidth)
						{
							// Word is too long, we cut it
							for($i=0; $i<strlen($word); $i++)
							{
								$wordwidth = $this->GetStringWidth(substr($word, $i, 1));
								if($width + $wordwidth <= $maxwidth)
								{
									$width += $wordwidth;
									$text .= substr($word, $i, 1);
								}
								else
								{
									$width = $wordwidth;
									$text = rtrim($text)."\n".substr($word, $i, 1);
									$count++;
								}
							}
						}
						elseif($width + $wordwidth <= $maxwidth)
						{
							$width += $wordwidth + $space;
							$text .= $word.' ';
						}
						else
						{
							$width = $wordwidth + $space;
							$text = rtrim($text)."\n".$word.' ';
							$count++;
						}
					}
					$text = rtrim($text)."\n";
					$count++;
				}
				$text = rtrim($text);
				return $count;
			}
		}	

		$pdf = new PDF('L', 'cm', 'A4');
		$pdf->AliasNbPages();
		$pdf->SetLeftMargin(1);
		$pdf->SetRightMargin(1);
		$pdf->AddPage();
		$pdf->Table();
		
		$filename = "Laporan Proyek ".$ProjectName.".pdf";
		$pdf->Output($filename, "D");
	}
?> 
