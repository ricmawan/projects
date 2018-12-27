<?php
    require "./assets/lib/escpos-php/autoload.php";
    use Mike42\Escpos\Printer;
    use Mike42\Escpos\EscposImage;
    use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
    use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
    $RequestedPath = "$_SERVER[REQUEST_URI]";
    $file = basename($RequestedPath);
    $RequestedPath = str_replace($file, "", $RequestedPath);
    include "./DBConfig.php";
    include "./GetSession.php";
    $TransactionDate = date('Y\-m\-d');
    
    $dayName = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
    $monthName = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");
    $transactionOrder = array("SALDO AWAL", "PENJUALAN TUNAI", "PEMESANAN TUNAI", "RETUR PENJUALAN", "DP PENJUALAN", "PEMBAYARAN PIUTANG");

    $IPAddress = get_client_ip();

    $sql = "CALL spSelPrinterList('".$IPAddress."', '".$_SESSION['UserLoginKasir']."')";
    if (! $result=mysqli_query($dbh, $sql)) {
        $Message = "Terjadi Kesalahan Sistem";
        $MessageDetail = mysqli_error($dbh);
        $FailedFlag = 1;
        logEvent(mysqli_error($dbh), '/PrintDailyReport.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
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
    /*$printer -> text("TOKO MUDA\n");
    $printer -> selectPrintMode(Printer::MODE_FONT_A);
    $printer -> text("Jl. Raya Bojong\n");
    $printer -> feed();*/
    $printer -> setEmphasis(true);
    $printer -> text("LAPORAN HARIAN\n");
    $printer -> setEmphasis(false);

    $printer -> setJustification(Printer::JUSTIFY_LEFT);

    $printer -> selectPrintMode(Printer::MODE_FONT_B);
    $printer -> text($dayName[date("w", strtotime($TransactionDate))] . ", " . date("d", strtotime($TransactionDate)) . " " . $monthName[date("n", strtotime($TransactionDate)) - 1] . " " . date("Y", strtotime($TransactionDate)) . "/" . date("H") . ":" . date("i") . "\n");

    $printer -> text(str_pad("", 39, "-") . "\n");

    $sql = "CALL spSelDailyReportPrint('".$_SESSION['UserLoginKasir']."')";
    $FailedFlag = 0;

    if (! $result = mysqli_query($dbh, $sql)) {
        logEvent(mysqli_error($dbh), '/Transaction/Sale/Print.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
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
    $i = 0;
    while ($row = mysqli_fetch_array($result)) {
        $printer -> text(" " . str_pad($transactionOrder[$i] , 18, " ") . " ");
        $printer -> text(str_pad(number_format($row['Amount'],0,".",","), 19, " ", STR_PAD_LEFT) . "\n");
        $GrandTotal += $row['Amount'];
        $i++;
    }

    $printer -> text(str_pad("", 39, "-") . "\n");

    $printer -> setEmphasis(true);
    $printer -> text("TOTAL          : " . str_pad(number_format($GrandTotal,0,".",","), 22, " ", STR_PAD_LEFT) ."\n" );

    $printer -> setEmphasis(false);

    $printer -> text("Kasir          : " . str_pad($_SESSION['UserLoginKasir'], 22, " ") . "\n");Kasir
    /*$data = $connector -> getData();
    fwrite($handle, $data);
    fclose($handle);*/

    /* Cut the receipt and open the cash drawer */
    $printer -> cut();
    $printer -> close();

    $Message = "Pembayaran berhasil";
    $FinishFlag = 1;
    $MessageDetail = "";
    $FailedFlag = 0;
    $State = 1;

    echo returnstate("", $Message, $MessageDetail, $FailedFlag, $State);
    
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
