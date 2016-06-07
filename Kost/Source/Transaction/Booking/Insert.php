<?php
	SESSION_START();
	if(isset($_POST['hdnRoomID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		//include "../../GetPermission.php";
		include "../../DBConfig.php";
		$RoomID = mysql_real_escape_string($_POST['hdnRoomID']);
		$RateType = mysql_real_escape_string($_POST['rdRateType']);
		$DailyRate = mysql_real_escape_string($_POST['hdnDailyRate']);
		$HourlyRate = mysql_real_escape_string($_POST['hdnHourlyRate']);
		$BookingID = mysql_real_escape_string($_POST['hdnBookingID']);
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
		
		$BookingFlag = 0;
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
		if($hdnIsEdit == 0) {			
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
						AND CheckOutFlag = 0";
						
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
						AND IsCancelled = 0
						AND BookingID <> $BookingID";

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
			
			/*$State = 3;
			$sql = "UPDATE
						master_room
					SET
						StatusID = 2
					WHERE
						RoomID = $RoomID";
						
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($RoomID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}*/
			
			$State = 4;
			$sql = "INSERT INTO transaction_booking
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
						CheckInFlag,
						DailyRate,
						HourlyRate,
						IsCancelled,
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
						0,
						".$DailyRate.",
						".$HourlyRate.",
						0,
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
