<?php
	if(isset($_GET['ScheduleID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../DBConfig.php";
		$OnlineScheduleID = base64_url_decode($_GET['ScheduleID']);
		$CustomerConfirmation = base64_url_decode($_GET['Confirmation']);
		//echo $ScheduledDate;
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$Message = "Data has been saved!";
		$MessageDetail = "";
		$FailedFlag = 0;
		//echo $DetailID;		
		$State = 1;
		
		$sql2 = 'UPDATE transaction_onlineschedule
				SET
					CustomerConfirmation = '".$CustomerConfirmation."',
					ConfirmedDate = NOW()
				WHERE
					OnlineScheduleID = '.$OnlineScheduleID;

		//AND IFNULL(ConfirmedDate, 0) = 0';
			
			if (! $result2 = mysql_query($sql2, $dbh)) {
				echo mysql_error();
			}
			//$mail->clearAddresses();

	//		echo returnstate($Message, $MessageDetail, $FailedFlag, $State);
			echo "<script>alert('Your confirmation has been saved!'); </script>";
			return 0;
	}
	
	function base64_url_decode($input)
	{
		return base64_decode(strtr($input, '-_,', '+/='));
	}
?>
