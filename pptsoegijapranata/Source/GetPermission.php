<?php
	include "../../DBConfig.php";
	include "../../GetSession.php";
	date_default_timezone_set("Asia/Jakarta");
	$sql = "SELECT
				1,
				MR.EditFlag,
				MR.DeleteFlag
			FROM
				master_role MR
				JOIN master_menu MM
					ON MM.MenuID = MR.MenuID
			WHERE
				CONCAT('".$APPLICATION_PATH."', MM.Nama) = '".$RequestPath."'
				AND MR.UserID = '".$_SESSION['UserID']."'";
				
	if (! $result=mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$cek=mysql_num_rows($result);
	
?>
