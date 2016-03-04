<?php
	//http://www.lprng.com/RESOURCES/PPD/epson.htm
	if(isset($_POST['hdnOutgoingID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$tanggal = date('d') . "-" . date('m') . "-" . date('Y');
		/*$tmpdir = sys_get_temp_dir();   # ambil direktori temporary untuk simpan file.
		$file =  tempnam($tmpdir, 'ctk');  # nama file temporary yang akan dicetak*/
		$file =  "Print.txt";  # nama file temporary yang akan dicetak
		$handle = fopen($file, 'w');
		$condensed = Chr(27) . Chr(33) . Chr(8);
		$bold1 = Chr(27) . Chr(69);
		$bold0 = Chr(27) . Chr(70);
		$initialized = chr(27).chr(64);
		$condensed1 = chr(15);
		$condensed0 = chr(18);
		$underline1 = chr(27) . chr(45) . chr(49);
		$underline0 = chr(27) . chr(45) . chr(48);
		$italic1 = chr(27) . chr(52);
		$italic0 = chr(27) . chr(53);
		
		$double = chr(27) . chr(87) . chr(49);
		$Id = $_POST['hdnId'];
		$RecordNew = $_POST['recordnew'];
		$No = "";
		$Data  = $initialized.$double;
		$Data .= $condensed1;
		$spaceCount = 80;
		$space = "";
		for($i = 0; $i< $spaceCount; $i++) {
			$space .= " ";
		}
		$Data .= $space . "Tgl : " .$tanggal. "\n";
		$spaceCount = 60;
		$space = "";
		for($i = 0; $i< $spaceCount; $i++) {
			$space .= " ";
		}
		$Data .= $space ."Kepada Yth.    ";
		$Data .= "KARYA INDAH WALPAPER\n";
		$spaceCount = 64;
		$space = "";
		for($i = 0; $i< $spaceCount; $i++) {
			$space .= " ";
		}
		$Data .= $space . "TITI BUMI BARAT NO .40\n";
		$Data .= $space . "(GODEAN KM 4,5)\n";
		
		$Data .= "Nopo  :\n";
		$Data .= "Sales : TR\n";
		$Data .= "Tgl Jatuh Tempo : " .$tanggal;
		
		$spaceCount = 35;
		$space = "";
		for($i = 0; $i< $spaceCount; $i++) {
			$space .= " ";
		}
		
		
		$Data .= $space.$condensed."INVOICE".$condensed1;
		
		
		
		fwrite($handle, $Data);
		fclose($handle);
		//copy($file, $PRINTER_IP.$SHARE_PRINTER_NAME);  # Lakukan cetak
		//exec("lp -d epson ".$file);  # Lakukan cetak
		//unlink($file);
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
