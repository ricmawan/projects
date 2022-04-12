<?php
	$DBUser = "root";
	$DBPass = "";
	$DBName = "pos_david";
	$Host = "localhost";
	GLOBAL $dbh;
	$dbh = mysqli_connect($Host, $DBUser, $DBPass, $DBName);
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$currentUser = "user";
	if(ISSET($_SESSION['UserLoginKasir'])) $currentUser = $_SESSION['UserLoginKasir'];
	$sql = "CALL spSelParameter('$currentUser')";
	if(!$result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), '/DBConfig.php', mysqli_real_escape_string($dbh, $currentUser));
		return 0;
	}
	while($row = mysqli_fetch_array($result)) {
		${$row['ParameterName']} = $row['ParameterValue'];
	}
	mysqli_free_result($result);
	mysqli_next_result($dbh);
	function logEvent($message, $source, $user) {
		mysqli_next_result($GLOBALS['dbh']);
		$message = mysqli_real_escape_string($GLOBALS['dbh'], $message);
		$sql = "CALL spInsEventLog('$message', '$source', '$user');";
		if (!$result = mysqli_query($GLOBALS['dbh'], $sql)) {
			echo mysqli_error($GLOBALS['dbh']);
		}
		mysqli_next_result($GLOBALS['dbh']);
	}
?>