<?php
	require __DIR__ . '\assets\lib\PHPMailer\PHPMailerAutoload.php';
	require  __DIR__ . '\DBConfig.php';
	$MessageBody = file_get_contents( __DIR__ . "\EmailTemplate.html");
	
	$mail = new PHPMailer;

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Host = 'smtp-mail.outlook.com';  // Specify main and backup SMTP servers
	$mail->Username = 'test@example.com';                 // SMTP username
	$mail->Password = '*************';                           // SMTP password
	$mail->Port = 587;                                    // TCP port to connect to
	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted

	$mail->setFrom('test@example.com', 'Mailer');
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->addReplyTo('info@example.com', 'Information');
	$monthName = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
	$dayName = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");

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
				ADDTIME(CS.ScheduledDate, '06:00:00') <= NOW()
				AND IFNULL(CS.EmailStatus, '') <> 'Sent'";
				
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
	}
		
	while ($row = mysql_fetch_array($result)) {
		$MessageSent = $MessageBody;
		$MessageSent = str_replace("[Day_Name]", $dayName[$row['DayCount']], $MessageSent);
		$MessageSent = str_replace("[ScheduledDate]", date("d", strtotime($row['ScheduledDate'])) . " " . $monthName[date("m", strtotime($row['ScheduledDate'])) - 1] . " " . date("Y", strtotime($row['ScheduledDate'])), $MessageSent);
		$mail->addAddress($row['Email'], $row['PatientName']);     // Add a recipient
		$mail->Subject = 'Pengingat Pemeriksaan Gigi';
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
						DeliveredDate = NOW()					
					WHERE
						CheckScheduleID = '.$row['CheckScheduleID'];
		}
		
		if (! $result2 = mysql_query($sql2, $dbh)) {
			echo mysql_error();
		}
	}
?>