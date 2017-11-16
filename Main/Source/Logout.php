<?php
	SESSION_START();
	include __DIR__ . "/DBConfig.php";
	if(ISSET($_SESSION['UserLogin']) && ISSET($_SESSION['UserPassword'])) {
		$sql = "SELECT 
					1
				FROM 
					master_user 
				WHERE 
					UserLogin = '".mysqli_real_escape_string($dbh, $_SESSION['UserLogin'])."' 
					AND UserPassword = '".mysqli_real_escape_string($dbh, $_SESSION['UserPassword'])."'";
					
		if (!$result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Login.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo "<script>$('#loading').hide();</script>";
			return 0;
		}
		
		$cek = mysqli_num_rows($result);
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
