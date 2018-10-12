<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";

	$where = " 1=1 ";
	$order_by = "BackupHistoryID";
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
			else $order_by = "BrandID";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( DATE_FORMAT(BackupDate, '%d-%m-%Y') LIKE '%".$search."%' OR FilePath LIKE '%".$search."%' ) ";
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
	
	/*$sql = "SELECT
				COUNT(*) AS nRows
			FROM
				backup_history
			WHERE
				$where";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
	$sql = "SELECT
				BackupHistoryID,
				DATE_FORMAT(BackupDate, '%d-%m-%Y') BackupDate,
				FilePath
			FROM
				backup_history
			WHERE
				$where
			ORDER BY 
				$order_by
				$limit";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}*/
	
	$TargetFolder = $BACKUP_FULLPATH;
	$Sql = glob($TargetFolder . "/*.{sql}",GLOB_BRACE);
	natsort($Sql);
	
	$return_arr = array();
	$RowNumber = $limit_l;
	$nRows = count($Sql);
	$j = 0;
	for($i=$RowNumber;$j<$rows;$j++) {
		//echo "RowNumber : ".$RowNumber."<br /> nRows: ".$nRows;
		if($RowNumber < $nRows) {
			$RowNumber++;
			$row_array['RowNumber'] = $RowNumber;
			//$row_array['BackupHistoryID'] = (Integer)$row['BackupHistoryID'];
			$row_array['BackupDate'] = date("d-m-Y", filectime("$Sql[$i]"));
			$row_array['FilePath']= $BACKUP_FULLPATH.$Sql[$i];
			$row_array['FileName']= basename($Sql[$i], ".sql");
			$i++;
			array_push($return_arr, $row_array);
		}
	}
	$RowNumber++;

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
