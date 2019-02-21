<?php
	require "../../assets/lib/escpos-php/autoload.php";
    use Mike42\Escpos\Printer;
    use Mike42\Escpos\EscposImage;
    use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
    use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
	if(ISSET($_POST['TransactionNumber']) && ISSET($_POST['TotalSale'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$DPFlag = mysqli_real_escape_string($dbh, $_POST['DPFlag']);
		$TotalSale = mysqli_real_escape_string($dbh, $_POST['TotalSale']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['TransactionDate']);
		$CustomerName = mysqli_real_escape_string($dbh, $_POST['CustomerName']);
		$TransactionNumber = mysqli_real_escape_string($dbh, $_POST['TransactionNumber']);
		$TotalPayment = mysqli_real_escape_string($dbh, $_POST['TotalPayment']);
		$Amount = mysqli_real_escape_string($dbh, $_POST['Amount']);

		$dayName = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
		$monthName = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");

		$IPAddress = get_client_ip();

	    $sql = "CALL spSelPrinterList('".$IPAddress."', '".$_SESSION['UserLoginKasir']."')";
	    if (! $result=mysqli_query($dbh, $sql)) {
	        $Message = "Terjadi Kesalahan Sistem";
	        $MessageDetail = mysqli_error($dbh);
	        $FailedFlag = 1;
	        logEvent(mysqli_error($dbh), '/Transaction/Sale/UpdatePayment.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
	        echo returnstate($SaleID, $Message, $MessageDetail, $FailedFlag, $State);
	        return 0;
	    }
	    
	    $cek = mysqli_num_rows($result);
	    if($cek > 0) {
	        $row3=mysqli_fetch_array($result);
	        $SharedPrinterName = $row3['SharedPrinterName'];
	    }
	    else {
	        $SharedPrinterName = $SHARED_PRINTER_ADDRESS;
	    }

	    mysqli_free_result($result);
		mysqli_next_result($dbh);

	    $connector = new WindowsPrintConnector("smb:".$SharedPrinterName);
	    $printer = new Printer($connector);
	    $printer -> pulse();
		/*$connector = new DummyPrintConnector();
	    $file =  "PrintInvoice.txt";  # nama file temporary yang akan dicetak
	    $handle = fopen($file, 'w');*/

	    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
	    $printer -> setJustification(Printer::JUSTIFY_CENTER);
	    //$printer -> text("TOKO MUDA\n");
	    //$printer -> selectPrintMode(Printer::MODE_FONT_A);
	    //$printer -> text("Jl. Raya Bojong\n");
	    //$printer -> feed();
	    $printer -> setEmphasis(true);
	    $printer -> text("BUKTI PEMBAYARAN\n");
	    $printer -> setEmphasis(false);

     	$printer -> setJustification(Printer::JUSTIFY_LEFT);

	    $printer -> text($dayName[date("w", strtotime($TransactionDate))] . ", " . date("d", strtotime($TransactionDate)) . " " . $monthName[date("n", strtotime($TransactionDate)) - 1] . " " . date("Y", strtotime($TransactionDate)) . "/" . date("H") . ":" . date("i") . "\n");

	    $printer -> selectPrintMode(Printer::MODE_FONT_B);
	    $printer -> text(str_pad("", 39, "-") . "\n");

	    if($DPFlag == 1) {
	    	$printer -> text(" " . str_pad("DP" , 10, " "));
	    }
	    else {
	    	$printer -> text(" " . str_pad("PEMBAYARAN" , 10, " "));
	    }
	    $printer -> text(" " . str_pad(number_format($Amount,0,".",",") , 27, " ", STR_PAD_LEFT) . "\n");

	    $printer -> text(str_pad("", 39, "-") . "\n");

	    $Credit = str_replace(",", "", $TotalSale) - str_replace(",", "", $TotalPayment);
	    $printer -> setEmphasis(true);
	    $printer -> text("TOTAL          : " . str_pad($TotalSale, 22, " ", STR_PAD_LEFT) ."\n" );
	    $printer -> text("TOTAL BAYAR    : " . str_pad($TotalPayment, 22, " ", STR_PAD_LEFT) ."\n" );
	    $printer -> text("KEKURANGAN     : " . str_pad(number_format($Credit ,0,".",","), 22, " ", STR_PAD_LEFT) ."\n" );

	    $printer -> setEmphasis(false);

	    $printer -> text("Kasir : " . str_pad($_SESSION['UserLoginKasir'] . ", ", 10, " ") . " No : " . str_pad($TransactionNumber, 14, " ") . "\n");
	    $printer -> text(str_pad("", 39, "-") . "\n");
	    $printer -> setJustification(Printer::JUSTIFY_CENTER);
	    $printer -> text("KAMI TIDAK MELAYANI PENUKARAN BARANG\n");
	    $printer -> text("TANPA DISERTAI NOTA ASLI\n");
	    $printer -> text("TERIMAKASIH ATAS KUNJUNGAN ANDA\n\n");

	    //$data = $connector -> getData();
	    //fwrite($handle, $data);
	    //fclose($handle);

	    /* Cut the receipt and open the cash drawer */
	    $printer -> cut();
	    $printer -> close();

		$Message = "Pembayaran berhasil";
		$FinishFlag = 1;
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;

		echo returnstate($TransactionNumber, $Message, $MessageDetail, $FailedFlag, $State);
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

	function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
?>
