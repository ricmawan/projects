<?php
	require "../../assets/lib/escpos-php/autoload.php";
    use Mike42\Escpos\Printer;
    use Mike42\Escpos\EscposImage;
    use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
    use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
	//http://www.lprng.com/RESOURCES/PPD/epson.htm
	if(isset($_POST['BookingDetailsID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";

		$connector = new WindowsPrintConnector("smb:".$SHARED_PRINTER_ADDRESS);
		//$connector = new DummyPrintConnector();
	    
	    $BookingDetailsID = $_POST['BookingDetailsID'];
		$BookingID = mysqli_real_escape_string($dbh, $_POST['BookingID']);		
		$tanggal = date('d') . "-" . date('m') . "-" . date('Y');
		/*$tmpdir = sys_get_temp_dir();   # ambil direktori temporary untuk simpan file.
		$file =  tempnam($tmpdir, 'ctk');  # nama file temporary yang akan dicetak*/
		//$file =  "PrintShipment.txt";  # nama file temporary yang akan dicetak
		//$handle = fopen($file, 'w');
		$Message = "Nota Berhasil Dicetak";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		// 137 column 30 row
		/*$bold1 = Chr(27) . Chr(69);
		$bold0 = Chr(27) . Chr(70);
		$double1 = Chr(27) . Chr(87) . Chr(49);
		$double0 = Chr(27) . Chr(87) . Chr(48);
		$initialized = Chr(27) . Chr(64). Chr(27) . Chr(67) . Chr(33);
		$condensed1 = chr(15);
		$condensed0 = chr(18);
		$underline1 = Chr(27) . Chr(45) . Chr(49);
		$underline0 = Chr(27) . Chr(45) . Chr(48);
		$italic1 = Chr(27) . Chr(52);
		$italic0 = Chr(27) . Chr(53);*/
		
		$sql = "CALL spSelBookingHeader(".$BookingID.", '".$_SESSION['UserLoginKasir']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/Booking/PrintShipment.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($BookingID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}

		$row = mysqli_fetch_array($result);
		$BookingNumber = $row['BookingNumber'];
		$CustomerName = $row['CustomerName'];
		$City = $row['City'];
		$TransactionDate = $row['TransactionDate'];
		$Address = $row['Address'];
		$Telephone = $row['Telephone'];
		$CreatedBy = $row['CreatedBy'];
		$Remarks = "";
		mysqli_free_result($result);
		mysqli_next_result($dbh);

		$printer = new Printer($connector);
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		$printer -> text("SURAT JALAN");
		$printer -> text(str_pad("", 40, " "));
		$printer -> text("No : ". $BookingNumber ."\n");
		$printer -> text(str_pad("", 137, "_") . "\n");
		$printer -> text(str_pad("", 73, " ") . "Kepada Yth.  ");
		$printer -> selectPrintMode(Printer::MODE_FONT_A);
		$printer -> text($CustomerName ."\n");
		$printer -> text(str_pad("", 86, " ") . $Address);
		//if($Address != "") $printer -> text(str_pad("", 86, " ") . $Address);
		$printer -> text("\n   Tanggal : " . $TransactionDate);
		$printer -> text(str_pad("", 63, " ") . $City . "\n");
		$printer -> text("   Kasir   : ");
		$printer -> text(str_pad($CreatedBy, 73, " ") . "Ph " . $Telephone . "\n");
		
		$sql = "CALL spSelBookingDetailsPrint('(".implode(",", $BookingDetailsID).")', '".$_SESSION['UserLoginKasir']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/Booking/PrintShipment.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($BookingID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}

		$printer -> text("|" . str_pad("", 135, "-") . "|\n");
		$printer -> text("|      Qty      |                Nama Barang                              |                          Keterangan                         |\n");
		$printer -> text("|---------------|---------------------------------------------------------|-------------------------------------------------------------|\n");
		
		while($row = mysqli_fetch_array($result)) {
			if(strpos($row['Quantity'], ".")) $Quantity = number_format(round($row['Quantity'], 2),2,".",",");	    		
		    else $Quantity = number_format($row['Quantity'],0,".",",");
			$printer -> text("| " . str_pad($Quantity, 6, " ", STR_PAD_LEFT) . " ");
			$printer -> text(str_pad($row['UnitName'], 6, " ") . " | ");
			$printer -> text(str_pad(htmlspecialchars_decode($row['ItemName'], ENT_QUOTES), 55, " ") . " | ");
			$printer -> text(str_pad($Remarks, 59, " ") . " |\n");
		}
		
		$printer -> text("|" . str_pad("", 135, "-") . "|\n");
		$printer -> text("   Catatan   : " . $Remarks . "\n");
		$printer -> text(str_pad("", 15, " ") . "Barang yang sudah dibeli tidak dapat ditukar/dikembalikan\n");
		$printer -> text(str_pad("", 137, "_") . "\n");
		$printer -> text("   Penerima," . str_pad("", 50, " ") ."Checker,". str_pad("", 50, " ") ."Hormat Kami,\n\n\n");
		$printer -> text("_______________" . str_pad("", 44, " "));
		$printer -> text("_______________" . str_pad("", 46, " "));
		$printer -> text(str_pad($CreatedBy, 12, " ", STR_PAD_BOTH));
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		
		//copy($file, $SHARED_PRINTER_ADDRESS); 
		//exec("lp -d epson ".$file);  # Lakukan cetak
		//unlink($file);

		//$data = $connector -> getData();
	    //fwrite($handle, $data);
	    //fclose($handle);
	    $printer -> pulse();
		$printer -> close();
		echo returnstate($BookingID, $Message, $MessageDetail, $FailedFlag, $State);
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
?>
