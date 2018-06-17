<?php
	require "../../assets/lib/escpos-php/autoload.php";
    use Mike42\Escpos\Printer;
    use Mike42\Escpos\EscposImage;
    use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
    use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
	if(ISSET($_POST['SaleID']) && ISSET($_POST['Payment'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$SaleID = mysqli_real_escape_string($dbh, $_POST['SaleID']);
		$Payment = mysqli_real_escape_string($dbh, $_POST['Payment']);
		$PaymentType = mysqli_real_escape_string($dbh, $_POST['PaymentType']);
		$PrintInvoice = mysqli_real_escape_string($dbh, $_POST['PrintInvoice']);
		$Message = "Pembayaran berhasil";
		$FinishFlag = 1;
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "CALL spUpdSalePayment(".$SaleID.", ".$Payment.", ".$PaymentType.", ".$FinishFlag.", '".$_SESSION['UserLogin']."')";
		if (! $result=mysqli_query($dbh, $sql)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/Sale/UpdatePayment.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($SaleID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		
		$row2=mysqli_fetch_array($result);
		
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);

		if($PrintInvoice == "true") {
		    /* Fill in your own connector here */
		    //$connector = new WindowsPrintConnector("smb://192.168.43.249/printer1");
		    $connector = new DummyPrintConnector();
		    $file =  "PrintInvoice.txt";  # nama file temporary yang akan dicetak
		    $handle = fopen($file, 'w');

		    $TransactionDate = date($_POST['TransactionDate']);
		    $Change = mysqli_real_escape_string($dbh, $_POST['Change']);
		    $SaleNumber = mysqli_real_escape_string($dbh, $_POST['SaleNumber']);
		    $PaymentMethod = mysqli_real_escape_string($dbh, $_POST['PaymentMethod']);
		    $dayName = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
		    $monthName = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");

		    $printer = new Printer($connector);
		   
		    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		    $printer -> setJustification(Printer::JUSTIFY_CENTER);
		    $printer -> text("TOKO MUDA\n");
		    $printer -> selectPrintMode(Printer::MODE_FONT_A);
		    $printer -> text("Jl. Raya Bojong\n");
		    $printer -> feed();
		    $printer -> setEmphasis(true);
		    $printer -> text("NOTA PENJUALAN\n");
		    $printer -> setEmphasis(false);
		    
		    $printer -> setJustification(Printer::JUSTIFY_LEFT);

		    $printer -> text($dayName[date("w", strtotime($TransactionDate))] . ", " . date("d", strtotime($TransactionDate)) . " " . $monthName[date("n", strtotime($TransactionDate)) - 1] . " " . date("Y", strtotime($TransactionDate)) . "/" . date("H") . ":" . date("i") . "\n");

		    $printer -> selectPrintMode(Printer::MODE_FONT_B);
		    $printer -> text(str_pad("", 39, "-") . "\n");
		    
		    $sql = "CALL spSelSaleDetails(".$SaleID.", '".$_SESSION['UserLogin']."')";
		    $FailedFlag = 0;

		    if (! $result = mysqli_query($dbh, $sql)) {
		        logEvent(mysqli_error($dbh), '/Transaction/Sale/Print.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		        $FailedFlag = 1;
		        $json_data = array(
		                        "FailedFlag" => $FailedFlag
		                    );
		    
		        echo json_encode($json_data);
		        return 0;
		    }
		    $Discount = 0;
		    $GrandTotal = 0;
		    $rowPrice = "";
		    while ($row = mysqli_fetch_array($result)) {
		        $rowPrice .= number_format($row['Quantity'],0,".",",") . " " . $row['UnitName'] . " @ " . number_format($row['SalePrice'],0,".",",");
		        if($row['Discount'] != 0) $rowPrice += " - " . number_format($row['Discount'],0,".",",");
		        $printer -> text(" " . $row['ItemName'] . "\n");
		        $printer -> text(" " . str_pad($rowPrice , 26, " ") . " ");
		        $printer -> text(str_pad(number_format(($row['SalePrice'] - $row['Discount']) * $row['Quantity'],0,".",","), 11, " ", STR_PAD_LEFT) . "\n");
		        //$printer -> text("  " . str_pad(number_format($row['Quantity'],0,".",","), 5, " ", STR_PAD_LEFT) . " " . str_pad($row['UnitName'], 6, " ") . " @ " . str_pad(number_format($row['SalePrice'],0,".",","), 10, " ") . " " . str_pad(number_format($row['SalePrice'] * $row['Quantity'],0,".",","), 11, " ", STR_PAD_LEFT) . "\n");
		        //$Discount += $row['Discount'] * $row['Quantity'];
		        $GrandTotal += ($row['SalePrice'] - $row['Discount']) * $row['Quantity'];
		        $rowPrice = "";
		    }

		    //$printer -> text("DISKON" . str_pad("", 22, " ") . str_pad("(" . number_format($Discount ,0,".",",") . ")" , 11, " ", STR_PAD_LEFT) . "\n");

		    $printer -> text(str_pad("", 39, "-") . "\n");

		    //$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		    $printer -> setEmphasis(true);
		    $printer -> text("PEMBAYARAN     : " . $PaymentMethod ."\n" );
		    $printer -> text("TOTAL          : " . str_pad(number_format($GrandTotal ,0,".",","), 16, " ", STR_PAD_LEFT) ."\n" );
		    $printer -> text("BAYAR          : " . str_pad(number_format($Payment ,0,".",","), 16, " ", STR_PAD_LEFT) ."\n" );
		    if($PaymentMethod == "Tunai") $printer -> text("KEMBALI        : " . str_pad(number_format($Change ,0,".",","), 16, " ", STR_PAD_LEFT) . "\n" );
		    else $printer -> text("KEKURANGAN     : " . str_pad(number_format($Change ,0,".",","), 16, " ", STR_PAD_LEFT) . "\n" );
		    $printer -> setEmphasis(false);

		    $printer -> text("Kasir : " . str_pad($_SESSION['UserLogin'] . ", ", 10, " ") . " No : " . str_pad($SaleNumber, 14, " ") . "\n");
		    $printer -> text(str_pad("", 39, "-") . "\n");
		    $printer -> setJustification(Printer::JUSTIFY_CENTER);
		    $printer -> text("KAMI TIDAK MELAYANI PENUKARAN BARANG\n");
		    $printer -> text("TANPA DISERTAI NOTA ASLI\n");
		    $printer -> text("TERIMAKASIH ATAS KUNJUNGAN ANDA\n\n");

		    $data = $connector -> getData();
		    fwrite($handle, $data);
		    fclose($handle);

		    /* Cut the receipt and open the cash drawer */
		    //$printer -> cut();
		    $printer -> pulse();
		    $printer -> close();
		}

		echo returnstate($row2['ID'], $row2['Message'], $row2['MessageDetail'], $row2['FailedFlag'], $row2['State']);
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
