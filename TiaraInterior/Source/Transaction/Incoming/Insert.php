<?php
	if(isset($_POST['hdnIncomingID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnIncomingID']);
		$SupplierID = mysql_real_escape_string($_POST['ddlSupplier']);
		$TransactionDate = explode('-', mysql_real_escape_string($_POST['txtTransactionDate']));
		$TransactionDate = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]";
		$IncomingNumber = mysql_real_escape_string($_POST['txtIncomingNumber']);
		$txtDeliveryCost = str_replace(",", "", $_POST['txtDeliveryCost']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$txtRemarks = mysql_real_escape_string($_POST['txtRemarks']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		for($i=1;$i<=$RecordNew;$i++) {
			$DetailsID .= mysql_real_escape_string($_POST['hdnIncomingDetailsID'.$i]).",";
		}
		$DetailsID = substr($DetailsID, 0, -1);
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_incoming
					(
						SupplierID,
						IncomingNumber,
						TransactionDate,
						DeliveryCost,
						Remarks,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						".$SupplierID.",
						'".$IncomingNumber."',
						'".$TransactionDate."',
						".$txtDeliveryCost.",
						'".$txtRemarks."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_incoming
					SET
						SupplierID = ".$SupplierID.",
						IncomingNumber = '".$IncomingNumber."',
						TransactionDate = '".$TransactionDate."',
						DeliveryCost = ".$txtDeliveryCost.",
						Remarks = '".$txtRemarks."',
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						IncomingID = $ID";
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
						0,
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
						MAX(IncomingID) AS IncomingID
					FROM 
						transaction_incoming";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['IncomingID'];
		}
		
		$State = 5;
		$sql = "DELETE 
				FROM 
					transaction_incomingdetails 
				WHERE
					IncomingDetailsID NOT IN($DetailsID)			 
					AND IncomingID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		for($j=1;$j<=$RecordNew;$j++) {
			if(ISSET($_POST['chkIsPercentage'.$j])) {
				$chkIsPercentage = true;
			}
			else {
				$chkIsPercentage = false;
			}
			if($_POST['hdnIncomingDetailsID'.$j] == "0") {
				$State = 6;
				$sql = "INSERT INTO transaction_incomingdetails
						(
							IncomingID,
							TypeID,
							Quantity,
							BuyPrice,
							SalePrice,
							Discount,
							BatchNumber,
							IsPercentage,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(
							".$ID.",
							".mysql_real_escape_string($_POST['hdnTypeID'.$j]).",
							".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							".str_replace(",", "", $_POST['txtBuyPrice'.$j]).",
							".str_replace(",", "", $_POST['txtSalePrice'.$j]).",
							".str_replace(",", "", $_POST['txtDiscount'.$j]).",
							'".mysql_real_escape_string($_POST['txtBatchNumber'.$j])."',
							'".$chkIsPercentage."',
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 7;
				$sql = "UPDATE 
							transaction_incomingdetails
						SET
							TypeID = ".mysql_real_escape_string($_POST['hdnTypeID'.$j]).",
							Quantity = ".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							BuyPrice = ".str_replace(",", "", $_POST['txtBuyPrice'.$j]).",
							SalePrice = ".str_replace(",", "", $_POST['txtSalePrice'.$j]).",
							Discount = ".str_replace(",", "", $_POST['txtDiscount'.$j]).",
							BatchNumber = '".mysql_real_escape_string($_POST['txtBatchNumber'.$j])."',
							IsPercentage = '".$chkIsPercentage."',
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							IncomingDetailsID = ".mysql_real_escape_string($_POST['hdnIncomingDetailsID'.$j]);
			}

			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}

			$State = 8;
			if($chkIsPercentage == true) {
				$BuyPrice = (str_replace(",", "", $_POST['txtBuyPrice'.$j]) - ((str_replace(",", "", $_POST['txtBuyPrice'.$j]) * mysql_real_escape_string($_POST['txtDiscount'.$j]))/100));
			}
			else {
				$BuyPrice = (str_replace(",", "", $_POST['txtBuyPrice'.$j]) - str_replace(",", "", $_POST['txtDiscount'.$j]) );
			}
			$sql = "UPDATE 
						master_type
					SET
						BuyPrice = ".$BuyPrice.",
						SalePrice = ".str_replace(",", "", $_POST['txtSalePrice'.$j]).",
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						TypeID = ".mysql_real_escape_string($_POST['hdnTypeID'.$j]);
						
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
