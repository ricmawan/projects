<?php
	if(isset($_POST['hdnOutgoingInventoryID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$RoomID = mysql_real_escape_string($_POST['hdnRoomID']);
		$RateType = mysql_real_escape_string($_POST['rdRateType']);
		$DailyRate = mysql_real_escape_string($_POST['hdnDailyRate']);
		$HourlyRate = mysql_real_escape_string($_POST['hdnHourlyRate']);
		$StartDate = "";
		$EndDate = "";
		$StartDateHourly = "";
		$StartHour = "";
		$EndHour = "";
		$CustomerName = mysql_real_escape_string($_POST['txtCustomerName']);
		$BirthDate = mysql_real_escape_string($_POST['txtBirthDate']);
		$PhoneNumber = mysql_real_escape_string($_POST['txtPhoneNumber']);
		$Address = mysql_real_escape_string($_POST['txtAddress']);
		$Remarks = mysql_real_escape_string($_POST['txtRemarks']);
		$DownPaymentDate = mysql_real_escape_string($_POST['txtDownPaymentDate']);
		$DownPaymentAmount = mysql_real_escape_string($_POST['txtDownPaymentAmount']);
		$PaymentDate = mysql_real_escape_string($_POST['txtPaymentDate']);
		$PaymentAmount = mysql_real_escape_string($_POST['txtPaymentAmount']);
		if(isset($_POST['txtStartDate'])) $StartDate = mysql_real_escape_string($_POST['txtStartDate']);
		if(isset($_POST['txtStartDateHourly'])) $StartDateHourly = mysql_real_escape_string($_POST['txtStartDateHourly']);
		if(isset($_POST['txtEndDate'])) $EndDate = mysql_real_escape_string($_POST['txtEndDate']);
		if(isset($_POST['ddlStartHour'])) $StartHour = mysql_real_escape_string($_POST['ddlStartHour']);
		if(isset($_POST['ddlEndHour'])) $EndHour = mysql_real_escape_string($_POST['ddlEndHour']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_checkin
					(
						RoomID,
						TransactionDate,
						RateType,
						StartDate,
						EndDate,
						CustomerName,
						Phone,
						Address,
						BirthDate,
						Remarks,
						DownPaymentAmount,
						DownPaymentDate,
						PaymentAmount,
						PaymentDate,
						BookingFlag,
						DailyRate,
						HourlyRate,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						".$RoomID.",
						NOW(),
						'".$RateType."',
						'".$StartDate."',
						'".$EndDate."',
						'".$CustomerName."',
						'".$PhoneNumber."',
						'".$Address."',
						'".$BirthDate."',
						'".$Remarks."',
						".$DownPaymentAmount.",
						'".$DownPaymentDate."',
						".$PaymentAmount.",
						'".$PaymentDate."',
						".$BookingFlag.",
						".$DailyRate.",
						".$HourlyRate.",
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_checkin
					SET
						RoomID,
						TransactionDate,
						RateType,
						StartDate,
						EndDate,
						CustomerName,
						Phone,
						Address,
						BirthDate,
						Remarks,
						DownPaymentAmount,
						DownPaymentDate,
						PaymentAmount,
						PaymentDate,
						BookingFlag,
						DailyRate,
						HourlyRate,
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						CheckInID = $CheckInID";
		}

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
		mysql_query("COMMIT", $dbh);
		return 0;
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
