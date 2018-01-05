<?php
	include __DIR__ . "/DBConfig.php";
	include __DIR__ . "/GetSession.php";
	date_default_timezone_set("Asia/Jakarta");
	$EditFlag = "";
	$DeleteFlag = "";
	
	$sql = "CALL spSelUserMenuPermission('$APPLICATION_PATH', '$RequestedPath', '".$_SESSION['UserID']."')";
				
	if (!$result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), $RequestedPath, mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		echo "<script>$('#loading').hide();</script>";
		return 0;
	}
	
	$cek = mysqli_num_rows($result);
	
	if($cek == 0) {
		header($APPLICATION_PATH.'Home.php', true, 200);
		echo "<script>$('#Menu1').click();</script>";
		die();
	}
	else {
		$row = mysqli_fetch_array($result);
		$EditFlag = $row['EditFlag'];
		$DeleteFlag = $row['DeleteFlag'];
	}
	mysqli_free_result($result);
	mysqli_next_result($dbh);
?>