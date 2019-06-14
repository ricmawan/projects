<?php
	require __DIR__ . '/assets/lib/PHPMailer/PHPMailerAutoload.php';
	require  __DIR__ . '/DBConfig.php';
	$MessageBody = file_get_contents( __DIR__ . "/EmailTemplate.html");
	$MessageBody_Nurse = file_get_contents( __DIR__ . "/EmailTemplate_Nurse.html");
	
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

	//$mail->addCC('cc@example.com');
	//$mail->addBCC('bcc@example.com');

	//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

	$sql = "SELECT
				CS.CheckScheduleID,
				CS.ScheduledDate,
				DATE_FORMAT(CS.ScheduledDate, '%w') DayCount,
				MP.PatientName,
				MP.Email
			FROM
				transaction_checkschedule CS
				JOIN master_patient MP
					ON MP.PatientID = CS.PatientID
			WHERE
				DATE_FORMAT(DATE_ADD(CS.ScheduledDate, INTERVAL -1 DAY), '%Y-%m-%d') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 HOUR), '%Y-%m-%d')
				AND DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 HOUR), '%Y-%m-%d 10:00:00') <= DATE_ADD(NOW(), INTERVAL 7 HOUR)
				AND IFNULL(CS.EmailStatus, '') <> 'Sent'";
				
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
	}
		
	while ($row = mysql_fetch_array($result)) {
		$MessageSent = $MessageBody;
		$MessageSent = str_replace("[Day_Name]", $dayName[$row['DayCount']], $MessageSent);
		$MessageSent = str_replace("[Day_Name2]", $dayName2[$row['DayCount']], $MessageSent);
		$MessageSent = str_replace("[ScheduledDate]", date("d", strtotime($row['ScheduledDate'])) . " " . $monthName[date("m", strtotime($row['ScheduledDate'])) - 1] . " " . date("Y", strtotime($row['ScheduledDate'])), $MessageSent);
		$MessageSent = str_replace("[ScheduledDate2]", $monthName2[date("m", strtotime($row['ScheduledDate'])) - 1] . " " . date("d", strtotime($row['ScheduledDate'])) . ", " . date("Y", strtotime($row['ScheduledDate'])), $MessageSent);

		$mail->addAddress($row['Email'], $row['PatientName']);     // Add a recipient
		$mail->Subject = 'Dental Examination Reminder';
		$mail->Body    = str_replace('[Patient_Name]', $row['PatientName'], $MessageSent);
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if(!$mail->send()) {
			$sql2 = "UPDATE transaction_checkschedule
					SET
						EmailStatus = '".$mail->ErrorInfo."'
					WHERE
						CheckScheduleID = ".$row['CheckScheduleID'];
		} else {
			$sql2 = 'UPDATE transaction_checkschedule
					SET
						EmailStatus = "Sent",
						DeliveredDate = DATE_ADD(NOW(), INTERVAL 7 HOUR)
					WHERE
						CheckScheduleID = '.$row['CheckScheduleID'];
		}
		
		if (! $result2 = mysql_query($sql2, $dbh)) {
			echo mysql_error();
		}
		$mail->clearAddresses();

		$MessageSent2 = $MessageBody_Nurse;
		$MessageSent2 = str_replace("[ScheduledDate]", date("d", strtotime($row['ScheduledDate'])) . " " . $monthName[date("m", strtotime($row['ScheduledDate'])) - 1] . " " . date("Y", strtotime($row['ScheduledDate'])) . " " . date("H", strtotime($row['ScheduledDate'])) . ":" . date("i", strtotime($row['ScheduledDate'])), $MessageSent2);
		$MessageSent2 = str_replace("[Day_Name]", $dayName[$row['DayCount']], $MessageSent2);

		$mail->addAddress($NURSE_MAIL_ADDRESS1, 'imdentalspecialist');     // Add a recipient
		$mail->addCC($NURSE_MAIL_ADDRESS2, 'imdentalspecialist');     // Add a recipient
		$mail->Subject = 'Dental Examination Reminder';
		$mail->Body    = str_replace('[Patient_Name]', $row['PatientName'], $MessageSent2);

		$mail->send();
		$mail->clearAddresses();
	}
	
	$sql = "SELECT
				OS.OnlineScheduleID,
				OS.ScheduledDate,
				DATE_FORMAT(OS.ScheduledDate, '%w') DayCount,
				OS.PatientName,
				OS.Email
			FROM
				transaction_onlineschedule OS
			WHERE
				DATE_FORMAT(DATE_ADD(OS.ScheduledDate, INTERVAL -1 DAY), '%Y-%m-%d') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 HOUR), '%Y-%m-%d')
				AND DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 HOUR), '%Y-%m-%d 10:00:00') <= DATE_ADD(NOW(), INTERVAL 7 HOUR)
				AND IFNULL(OS.EmailStatus, '') <> 'Sent'";
				
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
	}
		
	while ($row = mysql_fetch_array($result)) {
		$MessageSent = $MessageBody;
		$MessageSent = str_replace("[Day_Name]", $dayName[$row['DayCount']], $MessageSent);
		$MessageSent = str_replace("[Day_Name2]", $dayName2[$row['DayCount']], $MessageSent);
		$MessageSent = str_replace("[ScheduledDate]", date("d", strtotime($row['ScheduledDate'])) . " " . $monthName[date("m", strtotime($row['ScheduledDate'])) - 1] . " " . date("Y", strtotime($row['ScheduledDate'])), $MessageSent);
		$MessageSent = str_replace("[ScheduledDate2]", $monthName2[date("m", strtotime($row['ScheduledDate'])) - 1] . " " . date("d", strtotime($row['ScheduledDate'])) . ", " . date("Y", strtotime($row['ScheduledDate'])), $MessageSent);
		$mail->addAddress($row['Email'], $row['PatientName']);     // Add a recipient
		$mail->Subject = 'Dental Examination Reminder';
		$mail->Body    = str_replace('[Patient_Name]', $row['PatientName'], $MessageSent);
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	
		if(!$mail->send()) {
			$sql2 = "UPDATE transaction_onlineschedule
					SET
						EmailStatus = '".$mail->ErrorInfo."'
					WHERE
						OnlineScheduleID = ".$row['OnlineScheduleID'];
		} else {
			$sql2 = 'UPDATE transaction_onlineschedule
					SET
						EmailStatus = "Sent",
						DeliveredDate = DATE_ADD(NOW(), INTERVAL 7 HOUR)
					WHERE
						OnlineScheduleID = '.$row['OnlineScheduleID'];
		}
		
		if (! $result2 = mysql_query($sql2, $dbh)) {
			echo mysql_error();
		}
		$mail->clearAddresses();

		$MessageSent2 = $MessageBody_Nurse;
		$MessageSent2 = str_replace("[ScheduledDate]", date("d", strtotime($row['ScheduledDate'])) . " " . $monthName[date("m", strtotime($row['ScheduledDate'])) - 1] . " " . date("Y", strtotime($row['ScheduledDate'])), $MessageSent2);
		$MessageSent2 = str_replace("[Day_Name]", $dayName[$row['DayCount']], $MessageSent2);

		$mail->addAddress($NURSE_MAIL_ADDRESS1, 'imdentalspecialist');     // Add a recipient
		$mail->addCC($NURSE_MAIL_ADDRESS2, 'imdentalspecialist');     // Add a recipient
		$mail->Subject = 'Dental Examination Reminder';
		$mail->Body    = str_replace('[Patient_Name]', $row['PatientName'], $MessageSent2);

		$mail->send();
		$mail->clearAddresses();
	}
?>