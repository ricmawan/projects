<?php
	include "DBConfig.php";
	SESSION_START();
	if(isset($_POST['txtUserLogin']) && isset($_POST['txtPassword'])) {
		$sql = "SELECT 
				UserID,
				UserName,
				UserLogin,
				UserPassword,
				UserTypeID
			FROM
				master_user 
			WHERE 
				UserLogin = '".mysql_real_escape_string($_POST['txtUserLogin'])."'
				AND IsActive = 1
				AND UserPassword = MD5('".mysql_real_escape_string($_POST['txtPassword'])."')";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$cek = mysql_num_rows($result);
		$row = mysql_fetch_array($result);
		if($cek == 1) {
			$_SESSION['UserID'] = $row['UserID'];;
			$_SESSION['Nama'] = $row['UserName'];
			$_SESSION['UserLogin'] = $row['UserLogin'];
			$_SESSION['UserPassword'] = $row['UserPassword'];
			$_SESSION['UserTypeID'] = $row['UserTypeID'];
			echo "Success";				
		}
		else echo "Username & Password tidak cocok";
	}
?>
