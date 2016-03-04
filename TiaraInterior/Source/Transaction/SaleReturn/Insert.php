<?php
	if(isset($_POST['hdnSaleReturnID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnSaleReturnID']);
		$TransactionDate = explode('-', $_POST['txtTransactionDate']);
		$_POST['txtTransactionDate'] = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]"; 
		$TransactionDate = $_POST['txtTransactionDate'];
		$CustomerID = mysql_real_escape_string($_POST['ddlCustomer']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$txtRemarks = mysql_real_escape_string($_POST['txtRemarks']);
		$SaleReturnNumber = mysql_real_escape_string($_POST['txtSaleReturnNumber']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		for($i=1;$i<=$RecordNew;$i++) {
			$DetailsID .= mysql_real_escape_string($_POST['hdnSaleReturnDetailsID'.$i]).",";
		}
		$DetailsID = substr($DetailsID, 0, -1);
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_salereturn
					(
						SaleReturnNumber,
						CustomerID,
						TransactionDate,
						Remarks,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'".$SaleReturnNumber."',
						".$CustomerID.",
						'".$TransactionDate."',
						'".$txtRemarks."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_salereturn
					SET
						SaleReturnNumber = '".$SaleReturnNumber."',
						CustomerID = ".$CustomerID.",
						Remarks = '".$txtRemarks."',
						TransactionDate = '".$TransactionDate."',
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						SaleReturnID = $ID";
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
			$sql = "INSERT INTO transaction_invoicenumber
					(
						InvoiceNumberType,
						InvoiceDate,
						InvoiceNumber,
						DeleteFlag,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'TB',
						'".$TransactionDate."',
						'".$IncomingNumber."',
						'0',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
					
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$State = 4;
			$sql = "SELECT
						MAX(SaleReturnID) AS SaleReturnID
					FROM 
						transaction_salereturn";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['SaleReturnID'];
		}
		$State = 5;
		$sql = "DELETE 
				FROM 
					transaction_salereturndetails 
				WHERE
					SaleReturnDetailsID NOT IN($DetailsID)			 
					AND SaleReturnID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		for($j=1;$j<=$RecordNew;$j++) {
			if($_POST['hdnSaleReturnDetailsID'.$j] == "0") {
				$State = 6;
				$sql = "INSERT INTO transaction_salereturndetails
						(
							SaleReturnID,
							TypeID,
							Quantity,
							SalePrice,
							BatchNumber,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(
							".$ID.",
							".mysql_real_escape_string($_POST['hdnTypeID'.$j]).",
							".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							".str_replace(",", "", $_POST['txtSalePrice'.$j]).",
							'".mysql_real_escape_string($_POST['txtBatchNumber'.$j])."',
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 7;
				$sql = "UPDATE 
							transaction_salereturndetails
						SET
							TypeID = ".mysql_real_escape_string($_POST['hdnTypeID'.$j]).",
							Quantity = ".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							SalePrice = ".str_replace(",", "", $_POST['txtSalePrice'.$j]).",
							BatchNumber = '".mysql_real_escape_string($_POST['txtBatchNumber'.$j])."',
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							SaleReturnDetailsID = ".$_POST['hdnSaleReturnDetailsID'.$j];
			}

			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}

			/*$sql = "UPDATE 
						master_item
					SET
						Price = ".str_replace(",", "", $_POST['txtPrice'.$j]).",
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						ItemID = ".$_POST['hdnItemID'.$j];
						
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}*/
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
