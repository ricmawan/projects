<?php
	if(ISSET($_POST['TransactionID'])) {
		header('Content-Type: application/json');
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$TransactionID = mysqli_real_escape_string($dbh, $_POST['TransactionID']);
		$TransactionType = mysqli_real_escape_string($dbh, $_POST['TransactionType']);
		$sql = "CALL spSelPaymentDetails(".$TransactionID.", '".$TransactionType."', '".$_SESSION['UserLoginKasir']."')";
		$FailedFlag = 0;

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/Payment/PaymentDetails.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
			$FailedFlag = 1;
			$json_data = array(
							"FailedFlag" => $FailedFlag
						);
		
			echo json_encode($json_data);
			return 0;
		}
		
		$return_arr = array();
		while ($row = mysqli_fetch_array($result)) {
			$row_array = array();
			//data yang dikirim ke table
			$row_array[] = $row['PaymentDetailsID'];
			$row_array[] = $row['PlainTransactionDate'];
			$row_array[] = $row['TransactionDate'];
			$row_array[] = number_format($row['Amount'],0,".",",");
			$row_array[] = $row['Remarks'];
			array_push($return_arr, $row_array);
		}
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);

		$json_data = array(
						"FailedFlag" => $FailedFlag,
						"data"				=> $return_arr
					);
		
		echo json_encode($json_data);
	}
?>
