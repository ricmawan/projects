<?php
	if(isset($_POST['hdnItemID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$ItemID = mysql_real_escape_string($_POST['hdnItemID']);
		$ItemName = mysql_real_escape_string($_POST['txtItemName']);
		$BrandID = mysql_real_escape_string($_POST['ddlBrand']);
		$ReminderCount = 0;
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$UnitID = mysql_real_escape_string($_POST['ddlUnit']);
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsItem(".$ItemID.", '".$ItemName."', '".$BrandID."', ".$UnitID.", '".$ReminderCount."', ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
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
