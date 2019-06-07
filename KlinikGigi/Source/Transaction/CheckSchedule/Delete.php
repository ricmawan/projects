<?php
	if(isset($_POST['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$MessageSuccessDelete = "";
		$MessageFailedDelete = "";
	
		$ID = mysql_real_escape_string($_POST['ID']);
		$OnlineFlag = mysql_real_escape_string($_POST['OnlineFlag']);
		try
		{
			$sql = "delete from transaction_checkschedule WHERE CheckScheduleID = ".$ID." AND 0 = ".$OnlineFlag;
			if (! $result=mysql_query($sql, $dbh)) {
				throw new Exception($ID);
			}
			$MessageSuccessDelete .= "$ID, ";

			$sql = "delete from transaction_onlineschedule WHERE OnlineScheduleID = ".$ID." AND 1 = ".$OnlineFlag;
			if (! $result=mysql_query($sql, $dbh)) {
				throw new Exception($ID);
			}
			$MessageSuccessDelete .= "$ID, ";
		}
		catch (Exception $e)
		{
			$MessageFailedDelete .= $e->getMessage() .", ";
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "Jadwal Berhasil Dibatalkan";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "Jadwal Gagal Dibatalkan";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>
