<?php
	header('Content-Type: application/json');
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	$DoctorID = mysql_real_escape_string($_POST['DoctorID']);
	$Year = mysql_real_escape_string($_POST['Year']);
	$sql = "SELECT
				BusinessMonth,
				CommisionPercentage,
				ToolsFee
			FROM
				transaction_doctorcommision
			WHERE
				BusinessYear = ".$Year."
				AND DoctorID = ".$DoctorID."
			ORDER BY
				BusinessMonth";
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$return_arr = array();
	while ($row = mysql_fetch_array($result)) {
		$row_array['BusinessMonth'] = $row['BusinessMonth'];
		$row_array['CommisionPercentage'] = $row['CommisionPercentage'];
		$row_array['ToolsFee'] = $row['ToolsFee'];
		array_push($return_arr, $row_array);
	}

	$json = json_encode($return_arr);
	echo $json;
?>
