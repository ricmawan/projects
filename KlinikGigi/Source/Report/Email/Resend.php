<?php
	if(ISSET($_POST['CheckScheduleID'])) {
		$ID = mysql_real_escape_string($_POST['CheckScheduleID']);
		$Message = "Email berhasil dikirim";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		require '..\..\assets\lib\PHPMailer\PHPMailerAutoload.php';
		require '..\..\DBConfig.php';
		$MessageBody = file_get_contents( "..\..\EmailTemplate.html");
		
		$mail = new PHPMailer;

		//$mail->SMTPDebug = 1;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Host = 'smtp.live.com';  // Specify main and backup SMTP servers
		$mail->Username = 'ricmawan@hotmail.com';                 // SMTP username
		$mail->Password = 'Wijayaadi10+14=0811';                           // SMTP password
		$mail->Port = 587;                                    // TCP port to connect to
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted

		$mail->setFrom('ricmawan@hotmail.com', 'Mailer');
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->addReplyTo('ricmawan@hotmail.com', 'Information');
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
					CS.CheckScheduleID = $ID";
					
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
							CheckScheduleID = ".$ID;
				$FailedFlag = 1;
				$MessageDetail = $mail->ErrorInfo;
				$Message = "Email gagal dikirim";
				
			} else {
				$sql2 = 'UPDATE transaction_checkschedule
						SET
							EmailStatus = "Sent",
							DeliveredDate = NOW()					
						WHERE
							CheckScheduleID = '.$ID;
			}
			
			if (! $result2 = mysql_query($sql2, $dbh)) {
				echo mysql_error();
			}
		}
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
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