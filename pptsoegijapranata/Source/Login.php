<?php
	include "DBConfig.php";
	SESSION_START();
	if(isset($_POST['txtUsername']) && isset($_POST['txtPassword'])) {
		$sql = "SELECT 
					*
				FROM
					master_user 
				WHERE 
					Username = '".$_POST['txtUsername']."' 
					AND Password = MD5('".$_POST['txtPassword']."')";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$cek = mysql_num_rows($result);
		$row = mysql_fetch_row($result);
		if($cek == 1) {
			$_SESSION['UserID'] = $row[0];;
			$_SESSION['Nama'] = $row[1];
			$_SESSION['Username'] = $row[2];
			$_SESSION['Password'] = $row[3];
			$_SESSION['Jabatan'] = $row[4];
			echo "Success";	
			
		}
		else echo "Username & Password tidak cocok";
	}
?>