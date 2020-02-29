<?php
	SESSION_START();
	include "DBConfig.php";
	//if(ISSET($_SESSION['UserLogin']) && ISSET($_SESSION['UserPassword'])) {
	if(ISSET($_SESSION['UserLogin']) ) {
		$sql = "SELECT 
				1
			FROM 
				master_patient 
			WHERE 
				PatientNumber = '".mysql_real_escape_string($_SESSION['UserLogin'])."' ";
				//AND UserPassword = '".mysql_real_escape_string($_SESSION['UserPassword'])."'";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$cek = mysql_num_rows($result);
		if($cek == 1) {
			session_unset();
			session_destroy();
			echo "<script>window.location='./'; </script>";
		} 
		else {	
			echo "<script>window.location='./'; </script>";
		}
	} 
	else {
		echo "<script>window.location='./'; </script>";
	}
?>
<html>
	<head>
		<title>Logout</title>
	</head>
	<body>
	</body>
</html>
