<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../DBConfig.php";

	$where = " 1=1 AND OMD.IsReceived = 0 ";
	$order_by = "OM.TransactionDate DESC";
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
			else $order_by = "OM.TransactionDate DESC";
		}
	}
	//Handles search querystring sent from Bootgrid
	if (ISSET($_REQUEST['searchPhrase']) )
	{
		$search = trim($_REQUEST['searchPhrase']);
		$where .= " AND ( DATE_FORMAT(OM.TransactionDate, '%d-%m-%Y') LIKE '%".$search."%' OR OM.ReceiptNumber LIKE '%".$search."%' OR MP.PatientName LIKE '%".$search."%' OR MD.UserName LIKE '%".$search."%' OR OMD.ExaminationName LIKE '%".$search."%' OR OMD.Remarks LIKE '%".$search."%' ) ";
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
				COUNT(*) AS nRows
			FROM
				transaction_outgoingmodel OM
				JOIN transaction_outgoingmodeldetails OMD
					ON OMD.OutgoingModelID = OM.OutgoingModelID
				JOIN master_user MD
					ON OMD.DoctorID = MD.UserID
				JOIN master_patient MP
					ON MP.PatientID = OMD.PatientID
			WHERE
				$where";
	
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$row = mysql_fetch_array($result);
	$nRows = $row['nRows'];
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
				OMD.IncomingReceiptNumber
			FROM
				transaction_outgoingmodel OM
				JOIN transaction_outgoingmodeldetails OMD
					ON OMD.OutgoingModelID = OM.OutgoingModelID
				JOIN master_user MD
					ON OMD.DoctorID = MD.UserID
				JOIN master_patient MP
					ON MP.PatientID = OMD.PatientID
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
		$row_array['OutgoingModelDetailsID'] = $row['OutgoingModelDetailsID'];
		$row_array['OutgoingModelID'] = $row['OutgoingModelID'];
		$row_array['TransactionDate'] = $row['TransactionDate'];
		$row_array['ReceiptNumber'] = $row['ReceiptNumber'];
		$row_array['DoctorName'] = $row['DoctorName'];
		$row_array['PatientName'] = $row['PatientName'];
		$row_array['ExaminationName'] = $row['ExaminationName'];
		$row_array['Remarks'] = $row['Remarks'];
		$row_array['ReceivedDate'] = "<input type='text' class='form-control-custom ReceivedDate' style='width: 80%; display: inline-block;margin-right: 5px;' id='txtReceivedDate".$row['OutgoingModelDetailsID']."' name='txtReceivedDate".$row['OutgoingModelDetailsID']."' readonly />";
		$row_array['IncomingReceiptNumber'] = "<input type='text' class='form-control-custom' id='txtIncomingReceiptNumber".$row['OutgoingModelDetailsID']."' name='txtIncomingReceiptNumber".$row['OutgoingModelDetailsID']."' />";
		
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
?>
