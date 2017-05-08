<?php
	//http://www.lprng.com/RESOURCES/PPD/epson.htm
	if(isset($_POST['hdnOutgoingID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ID = mysql_real_escape_string($_POST['hdnOutgoingID']);
		$tanggal = date('d') . "-" . date('m') . "-" . date('Y');
		/*$tmpdir = sys_get_temp_dir();   # ambil direktori temporary untuk simpan file.
		$file =  tempnam($tmpdir, 'ctk');  # nama file temporary yang akan dicetak*/
		$file =  "PrintInvoice.txt";  # nama file temporary yang akan dicetak
		$handle = fopen($file, 'w');
		$Message = "Nota Berhasil Dicetak";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		// 137 column 30 row
		$bold1 = Chr(27) . Chr(69);
		$bold0 = Chr(27) . Chr(70);
		$double1 = Chr(27) . Chr(87) . Chr(49);
		$double0 = Chr(27) . Chr(87) . Chr(48);
		$initialized = Chr(27) . Chr(64). Chr(27) . Chr(67) . Chr(33);
		$condensed1 = chr(15);
		$condensed0 = chr(18);
		$underline1 = Chr(27) . Chr(45) . Chr(49);
		$underline0 = Chr(27) . Chr(45) . Chr(48);
		$italic1 = Chr(27) . Chr(52);
		$italic0 = Chr(27) . Chr(53);
		$draft = Chr(27) . Chr(120) . Chr(48);
		
		$sql = "SELECT
					OT.OutgoingNumber,
					MC.CustomerName,
					MC.City,
					DATE_FORMAT(OT.TransactionDate, '%d-%m-%Y') TransactionDate,
					UPPER(MS.Alias) Alias,
					OT.Remarks,
					OT.CreatedBy
				FROM
					transaction_outgoing OT
					JOIN master_customer MC
						ON MC.CustomerID = OT.CustomerID
					JOIN master_sales MS
						ON MS.SalesID = OT.SalesID
				WHERE
					OT.OutgoingID = ".$ID;
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysql_fetch_array($result);
		$OutgoingNumber = $row['OutgoingNumber'];
		$CustomerName = $row['CustomerName'];
		$City = $row['City'];
		$TransactionDate = $row['TransactionDate'];
		$Alias = $row['Alias'];
		$Remarks = $row['Remarks'];
		$CreatedBy = $row['CreatedBy'];
		
		$Data  = $initialized ;
		//$Data .= "\n"; //16 
		$Data .= $bold1 . $double1 . $condensed1;
		//68 col
		$Data .= "INVOICE" . fnSpace(44);
		$Data .= "No : ". $OutgoingNumber . $double0 . $bold0 ."\n";
		$Data .= "_________________________________________________________________________________________________________________________________________\n";
		$Data .= $underline1 . "                                                                                                                                         \n" . $underline0;
		//$Data .= "¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯\n";
		$Data .= $condensed1."   Tanggal : " .$TransactionDate; //16 
		$Data .= fnSpace(54) ."Kepada Yth.  ".$condensed0; //13
		$Data .= $CustomerName."\n"; //max 30
		$Data .= $condensed1."   Sales   : ".$Alias;
		$Data .= fnSpace(77-strlen($Alias)) . $condensed0 . $City."\n".$condensed1;
		
		//$Data .= fnSpace(31 - strlen($Alias)) . $bold1 . $double1 ."INVOICE". $double0 . $bold0 . $condensed1 ."\n";
		//$Data .= "   Tgl Jatuh Tempo : " .$TransactionDate . "\n"; //28
		//$Data .= fnSpace(22) . $bold1 . $double1 ."INVOICE\n". $double0 . $bold0 . $condensed1;
		
		//$Data .= "   Kami kirim pesanan anda dalam keadaan baik, barang-barang sbb:\n";
		$sql = "SELECT
					TOD.Quantity,
					MU.UnitName,
					CONCAT(MB.BrandName, ' ', MT.TypeName) ItemName,
					TOD.BatchNumber,
					TOD.SalePrice,
					CASE
						WHEN TOD.IsPercentage = 1
						THEN (TOD.SalePrice * TOD.Discount)/100
						ELSE TOD.Discount
					END DiscountAmount,
					CASE
						WHEN TOD.IsPercentage = 1 AND TOD.Discount <> 0
						THEN CONCAT('(', TOD.Discount, '%)')
						ELSE ''
					END Discount,
					CASE
						WHEN TOD.IsPercentage = 1
						THEN TOD.Quantity * (TOD.SalePrice - ((TOD.SalePrice * TOD.Discount)/100))
						ELSE TOD.Quantity * (TOD.SalePrice - TOD.Discount)
					END Total
				FROM
					transaction_outgoingdetails TOD
					JOIN master_type MT
						ON MT.TypeID = TOD.TypeID
					JOIN master_unit MU
						ON MU.UnitID = MT.UnitID
					JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					TOD.OutgoingID = ".$ID;
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$GrandTotal = 0;	
		$Data .= "|---------------------------------------------------------------------------------------------------------------------------------------|\n";
		$Data .= "|      Qty      |                Nama Barang                 |     Lot    |    Harga Satuan   |       Diskon      |     Sub Total       |\n";
		$Data .= "|---------------|--------------------------------------------|------------|-------------------|-------------------|---------------------|\n";
		
		while($row=mysql_fetch_array($result)) {
			/*//Qty
			$Data .= "| " . fnSpace(6 - strlen($row['Quantity'])) . $row['Quantity'] . " " . $row['UnitName'] . fnSpace(6 - strlen($row['UnitName'])) . " | ";
			//ItemName
			$Data .= $row['ItemName'] . fnSpace(42 - strlen($row['ItemName'])) . " | ";
			//BatchNumber
			$Data .= fnSpace(10 - strlen($row['BatchNumber'])) . $row['BatchNumber'] . " | ";
			//Harga Satuan
			$Data .= fnSpace(17 - strlen(number_format((100/110) * $row['SalePrice'],2,".",","))) . number_format((100/110) * $row['SalePrice'],2,".",",") . " | ";
			//Diskon
			$Data .= fnSpace(17 - strlen(number_format((100/110) * $row['DiscountAmount'], 2, ".", ",")) - strlen($row['Discount'])) . number_format((100/110) * $row['DiscountAmount'], 2, ".", ",") . $row['Discount'] . " | ";
			//Total
			$Data .= fnSpace(19 - strlen(number_format((100/110) * $row['Total'],2,".",","))) . number_format((100/110) * $row['Total'],2,".",",") . " |\n";
			$GrandTotal += (100/110) * $row['Total'];*/ //DENGAN PPN
			
			//TANPA PPN
			$Data .= "| " . fnSpace(6 - strlen($row['Quantity'])) . $row['Quantity'] . " " . $row['UnitName'] . fnSpace(6 - strlen($row['UnitName'])) . " | ";
			//ItemName
			$Data .= $row['ItemName'] . fnSpace(42 - strlen($row['ItemName'])) . " | ";
			//BatchNumber
			$Data .= fnSpace(10 - strlen($row['BatchNumber'])) . $row['BatchNumber'] . " | ";
			//Harga Satuan
			$Data .= fnSpace(17 - strlen(number_format($row['SalePrice'],2,".",","))) . number_format($row['SalePrice'],2,".",",") . " | ";
			//Diskon
			$Data .= fnSpace(17 - strlen(number_format($row['DiscountAmount'], 2, ".", ",")) - strlen($row['Discount'])) . number_format($row['DiscountAmount'], 2, ".", ",") . $row['Discount'] . " | ";
			//Total
			$Data .= fnSpace(19 - strlen(number_format($row['Total'],2,".",","))) . number_format($row['Total'],2,".",",") . " |\n";
			$GrandTotal += $row['Total'];
		}
		
		$Data .= "|---------------------------------------------------------------------------------------------------------------------------------------|\n";
		$Data .= "   Catatan   : " . $Remarks . fnSpace(75 - strlen($Remarks)) ."Grand Total           Rp. " . fnSpace(19 - strlen(number_format($GrandTotal,2,".",","))) . number_format($GrandTotal,2,".",",") . "\n";
		$PPN = 0;
		$PPN = $GrandTotal * (10/100);
		//$GrandTotal = (float)($GrandTotal + ($GrandTotal * (10/100)));
		$GrandTotal = (float)($GrandTotal);
		
		$Data .= fnSpace(15) . $bold1 . "Barang yang sudah dibeli tidak dapat ditukar/dikembalikan\n" . $bold0;
		//$Data .= fnSpace(18) . "PPN (10%)             Rp. " . fnSpace(19 - strlen(number_format($PPN,2,".",","))) . number_format($PPN,2,".",",") . "\n";
		//$Data .= $condensed1 . "                                                                                          -----------------------------------------------\n";
		//$Data .= fnSpace(90) . "Grand Total           Rp. " . fnSpace(19 - strlen(number_format($GrandTotal,2,".",","))) . number_format($GrandTotal,2,".",",") . "\n";
		$Data .= "_________________________________________________________________________________________________________________________________________\n";
		$Data .= "   Penerima,". fnSpace(50) ."Checker,". fnSpace(50) ."Hormat Kami,\n\n\n";
		$Data .= "_______________                                            _______________                                             _______________". Chr(12);
		fwrite($handle, $Data);
		fclose($handle);
		copy($file, $SHARED_PRINTER_ADDRESS); 
		//exec("lp -d epson ".$file);  # Lakukan cetak
		//unlink($file);
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
	}
	
	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
	function fnSpace($loop) {
		$space = "";
		for($i = 0; $i< $loop; $i++) {
			$space .= " ";
		}
		return $space;
	}
	function terbilang($angka)
	{
		// pastikan kita hanya berususan dengan tipe data numeric
		$angka = (float)$angka;

		// array bilangan 
		// sepuluh dan sebelas merupakan special karena awalan 'se'
		$bilangan = array(
			'',
			'satu',
			'dua',
			'tiga',
			'empat',
			'lima',
			'enam',
			'tujuh',
			'delapan',
			'sembilan',
			'sepuluh',
			'sebelas'
		);

		// pencocokan dimulai dari satuan angka terkecil
		if ($angka < 12) {
			// mapping angka ke index array $bilangan
			return $bilangan[$angka];
		} else if ($angka < 20) {
			// bilangan 'belasan'
			// misal 18 maka 18 - 10 = 8
			return $bilangan[$angka - 10] . ' belas';
		} else if ($angka < 100) {
			// bilangan 'puluhan'
			// misal 27 maka 27 / 10 = 2.7 (integer => 2) 'dua'
			// untuk mendapatkan sisa bagi gunakan modulus
			// 27 mod 10 = 7 'tujuh'
			$hasil_bagi = (int)($angka / 10);
			$hasil_mod = $angka % 10;
			return trim(sprintf('%s puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
		} else if ($angka < 200) {
			// bilangan 'seratusan' (itulah indonesia knp tidak satu ratus saja? :))
			// misal 151 maka 151 = 100 = 51 (hasil berupa 'puluhan')
			// daripada menulis ulang rutin kode puluhan maka gunakan
			// saja fungsi rekursif dengan memanggil fungsi terbilang(51)
			return sprintf('seratus %s', terbilang($angka - 100));
		} else if ($angka < 1000) {
			// bilangan 'ratusan'
			// misal 467 maka 467 / 100 = 4,67 (integer => 4) 'empat'
			// sisanya 467 mod 100 = 67 (berupa puluhan jadi gunakan rekursif terbilang(67))
			$hasil_bagi = (int)($angka / 100);
			$hasil_mod = $angka % 100;
			return trim(sprintf('%s ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
		} else if ($angka < 2000) {
			// bilangan 'seribuan'
			// misal 1250 maka 1250 - 1000 = 250 (ratusan)
			// gunakan rekursif terbilang(250)
			return trim(sprintf('seribu %s', terbilang($angka - 1000)));
		} else if ($angka < 1000000) {
			// bilangan 'ribuan' (sampai ratusan ribu
			$hasil_bagi = (int)($angka / 1000); // karena hasilnya bisa ratusan jadi langsung digunakan rekursif
			$hasil_mod = $angka % 1000;
			
			return sprintf('%s ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod));
		} else if ($angka < 1000000000) {
			// bilangan 'jutaan' (sampai ratusan juta)
			// 'satu puluh' => SALAH
			// 'satu ratus' => SALAH
			// 'satu juta' => BENAR 
			// @#$%^ WT*

			// hasil bagi bisa satuan, belasan, ratusan jadi langsung kita gunakan rekursif
			$hasil_bagi = (int)($angka / 1000000);
			$hasil_mod = $angka % 1000000;
			return trim(sprintf('%s juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
		} else if ($angka < 1000000000000) {
			// bilangan 'milyaran'
			$hasil_bagi = (int)($angka / 1000000000);
			// karena batas maksimum integer untuk 32bit sistem adalah 2147483647
			// maka kita gunakan fmod agar dapat menghandle angka yang lebih besar
			$hasil_mod = fmod($angka, 1000000000);
			return trim(sprintf('%s milyar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
		} else if ($angka < 1000000000000000) {
			// bilangan 'triliun'
			$hasil_bagi = $angka / 1000000000000;
			$hasil_mod = fmod($angka, 1000000000000);
			return trim(sprintf('%s triliun %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
		} else {
			return 'Wow...';
		}
	}
?>
