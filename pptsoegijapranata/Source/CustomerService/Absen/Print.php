<?php
	//http://www.lprng.com/DISTRIB/RESOURCES/PPD/epson.htm
	if(isset($_POST['hdnId'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$tanggal = date('j');
		$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember');
		$bulan = $array_bulan[date('n')];
		$tahun = date('Y');
		if((INT)strlen($tanggal) == 1) $tanggal = "0".$tanggal;
		$date = $tanggal." ".$bulan." ".$tahun;
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
		$Id = $_POST['hdnId'];
		$RecordNew = $_POST['recordnew'];
		$No = "";
		
		//Id/id bulan tahun/ppt/bulan tahun
		for($i = 0; $i< (4- (INT)strlen($Id)); $i++) {
			$No .= "0";
		}
		$No = $No.$Id."/".$Id.date('m').date('Y')."/PPT/".date('m').date('Y');
		$spaceCount = 25;
		$space = "";
		for($i = 0; $i< $spaceCount; $i++) {
			$space .= " ";
		}
		$spaceCount2 = 20;
		$space2 = "";
		for($i = 0; $i< $spaceCount2; $i++) {
			$space2 .= " ";
		}
		$Data  = $initialized;
		$Data .= $condensed1;
		$Data .= "\n\n\n\n\n";
		$cetakhari = mktime(0,0,0,date("m"), date("d")+$i,date("Y"));
		$haribulan = date("d/m", $cetakhari);
		$sql = "SELECT
					LayananID,
					Kode,
					Jenis,
					Harga,
					Satuan
				FROM 
					master_layanan";
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$layanan = "";
		$countLayanan = 0;
		while($row = mysql_fetch_row($result)) {
			${"layanan".$row[0]} = 0;
			$kilat = 0;
			for($j=1;$j<=$RecordNew;$j++) {
				if($_POST['ddlLayanan'.$j] == $row[0]) {
					${"layanan".$row[0]} += 1;
				}
				//if($_POST['ddlProses'.$j] == 2) $kilat += 1;
			}
			if(${"layanan".$row[0]} > 0) {
				$countLayanan++;
				$layanan .= $row[2]." ".${"layanan".$row[0]}." orang\n".$space.$space2."  ";
			}
		}
		//if($kilat > 0) $layanan .= "Biaya Proses Kilat ".$kilat." orang\n";
		$nilai = 0;
		$guna = "";
		if($_POST['txtUangMuka'] != "0.00") {
			if($_POST['txtPembayaran'] != "0.00") {
				$temp = explode(".", $_POST['txtPembayaran']);
				$_POST['txtPembayaran'] = $temp[0];
				$nilai = str_replace(",", "", $_POST['txtPembayaran']);
				$guna = "Pembayaran";
			}
			else if($_POST['txtPembayaran'] == "0.00") {
				$temp = explode(".", $_POST['txtUangMuka']);
				$_POST['txtUangMuka'] = $temp[0];
				$nilai = str_replace(",", "", $_POST['txtUangMuka']);
				$guna = "Uang Muka";
			}
		}
		else if($_POST['txtPembayaran'] != "0.00") {
			$temp = explode(".", $_POST['txtPembayaran']);
			$_POST['txtPembayaran'] = $temp[0];
			$nilai = str_replace(",", "", $_POST['txtPembayaran']);
			$guna = "Pembayaran";
		}
		$Data .= $space.$space.$condensed."KWITANSI".$condensed1;
		if($countLayanan > 7) {
			$spasi = 46;
			for($tmbh=0; $tmbh<$spasi; $tmbh++) {
				$Data .= " ";
			}
			$Data .= "1/2\n\n";
		}
		else $Data .= "\n\n";
		$Data .= $space."No.   ".$No."\n\n";
		$Data .= $space."Telah diterima dari : ".$_POST['hdnKlien']."\n\n";
		$Data .= $space."Uang sebanyak       : #$italic1".strtoupper(terbilang($nilai))." RUPIAH#$italic0\n\n";
		$Data .= $space."Guna membayar       : ".$guna." ".$layanan."\n";
		$Data .= $space."Tanggal             : ".$_POST['hdnTanggal']."\n\n\n\n";
		
		if($countLayanan > 7) {
			if(($countLayanan - 8) > 0) $min = $countLayanan - 8;
			else $min = 0;
			$tambahbaris = 10 - $min;
			for($tmbh=0; $tmbh<$tambahbaris; $tmbh++) {
				$Data .= "\n";
			}
			$spasi = 104;
			for($tmbh=0; $tmbh<$spasi; $tmbh++) {
				$Data .= " ";
			}
			$Data .= "2/2\n\n";
		}
		$Data .= $space."   BANK CIMB NIAGA Semarang     ".$space."Semarang, ".$date."\n";
		$Data .= $space."   No. Rekening : 015-01-04414-003\n";
		$Data .= $space."   a.n YAYASAN SANDJOJO\n\n\n";
		$col = 82;
		$col -= $spaceCount;
		$col -= 23;
		$Data .= $space."   Terbilang   Rp.     ".number_format($nilai,0,",",".");
		$col -= strlen(number_format($nilai,0,",","."));
		$spasi = $col;
		for($tmbh=0; $tmbh<$spasi; $tmbh++) {
			$Data .= " ";
		}
		$Data .= $KABAG_USER."(Kabag Keuangan)";		
		fwrite($handle, $Data);
		fclose($handle);
		//copy($file, $PRINTER_IP.$SHARE_PRINTER_NAME);  # Lakukan cetak
		exec("lp -d epson ".$file);  # Lakukan cetak
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
