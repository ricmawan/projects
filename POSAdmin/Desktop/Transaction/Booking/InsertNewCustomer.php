<?php
	if(isset($_POST['txtCustomerCodeAdd'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		//include "../../GetPermission.php";
		include "../../DBConfig.php";
		include "../../GetSession.php";
		date_default_timezone_set("Asia/Jakarta");
		$EditFlag = "";
		$DeleteFlag = "";
		
		$sql = "CALL spSelUserMenuPermission('$DESKTOP_PATH', '$RequestedPath', '".$_SESSION['UserID']."')";
					
		if (!$result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), $RequestedPath, mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			return 0;
		}
		
		$cek = mysqli_num_rows($result);
		
		if($cek == 0) {
			echo '<script>
					var counterError = 0;
					Lobibox.alert("error",
					{
						msg: "User tidak memiliki akses untuk menu ini.",
						width: 480,
						delay: false,
						beforeClose: function() {
							if(counterError == 0) {
								//location.reload();
								counterError = 1;
								var lobibox = $(".lobibox-window").data("lobibox");
								lobibox.destroy();
							}
						}
					});
				</script>';
			exit;
		}
		else {
			$row = mysqli_fetch_array($result);
			$EditFlag = $row['EditFlag'];
			$DeleteFlag = $row['DeleteFlag'];
		}
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		$CustomerID = 0;
		$CustomerCode = mysqli_real_escape_string($dbh, $_POST['txtCustomerCodeAdd']);
		$CustomerName = mysqli_real_escape_string($dbh, $_POST['txtCustomerNameAdd']);
		$Telephone = mysqli_real_escape_string($dbh, $_POST['txtTelephoneAdd']);
		$Address = mysqli_real_escape_string($dbh, $_POST['txtAddressAdd']);
		$City = mysqli_real_escape_string($dbh, $_POST['txtCityAdd']);
		$Remarks = mysqli_real_escape_string($dbh, $_POST['txtRemarksAdd']);
		$hdnIsEdit = 0;
		$Message = "Terjadi Kesalahan Sistem!";
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
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/Booking/InsertNewCustomer.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
