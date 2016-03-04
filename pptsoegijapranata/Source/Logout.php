<?php
	SESSION_START();
	include "DBConfig.php";
	if(isset($_SESSION['Username']) && isset($_SESSION['Password'])) {
		$sql = "SELECT 
					* 
				FROM 
					master_user 
				WHERE 
					Username = '".$_SESSION['Username']."' 
					AND Password = '".$_SESSION['Password']."'";
					
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$cek=mysql_num_rows($result);
		if($cek==1) {
			session_unset();
			session_destroy();
			echo "<script>window.location='./'; </script>";
		} else echo "<script>window.location='./'; </script>";	
	} else echo "<script>window.location='./'; </script>";	
?>
<html>
	<head>
		<title>Logout</title>
	</head>
	<body>
	</body>
</html>
