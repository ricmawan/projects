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

		$connector = new WindowsPrintConnector("smb:".$SHARED_PRINTER_ADDRESS);
		/*$connector = new DummyPrintConnector();
	    $file =  "PrintInvoice.txt";  # nama file temporary yang akan dicetak
	    $handle = fopen($file, 'w');*/

	    $printer = new Printer($connector);
		   
	    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
	    $printer -> setJustification(Printer::JUSTIFY_CENTER);
	    $printer -> text("TOKO MUDA\n");
	    $printer -> selectPrintMode(Printer::MODE_FONT_A);
	    $printer -> text("Jl. Raya Bojong\n");
	    $printer -> feed();
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
	    $printer -> text("TOTAL          : " . str_pad($TotalSale, 16, " ", STR_PAD_LEFT) ."\n" );
	    $printer -> text("TOTAL BAYAR    : " . str_pad($TotalPayment, 16, " ", STR_PAD_LEFT) ."\n" );
	    $printer -> text("KEKURANGAN     : " . str_pad(number_format($Credit ,0,".",","), 16, " ", STR_PAD_LEFT) ."\n" );

	    $printer -> setEmphasis(false);

	    $printer -> text("Kasir : " . str_pad($_SESSION['UserLogin'] . ", ", 10, " ") . " No : " . str_pad($TransactionNumber, 14, " ") . "\n");
	    $printer -> text(str_pad("", 39, "-") . "\n");
	    $printer -> setJustification(Printer::JUSTIFY_CENTER);
	    $printer -> text("KAMI TIDAK MELAYANI PENUKARAN BARANG\n");
	    $printer -> text("TANPA DISERTAI NOTA ASLI\n");
	    $printer -> text("TERIMAKASIH ATAS KUNJUNGAN ANDA\n\n");

	    /*$data = $connector -> getData();
	    fwrite($handle, $data);
	    fclose($handle);*/

	    /* Cut the receipt and open the cash drawer */
	    $printer -> cut();
	    $printer -> pulse();
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
?>
