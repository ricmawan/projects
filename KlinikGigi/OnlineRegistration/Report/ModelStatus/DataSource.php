<?php
	if(ISSET($_GET['IsReceived'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../DBConfig.php";
		date_default_timezone_set("Asia/Jakarta");
		$IsReceived = mysql_real_escape_string($_GET['IsReceived']);
		$sql = "SELECT
				OM.OutgoingModelID,
				OMD.OutgoingModelDetailsID,
				DATE_FORMAT(OM.TransactionDate, '%d-%m-%Y') TransactionDate,
				OM.ReceiptNumber,
				MD.UserName AS DoctorName,
				MP.PatientName,
				OMD.ExaminationName,
				OMD.Remarks,
				DATE_FORMAT(OMD.ReceivedDate, '%d-%m-%Y') ReceivedDate,
				OMD.IncomingReceiptNumber,
				OMD.IsReceived
			FROM
				transaction_outgoingmodel OM
				JOIN transaction_outgoingmodeldetails OMD
					ON OMD.OutgoingModelID = OM.OutgoingModelID
				JOIN master_user MD
					ON OMD.DoctorID = MD.UserID
				JOIN master_patient MP
					ON MP.PatientID = OMD.PatientID
			WHERE
				CASE
					WHEN ".$IsReceived." = 2
					THEN OMD.IsReceived
					ELSE ".$IsReceived."
				END = OMD.IsReceived
			ORDER BY 
				OM.TransactionDate";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$nRows = mysql_num_rows($result);		
		$return_arr = array();
		$RowNumber = 0;
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			$row_array['RowNumber'] = $RowNumber;
			$row_array['TransactionDate'] = $row['TransactionDate'];
			$row_array['ReceiptNumber'] = $row['ReceiptNumber'];
			$row_array['DoctorName'] = $row['DoctorName'];
			$row_array['PatientName'] = $row['PatientName'];
			$row_array['ExaminationName'] = $row['ExaminationName'];
			$row_array['Remarks'] = $row['Remarks'];
			if($row['IsReceived'] == "0") {
				$row_array['ReceivedDate'] = "-";
				$row_array['IncomingReceiptNumber'] = "-";
			}
			else {
				$row_array['ReceivedDate'] = $row['ReceivedDate'];
				$row_array['IncomingReceiptNumber'] = $row['IncomingReceiptNumber'];
			}
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
