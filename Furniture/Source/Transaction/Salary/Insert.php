<?php
	if(isset($_POST['hdnSalaryID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnSalaryID']);
		$PeriodID = mysql_real_escape_string($_POST['ddlPeriod']);
		$txtSalaryDate = explode('-', $_POST['txtSalaryDate']);
		$_POST['txtSalaryDate'] = "$txtSalaryDate[2]-$txtSalaryDate[1]-$txtSalaryDate[0]"; 
		$txtSalaryDate = $_POST['txtSalaryDate'];
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		if($RecordNew > 0) {
			for($i=1;$i<=$RecordNew;$i++) {
				$DetailID .= $_POST['hdnSalaryDetailsID'.$i].",";
			}
			$DetailID = substr($DetailID, 0, -1);
		}
		else {
			$DetailID = 0;
		}
		
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_salary
					(
						PeriodID,
						SalaryDate,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						".$PeriodID.",
						'".$txtSalaryDate."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_salary
					SET
						PeriodID = ".$PeriodID.",
						SalaryDate = '".$txtSalaryDate."',
						ModifiedBy = '".$_SESSION['UserLogin']."',
						ModifiedDate = NOW()
					WHERE
						SalaryID = $ID";
		}
		
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		if($hdnIsEdit == 0) {
			$sql = "SELECT
						MAX(SalaryID) AS SalaryID
					FROM 
						transaction_salary";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['SalaryID'];
		}
		$State = 3;
		$sql = "DELETE 
				FROM 
					transaction_salarydetails
				WHERE
					SalaryDetailsID NOT IN($DetailID)			 
					AND SalaryID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		if($RecordNew > 0) {
			for($j=1;$j<=$RecordNew;$j++) {
				if($_POST['hdnSalaryDetailsID'.$j] == "0") {
					$State = 4;
					$sql = "INSERT INTO transaction_salarydetails
							(
								SalaryID,
								ProjectID,
								EmployeeID,
								Remarks,
								DailySalary,
								Days,
								CreatedDate,
								CreatedBy
							)
							VALUES
							(
								".$ID.",
								".$_POST['ddlProject'.$j].",
								".$_POST['ddlEmployee'.$j].",
								'".$_POST['txtRemarks'.$j]."',
								".str_replace(",", "", $_POST['txtDailySalary'.$j]).",
								".$_POST['txtDays'.$j].",
								NOW(),
								'".$_SESSION['UserLogin']."'
							)";
				}
				else {
					$State = 5;
					$sql = "UPDATE 
								transaction_salarydetails
							SET
								ProjectID = ".$_POST['ddlProject'.$j].",
								EmployeeID = ".$_POST['ddlEmployee'.$j].",
								Remarks = '".$_POST['txtRemarks'.$j]."',
								DailySalary = ".str_replace(",", "", $_POST['txtDailySalary'.$j]).",
								Days = ".$_POST['txtDays'.$j].",
								ModifiedDate = NOW(),
								ModifiedBy = '".$_SESSION['UserLogin']."'
							WHERE
								SalaryDetailsID = ".$_POST['hdnSalaryDetailsID'.$j];
				}

				if (! $result = mysql_query($sql, $dbh)) {
					$Message = "Terjadi Kesalahan Sistem";
					$MessageDetail = mysql_error();
					$FailedFlag = 1;
					echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
					mysql_query("ROLLBACK", $dbh);
					return 0;					
				}							
			}
		}
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
		mysql_query("COMMIT", $dbh);
		return 0;
	}
	
	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"Id" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
