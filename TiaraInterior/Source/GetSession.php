<?php
	SESSION_START();
	if(ISSET($_SESSION['UserLogin']) && ISSET($_SESSION['UserPassword'])) {
		$sql = "SELECT 
				1
			FROM
				master_user 
			WHERE 
				UserLogin = '".mysql_real_escape_string($_SESSION['UserLogin'])."' 
				AND UserPassword = '".mysql_real_escape_string($_SESSION['UserPassword'])."'";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$cek = mysql_num_rows($result);
		if($cek != 1) {
			echo "<script>window.location='./'; </script>";
		}
	} 
	else {
		echo "<script>window.location='./'; </script>";
	}

?>
