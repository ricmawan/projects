<?php
	if(isset($_POST['hdnSupplierID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$SupplierID = mysql_real_escape_string($_POST['hdnSupplierID']);
		$Telephone = mysql_real_escape_string($_POST['txtTelephone']);
		$SupplierName = mysql_real_escape_string($_POST['txtSupplierName']);
		$Address = mysql_real_escape_string($_POST['txtAddress']);
		$City = mysql_real_escape_string($_POST['txtCity']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsSupplier(".$SupplierID.", '".$SupplierName."', '".$Address."', '".$City."', '".$Telephone."', ".$hdnIsEdit.", '".$_SESSION['UserLogin']."')";
		
		if (! $result=mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
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
