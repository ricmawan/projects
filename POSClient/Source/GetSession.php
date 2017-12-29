<?php
	SESSION_START();
	try {
		if(ISSET($_SESSION['UserLogin']) && ISSET($_SESSION['UserPassword'])) {
			$sql = "CALL spSelUserLogin('".mysqli_real_escape_string($dbh, $_SESSION['UserLogin'])."', '".mysqli_real_escape_string($dbh, $_SESSION['UserPassword'])."', 1, '".mysqli_real_escape_string($dbh, $_SESSION['UserLogin'])."')";
			if (! $result = mysqli_query($dbh, $sql)) {
				logEvent(mysqli_error($dbh), '/Login.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
				echo "<script>$('#loading').hide();</script>";
				return 0;
			}
			
			$cek = mysqli_num_rows($result);
			mysqli_free_result($result);
			mysqli_next_result($dbh);
			if($cek != 1) {
				echo "<script>window.location='./'; </script>";
			}
		}
		else {
			echo "<script>window.location='./'; </script>";
		}
	}
	catch (Exception $e)
	{
		logEvent($e->getMessage(), '/GetSession.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
	}
?>