<?php
	if(ISSET($_GET['ddlMonth']) && ISSET($_GET['ddlYear'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		$ddlDoctor = mysql_real_escape_string($_GET['DoctorID']);
		$ddlMonth = mysql_real_escape_string($_GET['ddlMonth']);
		$ddlYear = mysql_real_escape_string($_GET['ddlYear']);
		$sql = "SELECT
					DATE_FORMAT(TM.TransactionDate, '%d-%m-%Y') TransactionDate,
					MP.PatientName,
					ME.ExaminationName,
					TMD.Quantity
				FROM
					transaction_medication TM
					JOIN master_patient MP
						ON MP.PatientID = TM.PatientID
					JOIN transaction_medicationdetails TMD
						ON TM.MedicationID = TMD.MedicationID
					JOIN master_examination ME
						ON ME.ExaminationID = TMD.ExaminationID
				WHERE
					MONTH(TM.TransactionDate) = ".$ddlMonth."
					AND YEAR(TM.TransactionDate) = ".$ddlYear."
					AND TM.IsCancelled = 0
					AND TM.IsDone = 1
					AND TMD.DoctorID = ".$ddlDoctor."	
				ORDER BY	
					TM.TransactionDate ASC";
		
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
			$row_array['TransactionDate']= $row['TransactionDate'];
			$row_array['PatientName'] = $row['PatientName'];
			$row_array['ExaminationName'] = $row['ExaminationName'];
			$row_array['Quantity'] = number_format($row['Quantity'],2,".",",");
			array_push($return_arr, $row_array);
		}

		$sql = "SELECT
					SUBSTRING_INDEX( ME.ExaminationName , ' ', 1 ) ExaminationGroup,
					SUM(TMD.Quantity) Quantity
				FROM
					transaction_medication TM
					JOIN master_patient MP
						ON MP.PatientID = TM.PatientID
					JOIN transaction_medicationdetails TMD
						ON TM.MedicationID = TMD.MedicationID
					JOIN master_examination ME
						ON ME.ExaminationID = TMD.ExaminationID
				WHERE
					MONTH(TM.TransactionDate) = 1
					AND YEAR(TM.TransactionDate) = 2017
					AND TM.IsCancelled = 0
					AND TM.IsDone = 1
					AND TMD.DoctorID = 3
				GROUP BY
					SUBSTRING_INDEX( ME.ExaminationName , ' ', 1 )
				ORDER BY	
					1";

		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}

		$table = "<table class='table table-striped table-bordered table-hover'>";
		while ($row = mysql_fetch_array($result)) {
			$table .= "<tr>";
			$table .= "<td>".$row['ExaminationGroup']."</td>";
			$table .= "<td>".$row['Quantity']."</td>";
			$table .= "</tr>";
		}
		$table .= "</table>";

		$json = json_encode($return_arr);
		echo "{ \"rows\": ".$json.", \"total\": $nRows, \"table\" : \"$table\" }";
	}
?>
