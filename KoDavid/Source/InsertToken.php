<?php
	include "./DBConfig.php";
	include "./GetSession.php";
	$TokenCode = mysqli_real_escape_string($dbh, $_POST['TokenCode']);
	$Message = "Sudah diisi";
	$MessageDetail = "";
	$FailedFlag = 0;
	$State = 1;
	$ID = 0;
	
	$sql = "CALL spInsTokenCode('".$TokenCode."', '".$_SESSION['UserLogin']."')";
	if (! $result=mysqli_query($dbh, $sql)) {
		$Message = "Terjadi Kesalahan Sistem";
		$MessageDetail = mysqli_error($dbh);
		$FailedFlag = 1;
		logEvent(mysqli_error($dbh), '/InsertToken.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
		return 0;
	}
	$row=mysqli_fetch_array($result);
	mysqli_free_result($result);
	mysqli_next_result($dbh);
	
	echo returnstate($row['ID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);

	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
