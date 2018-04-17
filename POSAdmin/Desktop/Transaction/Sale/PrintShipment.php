<?php
	//http://www.lprng.com/RESOURCES/PPD/epson.htm
	if(isset($_POST['SaleDetailsID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$SaleDetailsID = $_POST['SaleDetailsID'];
		$SaleID = mysqli_real_escape_string($dbh, $_POST['SaleID']);		
		$tanggal = date('d') . "-" . date('m') . "-" . date('Y');
		/*$tmpdir = sys_get_temp_dir();   # ambil direktori temporary untuk simpan file.
		$file =  tempnam($tmpdir, 'ctk');  # nama file temporary yang akan dicetak*/
		$file =  "PrintShipment.txt";  # nama file temporary yang akan dicetak
		$handle = fopen($file, 'w');
		$Message = "Nota Berhasil Dicetak";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		// 137 column 30 row
		$bold1 = Chr(27) . Chr(69);
		$bold0 = Chr(27) . Chr(70);
		$double1 = Chr(27) . Chr(87) . Chr(49);
		$double0 = Chr(27) . Chr(87) . Chr(48);
		$initialized = Chr(27) . Chr(64). Chr(27) . Chr(67) . Chr(33);
		$condensed1 = chr(15);
		$condensed0 = chr(18);
		$underline1 = Chr(27) . Chr(45) . Chr(49);
		$underline0 = Chr(27) . Chr(45) . Chr(48);
		$italic1 = Chr(27) . Chr(52);
		$italic0 = Chr(27) . Chr(53);
		
		$sql = "CALL spSelSaleHeader(".$SaleID.", '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/Sale/PrintShipment.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($SaleID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}

		$row = mysqli_fetch_array($result);
		$SaleNumber = $row['SaleNumber'];
		$CustomerName = $row['CustomerName'];
		$City = $row['City'];
		$TransactionDate = $row['TransactionDate'];
		$Address = $row['Address'];
		$Telephone = $row['Telephone'];
		$CreatedBy = $row['CreatedBy'];
		$Remarks = "";
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		
		$Data  = $initialized ;
		//$Data .= "\n"; //16 
		$Data .= $bold1 . $double1 . $condensed1;
		//68 col
		$Data .= "SURAT JALAN" . fnSpace(40);
		$Data .= "No : ". $SaleNumber . $double0 . $bold0 ."\n";
		$Data .= "_________________________________________________________________________________________________________________________________________\n";
		$Data .= $underline1 . "                                                                                                                                         \n" . $underline0;
		
		$Data .= fnSpace(73) ."Kepada Yth.  ".$condensed0; //13
		$Data .= $CustomerName."\n" . $condensed1; //max 30
		//$Data .= fnSpace(86) . $condensed0 . $Address1 . $condensed1;
		if($Address != "") $Data .= fnSpace(86) . $condensed0 . $Address . $condensed1;
		$Data .= "\n   Tanggal : " .$TransactionDate; //16
		//$Data .= fnSpace(19) . $bold1 . $double1 ."SURAT JALAN". $double0 . $bold0;
		$Data .= fnSpace(63) . $condensed0 . $City."\n".$condensed1;
		$Data .= "   Kasir   : ".$CreatedBy;
		$Data .= fnSpace(73 - strlen($CreatedBy)) . $condensed0 . "Ph " . $Telephone . "\n" . $condensed1;
		
		$sql = "CALL spSelSaleDetailsPrint('(".implode(",", $SaleDetailsID).")', '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/Sale/PrintShipment.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($SaleID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}

		//$GrandTotal = 0;
		$Data .= "|---------------------------------------------------------------------------------------------------------------------------------------|\n";
		$Data .= "|      Qty      |                Nama Barang                              |                          Keterangan                         |\n";
		$Data .= "|---------------|---------------------------------------------------------|-------------------------------------------------------------|\n";
		
		while($row = mysqli_fetch_array($result)) {
			//Qty
			$Data .= "| " . fnSpace(6 - strlen($row['Quantity'])) . $row['Quantity']  . " " . fnSpace(6) . " | ";//$row['UnitName'] . fnSpace(6 - strlen($row['UnitName'])) . " | ";
			//ItemName
			$Data .= $row['ItemName'] . fnSpace(55 - strlen($row['ItemName'])) . " | ";
			//BatchNumber
			//$Data .= fnSpace(10 - strlen($row['BatchNumber'])) . $row['BatchNumber'] . " | ";
			//Remarks
			//$Data .= $row['Remarks'] . fnSpace(59 - strlen($row['Remarks'])) . " |\n";
			$Data .= $Remarks . fnSpace(59 - strlen($Remarks)) . " |\n";
			//Harga Satuan
			//$Data .= fnSpace(14 - strlen(number_format($row['SalePrice'],2,".",","))) . number_format($row['SalePrice'],2,".",",") . "  |  ";
			//Diskon
			//$Data .= fnSpace(9 - strlen(number_format($row['DiscountAmount'], 0, ".", ",")) - strlen($row['Discount'])) . number_format($row['DiscountAmount'], 0, ".", ",") . "(" . $row['Discount'] . "%)  |  ";
			//Total
			//$Data .= fnSpace(15 - strlen(number_format($row['Total'],2,".",","))) . number_format($row['Total'],2,".",",") . "  |\n";
			//$GrandTotal += $row['Total'];
		}
		//$Data .= "|    2,00 m lari  |  MAESTRO 646 XTC                                  |    522  |      155,000.00  |   15.500(10%)  |   100,201,500.00  |\n";
		$Data .= "|---------------------------------------------------------------------------------------------------------------------------------------|\n";
		$Data .= "   Catatan   : " . $Remarks . "\n";
		$Data .= fnSpace(15) . $bold1 . "Barang yang sudah dibeli tidak dapat ditukar/dikembalikan\n";
		//$Data .= "   Kredit    : Rp. " . number_format($GrandTotal,2,".",",") . "\n";
		//$Data .= "   Terbilang : " . strtoupper(Terbilang($GrandTotal)) . " RUPIAH\n";
		$Data .= "_________________________________________________________________________________________________________________________________________\n";
		$Data .= "   Penerima,". fnSpace(50) ."Checker,". fnSpace(50) ."Hormat Kami,\n\n\n";
		//$Data .= fnSpace(115) . fnSpace(ceil((22 - strlen($_SESSION['UserLogin']))/2)). $_SESSION['UserLogin'] ."\n";
		$Data .= "_______________                                            _______________                                             ". fnSpace(ceil((15 - strlen($CreatedBy))/2)). $CreatedBy . Chr(12);
		//$Data .=  $bold1 . "Barang yang sudah dibeli tidak dapat ditukar/dikembalikan" . Chr(12);
		fwrite($handle, $Data);
		fclose($handle);
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		//copy($file, $SHARED_PRINTER_ADDRESS); 
		//exec("lp -d epson ".$file);  # Lakukan cetak
		//unlink($file);
		echo returnstate($SaleID, $Message, $MessageDetail, $FailedFlag, $State);
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
	function fnSpace($loop) {
		$space = "";
		for($i = 0; $i< $loop; $i++) {
			$space .= " ";
		}
		return $space;
	}
?>
