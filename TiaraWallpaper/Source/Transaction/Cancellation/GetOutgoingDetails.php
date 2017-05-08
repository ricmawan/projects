<?php
	if(isset($_POST['OutgoingID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$OutgoingID = mysql_real_escape_string($_POST['OutgoingID']);
		$sql = "SELECT
					DATE_FORMAT(OT.TransactionDate, '%d-%m-%Y') TransactionDate,
					OT.DeliveryCost,
					CONCAT(MC.CustomerName, ' - ', MC.Address1) CustomerName,
					OTD.OutgoingDetailsID,
					OTD.TypeID,
					OTD.Quantity,
					OTD.BuyPrice,
					OTD.SalePrice,
					OTD.BatchNumber,
					OTD.Discount,
					CONCAT(MB.BrandName, ' ', I.TypeName, ' - ', OTD.BatchNumber) AS TypeName,
					OTD.IsPercentage,
					OTD.Remarks
				FROM
					transaction_outgoing OT
					JOIN master_customer MC
						ON MC.CustomerID = OT.CustomerID
					JOIN transaction_outgoingdetails OTD
						ON OTD.OutgoingID = OT.OutgoingID
					JOIN master_type I
						ON I.TypeID = OTD.TypeID
					JOIN master_brand MB
						ON MB.BrandID = I.BrandID
				WHERE
					OTD.OutgoingID = $OutgoingID";
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
