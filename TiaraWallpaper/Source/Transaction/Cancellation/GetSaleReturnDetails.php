<?php
	if(isset($_POST['SaleReturnID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$SaleReturnID = mysql_real_escape_string($_POST['SaleReturnID']);
		$FailedFlag = 0;
		$sql = "SELECT
					DATE_FORMAT(SR.TransactionDate, '%d-%m-%Y') TransactionDate,
					CONCAT(MC.CustomerName, ' - ', MC.Address1) CustomerName,
					SRD.SaleReturnDetailsID,
					SRD.TypeID,
					SRD.Quantity,
					SRD.SalePrice,
					SRD.BatchNumber,
					SRD.Discount,
					CONCAT(MB.BrandName, ' ', I.TypeName, ' - ', SRD.BatchNumber) AS TypeName,
					SRD.IsPercentage
				FROM
					transaction_salereturn SR
					JOIN master_customer MC
						ON MC.CustomerID = SR.CustomerID
					JOIN transaction_salereturndetails SRD
						ON SR.SaleReturnID = SRD.SaleReturnID
					JOIN master_type I
						ON I.TypeID = SRD.TypeID
					JOIN master_brand MB
						ON MB.BrandID = I.BrandID
				WHERE
					SRD.SaleReturnID = $SaleReturnID";
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
