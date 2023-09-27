<?php
	if(ISSET($_POST['TransactionID'])) {
		header('Content-Type: application/json');
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$TransactionID = mysqli_real_escape_string($dbh, $_POST['TransactionID']);
		$TransactionType = mysqli_real_escape_string($dbh, $_POST['TransactionType']);
		$sql = "CALL spSelPaymentDetails(".$TransactionID.", '".$TransactionType."', '".$_SESSION['UserLoginMobile']."')";
		$FailedFlag = 0;

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Transaction/Payment/PaymentDetails.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
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
			$row_array[] = '<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;"><i style="cursor:pointer;" class="fa fa-trash fa-2x" acronym title="Hapus Data" onclick="deletePayment(' . $row['PaymentDetailsID'] . ');"></i>';
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
