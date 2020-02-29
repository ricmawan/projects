<?php
	include "DBConfig.php";
	SESSION_START();
	if(isset($_POST['txtUserLogin']) && isset($_POST['txtPassword'])) {
		$sql = "SELECT 
					PatientID,
					PatientName,
					NIK,
					Telephone,
					Email,
					MD5(DATE_FORMAT(BirthDate, '%d%m%Y')) UserPassword
				FROM
					master_patient
				WHERE 
					NIK = '".mysql_real_escape_string($_POST['txtUserLogin'])."'
					AND MD5(DATE_FORMAT(BirthDate, '%d%m%Y')) = MD5('".mysql_real_escape_string($_POST['txtPassword'])."')";
					
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$cek = mysql_num_rows($result);
		$row = mysql_fetch_array($result);
		if($cek == 1) {
			$_SESSION['UserID'] = $row['PatientID'];;
			$_SESSION['Nama'] = $row['PatientName'];
			$_SESSION['UserLogin'] = $row['NIK'];
			$_SESSION['UserPassword'] = $row['UserPassword'];
			$_SESSION['Telephone'] = $row['Telephone'];
			$_SESSION['Email'] = $row['Email'];
			echo "Success";				
		}
		else echo "Username & Password tidak cocok";
	}
?>
