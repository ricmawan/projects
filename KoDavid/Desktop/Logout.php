<?php
	SESSION_START();
	include __DIR__ . "/DBConfig.php";
	if(ISSET($_SESSION['UserLoginKasir']) && ISSET($_SESSION['UserPasswordKasir'])) {
		$sql = "CALL spSelUserLogin('".mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir'])."', '".mysqli_real_escape_string($dbh, $_SESSION['UserPasswordKasir'])."', 1, '".mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir'])."')";
		if (!$result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Login.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
			return 0;
		}
		
		$cek = mysqli_num_rows($result);
		if($cek == 1) {
			unset($_SESSION['UserIDKasir']);
			unset($_SESSION['NamaKasir']);
			unset($_SESSION['UserLoginKasir']);
			unset($_SESSION['UserPasswordKasir']);
			unset($_SESSION['UserTypeIDKasir']);
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
