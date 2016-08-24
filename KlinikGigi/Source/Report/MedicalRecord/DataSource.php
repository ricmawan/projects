<?php
	if(ISSET($_GET['PatientID']) ) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		date_default_timezone_set("Asia/Jakarta");
		include "../../GetPermission.php";
		$PatientID = mysql_real_escape_string($_GET['PatientID']);
		$where = " 1=1 AND MP.PatientID = ".$PatientID."";
		$order_by = "DateNoFormat";
		$rows = 10;
		$current = 1;
		$limit_l = ($current * $rows) - ($rows);
		$limit_h = $limit_l + $rows ;
		//Handles Sort querystring sent from Bootgrid
		if (ISSET($_REQUEST['sort']) && is_array($_REQUEST['sort']) )
		{
			$order_by = "";
			foreach($_REQUEST['sort'] as $key => $value) {
				if($key != 'No') $order_by .= " $key $value";
				else $order_by = "DateNoFormat";
			}
		}
		//Handles search querystring sent from Bootgrid
		if (ISSET($_REQUEST['searchPhrase']) )
		{
			$search = trim($_REQUEST['searchPhrase']);
			$where .= " AND ( ME.ExaminationName LIKE '%".$search."%' OR TMD.Remarks LIKE '%".$search."%' OR DATE_FORMAT(TM.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' )";
		}
		//Handles determines where in the paging count this result set falls in
		if (ISSET($_REQUEST['rowCount']) ) $rows = $_REQUEST['rowCount'];
		//calculate the low and high limits for the SQL LIMIT x,y clause
		if (ISSET($_REQUEST['current']) )
		{
			$current = $_REQUEST['current'];
			$limit_l = ($current * $rows) - ($rows);
			$limit_h = $rows ;
		}
		if ($rows == -1) $limit = ""; //no limit
		else $limit = " LIMIT $limit_l, $limit_h ";
		//echo "$limit_l $limit_h";
		$sql = "SELECT
					COUNT(1) AS nRows
				FROM
					transaction_medication TM
					JOIN transaction_medicationdetails TMD
						ON TM.MedicationID = TMD.MedicationID
					JOIN master_patient MP
						ON MP.PatientID = TM.PatientID
					JOIN master_examination ME
						ON ME.ExaminationID = TMD.ExaminationID
				WHERE
					$where";
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$row = mysql_fetch_array($result);
		$nRows = $row['nRows'];
		
		$sql = "SELECT
					MP.PatientName,
					DATE_FORMAT(TM.TransactionDate, '%d-%m-%Y') TransactionDate,
					TM.TransactionDate DateNoFormat,
					ME.ExaminationName,
					TMD.Remarks
				FROM
					transaction_medication TM
					JOIN transaction_medicationdetails TMD
						ON TM.MedicationID = TMD.MedicationID
					JOIN master_patient MP
						ON MP.PatientID = TM.PatientID
					JOIN master_examination ME
						ON ME.ExaminationID = TMD.ExaminationID
				WHERE
					$where
				ORDER BY 
					$order_by
				$limit";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$return_arr = array();
		$RowNumber = $limit_l;
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			$row_array['RowNumber'] = $RowNumber;
			$row_array['PatientName']= $row['PatientName'];
			$row_array['TransactionDate']= $row['TransactionDate'];
			$row_array['ExaminationName']= $row['ExaminationName'];
			$row_array['Remarks']= $row['Remarks'];
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
