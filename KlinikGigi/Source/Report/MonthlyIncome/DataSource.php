<?php
	if(ISSET($_GET['ddlMonth']) && ISSET($_GET['ddlYear'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		$ddlMonth = mysql_real_escape_string($_GET['ddlMonth']);
		$ddlYear = mysql_real_escape_string($_GET['ddlYear']);
		$sql = "SELECT
					CASE MONTH(TM.TransactionDate)
						WHEN 1
						THEN 'Januari'
						WHEN 2
						THEN 'Februari'
						WHEN 3
						THEN 'Maret'
						WHEN 4
						THEN 'April'
						WHEN 5
						THEN 'Mei'
						WHEN 6
						THEN 'Juni'
						WHEN 7
						THEN 'Juli'
						WHEN 8
						THEN 'Agustus'
						WHEN 9
						THEN 'September'
						WHEN 10
						THEN 'Oktober'
						WHEN 11
						THEN 'November'
						WHEN 12
						THEN 'Desember'
					END	MonthName,
					SUM(TMD.Quantity * TMD.Price) AS TotalIncome
				FROM
					transaction_medication TM
					JOIN transaction_medicationdetails TMD
						ON TM.MedicationID = TMD.MedicationID
				WHERE
					MONTH(TM.TransactionDate) = ".$ddlMonth."
					AND YEAR(TM.TransactionDate) = ".$ddlYear."
					AND TM.IsCancelled = 0
				GROUP BY
					MONTH(TM.TransactionDate)
				ORDER BY	
					MONTH(TM.TransactionDate) ASC";
		
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
			$row_array['MonthName']= $row['MonthName'];
			$row_array['IncomeTotal'] = number_format($row['TotalIncome'],2,".",",");
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
