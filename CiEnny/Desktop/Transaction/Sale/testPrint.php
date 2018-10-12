<?php
	require "../../assets/lib/escpos-php/autoload.php";
    use Mike42\Escpos\Printer;
    use Mike42\Escpos\EscposImage;
    use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
    /* Fill in your own connector here */
    $connector = new WindowsPrintConnector("smb://192.168.43.26/printer3");
	$printer = new Printer($connector);
	$printer -> text("sipp\n");
	$printer -> cut();
    $printer -> pulse();
    $printer -> close();
?>