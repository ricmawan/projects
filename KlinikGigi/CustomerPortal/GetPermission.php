<?php
	include "../../DBConfig.php";
	include "../../GetSession.php";
	date_default_timezone_set("Asia/Jakarta");
	$EditFlag = "";
	$DeleteFlag = "";
	$sql = "SELECT
			1,
			MR.EditFlag,
			MR.DeleteFlag
		FROM
			master_role_web MR
			JOIN master_menu_web MM
				ON MM.MenuID = MR.MenuID
		WHERE
			CONCAT('".$WEBADMIN_PATH."', MM.Url) = '".$RequestPath."'
			AND MR.UserID = '".$_SESSION['UserID']."'";
				
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	$cek = mysql_num_rows($result);
	if($cek == 0) {
		header($WEBADMIN_PATH.'Home.php', true, 200);
		echo "<script>$('#Menu1').click();</script>";
		die();
	}
	else {
		$row = mysql_fetch_array($result);
		$EditFlag = $row['EditFlag'];
		$DeleteFlag = $row['DeleteFlag'];
	}
?>
