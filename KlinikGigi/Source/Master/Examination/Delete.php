<?php
	if(isset($_POST['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$MessageSuccessDelete = "";
		$MessageFailedDelete = "";
	
		$Data = $_POST['ID'];
		
		for($i=0; $i<count($Data); $i++) {
			try
			{
				$ExaminationData = explode("^", $Data[$i]);
				$ExaminationID = mysql_real_escape_string($ExaminationData[0]);
				$ExaminationName = $ExaminationData[1];
				$sql = "DELETE FROM master_examination WHERE ExaminationID = $ExaminationID";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($ExaminationName);
				}
				$MessageSuccessDelete .= "$ExaminationName, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "Jenis Periksa " .$MessageSuccessDelete. " Berhasil Dihapus";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "Jenis Periksa " .$MessageFailedDelete. " Gagal Dihapus";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>
