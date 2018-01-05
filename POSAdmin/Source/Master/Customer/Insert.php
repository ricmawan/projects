<?php
	if(isset($_POST['hdnCustomerID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$CustomerID = mysqli_real_escape_string($dbh, $_POST['hdnCustomerID']);
		$CustomerCode = mysqli_real_escape_string($dbh, $_POST['txtCustomerCode']);
		$CustomerName = mysqli_real_escape_string($dbh, $_POST['txtCustomerName']);
		$Telephone = mysqli_real_escape_string($dbh, $_POST['txtTelephone']);
		$Address = mysqli_real_escape_string($dbh, $_POST['txtAddress']);
		$City = mysqli_real_escape_string($dbh, $_POST['txtCity']);
		$Remarks = mysqli_real_escape_string($dbh, $_POST['txtRemarks']);
		$hdnIsEdit = mysqli_real_escape_string($dbh, $_POST['hdnIsEdit']);
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$sql = "CALL spInsCustomer( ".$CustomerID.",
									'".$CustomerCode."',
									'".$CustomerName."',
									'".$Telephone."',
									'".$Address."',
									'".$City."',
									'".$Remarks."',
									".$hdnIsEdit.",
									'".$_SESSION['UserLogin']."'
							  )";
		
		if (! $result=mysqli_query($dbh, $sql)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Master/Customer/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
