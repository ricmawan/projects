<?php
	date_default_timezone_set("Asia/Bangkok");
	require  __DIR__ . '/DBConfig_Host.php';
	require  __DIR__ . '/DBConfig_Sync.php';

	$State = 1;
	$sql = "SELECT
				PatientID,
				PatientNumber,
				PatientName,
				BirthDate,
				Address,
				City,
				Telephone,
				Allergy,
				Email,
				Info,
				NIK
			FROM
				master_patient
			WHERE
				Synchronized = 1
				AND DATE_FORMAT(ModifiedDate, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y')";

	if (! $result = mysqli_query($dbh3, $sql)) {
		file_put_contents('./sync.log', date("d-m-Y H:i:s") . " SendPatientUpdate.php State " . $State . "(". mysqli_error($dbh3) . ")\n", FILE_APPEND);
		return 0;
	}

	while ($row = mysqli_fetch_array($result)) {
		$State = 2;
		$sql2 = "CALL spInsPatient(".$row['PatientID'].", '".$row['NIK']."', '".$row['PatientNumber']."', '".$row['PatientName']."', '".$row['BirthDate']."', '".$row['Address']."', '".$row['Allergy']."', '".$row['City']."', '".$row['Telephone']."', '".$row['Email']."', 1, 'BatchSync', '".$row['Info']."')";

		if (! $result2=mysqli_query($dbh2, $sql2)) {
			file_put_contents('./sync.log',  date("d-m-Y H:i:s") . " SendPatientUpdate.php State " . $State . " PatientID : " . $row['PatientID'] . "(". mysqli_error($dbh2) . ")\n", FILE_APPEND);
			continue;
		}

		$row2 = mysqli_fetch_array($result2);
		mysqli_free_result($result2);
		mysqli_next_result($dbh2);

		if($row2['FailedFlag'] == 1) {
			file_put_contents('./sync.log',  date("d-m-Y H:i:s") . " SendPatientUpdate.php State " . $State . " PatientID : " . $row['PatientID'] . "(". $row2['Message'] . ")\n", FILE_APPEND);
		}

		else {
			file_put_contents('./sync.log', date("d-m-Y H:i:s") . " SendPatientUpdate.php State " . $State . " PatientID : " . $row['PatientID'] . "(Success)\n", FILE_APPEND);
		}
	}
	
	return 0;
?>