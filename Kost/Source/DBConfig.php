<?php
	$DBUser = "root";
	$DBPass = "";
	$DBName = "kost";
	$Host = "localhost";
	if (! $dbh = mysql_connect($Host, $DBUser, $DBPass)) {
		echo mysql_error();
		return 0;
	}

	if (! mysql_select_db($DBName)) {
		echo mysql_error();
		return 0;
	}

	$sql = "SELECT
				ParameterName,
				ParameterValue
			FROM
				master_parameter";
	if (! $result=mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}

	while($row = mysql_fetch_array($result)) {
		${$row['ParameterName']} = $row['ParameterValue'];
	}
?>
