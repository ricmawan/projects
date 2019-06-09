<?php
	if(isset($_POST['txtPatientName'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$txtPatientName = mysql_real_escape_string($_POST['txtPatientName']);
		$txtPhone = mysql_real_escape_string($_POST['txtPhone']);
		$ddlTime = mysql_real_escape_string($_POST['ddlTime']);
		$txtEmail = mysql_real_escape_string($_POST['txtEmail']);
		$ddlBranch = mysql_real_escape_string($_POST['hdnDDLBranch']);
		$ScheduledDate = $_POST['hdnStartDate'] . " " . $ddlTime;
		//$DayOfWeek = date("w", strtotime($_POST['hdnStartDate']));
		//echo $ScheduledDate;
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;		
		$State = 1;
		$countNumber = 0;
		$sql = "SELECT
					COUNT(1) countNumber,
					ScheduledDate
				FROM
					transaction_checkschedule
				WHERE
					ScheduledDate = '".$ScheduledDate."'
					AND $ddlBranch = 1
				GROUP BY
					ScheduledDate";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		$row = mysql_fetch_array($result);
		$countNumber += $row['countNumber'];
		
		
		$sql = "SELECT
					COUNT(1) countNumber,
					ScheduledDate
				FROM
					transaction_onlineschedule
				WHERE
					ScheduledDate  = '".$ScheduledDate."'
					AND BranchID = $ddlBranch
				GROUP BY
					ScheduledDate";
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		
		$row = mysql_fetch_array($result);
		$countNumber += $row['countNumber'];
		
		
		if($countNumber >= $MINUTE_SCHEDULE_LIMIT) {
			$Message = "Jadwal untuk jam yang dipilih sudah penuh!";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		else {
			$sql = "INSERT INTO transaction_onlineschedule
					(
						ScheduledDate,
						PatientName,
						PhoneNumber,
						Email,
						BranchID,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'".$ScheduledDate."',
						'".$txtPatientName."',
						'".$txtPhone."',
						'".$txtEmail."',
						".$ddlBranch.",
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
					
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}

			mysql_query("COMMIT", $dbh);
			
			/*require '../../assets/lib/PHPMailer/PHPMailerAutoload.php';
			$MessageBody = file_get_contents( __DIR__ . "/NotificationEmailTemplate.html");
			
			$mail = new PHPMailer;

			//$mail->SMTPDebug = 3;                               // Enable verbose debug output

			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Host = 'mx1.hostinger.co.id';  // Specify main and backup SMTP servers
			$mail->Username = 'cs@imdentalspecialist.com';                 // SMTP username
			$mail->Password = 'imdentalspecialist';                           // SMTP password
			$mail->Port = 587;                                    // TCP port to connect to
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted

			$mail->setFrom('cs@imdentalspecialist.com', 'IM Dental Specialist');
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->addReplyTo('noreply@imdentalspecialist.com', 'Information');
			$monthName = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
			$monthName2 = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
			$dayName = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
			$dayName2 = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

			$MessageSent = $MessageBody;
			$MessageSent = str_replace("[Day_Name]", $dayName[$DayOfWeek], $MessageSent);
			$MessageSent = str_replace("[Day_Name2]", $dayName2[$DayOfWeek], $MessageSent);
			$MessageSent = str_replace("[ScheduledDate]", date("d", strtotime($ScheduledDate)) . " " . $monthName[date("m", strtotime($ScheduledDate)) - 1] . " " . date("Y", strtotime($ScheduledDate)), $MessageSent);
			$MessageSent = str_replace("[ScheduledDate2]", $monthName2[date("m", strtotime($ScheduledDate)) - 1] . " " . date("d", strtotime($ScheduledDate)) . ", " . date("Y", strtotime($ScheduledDate)), $MessageSent);
			$mail->addAddress($txtEmail, $txtPatientName);     // Add a recipient
			$mail->Subject = 'Dental Examination Reminder';
			$mail->Body    = str_replace('[Patient_Name]', $txtPatientName, $MessageSent);
			//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			if(!$mail->send()) {
				/*$sql2 = "UPDATE transaction_onlineschedule
						SET
							EmailStatus = '".$mail->ErrorInfo."'
						WHERE
							OnlineScheduleID = ".$row['OnlineScheduleID'];
			} else {
				/*$sql2 = 'UPDATE transaction_onlineschedule
						SET
							EmailStatus = "Sent",
							DeliveredDate = NOW()
						WHERE
							OnlineScheduleID = '.$row['OnlineScheduleID'];*/
			}
			
			/*if (! $result2 = mysql_query($sql2, $dbh)) {
				echo mysql_error();
			}*/
			//$mail->clearAddresses();

			echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
	}
	
	function returnstate($Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
