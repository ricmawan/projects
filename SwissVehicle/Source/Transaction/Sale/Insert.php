<?php
	if(isset($_POST['hdnSaleID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnSaleID']);
		$TransactionDate = explode('-', mysql_real_escape_string($_POST['txtTransactionDate']));
		$TransactionDate = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]";
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$CustomerName = mysql_real_escape_string($_POST['txtCustomerName']);
		$txtRemarks = mysql_real_escape_string($_POST['txtRemarks']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		for($i=1;$i<=$RecordNew;$i++) {
			$DetailsID .= mysql_real_escape_string($_POST['hdnSaleDetailsID'.$i]).",";
		}
		$DetailsID = substr($DetailsID, 0, -1);
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_sale
					(
						TransactionDate,
						CustomerName,
						Remarks,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'".$TransactionDate."',
						'".$CustomerName."',
						'".$txtRemarks."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_sale
					SET
						TransactionDate = '".$TransactionDate."',
						CustomerName = '".$CustomerName."',
						Remarks = '".$txtRemarks."',
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						SaleID = $ID";
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
			$State = 3;
			$sql = "SELECT
						MAX(SaleID) AS SaleID
					FROM 
						transaction_sale";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['SaleID'];
		}
		
		$State = 4;
		$sql = "DELETE 
				FROM 
					transaction_saledetails 
				WHERE
					SaleDetailsID NOT IN($DetailsID)			 
					AND SaleID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		for($j=1;$j<=$RecordNew;$j++) {
			if($_POST['hdnSaleDetailsID'.$j] == "0") {
				$State = 5;
				$sql = "INSERT INTO transaction_saledetails
						(
							SaleID,
							ItemID,
							Quantity,
							Price,
							Remarks,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(
							".$ID.",
							".mysql_real_escape_string($_POST['hdnItemID'.$j]).",
							".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							".str_replace(",", "", $_POST['txtPrice'.$j]).",
							'".mysql_real_escape_string($_POST['txtRemarksDetail'.$j])."',
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 6;
				$sql = "UPDATE 
							transaction_saledetails
						SET
							ItemID = ".mysql_real_escape_string($_POST['hdnItemID'.$j]).",
							Quantity = ".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							Price = ".str_replace(",", "", $_POST['txtPrice'.$j]).",
							Remarks = '".mysql_real_escape_string($_POST['txtRemarksDetail'.$j])."',
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							SaleDetailsID = ".mysql_real_escape_string($_POST['hdnSaleDetailsID'.$j]);
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
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
		mysql_query("COMMIT", $dbh);
		return 0;
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
