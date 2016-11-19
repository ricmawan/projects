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
					TMD.MedicationDetailsID,
					ME.ExaminationName,
					TMD.Quantity,
					TMD.Price,
					(TMD.Price * TMD.Quantity) Total,
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
			$MedicationDetails .= "<td align='center' style='width: 36px;' >$RowNumber</td>";
			$MedicationDetails .= "<td align='left' style='width: 200px;' >".$row['ExaminationName']."</td>";
			$MedicationDetails .= "<td align='right' style='width: 80px;' >".$row['Quantity']."</td>";
			$MedicationDetails .= "<td align='right' style='width: 100px;' >".number_format($row['Price'],2,".",",")."</td>";
			$MedicationDetails .= "<td align='right' style='width: 125px;' >".number_format($row['Total'],2,".",",")."</td>";
			$MedicationDetails .= "<td align='left' style='width: 210px;' >".$row['Remarks']."</td>";
			$MedicationDetails .= '<td align="center" style="vertical-align:middle;width: 60px;">
										<i style="cursor:pointer;" class="fa fa-edit" onclick="EditData('.$MedicationID.', '.$row['MedicationDetailsID'].', '.$row['Quantity'].', \''.number_format($row['Price'],2,".",",").'\', \''.$row['Remarks'].'\', \''.$row['ExaminationName'].'\');" acronym title="Ubah Data"></i>
										&nbsp;&nbsp;<i class="fa fa-close btnDelete" onclick="DeleteExamination('.$MedicationID.', '.$row['MedicationDetailsID'].', \''.$row['ExaminationName'].'\');" style="cursor:pointer;" acronym title="Hapus Data" onclick="DeleteRow(this.getAttribute(\'row\'))"></i>
									</td>';
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