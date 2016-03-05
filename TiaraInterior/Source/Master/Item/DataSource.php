<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "MT.TypeID";
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
			else $order_by = "MT.TypeID ASC";
		}
		$order_by .= ", MT.TypeID ASC";
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( MU.UnitName LIKE '%".$search."%' OR MT.TypeName LIKE '%".$search."%' OR MB.BrandName LIKE '%".$search."%' OR MT.BuyPrice LIKE '%".$search."%' OR MT.SalePrice LIKE '%".$search."%' OR CONCAT(MB.BrandName, ' ', MT.TypeName) LIKE '%".$search."%' )";
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
				master_type MT
				JOIN master_brand MB
					ON MB.BrandID = MT.BrandID
				JOIN master_unit MU
					ON MU.UnitID = MT.UnitID
				LEFT JOIN
				(
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(SA.Quantity) Quantity
					FROM
					(
						SELECT
							TypeID,
							TRIM(BatchNumber) BatchNumber,
							SUM(Quantity) Quantity
						FROM
							transaction_firststockdetails
						GROUP BY
							TypeID,
							BatchNumber
						UNION
						SELECT
							TypeID,
							TRIM(BatchNumber) BatchNumber,
							SUM(Quantity) Quantity
						FROM
							transaction_incomingdetails
						GROUP BY
							TypeID,
							BatchNumber
					)SA
					GROUP BY
						TypeID,
						BatchNumber
				)FS
					ON FS.TypeID = MT.TypeID
				LEFT JOIN
				(
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(Quantity) Quantity
					FROM
						transaction_outgoingdetails
					GROUP BY
						TypeID,
						BatchNumber
				)TOD
					ON TOD.TypeID = MT.TypeID
					AND TOD.BatchNumber = FS.BatchNumber
				LEFT JOIN
				(
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(Quantity) Quantity
					FROM
						transaction_buyreturndetails
					GROUP BY
						TypeID,
						BatchNumber
				)BR
					ON BR.TypeID = MT.TypeID
					AND BR.BatchNumber = FS.BatchNumber
				LEFT JOIN
				(
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(Quantity) Quantity
					FROM
						transaction_salereturndetails
					GROUP BY
						TypeID,
						BatchNumber
				)SR
					ON SR.TypeID = MT.TypeID
					AND SR.BatchNumber = FS.BatchNumber
			WHERE
				$where";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				MT.TypeID,
				MT.TypeName,
				MB.BrandName,
				FS.BatchNumber,
				IFNULL(FS.BuyPrice, MT.BuyPrice) BuyPrice,
				IFNULL(FS.SalePrice, MT.SalePrice) SalePrice,
				(IFNULL(FS.Quantity, 0) - IFNULL(TOD.Quantity, 0) - IFNULL(BR.Quantity, 0) + IFNULL(SR.Quantity, 0)) Stock,
				MU.UnitName
			FROM
				master_type MT
				JOIN master_brand MB
					ON MB.BrandID = MT.BrandID
				JOIN master_unit MU
					ON MU.UnitID = MT.UnitID
				LEFT JOIN
				(
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(SA.Quantity) Quantity,
						BuyPrice,
						SalePrice
					FROM
					(
						SELECT
							TypeID,
							TRIM(BatchNumber) BatchNumber,
							SUM(Quantity) Quantity,
							BuyPrice,
							SalePrice
						FROM
							transaction_firststockdetails
						GROUP BY
							TypeID,
							BatchNumber
						UNION
						SELECT
							TypeID,
							TRIM(BatchNumber) BatchNumber,
							SUM(Quantity) Quantity,
							BuyPrice,
							SalePrice
						FROM
							transaction_incomingdetails
						GROUP BY
							TypeID,
							BatchNumber
					)SA
					GROUP BY
						TypeID,
						BatchNumber
				)FS
					ON FS.TypeID = MT.TypeID
				LEFT JOIN
				(
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(Quantity) Quantity
					FROM
						transaction_outgoingdetails
					GROUP BY
						TypeID,
						BatchNumber
				)TOD
					ON TOD.TypeID = MT.TypeID
					AND TOD.BatchNumber = FS.BatchNumber
				LEFT JOIN
				(
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(Quantity) Quantity
					FROM
						transaction_buyreturndetails
					GROUP BY
						TypeID,
						BatchNumber
				)BR
					ON BR.TypeID = MT.TypeID
					AND BR.BatchNumber = FS.BatchNumber
				LEFT JOIN
				(
					SELECT
						TypeID,
						TRIM(BatchNumber) BatchNumber,
						SUM(Quantity) Quantity
					FROM
						transaction_salereturndetails
					GROUP BY
						TypeID,
						BatchNumber
				)SR
					ON SR.TypeID = MT.TypeID
					AND SR.BatchNumber = FS.BatchNumber
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
		$row_array['TypeName'] = $row['TypeName'];
		$row_array['BrandName'] = $row['BrandName'];
		$row_array['BatchNumber'] = $row['BatchNumber'];
		$row_array['Stock'] = $row['Stock'];
		$row_array['UnitName'] = $row['UnitName'];
		$row_array['BuyPrice'] = number_format($row['BuyPrice'],2,".",",");
		$row_array['SalePrice'] = number_format($row['SalePrice'],2,".",",");
		array_push($return_arr, $row_array);
	}
	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	
?>
