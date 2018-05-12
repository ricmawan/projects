<?php
    require "../../assets/lib/escpos-php/autoload.php";
    use Mike42\Escpos\Printer;
    use Mike42\Escpos\EscposImage;
    use Mike42\Escpos\PrintConnectors\FilePrintConnector;
    /* Fill in your own connector here */
    $connector = new FilePrintConnector("php://stdout");

    $ID = $_GET['ID'];
    /* Information for the receipt */
    $items = array(
        new item("Example item #1", "4.00"),
        new item("Another thing", "3.50"),
        new item("Something else", "1.00"),
        new item("A final item", "4.45"),
    );
    $subtotal = new item('Subtotal', '12.95');
    $tax = new item('A local tax', '1.30');
    $total = new item('Total', '14.25', true);
    /* Date is kept the same for testing */
    // $date = date('l jS \of F Y h:i:s A');
    $date = "Monday 6th of April 2015 02:56:25 PM";
    /* Start the printer */
    //$logo = EscposImage::load("resources/escpos-php.png", false);
    $printer = new Printer($connector);
    /* Print top logo */
    //$printer -> setJustification(Printer::JUSTIFY_CENTER);
    //$printer -> graphics($logo);
    /* Name of shop */
    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    $printer -> text("TOKO MUDA.\n");
    $printer -> selectPrintMode();
    $printer -> text("Jl. Raya Bojong\n");
    $printer -> feed();
    /* Title of receipt */
    $printer -> setEmphasis(true);
    $printer -> text("NOTA PENJUALAN\n");
    $printer -> setEmphasis(false);
    /* Items */
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    //$printer -> setEmphasis(true);
    //$printer -> text(new item('', 'Rp'));
    //$printer -> setEmphasis(false);
    //foreach ($items as $item) {
        //$printer -> text($item);
    //}

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

    while ($row = mysqli_fetch_array($result)) {
        $printer -> text($row['ItemName']);
        $printer -> text(new item());
    }

    $printer -> setEmphasis(true);
    $printer -> text($subtotal);
    $printer -> setEmphasis(false);
    $printer -> feed();
    /* Tax and total */
    $printer -> text($tax);
    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    $printer -> text($total);
    $printer -> selectPrintMode();
    /* Footer */
    $printer -> feed(2);
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text("Thank you for shopping at ExampleMart\n");
    $printer -> text("For trading hours, please visit example.com\n");
    $printer -> feed(2);
    $printer -> text($date . "\n");
    /* Cut the receipt and open the cash drawer */
    $printer -> cut();
    $printer -> pulse();
    $printer -> close();

    /* A wrapper to do organise item names & prices into columns */
    class item
    {
        private $name;
        private $price;
        private $dollarSign;
        public function __construct($name = '', $price = '', $dollarSign = false)
        {
            $this -> name = $name;
            $this -> price = $price;
            $this -> dollarSign = $dollarSign;
        }
        
        public function __toString()
        {
            $rightCols = 10;
            $leftCols = 38;
            if ($this -> dollarSign) {
                $leftCols = $leftCols / 2 - $rightCols / 2;
            }
            $left = str_pad($this -> name, $leftCols) ;
            
            $sign = ($this -> dollarSign ? '$ ' : '');
            $right = str_pad($sign . $this -> price, $rightCols, ' ', STR_PAD_LEFT);
            return "$left$right\n";
        }
    }
?>