<?php
	if(isset($_POST['hdnDoctorID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$UserID = mysql_real_escape_string($_POST['hdnDoctorID']);
		$ddlYear = mysql_real_escape_string($_POST['ddlYear']);
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
	
		for($i=1;$i<=12;$i++) {
			$sql = "UPDATE transaction_doctorcommision
					SET
						CommisionPercentage = '".mysql_real_escape_string($_POST['commision'.$i])."',
						ToolsFee = '".str_replace(",", "", $_POST['fee'.$i])."'
					WHERE
						BusinessMonth = ".$i."
						AND BusinessYear = ".$ddlYear."
						AND DoctorID = ".$UserID."";
			
			if (! $result=mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
				return 0;
			}
			
			$State = 2;
			$sql = "INSERT INTO transaction_doctorcommision
					(
						BusinessMonth,
						BusinessYear,
						DoctorID,
						CommisionPercentage,
						ToolsFee,
						CreatedDate,
						CreatedBy
					)
					SELECT
						".$i.",
						".$ddlYear.",
						".$UserID.",
						'".mysql_real_escape_string($_POST['commision'.$i])."',
						'".str_replace(",", "", $_POST['fee'.$i])."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					FROM
						tbl_temp
					WHERE
						NOT EXISTS
						(
							SELECT
								1
							FROM
								transaction_doctorcommision
							WHERE
								BusinessMonth = ".$i."
								AND BusinessYear = ".$ddlYear."
								AND DoctorID = ".$UserID."
						)";
						
			if (! $result=mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
				return 0;
			}
		}
		
		echo returnstate($UserID, $Message, $MessageDetail, $FailedFlag, $State);
	}
	
	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
