<?php
	if(isset($_POST['hdnFirstStockID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnFirstStockID']);
		//$SupplierID = mysql_real_escape_string($_POST['ddlSupplier']);
		$SelectedDate = explode('-', mysql_real_escape_string($_POST['txtTransactionDate']));
		$Date = $SelectedDate[0].$SelectedDate[1].substr($SelectedDate[2], -2);
		$SelectedDate = "$SelectedDate[2]-$SelectedDate[1]-$SelectedDate[0]";
		$TransactionDate = explode('-', mysql_real_escape_string($_POST['txtTransactionDate']));
		$TransactionDate = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]";
		$InvoiceNumber = mysql_real_escape_string($_POST['txtFirstStockNumber']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$txtRemarks = mysql_real_escape_string($_POST['txtRemarks']);
		$InvoiceNumberType = "SA";
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		for($i=1;$i<=$RecordNew;$i++) {
			$DetailsID .= mysql_real_escape_string($_POST['hdnFirstStockDetailsID'.$i]).",";
		}
		$DetailsID = substr($DetailsID, 0, -1);
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "SELECT
						CONCAT('".$InvoiceNumberType."-', '".$Date."', RIGHT(CONCAT('0000', RIGHT(IFNULL(MAX(InvoiceNumber), 000), 3) + 1), 3)) AS InvoiceNumber
					FROM 
						transaction_invoicenumber
					WHERE
						InvoiceDate = '".$SelectedDate."'
						AND InvoiceNumberType = '".$InvoiceNumberType."'";
			
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($InvoiceNumber, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber);
				return 0;
			}
			$row = mysql_fetch_array($result);
			$InvoiceNumber = $row['InvoiceNumber'];
			
			$State = 2;
			$sql = "INSERT INTO transaction_firststock
					(
						FirstStockNumber,
						TransactionDate,
						Remarks,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'".$InvoiceNumber."',
						'".$TransactionDate."',
						'".$txtRemarks."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_firststock
					SET
						TransactionDate = '".$TransactionDate."',
						Remarks = '".$txtRemarks."',
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						FirstStockID = $ID";
		}		

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber);
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
					SELECT
						'SA',
						'".$TransactionDate."',
						'".$InvoiceNumber."',
						0,
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
								transaction_invoicenumber TIN 
							WHERE
								'".$InvoiceNumber."' = TRIM(TIN.InvoiceNumber)
						)";
					
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$State = 4;
			$sql = "SELECT
						MAX(FirstStockID) AS FirstStockID
					FROM 
						transaction_firststock";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['FirstStockID'];
		}
				
		$State = 5;
		$sql = "DELETE 
				FROM 
					transaction_firststockdetails 
				WHERE
					FirstStockDetailsID NOT IN($DetailsID)			 
					AND FirstStockID = $ID";

		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber);
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
			if($_POST['hdnFirstStockDetailsID'.$j] == "0") {
				$State = 6;
				$sql = "INSERT INTO transaction_firststockdetails
						(
							FirstStockID,
							TypeID,
							Quantity,
							BuyPrice,
							SalePrice,
							Discount,
							IsPercentage,
							BatchNumber,
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
							'".$chkIsPercentage."',
							'".mysql_real_escape_string($_POST['txtBatchNumber'.$j])."',
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 7;
				$sql = "UPDATE 
							transaction_firststockdetails
						SET
							TypeID = ".mysql_real_escape_string($_POST['hdnTypeID'.$j]).",
							Quantity = ".mysql_real_escape_string($_POST['txtQuantity'.$j]).",
							BuyPrice = ".str_replace(",", "", $_POST['txtBuyPrice'.$j]).",
							SalePrice = ".str_replace(",", "", $_POST['txtSalePrice'.$j]).",
							Discount = ".str_replace(",", "", $_POST['txtDiscount'.$j]).",
							IsPercentage = '".$chkIsPercentage."',
							BatchNumber = '".mysql_real_escape_string($_POST['txtBatchNumber'.$j])."',
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							FirstStockDetailsID = ".mysql_real_escape_string($_POST['hdnFirstStockDetailsID'.$j]);
			}

			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber);
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
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
		}
		echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber);
		mysql_query("COMMIT", $dbh);
		return 0;
	}
	
	function returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State, $InvoiceNumber) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State,
			"InvoiceNumber" => $InvoiceNumber
		);
		return json_encode($data);
	}
?>
