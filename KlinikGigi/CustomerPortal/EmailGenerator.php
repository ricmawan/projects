<?php
	require __DIR__ . '/assets/lib/PHPMailer/PHPMailerAutoload.php';
	require  __DIR__ . '/DBConfig.php';
	$MessageBody = file_get_contents( __DIR__ . "/EmailTemplate.html");
	//$MessageBody_Nurse = file_get_contents( __DIR__ . "/EmailTemplate_Nurse.html");
	
	$mail = new PHPMailer;

	$mail->SMTPDebug = 2;                               // Enable verbose debug output
	//Ask for HTML-friendly debug output
	$mail->Debugoutput = 'html';

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
	$mail->Username = '****@gmail.com';                 // SMTP username
	//$mail->From = 'cs@imdentalspecialist.com';
	$mail->Password = '****';                           // SMTP password
	$mail->Port = 587;                                    // TCP port to connect to
	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted

	$mail->setFrom('cs@imdentalspecialist.com', 'IM Dental Specialist');
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->addReplyTo('noreply@imdentalspecialist.com', 'Information');
	$monthName = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
	$monthName2 = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	$dayName = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
	$dayName2 = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

	//$mail->addCC('cc@example.com');
	//$mail->addBCC('bcc@example.com');

	//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	
	$sql3 = "SELECT
				OS.OnlineScheduleID,
				OS.ScheduledDate,
				DATE_FORMAT(OS.ScheduledDate, '%w') DayCount,
				OS.PatientName,
				OS.Email,
				MU.Username DoctorName,
				MB.BranchName
			FROM
				transaction_onlineschedule OS
				JOIN master_user MU
					ON OS.DoctorID = MU.UserID
				JOIN master_branch MB
					ON MB.BranchID = OS.BranchID
			WHERE
				IFNULL(OS.EmailStatus, '') <> 'Sent'
				AND CustomerSelfRegFlag = 1
			LIMIT
				20";

	//DATE_FORMAT(DATE_ADD(OS.ScheduledDate, INTERVAL -2 DAY), '%Y-%m-%d') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 HOUR), '%Y-%m-%d')
				//AND DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 HOUR), '%Y-%m-%d 10:00:00') <= DATE_ADD(NOW(), INTERVAL 7 HOUR)
				//AND 
				
	if (! $result3 = mysql_query($sql3, $dbh)) {
		echo mysql_error();
	}
		
	while ($row3 = mysql_fetch_array($result3)) {

		$url = 'http://localhost/Projects/KlinikGigi/CustomerPortal/Scheduling/Confirm.php?ScheduleID='.base64_url_encode($row3['OnlineScheduleID']).'&Confirmation=';

		$MessageSent = $MessageBody;

		$MessageSent = str_replace("[URLY]", $url . base64_url_encode('Y'), $MessageSent);
		$MessageSent = str_replace("[URLN]", $url . base64_url_encode('N'), $MessageSent);

		$MessageSent = str_replace("[BranchName]", $row3['BranchName'], $MessageSent);
		$MessageSent = str_replace("[DoctorName]", $row3['DoctorName'], $MessageSent);
		
		$MessageSent = str_replace("[Day_Name]", $dayName[$row3['DayCount']], $MessageSent);
		$MessageSent = str_replace("[Day_Name2]", $dayName2[$row3['DayCount']], $MessageSent);
		$MessageSent = str_replace("[ScheduledDate]", date("d", strtotime($row3['ScheduledDate'])) . " " . $monthName[date("m", strtotime($row3['ScheduledDate'])) - 1] . " " . date("Y", strtotime($row3['ScheduledDate'])), $MessageSent);
		$MessageSent = str_replace("[ScheduledDate2]", $monthName2[date("m", strtotime($row3['ScheduledDate'])) - 1] . " " . date("d", strtotime($row3['ScheduledDate'])) . ", " . date("Y", strtotime($row3['ScheduledDate'])), $MessageSent);
		$mail->addAddress($row3['Email'], $row3['PatientName']);     // Add a recipient
		$mail->Subject = 'Dental Examination Confirmation';
		$mail->Body    = str_replace('[Patient_Name]', $row3['PatientName'], $MessageSent);
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	
		if(!$mail->send()) {
			$sql4 = "UPDATE transaction_onlineschedule
					SET
						EmailStatus = '".$mail->ErrorInfo."'
					WHERE
						OnlineScheduleID = ".$row3['OnlineScheduleID'];
		} else {
			$sql4 = 'UPDATE transaction_onlineschedule
					SET
						EmailStatus = "Sent",
						DeliveredDate = DATE_ADD(NOW(), INTERVAL 7 HOUR)
					WHERE
						OnlineScheduleID = '.$row3['OnlineScheduleID'];
		}
		
		if (! $result4 = mysql_query($sql4, $dbh)) {
			echo mysql_error();
		}
		$mail->clearAddresses();

		/*$MessageSent2 = $MessageBody_Nurse;
		$MessageSent2 = str_replace("[ScheduledDate]", date("d", strtotime($row3['ScheduledDate'])) . " " . $monthName[date("m", strtotime($row3['ScheduledDate'])) - 1] . " " . date("Y", strtotime($row3['ScheduledDate'])), $MessageSent2);
		$MessageSent2 = str_replace("[Day_Name]", $dayName[$row3['DayCount']], $MessageSent2);

		$mail->addAddress($NURSE_MAIL_ADDRESS1, 'imdentalspecialist');     // Add a recipient
		$mail->addCC($NURSE_MAIL_ADDRESS2, 'imdentalspecialist');     // Add a recipient
		$mail->Subject = 'Dental Examination Reminder';
		$mail->Body    = str_replace('[Patient_Name]', $row3['PatientName'], $MessageSent2);*/

		$mail->send();
		$mail->clearAddresses();
		$mail->clearCCs();
	}

	function base64_url_encode($input)
	{
		return strtr(base64_encode($input), '+/=', '-_,');
	}
?>