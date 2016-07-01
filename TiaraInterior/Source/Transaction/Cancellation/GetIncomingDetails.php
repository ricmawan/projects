<?php
	if(isset($_POST['IncomingID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$IncomingID = mysql_real_escape_string($_POST['IncomingID']);
		$sql = "SELECT
					DATE_FORMAT(TI.TransactionDate, '%d-%m-%Y') TransactionDate,
					TI.DeliveryCost,
					MS.SupplierName,
					TID.IncomingDetailsID,
					TID.TypeID,
					TID.Quantity,
					TID.BuyPrice,
					TID.SalePrice,
					TID.BatchNumber,
					TID.Discount,
					CONCAT(MB.BrandName, ' ', I.TypeName, ' - ', TID.BatchNumber) AS TypeName,
					TID.IsPercentage
				FROM
					transaction_incoming TI
					JOIN master_supplier MS
						ON MS.SupplierID = TI.SupplierID
					JOIN transaction_incomingdetails TID
						ON TI.IncomingID = TID.IncomingID
					JOIN master_type I
						ON I.TypeID = TID.TypeID
					JOIN master_brand MB
						ON MB.BrandID = I.BrandID
				WHERE
					TI.IncomingID = $IncomingID";
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
