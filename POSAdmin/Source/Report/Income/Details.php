<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";
	if(ISSET($_POST['ID']) && ISSET($_POST['TransactionType']) && ISSET($_POST['BranchID']))
	{
		$ID = $_POST['ID'];
		$BranchID = $_POST['BranchID'];
		$TransactionType = $_POST['TransactionType'];
		
		$sql = "CALL spSelIncomeDetailsReport(".$ID.", ".$BranchID.", '".$TransactionType."', '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/Sale/Details.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		$table = "<table class='table table-striped table-bordered table-hover'>";
		$table .= "<tr>";
		$table .= "<td>Kode Barang</td>";
		$table .= "<td>Nama Barang</td>";
		$table .= "<td>Quantity</td>";
		$table .= "<td>Harga Beli</td>";
		$table .= "<td>Total Beli</td>";
		$table .= "<td>Harga Jual</td>";
		$table .= "<td>Diskon</td>";
		$table .= "<td>Total Jual</td>";
		$table .= "<td>Laba</td>";
		$table .= "</tr>";
		while ($row = mysqli_fetch_array($result)) {
			$table .= "<tr><td>";
			$table .= $row['ItemCode'] ."</td><td>";
			$table .= $row['ItemName'] ."</td><td align='right'>";
			$table .= $row['Quantity'] ."</td><td align='right'>";
			$table .= number_format($row['BuyPrice'],0,".",",") ."</td><td align='right'>";
			$table .= number_format($row['TotalBuy'],0,".",",") ."</td><td align='right'>";
			$table .= number_format($row['SalePrice'],0,".",",") ."</td><td align='right'>";
			$table .= number_format($row['Discount'],0,".",",") ."</td><td align='right'>";
			$table .= number_format($row['TotalSale'],0,".",",") ."</td><td align='right'>";
			$table .= number_format($row['Income'],0,".",",") ."</td></tr>";
		}

		$table .= "</table>";
		echo $table;

		
		mysqli_free_result($result);
		mysqli_next_result($dbh);

	}
?>