<?php
	if(isset($_POST['OutgoingModelID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$OutgoingModelID = mysql_real_escape_string($_POST['OutgoingModelID']);
		$Message = "";
		$OutgoingModelDetails = "";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		
		$sql = "SELECT
					OMD.OutgoingModelDetailsID,
					MD.UserID,
					MD.UserName,
					MP.PatientID,
					MP.PatientName,
					OMD.ExaminationName,
					OMD.Remarks
				FROM
					transaction_outgoingmodeldetails OMD
					JOIN master_user MD
						ON MD.UserID = OMD.DoctorID
					JOIN master_Patient MP
						ON MP.PatientID = OMD.PatientID
				WHERE
					OMD.OutgoingModelID = $OutgoingModelID
				ORDER BY
					OMD.OutgoingModelDetailsID";
					
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($OutgoingModelID, $Message, $MessageDetail, $OutgoingModelDetails, $FailedFlag, $State);
			return 0;
		}
		$RowNumber = 1;
		while ($row = mysql_fetch_array($result)) {
			$OutgoingModelDetails .= "<tr>";
			$OutgoingModelDetails .= "<td align='center' style='width: 40px;' >$RowNumber</td>";
			$OutgoingModelDetails .= "<td align='left' style='width: 165px;' >".$row['UserName']."</td>";
			$OutgoingModelDetails .= "<td align='left' style='width: 165px;' >".$row['PatientName']."</td>";
			$OutgoingModelDetails .= "<td align='left' style='width: 200px;' >".$row['ExaminationName']."</td>";
			$OutgoingModelDetails .= "<td align='left' style='width: 180px;' >".$row['Remarks']."</td>";
			$OutgoingModelDetails .= '<td align="center" style="vertical-align:middle;width: 60px;">
										<i style="cursor:pointer;" class="fa fa-edit" onclick="EditData('.$OutgoingModelID.', '.$row['OutgoingModelDetailsID'].', '.$row['UserID'].', '.$row['PatientID'].', \''.$row['Remarks'].'\', \''.$row['ExaminationName'].'\');" acronym title="Ubah Data"></i>
										&nbsp;&nbsp;<i class="fa fa-close btnDelete" onclick="DeleteOutgoingModelDetails('.$OutgoingModelID.', '.$row['OutgoingModelDetailsID'].', \''.$row['ExaminationName'].'\');" style="cursor:pointer;" acronym title="Hapus Data" ></i>
									</td>';
			$OutgoingModelDetails .= "</tr>";
			$RowNumber++;
		}
		echo returnstate($OutgoingModelID, $Message, $MessageDetail, $OutgoingModelDetails, $FailedFlag, $State);
		return 0;
	}
	
	function returnstate($ID, $Message, $MessageDetail, $OutgoingModelDetails, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"OutgoingModelDetails" => $OutgoingModelDetails,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>