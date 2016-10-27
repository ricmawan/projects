<?php
	if(ISSET($_GET['txtDate'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		$ItemID = mysql_real_escape_string($_GET['ItemID']);
		$txtDate = explode('-', mysql_real_escape_string($_GET['txtDate']));
		$_GET['txtDate'] = "$txtDate[2]-$txtDate[1]-$txtDate[0]"; 
		$txtDate = $_GET['txtDate'];
		
		
		$where = " 1=1 AND MM.MachineKind = 'Mobil'";
		$order_by = "MM.MachineCode";
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
				else $order_by = "MM.MachineCode";
			}
		}
		//Handles search querystring sent from Bootgrid
		if (ISSET($_REQUEST['searchPhrase']) )
		{
			$search = trim($_REQUEST['searchPhrase']);
			$where .= " AND ( MM.MachineType LIKE '%".$search."%' OR MM.MachineCode LIKE '%".$search."%' OR SK.Kilometer LIKE '%".$search."%' OR EK.Kilometer LIKE '%".$search."%' OR (EK.Kilometer - SK.Kilometer) LIKE '%".$search."%' )";
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

		$sql = "SELECT
					COUNT(1) AS nRows
				FROM
					master_machine MM
					JOIN 
					(
						SELECT
							TS.MachineID,
							MAX(TS.Kilometer) Kilometer
						FROM
							transaction_service TS
							JOIN transaction_servicedetails SD
								ON TS.ServiceID = SD.ServiceID
						WHERE
							CAST(TS.TransactionDate AS DATE) = '".$txtDate."'
							AND SD.ItemID = ".$ItemID."
						GROUP BY
							TS.MachineID
					)EK
						ON MM.MachineID = EK.MachineID
					JOIN
					(
						SELECT
							MAX(TS.TransactionDate),
							TS.MachineID,
							TS.Kilometer
						FROM
							transaction_service TS
							JOIN transaction_servicedetails SD
								ON TS.ServiceID = TS.ServiceID
						WHERE
							CAST(TS.TransactionDate AS DATE) < '".$txtDate."'
							AND SD.ItemID = ".$ItemID."
						GROUP BY
							TS.MachineID
					)SK
						ON MM.MachineID = SK.MachineID
				WHERE
					$where";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$row = mysql_fetch_array($result);
		$nRows = $row['nRows'];

		$sql = "SELECT
					MM.MachineType,
					MM.MachineCode,
					IFNULL(SK.Kilometer, '-') StartKilometer,
					SK.StartDate,
					IFNULL(EK.Kilometer, '-') EndKilometer,
					EK.EndDate,
					IFNULL((EK.Kilometer - SK.Kilometer), '-') Difference
				FROM
					master_machine MM
					JOIN 
					(
						SELECT
							TS.MachineID,
							DATE_FORMAT(TS.TransactionDate, '%d-%m-%Y') EndDate, 
							MAX(TS.Kilometer) Kilometer
						FROM
							transaction_service TS
							JOIN transaction_servicedetails SD
								ON TS.ServiceID = SD.ServiceID
						WHERE
							CAST(TS.TransactionDate AS DATE) = '".$txtDate."'
							AND SD.ItemID = ".$ItemID."
						GROUP BY
							TS.MachineID
					)EK
						ON MM.MachineID = EK.MachineID
					JOIN
					(
						SELECT
							DATE_FORMAT(MAX(TS.TransactionDate), '%d-%m-%Y') StartDate,
							TS.MachineID,
							MAX(TS.Kilometer) Kilometer
						FROM
							transaction_service TS
							JOIN transaction_servicedetails SD
								ON TS.ServiceID = SD.ServiceID
						WHERE
							CAST(TS.TransactionDate AS DATE) < '".$txtDate."'
							AND SD.ItemID = ".$ItemID."
						GROUP BY
							TS.MachineID
					)SK
						ON MM.MachineID = SK.MachineID
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
		while ($row = mysql_fetch_array($result)) {
			$row_array['MachineType'] = $row['MachineType'];
			$row_array['MachineCode'] = $row['MachineCode'];
			$row_array['StartDate'] = $row['StartDate'];
			$row_array['EndDate'] = $row['EndDate'];
			$row_array['StartKilometer'] = number_format($row['StartKilometer'],0,".",",");
			$row_array['EndKilometer'] = number_format($row['EndKilometer'],0,".",",");
			$row_array['Difference'] = number_format($row['Difference'],0,".",",");
		array_push($return_arr, $row_array);
		}
		$json = json_encode($return_arr);
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
