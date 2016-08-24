<?php
	if(isset($_POST['MedicationID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$MedicationID = mysql_real_escape_string($_POST['MedicationID']);
		$Message = "";
		$MedicationDetails = "";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "SELECT
					ME.ExaminationName,
					TMD.Quantity,
					TMD.Remarks
				FROM
					transaction_medicationdetails TMD
					JOIN master_examination ME
						ON ME.ExaminationID = TMD.ExaminationID
				WHERE
					TMD.MedicationID = $MedicationID
				ORDER BY
					TMD.CreatedDate";
					
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($MedicationID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		$RowNumber = 1;
		while ($row = mysql_fetch_array($result)) {
			$MedicationDetails .= "<tr>";
			$MedicationDetails .= "<td align='center' style='width: 33px;' >$RowNumber</td>";
			$MedicationDetails .= "<td align='left' style='width: 200px;' >".$row['ExaminationName']."</td>";
			$MedicationDetails .= "<td align='right' style='width: 80px;' >".$row['Quantity']."</td>";
			$MedicationDetails .= "<td align='left' style='width: 210px;' >".$row['Remarks']."</td>";
			$MedicationDetails .= "</tr>";
			$RowNumber++;
		}
		echo returnstate($MedicationID, $Message, $MessageDetail, $MedicationDetails, $FailedFlag, $State);
		return 0;
	}
	
	function returnstate($ID, $Message, $MessageDetail, $MedicationDetails, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"MedicationDetails" => $MedicationDetails,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>