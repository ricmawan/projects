<?php
	if(isset($_POST['SaleID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$IsEdit = 1;
		$SaleID = mysql_real_escape_string($_POST['SaleID']);
		if($SaleID != 0) {
			$sql = "SELECT
						TSD.SaleDetailsID,
						TSD.MenuListID,
						ML.MenuName,
						TSD.Quantity,
						TSD.Price,
						TSD.IsPercentage,
						CASE
							WHEN TSD.IsPercentage = 1
							THEN CONCAT(TSD.Discount, '%')
							ELSE TSD.Discount
						END Discount,
						CASE
							WHEN TSD.IsPercentage = 1
							THEN (TSD.Price - ((TSD.Price * TSD.Discount) / 100)) * TSD.Quantity
							ELSE (TSD.Price - TSD.Discount) * TSD.Quantity
						END SubTotal
					FROM
						transaction_saledetails TSD
						JOIN master_menulist ML
							ON ML.MenuListID = TSD.MenuListID
					WHERE
						TSD.SaleID = $SaleID";
						
			if(!$result = mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}
			$RowNumber = 0;
			$SubTotal = 0;
			$rowCount = mysql_num_rows($result);
			if($rowCount > 0) {
				while($row = mysql_fetch_array($result)) {
					$RowNumber++;
					$SubTotal += $row['SubTotal'];
					echo "<tr>
							<td style='width:34px;border: solid 1px black;'>$RowNumber</td>
							<td style='width:310px;border: solid 1px black;'>".$row['MenuName']."</td>
							<td style='width:80px;border: solid 1px black;text-align:right;'>".$row['Quantity']."</td>
							<td style='width:170px;border: solid 1px black;text-align:right;'>".number_format($row['Price'],2,".",",")."</td>
							<td style='width:195px;border: solid 1px black;text-align:right;'>";
								if($row['IsPercentage'] == true) echo $row['Discount']; else echo number_format($row['Discount'],2,".",",");
							echo "</td>
							<td style='width:190px;border: solid 1px black;text-align:right;'>".number_format($row['SubTotal'],2,".",",")."</td>
						</tr>";
				}
				/*echo "<tr>
						<td colspan=5 style='border: solid 1px black;'>Sub Total</td>
						<td style='border: solid 1px black;text-align:right'>".number_format($SubTotal,2,".",",")."
					</tr>";*/
			}
		}
	}
?>