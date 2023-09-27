<?php
	SESSION_START();
	include __DIR__ . "/DBConfig.php";
	if(ISSET($_SESSION['UserLoginMobile']) && ISSET($_SESSION['UserPassword'])) {
		$sql = "CALL spSelUserLogin('".mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile'])."', '".mysqli_real_escape_string($dbh, $_SESSION['UserPassword'])."', 1, '".mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile'])."')";
		if (!$result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Login.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
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
		mysqli_free_result($result);
		mysqli_next_result($dbh);
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
