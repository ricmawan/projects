<?php
	if(isset($_GET['ScheduleID'])) {
		include "../DBConfig.php";
		$OnlineScheduleID = base64_url_decode($_GET['ScheduleID']);
		$CustomerConfirmation = base64_url_decode($_GET['Confirmation']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$Message = "Data has been saved!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;

		$sql1 = "SELECT
					1
				FROM
					transaction_onlineschedule TOS
				WHERE
					DATE_FORMAT(NOW(), '%Y-%m-%d') = DATE_FORMAT(TOS.ScheduledDate, '%Y-%m-%d')
					AND OnlineScheduleID = ".$OnlineScheduleID;

		//DATE_ADD(NOW(), INTERVAL 7 HOUR)

		if (! $result1 = mysql_query($sql1, $dbh)) {
			echo mysql_error();
			mysql_query("ROLLBACK", $dbh);
		}

		$cek = mysql_num_rows($result1);
		if($cek > 0) {
			$sql = "UPDATE transaction_onlineschedule
					SET
						CustomerConfirmation = '".$CustomerConfirmation."',
						ConfirmedDate = NOW()
					WHERE
						OnlineScheduleID = ".$OnlineScheduleID;


			if (! $result = mysql_query($sql, $dbh)) {
				echo mysql_error();
				mysql_query("ROLLBACK", $dbh);
			}

			mysql_query("COMMIT", $dbh);
			
			echo "<script>alert('Your confirmation has been saved!');close(); </script>";
		}
		
		else {
			echo "<script>alert('You are too late!');close(); </script>";
		}
		
		return 0;
	}

	function base64_url_decode($input)
	{
		return base64_decode(strtr($input, '-_,', '+/='));
	}
?>
