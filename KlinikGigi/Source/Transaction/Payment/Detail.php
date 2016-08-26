
<?php
	if(isset($_POST['MedicationID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$MedicationID = mysql_real_escape_string($_POST['MedicationID']);
		$sql = "SELECT
					Cash,
					Debit
				FROM
					transaction_medication
				WHERE
					MedicationID = ".$MedicationID."";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$return_arr = array();
		while ($row = mysql_fetch_array($result)) {
			$row_array['Cash'] = $row['Cash'];
			$row_array['Debit'] = $row['Debit'];
			array_push($return_arr, $row_array);
		}
		$json = json_encode($return_arr);
		echo $json;
	}
?>
