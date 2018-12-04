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
    $transactionOrder = array("SALDO AWAL", "PENJUALAN", "RETUR PENJUALAN", "DP PENJUALAN", "PEMBAYARAN PIUTANG");

    $connector = new WindowsPrintConnector("smb:".$SHARED_PRINTER_ADDRESS);
    /*$connector = new DummyPrintConnector();
    $file =  "PrintInvoice.txt";  # nama file temporary yang akan dicetak
    $handle = fopen($file, 'w');*/

    $printer = new Printer($connector);
       
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

    $printer -> text($dayName[date("w", strtotime($TransactionDate))] . ", " . date("d", strtotime($TransactionDate)) . " " . $monthName[date("n", strtotime($TransactionDate)) - 1] . " " . date("Y", strtotime($TransactionDate)) . "/" . date("H") . ":" . date("i") . "\n");

    $printer -> selectPrintMode(Printer::MODE_FONT_B);
    $printer -> text(str_pad("", 39, "-") . "\n");

    $sql = "CALL spSelDailyReportPrint('".$_SESSION['UserLogin']."')";
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
    $i = 0;
    while ($row = mysqli_fetch_array($result)) {
        $printer -> text(" " . str_pad($transactionOrder[$i] , 18, " ") . " ");
        $printer -> text(str_pad(number_format($row['Amount'],0,".",","), 19, " ", STR_PAD_LEFT) . "\n");
        $GrandTotal += $row['Amount'];
        $i++;
    }

    $printer -> text(str_pad("", 39, "-") . "\n");

    $printer -> setEmphasis(true);
    $printer -> text("TOTAL          : " . str_pad($GrandTotal, 16, " ", STR_PAD_LEFT) ."\n" );

    $printer -> setEmphasis(false);

    $printer -> text("Kasir : " . str_pad($_SESSION['UserLogin'], 10, " ") . "\n");
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
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
?>
