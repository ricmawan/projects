
<?php
	if(isset($_POST['MedicationID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$MedicationID = mysql_real_escape_string($_POST['MedicationID']);
		$sql = "SELECT
					Cash,
					Debit,
					PatientID
				FROM
					transaction_medication
				WHERE
					MedicationID = ".$MedicationID."";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$return_arr = array();
		$row = mysql_fetch_array($result);
		$row_array['Cash'] = $row['Cash'];
		$row_array['Debit'] = $row['Debit'];
		$PatientID = $row['PatientID'];
		$sql = "SELECT
					MP.PatientID,
					TOT.Total,
					PAY.Payment,
					(TOT.Total - PAY.Payment) Debt
				FROM
					master_patient MP
					LEFT JOIN
					(
						SELECT
							TM.PatientID,
							SUM(TMD.Price * TMD.Quantity) Total
						FROM
							transaction_medication TM
							LEFT JOIN transaction_medicationdetails TMD
								ON TM.MedicationID = TMD.MedicationID
						WHERE
							TM.IsDone = 1 
							AND TM.IsCancelled = 0
							AND TM.MedicationID <> ".$MedicationID."
						GROUP BY
							TM.PatientID
					)TOT
						ON MP.PatientID = TOT.PatientID
					LEFT JOIN
					(
						SELECT
							TM.PatientID,
							SUM(TM.Debit + TM.Cash + IFNULL(DP.Debit, 0) + IFNULL(DP.Cash, 0)) Payment
						FROM
							transaction_medication TM
							LEFT JOIN transaction_debtpayment DP
								ON TM.MedicationID = DP.MedicationID
						WHERE
							TM.IsDone = 1 
							AND TM.IsCancelled = 0
							AND TM.MedicationID <> ".$MedicationID."
						GROUP BY
							TM.PatientID
					)PAY
						ON MP.PatientID = PAY.PatientID
				WHERE
					MP.PatientID = ".$PatientID."
				GROUP BY
					MP.PatientID";
				
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$row = mysql_fetch_array($result);
		$row_array['Debt'] = $row['Debt'];
		
		array_push($return_arr, $row_array);
		
		$json = json_encode($return_arr);
		echo $json;
	}
?>
