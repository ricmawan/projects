<?php
	SESSION_START();
	if(ISSET($_SESSION['Username']) && ISSET($_SESSION['Password'])) {
		$sql = "SELECT 
					*
				FROM
					master_user 
				WHERE 
					Username = '".$_SESSION['Username']."' 
					AND Password = '".$_SESSION['Password']."'";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$cek = mysql_num_rows($result);
		if($cek != 1) {
			echo "<script>window.location='./'; </script>";	
		}

		$sql = "SELECT
				Nama,
				Nilai
			FROM
				master_parameter";
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
	
		while($row = mysql_fetch_row($result)) {
			${$row[0]} = $row[1];
		}
	} else echo "<script>window.location='./'; </script>";
?>
