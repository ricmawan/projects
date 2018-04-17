<?php
	include __DIR__ . "/DBConfig.php";
	include __DIR__ . "/GetSession.php";
	date_default_timezone_set("Asia/Jakarta");
	$EditFlag = "";
	$DeleteFlag = "";
	//$RequestedPath = str_replace("Desktop", "Source", $RequestedPath);
	//echo $RequestedPath;
	
	$sql = "CALL spSelUserMenuPermission('$DESKTOP_PATH', '$RequestedPath', '".$_SESSION['UserID']."')";
				
	if (!$result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), $RequestedPath, mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		return 0;
	}
	
	$cek = mysqli_num_rows($result);
	
	if($cek == 0) {
		echo '<script>
				var counterError = 0;
				Lobibox.alert("error",
				{
					msg: "User tidak memiliki akses untuk menu ini.",
					width: 480,
					delay: false,
					beforeClose: function() {
						if(counterError == 0) {
							location.reload();
							counterError = 1;
						}
					}
				});
			</script>';
		exit;
	}
	else {
		$row = mysqli_fetch_array($result);
		$EditFlag = $row['EditFlag'];
		$DeleteFlag = $row['DeleteFlag'];
	}
	mysqli_free_result($result);
	mysqli_next_result($dbh);
?>