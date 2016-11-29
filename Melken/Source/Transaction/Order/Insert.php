<?php
	if(isset($_POST['hdnIsEdit'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnSaleID']);
		$TransactionDate = explode('-', $_POST['txtTransactionDate']);
		$_POST['txtTransactionDate'] = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]"; 
		$TransactionDate = $_POST['txtTransactionDate'];
		$txtDiscountTotal = str_replace(",", "", $_POST['txtDiscountTotal']);
		$TableID = mysql_real_escape_string($_POST['hdnTableID']);
		$ddlDiscountTotal = mysql_real_escape_string($_POST['ddlDiscountTotal']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
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
						TableID,
						TransactionDate,
						Discount,
						IsPercentage,
						IsDone,
						IsCancelled,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						".$TableID.",
						'".$TransactionDate."',
						".$txtDiscountTotal.",
						".$ddlDiscountTotal.",
						0,
						0,
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_sale
					SET
						TableID = ".$TableID.",
						TransactionDate = '".$TransactionDate."',
						Discount = ".$txtDiscountTotal.",
						IsPercentage = ".$ddlDiscountTotal.",
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
		$State = 5;
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
			if($_POST['ddlDiscount'.$j] == "true") {
				$chkIsPercentage = true;
			}
			else {
				$chkIsPercentage = false;
			}
			if($_POST['hdnSaleDetailsID'.$j] == "0") {
				$State = 6;
				$sql = "INSERT INTO transaction_saledetails
						(
							SaleID,
							MenuListID,
							Quantity,
							Price,
							Discount,
							IsPercentage,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(
							".$ID.",
							".mysql_real_escape_string($_POST['hdnMenuListID'.$j]).",
							".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							".str_replace(",", "", $_POST['txtPrice'.$j]).",
							".str_replace(",", "", $_POST['txtDiscount'.$j]).",
							'".$chkIsPercentage."',
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 7;
				$sql = "UPDATE 
							transaction_saledetails
						SET
							SaleID = ".$ID.",
							MenuListID = ".mysql_real_escape_string($_POST['hdnMenuListID'.$j]).",
							Quantity = ".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							Price = ".str_replace(",", "", $_POST['txtPrice'.$j]).",
							Discount = ".str_replace(",", "", $_POST['txtDiscount'.$j]).",
							IsPercentage = '".$chkIsPercentage."',
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							SaleDetailsID = ".$_POST['hdnSaleDetailsID'.$j];
			}

			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			else {
				$sql = "UPDATE
							master_table
						SET
							TableStatusID = 2
						WHERE
							TableID = ".$TableID;
				
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
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	}
?>
