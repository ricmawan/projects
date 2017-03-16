<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestPath);
	$RequestPath = str_replace($file, "", $RequestPath);
	include "../../GetPermission.php";
	
	$sql = "SELECT
				PatientID,
				PatientNumber,
				PatientName,
				CONCAT(PatientNumber, '_', REPLACE(PatientName, ' ', '_')) FolderName
			FROM
				master_patient";
				
	if (! $result = mysql_query($sql, $dbh)) {
		echo mysql_error();
		return 0;
	}
	
	$RootFolder = '../../assets/photos/';
	while ($row = mysql_fetch_array($result)) {
		$FolderName = $RootFolder . $row['FolderName'];
		if(!is_dir($FolderName)) {
			mkdir($FolderName, true);
			mkdir($FolderName . "/Sebelum", true);
			mkdir($FolderName . "/Proses", true);
			mkdir($FolderName . "/Setelah", true);
		}
	}
?>