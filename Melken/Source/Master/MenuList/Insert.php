<?php
	if(isset($_POST['hdnMenuListID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$MenuListID = mysql_real_escape_string($_POST['hdnMenuListID']);
		$MenuName = mysql_real_escape_string($_POST['txtMenuName']);
		$MenuListCategoryID = mysql_real_escape_string($_POST['ddlCategory']);
		$Price = str_replace(",", "", $_POST['txtPrice']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$State = 1;
		$sql = "CALL spInsMenuList(".$MenuListID.", ".$MenuListCategoryID.", '".$MenuName."', ".$Price.", ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($MenuListID, $Message, $MessageDetail, $FailedFlag, $State);
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
