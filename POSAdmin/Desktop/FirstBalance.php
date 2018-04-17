<?php
	include "./DBConfig.php";
	include "./GetSession.php";
	$UserID = mysqli_real_escape_string($dbh, $_SESSION['UserID']);
	$Message = "Sudah diisi";
	$MessageDetail = "";
	$FailedFlag = 0;
	$State = 1;
	$IsFilled = 0;
	
	$sql = "SELECT
				1
			FROM
				transaction_firstbalance
			WHERE
				UserID = ".$UserID."
				AND DATE_FORMAT(TransactionDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')";
	if (! $result=mysqli_query($dbh, $sql)) {
		$Message = "Terjadi Kesalahan Sistem";
		$MessageDetail = mysqli_error($dbh);
		$FailedFlag = 1;
		logEvent(mysqli_error($dbh), '/FirstBalance.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State, $IsFilled);
		return 0;
	}
	if(mysqli_num_rows($result) > 0) $IsFilled = 1;
	mysqli_free_result($result);
	mysqli_next_result($dbh);
	
	echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State, $IsFilled);

	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $IsFilled) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State,
			"IsFilled" => $IsFilled
		);
		return json_encode($data);
	
	}
?>
