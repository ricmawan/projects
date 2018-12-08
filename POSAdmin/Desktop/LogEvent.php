<?php
	if(isset($_POST['Description']) && isset($_POST['Source']) ) {
		include __DIR__ . "/DBConfig.php";
		include __DIR__ . "/GetSession.php";
		logEvent($_POST['Description'], $_POST['Source'], mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
		return 0;
	}
?>