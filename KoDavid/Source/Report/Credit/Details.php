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
		$txtFromDate = explode('-', mysql_real_escape_string($_POST['FromDate']));
		$_POST['FromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
		$txtFromDate = $_POST['FromDate'];
		
		$sql = "CALL spSelPaymentDetailsReport(".$ID.", '".$txtFromDate."', '".$TransactionType."', '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/Credit/Details.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		$table = "<table class='table table-striped table-bordered table-hover'>";
		$table .= "<tr>";
		$table .= "<td>Tanggal Bayar</td>";
		$table .= "<td>Jumlah </td>";
		$table .= "<td>Keterangan</td>";
		$table .= "</tr>";
		while ($row = mysqli_fetch_array($result)) {
			$table .= "<tr><td>";
			$table .= $row['PaymentDate'] ."</td><td align='right'>";
			$table .= number_format($row['Amount'],0,".",",") ."</td><td>";
			$table .= $row['Remarks'] ."</td></tr>";
		}

		$table .= "</table>";
		echo $table;

		
		mysqli_free_result($result);
		mysqli_next_result($dbh);

	}
?>
