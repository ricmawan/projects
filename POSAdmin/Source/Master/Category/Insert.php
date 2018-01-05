<?php
	if(isset($_POST['hdnCategoryID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$CategoryID = mysqli_real_escape_string($dbh, $_POST['hdnCategoryID']);
		$CategoryCode = mysqli_real_escape_string($dbh, $_POST['txtCategoryCode']);
		$CategoryName = mysqli_real_escape_string($dbh, $_POST['txtCategoryName']);
		$hdnIsEdit = mysqli_real_escape_string($dbh, $_POST['hdnIsEdit']);
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsCategory(".$CategoryID.", '".$CategoryCode."', '".$CategoryName."', ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysqli_query($dbh, $sql)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Master/Category/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo "<script>$('#loading').hide();</script>";
			echo returnstate($CategoryID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}				
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
	}
	
	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"Id" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
