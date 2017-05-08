<?php
	//http://www.lprng.com/RESOURCES/PPD/epson.htm
	if(isset($_POST['hdnBuyReturnID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ID = mysql_real_escape_string($_POST['hdnBuyReturnID']);
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
		
		$sql = "SELECT
					BR.BuyReturnNumber,
					MS.SupplierName,
					MS.City,
					DATE_FORMAT(BR.TransactionDate, '%d-%m-%Y') TransactionDate,
					BR.Remarks,
					BR.CreatedBy
				FROM
					transaction_buyreturn BR
					JOIN master_supplier MS
						ON MS.SupplierID = BR.SupplierID
				WHERE
					BR.BuyReturnID = ".$ID;
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysql_fetch_array($result);
		$BuyReturnNumber = $row['BuyReturnNumber'];
		$SupplierName = $row['SupplierName'];
		$City = $row['City'];
		$TransactionDate = $row['TransactionDate'];
		$Remarks = $row['Remarks'];
		$CreatedBy = $row['CreatedBy'];
		
		$Data  = $initialized ;
		//$Data .= "\n"; //16 
		$Data .= $bold1 . $double1 . $condensed1;
		//68 col
		$Data .= "RETUR BELI" . fnSpace(41);
		$Data .= "No : ". $BuyReturnNumber . $double0 . $bold0 ."\n";
		$Data .= "_________________________________________________________________________________________________________________________________________\n";
		$Data .= $underline1 . "                                                                                                                                         \n" . $underline0;
		$Data .= "   Kepada Yth.  " . $condensed0; //13
		$Data .= $SupplierName . $condensed1; //max 30
		$Data .= fnSpace(89 - strlen($SupplierName)) . "Tanggal : " .$TransactionDate. "\n"; //16 
		$Data .= fnSpace(16) . $condensed0 . $City."\n".$condensed0;
		//$Data .= fnSpace(27) . $bold1 . $double1 . $italic1 . "RETUR BELI\n" . $italic0 . $double0 . $bold0 . $condensed1;
		
		$Data .= "   Kami retur barang-barang sbb:\n" . $condensed1;
		//$Data .= fnSpace(88) . "No : ".$BuyReturnNumber."\n";
		$sql = "SELECT
					BRD.Quantity,
					MU.UnitName,
					CONCAT(MB.BrandName, ' ', MT.TypeName) ItemName,
					BRD.BatchNumber,
					BRD.BuyPrice,
					CASE
						WHEN BRD.IsPercentage = 1
						THEN (BRD.BuyPrice * BRD.Discount)/100
						ELSE BRD.Discount
					END DiscountAmount,
					CASE
						WHEN BRD.IsPercentage = 1
						THEN CONCAT('(', BRD.Discount, '%)')
						ELSE ''
					END Discount,
					CASE
						WHEN BRD.IsPercentage = 1
						THEN BRD.Quantity * (BRD.BuyPrice - ((BRD.BuyPrice * BRD.Discount)/100))
						ELSE BRD.Quantity * (BRD.BuyPrice - BRD.Discount)
					END AS Total
				FROM
					transaction_buyreturndetails BRD
					JOIN master_type MT
						ON MT.TypeID = BRD.TypeID
					JOIN master_unit MU
						ON MU.UnitID = MT.UnitID
					JOIN master_brand MB
						ON MB.BrandID = MT.BrandID
				WHERE
					BRD.BuyReturnID = ".$ID;
		
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
			//Qty
			$Data .= "| " . fnSpace(6 - strlen($row['Quantity'])) . $row['Quantity'] . " " . $row['UnitName'] . fnSpace(6 - strlen($row['UnitName'])) . " | ";
			//ItemName
			$Data .= $row['ItemName'] . fnSpace(42 - strlen($row['ItemName'])) . " | ";
			//BatchNumber
			$Data .= fnSpace(10 - strlen($row['BatchNumber'])) . $row['BatchNumber'] . " | ";
			//Harga Satuan
			$Data .= fnSpace(17 - strlen(number_format($row['BuyPrice'],2,".",","))) . number_format($row['BuyPrice'],2,".",",") . " | ";
			//Diskon
			$Data .= fnSpace(17 - strlen(number_format($row['DiscountAmount'], 2, ".", ",")) - strlen($row['Discount'])) . number_format($row['DiscountAmount'], 2, ".", ",") . $row['Discount'] . " | ";
			//Total
			$Data .= fnSpace(19 - strlen(number_format($row['Total'],2,".",","))) . number_format($row['Total'],2,".",",") . " |\n";
			$GrandTotal += $row['Total'];
		}
		//$Data .= "|    2,00 m lari  |  MAESTRO 646 XTC                                  |    522  |      155,000.00  |   15.500(10%)  |   100,201,500.00  |\n";
		$Data .= "|---------------------------------------------------------------------------------------------------------------------------------------|\n";
		$Data .= "   Catatan   : " . $Remarks . fnSpace(75 - strlen($Remarks)) ."Jumlah Retur          Rp. " . fnSpace(19 - strlen(number_format($GrandTotal,2,".",","))) . number_format($GrandTotal,2,".",",") . "\n";
		//$Data .= "   Terbilang : " . trim(strtoupper(Terbilang($GrandTotal))) . " RUPIAH\n";
		$Data .= "_________________________________________________________________________________________________________________________________________\n";
		$Data .= "   Penerima,". fnSpace(50) ."Checker,". fnSpace(50) ."Hormat Kami,\n\n\n";
		//$Data .= fnSpace(115) . fnSpace(ceil((22 - strlen($CreatedBy))/2)). $CreatedBy . Chr(12);
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
