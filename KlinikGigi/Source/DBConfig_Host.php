<?php
	date_default_timezone_set("Asia/Bangkok");
	$DBUser2 = "u360410457_im";
	$DBPass2 = "imdentalspecialist";
	$DBName2 = "u360410457_im";
	$Host2 = "sql170.main-hosting.eu";
	GLOBAL $dbh2;
	$dbh2 = mysqli_connect($Host2, $DBUser2, $DBPass2, $DBName2);
	if (mysqli_connect_errno()) {
		file_put_contents('./sync.log', date("d-m-Y H:i:s") . " DBConfig_Host.php : " . "(". mysqli_connect_error() . ")\n", FILE_APPEND);
		//echo "Failed to connect to MySQL: " . ;
		exit();
	}
?>