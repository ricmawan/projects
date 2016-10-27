<?php
	if(isset($_POST['hdnItemID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ItemID = mysql_real_escape_string($_POST['hdnItemID']);
		$ItemName = mysql_real_escape_string($_POST['txtItemName']);
		$ItemCode = mysql_real_escape_string($_POST['txtItemCode']);
		if(ISSET($_POST['chkSecond'])) $chkSecond = true;
		else $chkSecond = false;
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$Price = str_replace(",", "", $_POST['txtPrice']);
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsItem(".$ItemID.", '".$ItemName."', '".$ItemCode."', '".$chkSecond."', ".$Price.", ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ItemID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}				
		$row=mysql_fetch_array($result);
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
