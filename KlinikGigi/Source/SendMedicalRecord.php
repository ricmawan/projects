<?php
	date_default_timezone_set("Asia/Bangkok");
	require  __DIR__ . '/DBConfig_Host.php';
	require  __DIR__ . '/DBConfig.php';

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
				Info
			FROM
				master_patient
			WHERE
				Synchronized = 0
			 LIMIT
			 	100";

	if (! $result = mysqli_query($dbh3, $sql)) {
		file_put_contents('./sync.log', date("d-m-Y H:i:s") . " SendMedicalRecord.php State " . $State . "(". mysqli_error($dbh3) . ")\n", FILE_APPEND);
		return 0;
	}

	while ($row = mysqli_fetch_array($result)) {
		$State = 2;
		$sql2 = "CALL spInsPatient(".$row['PatientID'].", '".$row['PatientNumber']."', '".$row['PatientName']."', '".$row['BirthDate']."', '".$row['Address']."', '".$row['Allergy']."', '".$row['City']."', '".$row['Telephone']."', '".$row['Email']."', 0, 'BatchSync', '".$row['Info']."')";

		if (! $result2=mysqli_query($dbh2, $sql2)) {
			file_put_contents('./sync.log',  date("d-m-Y H:i:s") . " SendMedicalRecord.php State " . $State . " PatientID : " . $row['PatientID'] . "(". mysqli_error($dbh2) . ")\n", FILE_APPEND);
			continue;
		}

		$row2 = mysqli_fetch_array($result2);
		mysqli_free_result($result2);
		mysqli_next_result($dbh2);

		$State = 3;
		$sql3 = "UPDATE master_patient
				SET
					Synchronized = 1
				WHERE
					PatientID = ".$row['PatientID']."";

		if (! $result3 = mysqli_query($dbh3, $sql3)) {
			file_put_contents('./sync.log', date("d-m-Y H:i:s") . " SendMedicalRecord.php State " . $State . " PatientID : " . $row['PatientID'] . "(". mysqli_error($dbh3) . ")\n", FILE_APPEND);
			continue;
		}

		if($row2['FailedFlag'] == 1) {
			file_put_contents('./sync.log',  date("d-m-Y H:i:s") . " SendMedicalRecord.php State " . $State . " PatientID : " . $row['PatientID'] . "(". $row2['Message'] . ")\n", FILE_APPEND);
		}

		else {
			file_put_contents('./sync.log', date("d-m-Y H:i:s") . " SendMedicalRecord.php State " . $State . " PatientID : " . $row['PatientID'] . "(Success)\n", FILE_APPEND);
		}
	}

	$State = 4;
	$sql4 = "SELECT
				MP.PatientID,
				TM.MedicationID,
				TMD.MedicationDetailsID,
				ME.ExaminationName,
				TM.TransactionDate,
				TMD.Remarks
			FROM
				transaction_medication TM
				JOIN transaction_medicationdetails TMD
					ON TM.MedicationID = TMD.MedicationID
				JOIN master_patient MP
					ON TM.PatientID = MP.PatientID
				JOIN master_examination ME
					ON TMD.ExaminationID = ME.ExaminationID
			WHERE
				TMD.Synchronized = 0
				AND TM.IsDone = 1
				AND TM.IsCancelled = 0
			LIMIT
				100";

	if (! $result4 = mysqli_query($dbh3, $sql4)) {
		file_put_contents('./sync.log', date("d-m-Y H:i:s") . " SendMedicalRecord.php State " . $State . "(". mysqli_error($dbh3) . ")\n", FILE_APPEND);
		return 0;
	}

	while ($row4 = mysqli_fetch_array($result4)) {
		$State = 5;
		$sql5 = "CALL spInsMedicalRecord(".$row4['PatientID'].", ".$BRANCH_ID.", ".$row4['MedicationID'].", ".$row4['MedicationDetailsID'].", '".$row4['ExaminationName']."', '".$row4['TransactionDate']."', '".$row4['Remarks']."', 'BatchSync')";

		if (! $result5=mysqli_query($dbh2, $sql5)) {
			file_put_contents('./sync.log',  date("d-m-Y H:i:s") . " SendMedicalRecord.php State " . $State . " MedicationDetailsID : " . $row4['MedicationDetailsID'] . "(". mysqli_error($dbh2) . ")\n", FILE_APPEND);
			continue;
		}

		$row5 = mysqli_fetch_array($result5);
		mysqli_free_result($result5);
		mysqli_next_result($dbh2);

		$State = 6;
		$sql6 = "UPDATE transaction_medicationdetails
				SET
					Synchronized = 1
				WHERE
					MedicationDetailsID = ".$row4['MedicationDetailsID']."";

		if (! $result6 = mysqli_query($dbh3, $sql6)) {
			file_put_contents('./sync.log', date("d-m-Y H:i:s") . " SendMedicalRecord.php State " . $State . " MedicationDetailsID : " . $row4['MedicationDetailsID'] . "(". mysqli_error($dbh3) . ")\n", FILE_APPEND);
			continue;
		}

		if($row5['FailedFlag'] == 1) {
			file_put_contents('./sync.log',  date("d-m-Y H:i:s") . " SendMedicalRecord.php State " . $State . " MedicationDetailsID : " . $row4['MedicationDetailsID'] . "(". $row5['Message'] . ")\n", FILE_APPEND);
		}

		else {
			file_put_contents('./sync.log', date("d-m-Y H:i:s") . " SendMedicalRecord.php State " . $State . " MedicationDetailsID : " . $row4['MedicationDetailsID'] . "(Success)\n", FILE_APPEND);
		}
	}
?>