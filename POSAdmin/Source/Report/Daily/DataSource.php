<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";
	
	if(ISSET($_POST['UserID']))
	{
		$UserID = $_POST['UserID'];
		if($_POST['TransactionDate'] == "") {
			$TransactionDate = date("Y-m-d");
		}
		else {
			$TransactionDate = explode('-', mysql_real_escape_string($_POST['TransactionDate']));
			$_POST['TransactionDate'] = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]"; 
			$TransactionDate = $_POST['TransactionDate'];
		}
		
		$sql = "CALL spSelDailyReport(".$UserID.", '".$TransactionDate."', '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/Daily/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		$GrandTotal = 0;
		$Data = "";
		$Kasir = "";
		$UnionLevel = 0;
		$TransactionNumber = "";
		$TransactionName = "";
		$UnionTotal = 0;
		$SubTotal = 0;
		$GrandTotal = 0;
		$TotalKasir = 0;
		$Payment = 0;
		$DiscountTotal = 0;
		$UnionDiscountTotal = 0;
		$KasirDiscountTotal = 0;
		$GrandDiscountTotal = 0;
		while ($row = mysqli_fetch_array($result)) {
			if($Kasir != $row['UserName']) {
				if($Kasir != "") {
					if($SubTotal > 0 || ($UnionLevel == 3 && $SubTotal < 0)) {
						if($UnionLevel == 1 || $UnionLevel == 2) {
							$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
							$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
							$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";	
							$UnionDiscountTotal += $row['DiscountTotal'];
							$KasirDiscountTotal += $row['DiscountTotal'];
							$GrandDiscountTotal += $row['DiscountTotal'];
						}
						else if($UnionLevel == 4 || $UnionLevel == 5) {
							$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
							$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
							$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";	
						}
						else $Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";
						$SubTotal = 0;
					} 
					if($UnionTotal > 0 || ($UnionLevel == 3 && $UnionTotal < 0)) {
						$Data .= "<tr class='UnionTotal' ><td class='TransactionName'>Total ". $TransactionName ."</td><td colspan=4></td><td class='text-right TransactionName'>". number_format($UnionTotal - $UnionDiscountTotal,0,".",",") ."</td></tr>";
						$UnionTotal = 0;
						$UnionDiscountTotal = 0;
						$Data .= "<tr><td colspan=6>&nbsp;</td></tr>";
					}
					$Data .= "<tr class='TotalKasir'><td class='TransactionName'>Total ". $Kasir ."</td><td colspan=4></td><td class='text-right TransactionName'>". number_format($TotalKasir - $KasirDiscountTotal,0,".",",") ."</td></tr>";
					$TotalKasir = 0;
					$KasirDiscountTotal = 0;
					$Data .= "<tr><td colspan=6>&nbsp;</td></tr>";
				}
				$Data .= "<tr><td colspan=6 class='Cashier'>".$row['UserName']."</td></tr>";
			}
			if($row['UnionLevel'] == '0') {
				$Data .= "<tr class='UnionTotal'><td class='TransactionName'>". $row['TransactionName'] ."</td><td colspan=4></td><td class='text-right'>". number_format($row['SubTotal'],0,".",",") ."</td></tr>";
				$TotalKasir += $row['SubTotal'];
				$GrandTotal += $row['SubTotal'];
			}
			else if($row['UnionLevel'] > 0 && $row['UnionLevel'] < 4) {
				if($UnionLevel != $row['UnionLevel']) {
					if($row['UnionLevel'] > 1) {
						if($SubTotal > 0) {
							if($UnionLevel == 1 || $UnionLevel == 2) {
								$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";	
								$UnionDiscountTotal += $row['DiscountTotal'];
								$KasirDiscountTotal += $row['DiscountTotal'];
								$GrandDiscountTotal += $row['DiscountTotal'];
							}
							else $Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";
							$SubTotal = 0;
						}
						if($UnionTotal > 0) {
							$Data .= "<tr class='UnionTotal'><td class='TransactionName'>Total ". $TransactionName ."</td><td colspan=4></td><td class='text-right TransactionName'>". number_format($UnionTotal - $UnionDiscountTotal,0,".",",") ."</td></tr>";
							$UnionTotal = 0;
							$UnionDiscountTotal = 0;
							$Data .= "<tr><td colspan=6>&nbsp;</td></tr>";
						}
					}
					$Data .= "<tr><td colspan=6 class='TransactionName'>". $row['TransactionName'] ."</td></tr>";
				}
				if($TransactionNumber != $row['TransactionNumber']) {
					if($UnionLevel == $row['UnionLevel']) {
						if($SubTotal > 0) { 
							if($UnionLevel == 1 || $UnionLevel == 2) {
								$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";	
								$UnionDiscountTotal += $DiscountTotal;
								$KasirDiscountTotal += $DiscountTotal;
								$GrandDiscountTotal += $DiscountTotal;
							}
							else $Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";
							$SubTotal = 0;
						}
					}
					$Data .= "<tr><td></td><td>". $row['CustomerName'] . " (". $row['TransactionNumber'] .")  ".date("H", strtotime($row['CreatedDate'])) . ":" . date("i", strtotime($row['CreatedDate'])) . " </td><td colspan=4></td></tr>";					
				}
				$Data .= "<tr><td></td><td>". $row['ItemName'] ."</td><td class='text-right'>". number_format($row['SalePrice'],0,".",",") ."</td>";
				if(strpos($row['Quantity'], ".")) $Quantity = number_format(round($row['Quantity'], 2),2,".",",");	    		
		    	else $Quantity = number_format($row['Quantity'],0,".",",");
				$Data .= "<td class='text-right'>". $Quantity . " ". $row['UnitName'] ."</td>";
				$Data .= "<td class='text-right'>(". number_format($row['Discount'],0,".",",") . ")</td><td class='text-right'>". number_format($row['SubTotal'],0,".",",") ."</td></tr>";
				$UnionTotal += $row['SubTotal'];
				$SubTotal += $row['SubTotal'];
				$TotalKasir += $row['SubTotal'];
				$GrandTotal += $row['SubTotal'];
				
				//$UnionTotal -= $row['DiscountTotal'];
				//$SubTotal -= $row['DiscountTotal'];
				//$TotalKasir -= $row['DiscountTotal'];
				//$GrandTotal -= $row['DiscountTotal'];
			}
			else if($row['UnionLevel'] > 3 && $row['UnionLevel'] < 6) {
				if($UnionLevel != $row['UnionLevel']) {
					if($row['UnionLevel'] > 1) {
						if($SubTotal > 0 || ($UnionLevel == 3 && $SubTotal < 0)) { 
							if($UnionLevel == 1 || $UnionLevel == 2) {
								$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";	
								$UnionDiscountTotal += $DiscountTotal;
								$KasirDiscountTotal += $DiscountTotal;
								$GrandDiscountTotal += $DiscountTotal;
							}
							else if($UnionLevel == 4 || $UnionLevel == 5) {
								$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";	
							}
							else $Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";
							
							if($UnionLevel > 3) {
								$Data .= "<tr><td colspan=4></td><td>DP</td><td class='text-right'>". number_format($Payment,0,".",",") ."</td></tr>";
								$UnionTotal += $Payment;
								$TotalKasir += $Payment;
								$GrandTotal += $Payment;
							}
							$SubTotal = 0;
						}
						if($UnionTotal > 0 || ($UnionLevel == 3 && $UnionTotal < 0)) {
							$Data .= "<tr class='UnionTotal'><td class='TransactionName'>Total ". $TransactionName ."</td><td colspan=4></td><td class='text-right TransactionName'>". number_format($UnionTotal - $UnionDiscountTotal,0,".",",") ."</td></tr>";
							$UnionTotal = 0;
							$UnionDiscountTotal = 0;
							$Data .= "<tr><td colspan=6>&nbsp;</td></tr>";
						}
					}
					$Data .= "<tr><td colspan=6 class='TransactionName'>". $row['TransactionName'] ."</td></tr>";
				}
				if($TransactionNumber != $row['TransactionNumber']) {
					if($UnionLevel == $row['UnionLevel'] && $SubTotal > 0) {
						if($UnionLevel == 1 || $UnionLevel == 2) {
							$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
							$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
							$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";	
							$UnionDiscountTotal += $DiscountTotal;
							$KasirDiscountTotal += $DiscountTotal;
							$GrandDiscountTotal += $DiscountTotal;
						}
						else if($UnionLevel == 4 || $UnionLevel == 5) {
							$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
							$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
							$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";	
						}
						else $Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";
						
						$UnionTotal += $Payment;
						$TotalKasir += $Payment;
						$GrandTotal += $Payment;
						$Data .= "<tr><td colspan=4></td><td>DP</td><td class='text-right'>". number_format($Payment,0,".",",") ."</td></tr>";
						$SubTotal = 0;
					}
					$Data .= "<tr><td></td><td>". $row['CustomerName'] . " (". $row['TransactionNumber'] .") ".date("H", strtotime($row['CreatedDate'])) . ":" . date("i", strtotime($row['CreatedDate'])) . " </td><td colspan=4></td></tr>";
				}
				$Data .= "<tr><td></td><td>". $row['ItemName'] ."</td><td class='text-right'>". number_format($row['SalePrice'],0,".",",") ."</td>";
				if(strpos($row['Quantity'], ".")) $Quantity = number_format(round($row['Quantity'], 2),2,".",",");	    		
		    	else $Quantity = number_format($row['Quantity'],0,".",",");
				$Data .= "<td class='text-right'>". $Quantity . " ". $row['UnitName'] ."</td>";
				$Data .= "<td class='text-right'>(". number_format($row['Discount'],0,".",",") . ")</td><td class='text-right'>". number_format($row['SubTotal'],0,".",",") ."</td></tr>";
				$SubTotal += $row['SubTotal'];
			}
			else {
				if($UnionLevel != $row['UnionLevel']) {
					if($row['UnionLevel'] > 1) {
						if($SubTotal > 0) { 
							if($UnionLevel == 1 || $UnionLevel == 2) {
								$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";	
								$UnionDiscountTotal += $DiscountTotal;
								$KasirDiscountTotal += $DiscountTotal;
								$GrandDiscountTotal += $DiscountTotal;
							}
							else if($UnionLevel == 4 || $UnionLevel == 5) {
								$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
								$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";
							}
							else $Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";

							$SubTotal = 0;
							if($UnionLevel > 3 && $UnionLevel < 6) {
								$UnionTotal += $Payment;
								$TotalKasir += $Payment;
								$GrandTotal += $Payment;
								$Data .= "<tr><td colspan=4></td><td>DP</td><td class='text-right'>". number_format($Payment,0,".",",") ."</td></tr>";
							}
						}
						if($UnionTotal > 0) {
							$Data .= "<tr class='UnionTotal'><td class='TransactionName'>Total ". $TransactionName ."</td><td colspan=4></td><td class='text-right TransactionName'>". number_format($UnionTotal - $UnionDiscountTotal,0,".",",") ."</td></tr>";
							$UnionTotal = 0;
							$UnionDiscountTotal = 0;
							$Data .= "<tr><td colspan=6>&nbsp;</td></tr>";
						}
					}
					$Data .= "<tr><td colspan=6 class='TransactionName'>". $row['TransactionName'] ."</td></tr>";
				}
				$Data .= "<tr><td></td><td>". $row['CustomerName'] . " (". $row['TransactionNumber'] .")  ".date("H", strtotime($row['CreatedDate'])) . ":" . date("i", strtotime($row['CreatedDate'])) . " </td><td colspan=3></td><td class='text-right'>". number_format($row['SubTotal'],0,".",",") ."</td></tr>";
				$UnionTotal += $row['SubTotal'];
				$TotalKasir += $row['SubTotal'];
				$GrandTotal += $row['SubTotal'];
			}
			$Payment = $row['Payment'];
			$UnionLevel = $row['UnionLevel'];
			$Kasir = $row['UserName'];
			$TransactionNumber = $row['TransactionNumber'];
			$TransactionName = $row['TransactionName'];
			$DiscountTotal = $row['DiscountTotal'];
		}
		if($SubTotal > 0 || ($UnionLevel == 3 && $SubTotal < 0)) {
			if($UnionLevel == 1 || $UnionLevel == 2) {
				$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
				$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
				$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";	
				$UnionDiscountTotal += $DiscountTotal;
				$KasirDiscountTotal += $DiscountTotal;
				$GrandDiscountTotal += $DiscountTotal;
			}
			else if($UnionLevel == 4 || $UnionLevel == 5) {
				$Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";	
				$Data .= "<tr><td colspan=4></td><td>Diskon</td><td class='text-right'>". number_format($DiscountTotal,0,".",",") ."</td></tr>";	
				$Data .= "<tr><td colspan=4></td><td>Total</td><td class='text-right'>". number_format($SubTotal - $DiscountTotal,0,".",",") ."</td></tr>";
			}
			else $Data .= "<tr><td colspan=4></td><td>Sub Total</td><td class='text-right'>". number_format($SubTotal,0,".",",") ."</td></tr>";

			$SubTotal = 0;
		}

		if($Payment > 0) {
			$Data .= "<tr><td colspan=4></td><td>DP</td><td class='text-right'>". number_format($Payment,0,".",",") ."</td></tr>";
			$UnionTotal += $Payment;
			$TotalKasir += $Payment;
			$GrandTotal += $Payment;
		}

		if($UnionTotal > 0 || $UnionTotal < 0 || ($UnionLevel == 3 && $UnionTotal < 0)) $Data .= "<tr class='UnionTotal'><td class='TransactionName'>Total ". $TransactionName ."</td><td colspan=4></td><td class='text-right TransactionName'>". number_format($UnionTotal - $UnionDiscountTotal,0,".",",") ."</td></tr>";

		if($TotalKasir > 0 || $TotalKasir < 0) $Data .= "<tr class='TotalKasir'><td class='TransactionName'>Total ". $Kasir ."</td><td colspan=4></td><td class='text-right TransactionName'>". number_format($TotalKasir - $KasirDiscountTotal,0,".",",") ."</td></tr>";

		if($GrandTotal > 0 || $GrandTotal < 0) $Data .= "<tr><td colspan=6>&nbsp;</td></tr><tr class='GrandTotal'><td>Grand Total</td><td colspan=4></td><td class='text-right'>". number_format($GrandTotal - $GrandDiscountTotal,0,".",",") ."</td></tr>";

		if($Data == "") echo $Data = "<tr><td colspan=6>Data tidak ditemukan</td></tr>";
		else echo $Data;
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);

		
	}
?>