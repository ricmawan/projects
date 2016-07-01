<?php
	if(isset($_POST['BuyReturnID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$BuyReturnID = mysql_real_escape_string($_POST['BuyReturnID']);
		$sql = "SELECT
					DATE_FORMAT(BR.TransactionDate, '%d-%m-%Y') TransactionDate,					
					MS.SupplierName,
					BRD.BuyReturnID,
					BRD.TypeID,
					BRD.Quantity,
					BRD.BuyPrice,
					BRD.BatchNumber,
					BRD.Discount,
					CONCAT(MB.BrandName, ' ', I.TypeName, ' - ', BRD.BatchNumber) AS TypeName,
					BRD.IsPercentage
				FROM
					transaction_buyreturn BR
					JOIN master_supplier MS
						ON MS.SupplierID = BR.SupplierID
					JOIN transaction_buyreturndetails BRD
						ON BR.BuyReturnID = BRD.BuyReturnID
					JOIN master_type I
						ON I.TypeID = BRD.TypeID
					JOIN master_brand MB
						ON MB.BrandID = I.BrandID
				WHERE
					BR.BuyReturnID = $BuyReturnID";
		if(!$result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$rows = array();
		while($row = mysql_fetch_assoc($result)) {
			$rows[] = $row;
		}
		echo json_encode($rows);
		return 0;
	}
?>
