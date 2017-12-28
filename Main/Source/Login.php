<?php
	include __DIR__ . "/DBConfig.php";
	SESSION_START();
	try {
		if(isset($_POST['txtUserLogin']) && isset($_POST['txtPassword'])) {
			$sql = "CALL spSelUserLogin('".mysqli_real_escape_string($dbh, $_POST['txtUserLogin'])."', MD5('".mysqli_real_escape_string($dbh, $_POST['txtPassword'])."'), 1, '".mysqli_real_escape_string($dbh, $_POST['txtUserLogin'])."')";
			if (! $result = mysqli_query($dbh, $sql)) {
				logEvent(mysqli_error($dbh), '/Login.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
				echo "<script>$('#loading').hide();</script>";
				return 0;
			}
			
			$cek = mysqli_num_rows($result);
			$row = mysqli_fetch_array($result);
			if($cek == 1) {
				$_SESSION['UserID'] = $row['UserID'];;
				$_SESSION['Nama'] = $row['UserName'];
				$_SESSION['UserLogin'] = $row['UserLogin'];
				$_SESSION['UserPassword'] = $row['UserPassword'];
				echo "Success";				
			}
			else echo "Username & Password tidak cocok";
			mysqli_free_result($result);
			mysqli_next_result($dbh);
		}
	}
	catch (Exception $e)
	{
		logEvent($e->getMessage(), '/Login.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
	}
?>
