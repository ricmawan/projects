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
					MU.UserName,
					SUM(TMD.Quantity * TMD.Price) AS TotalIncome,
					TDC.ToolsFee,
					CONCAT(TDC.CommisionPercentage, '%') AS Commision,
					((SUM(TMD.Quantity * TMD.Price) - TDC.ToolsFee) /100) * TDC.CommisionPercentage AS Earning
				FROM
					transaction_medication TM
					JOIN transaction_medicationdetails TMD
						ON TM.MedicationID = TMD.MedicationID
					JOIN master_user MU
						ON MU.UserID = TMD.DoctorID
					LEFT JOIN transaction_doctorcommision TDC
						ON TDC.DoctorID = TMD.DoctorID
						AND TDC.BusinessMonth = ".$ddlMonth."
						AND TDC.BusinessYear = ".$ddlYear."
				WHERE
					MONTH(TM.TransactionDate) = ".$ddlMonth."
					AND YEAR(TM.TransactionDate) = ".$ddlYear."
					AND MU.UserTypeID = 2
					AND TM.IsCancelled = 0
				GROUP BY
					MU.UserName
				ORDER BY	
					MU.UserName ASC";
		
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
			$row_array['DoctorName']= $row['UserName'];
			$row_array['IncomeTotal'] = number_format($row['TotalIncome'],2,".",",");
			$row_array['ToolsFee'] = number_format($row['ToolsFee'],2,".",",");
			$row_array['Commision'] = $row['Commision'];
			$row_array['Earning'] = number_format($row['Earning'],2,".",",");
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
