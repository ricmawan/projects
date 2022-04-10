<?php
	require "../../assets/lib/escpos-php/autoload.php";
    use Mike42\Escpos\Printer;
    use Mike42\Escpos\EscposImage;
    use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
    use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
	//http://www.lprng.com/RESOURCES/PPD/epson.htm
	//if(isset($_POST['SaleDetailsID'])) {
		
		include "../../DBConfig.php";
		$connector = new WindowsPrintConnector("smb:".$SHIPMENT_PRINTER);
		//$connector = new DummyPrintConnector();
	    
	    
		$printer = new Printer($connector);
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		$printer -> text("-------------------------------------------------");
		//$printer -> text(str_pad("", 40, " "));
		$printer -> setJustification(Printer::JUSTIFY_LEFT);
		$printer -> selectPrintMode(Printer::MODE_FONT_A);
		$printer -> text("Mode A\n");
		$printer -> text("-----------------------------------------------------------------------------------------------------------------------------------\n");
		
		$printer -> selectPrintMode(Printer::MODE_FONT_B);
		$printer -> text("Mode B\n");
		$printer -> text("---------------------------------------------------------------------------------------------------------------------------------------------------------------------\n");
		
		//copy($file, $SHARED_PRINTER_ADDRESS); 
		//exec("lp -d epson ".$file);  # Lakukan cetak
		//unlink($file);

		//$data = $connector -> getData();
	    //fwrite($handle, $data);
	    //fclose($handle);
	    //$printer -> pulse();
		$printer -> feedForm();
		$printer -> close();
		//echo returnstate($SaleID, $Message, $MessageDetail, $FailedFlag, $State);
	//}
	
	
?>