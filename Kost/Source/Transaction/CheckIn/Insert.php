<?php
	if(isset($_POST['hdnRoomID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//include "../../DBConfig.php";
		$RoomID = mysql_real_escape_string($_POST['hdnRoomID']);
		$RateType = mysql_real_escape_string($_POST['rdRateType']);
		$DailyRate = mysql_real_escape_string($_POST['hdnDailyRate']);
		$HourlyRate = mysql_real_escape_string($_POST['hdnHourlyRate']);
		$BookingID = mysql_real_escape_string($_POST['hdnBookingID']);
		$CheckInID = mysql_real_escape_string($_POST['hdnCheckInID']);
		$StartDate = "";
		$EndDate = "";
		$StartDateHourly = "";
		$StartHour = "";
		$EndHour = "";
		$CustomerName = mysql_real_escape_string($_POST['txtCustomerName']);
		$BirthDate = explode('-', mysql_real_escape_string($_POST['txtBirthDate']));
		$BirthDate = "$BirthDate[2]-$BirthDate[1]-$BirthDate[0]";
		$PhoneNumber = mysql_real_escape_string($_POST['txtPhoneNumber']);
		$Address = mysql_real_escape_string($_POST['txtAddress']);
		$Remarks = mysql_real_escape_string($_POST['txtRemarks']);
		if($_POST['txtDownPaymentDate'] != "") {
			$DownPaymentDate = explode('-', mysql_real_escape_string($_POST['txtDownPaymentDate']));
			$DownPaymentDate = "$DownPaymentDate[2]-$DownPaymentDate[1]-$DownPaymentDate[0]";
		}
		else $DownPaymentDate = "";
		$DownPaymentAmount = $_POST['txtDownPaymentAmount'];
		if($DownPaymentAmount == "") $DownPaymentAmount = 0;
		
		if($_POST['txtPaymentDate'] != "") {
			$PaymentDate = explode('-', mysql_real_escape_string($_POST['txtPaymentDate']));
			$PaymentDate = "$PaymentDate[2]-$PaymentDate[1]-$PaymentDate[0]";
		}
		else $PaymentDate = "";
		
		$PaymentAmount = $_POST['txtPaymentAmount'];
		if($PaymentAmount == "") $PaymentAmount = 0;
		if(isset($_POST['txtStartDate'])) {
			$StartDate = explode('-', mysql_real_escape_string($_POST['txtStartDate']));
			$StartDate = "$StartDate[2]-$StartDate[1]-$StartDate[0] 14:00:00";
		}
		if(isset($_POST['txtEndDate'])) {
			$EndDate = explode('-', mysql_real_escape_string($_POST['txtEndDate']));
			$EndDate = "$EndDate[2]-$EndDate[1]-$EndDate[0] 12:00:00";
		}
		if(isset($_POST['txtStartDateHourly']) && isset($_POST['ddlStartHour']) && isset($_POST['ddlEndHour'])) {
			$StartDate = explode('-', mysql_real_escape_string($_POST['txtStartDateHourly']));
			$EndDate = "$StartDate[2]-$StartDate[1]-$StartDate[0] ".mysql_real_escape_string($_POST['ddlEndHour']).":00:00";
			$StartDate = "$StartDate[2]-$StartDate[1]-$StartDate[0] ".mysql_real_escape_string($_POST['ddlStartHour']).":00:00";
		}
		
		if($BookingID != 0) $BookingFlag = 1;
		else $BookingFlag = 0;
		//if(isset($_POST['ddlStartHour'])) $StartHour = mysql_real_escape_string($_POST['ddlStartHour']);
		//if(isset($_POST['ddlEndHour'])) $EndHour = mysql_real_escape_string($_POST['ddlEndHour']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;
		$State = 1;
		$sql = "SELECT
					1
				FROM
					transaction_checkin
				WHERE
					(StartDate BETWEEN '".$StartDate."' AND '".$EndDate."'
					OR EndDate BETWEEN '".$StartDate."' AND '".$EndDate."'
					OR '".$StartDate."' BETWEEN StartDate AND EndDate
					OR '".$EndDate."' BETWEEN StartDate AND EndDate)
					AND CheckInID <> $CheckInID
					AND CheckOutFlag = 0
					AND RoomID = $RoomID";
					
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($RoomID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		$State = 2;
		$sql = "SELECT
					1
				FROM
					transaction_booking
				WHERE
					(StartDate BETWEEN '".$StartDate."' AND '".$EndDate."'
					OR EndDate BETWEEN '".$StartDate."' AND '".$EndDate."'
					OR '".$StartDate."' BETWEEN StartDate AND EndDate
					OR '".$EndDate."' BETWEEN StartDate AND EndDate)
					AND CheckInFlag = 0
					AND BookingID <> $BookingID
					AND IsCancelled = 0
					AND RoomID = $RoomID";
					
		if (! $result2 = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($RoomID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		if(mysql_num_rows($result) > 0 || mysql_num_rows($result2) > 0) {
			$Message = "Kamar tidak tersedia untuk tanggal yang dipilih!";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($RoomID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		if($hdnIsEdit == 0) {			
			$State = 3;
			$sql = "UPDATE
						master_room
					SET
						StatusID = 3
					WHERE
						RoomID = $RoomID";
						
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($RoomID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			if($BookingID != 0) {
				$State = 4;
				$sql = "UPDATE
							transaction_booking
						SET
							CheckInFlag = 1
						WHERE
							BookingID = $BookingID";
							
				if (! $result = mysql_query($sql, $dbh)) {
					$Message = "Terjadi Kesalahan Sistem";
					$MessageDetail = mysql_error();
					$FailedFlag = 1;
					echo returnstate($RoomID, $Message, $MessageDetail, $FailedFlag, $State);
					mysql_query("ROLLBACK", $dbh);
					return 0;
				}
			}
			
			$State = 5;
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
						CheckOutFlag,
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
						".str_replace(",", "", $DownPaymentAmount).",
						'".$DownPaymentDate."',
						".str_replace(",", "", $PaymentAmount).",
						'".$PaymentDate."',
						".$BookingFlag.",
						0,
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
						TransactionDate = NOW(),
						RateType = '".$RateType."',
						StartDate = '".$StartDate."',
						EndDate = '".$EndDate."',
						CustomerName = '".$CustomerName."',
						Phone = '".$PhoneNumber."',
						Address = '".$Address."',
						BirthDate = '".$BirthDate."',
						Remarks = '".$Remarks."',
						DownPaymentAmount = ".str_replace(",", "", $DownPaymentAmount).",
						DownPaymentDate = '".$DownPaymentDate."',
						PaymentAmount = ".str_replace(",", "", $PaymentAmount).",
						PaymentDate = '".$PaymentDate."',
						BookingFlag = ".$BookingFlag.",
						DailyRate = ".$DailyRate.",
						HourlyRate = ".$HourlyRate.",
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						CheckInID = $CheckInID";
		}

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($RoomID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		echo returnstate($RoomID, $Message, $MessageDetail, $FailedFlag, $State);
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
