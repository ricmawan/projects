<?php
	date_default_timezone_set("Asia/Bangkok");
	$DBUser3 = "root";
	$DBPass3 = "";
	$DBName3 = "klinik_gigi";
	$Host3 = "localhost";
	GLOBAL $dbh3;
	$dbh3 = mysqli_connect($Host3, $DBUser3, $DBPass3, $DBName3);
	if (mysqli_connect_errno()) {
		file_put_contents('./sync.log', date("d-m-Y H:i:s") . " DBConfig_Sync.php : " . "(". mysqli_connect_error() . ")\n", FILE_APPEND);
		//echo "Failed to connect to MySQL: " . ;
		exit();
	}

	$sql = "SELECT
			ParameterName,
			ParameterValue
		FROM
			master_parameter";
			
	if (! $result=mysqli_query($dbh3, $sql)) {
		file_put_contents('./sync.log', date("d-m-Y H:i:s") . " DBConfig_Sync.php : " . "(". mysqli_connect_error() . ")\n", FILE_APPEND);
		exit();
	}

	while($row = mysqli_fetch_array($result)) {
		${$row['ParameterName']} = $row['ParameterValue'];
	}

?>