<?php
	if(ISSET($_GET['txtDate'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		date_default_timezone_set("Asia/Jakarta");
		
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
			$where .= " AND ( MM.MachineType LIKE '%".$search."%' OR MM.MachineCode LIKE '%".$search."%' OR SK.Kilometer LIKE '%".$search."%' OR EK.Kilometer LIKE '%".$search."%' OR (EK.Kilometer - SK.Kilometer) LIKE '%".$search."%' OR EK.FuelTypeName LIKE '%".$search."%' OR EK.Price LIKE '%".$search."%' OR EK.Quantity LIKE '%".$search."%' OR CONCAT('1:', (EK.Kilometer - SK.Kilometer)/EK.Quantity) LIKE '%".$search."%' )";
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
							TF.MachineID,
							TF.Kilometer,
							FT.FuelTypeName,
							TF.Quantity,
							TF.Price
						FROM
							transaction_fuel TF
							JOIN master_fueltype FT
								ON FT.FuelTypeID = TF.FuelTypeID
						WHERE
							CAST(TF.TransactionDate AS DATE) = '".$txtDate."'
					)EK
						ON MM.MachineID = EK.MachineID
					JOIN
					(
						SELECT
							MAX(TF.TransactionDate),
							TF.MachineID,
							TF.Kilometer
						FROM
							transaction_fuel TF
						WHERE
							CAST(TF.TransactionDate AS DATE) < '".$txtDate."'
						GROUP BY
							TF.MachineID
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
					IFNULL(EK.Kilometer, '-') EndKilometer,
					IFNULL((EK.Kilometer - SK.Kilometer), '-') Difference,
					IFNULL(EK.FuelTypeName, '') FuelTypeName,
					EK.Price,
					IFNULL(EK.Quantity, '-') Quantity,
					IFNULL(CONCAT('1:', ROUND((EK.Kilometer - SK.Kilometer)/EK.Quantity, 2)), '-') FuelRatio
				FROM
					master_machine MM
					JOIN 
					(
						SELECT
							TF.MachineID,
							TF.Kilometer,
							FT.FuelTypeName,
							TF.Quantity,
							TF.Price
						FROM
							transaction_fuel TF
							JOIN master_fueltype FT
								ON FT.FuelTypeID = TF.FuelTypeID
						WHERE
							CAST(TF.TransactionDate AS DATE) = '".$txtDate."'
					)EK
						ON MM.MachineID = EK.MachineID
					JOIN
					(
						SELECT
							MAX(TF.TransactionDate),
							TF.MachineID,
							MAX(TF.Kilometer) Kilometer
						FROM
							transaction_fuel TF
						WHERE
							CAST(TF.TransactionDate AS DATE) < '".$txtDate."'
						GROUP BY
							TF.MachineID
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
			$row_array['StartKilometer'] = number_format($row['StartKilometer'],0,".",",");
			$row_array['EndKilometer'] = number_format($row['EndKilometer'],0,".",",");
			$row_array['Difference'] = number_format($row['Difference'],0,".",",");
			$row_array['FuelTypeName'] = $row['FuelTypeName'];
			$row_array['Quantity'] = number_format($row['Quantity'],2,".",",");
			$row_array['Price'] = number_format($row['Price'],2,".",",");
			$row_array['Total'] = number_format($row['Price'] * $row['Quantity'],2,".",",");
			$row_array['FuelRatio'] = $row['FuelRatio'];
			array_push($return_arr, $row_array);
		}
		$json = json_encode($return_arr);
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
