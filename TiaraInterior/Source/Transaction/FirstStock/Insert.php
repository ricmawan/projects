<?php
	if(isset($_POST['hdnFirstStockID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$ID = mysql_real_escape_string($_POST['hdnFirstStockID']);
		$TransactionDate = explode('-', mysql_real_escape_string($_POST['txtTransactionDate']));
		$TransactionDate = "$TransactionDate[2]-$TransactionDate[1]-$TransactionDate[0]";
		$FirstStockNumber = mysql_real_escape_string($_POST['txtFirstStockNumber']);
		$hdnIsEdit = mysql_real_escape_string($_POST['hdnIsEdit']);
		$State = 1;
		mysql_query("START TRANSACTION", $dbh);
		mysql_query("SET autocommit=0", $dbh);
		$DetailsID = "";
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		for($i=1;$i<=$RecordNew;$i++) {
			$DetailsID .= $_POST['hdnFirstStockDetailsID'.$i].",";
		}
		$DetailsID = substr($DetailsID, 0, -1);
		//echo $DetailID;
		if($hdnIsEdit == 0) {
			$State = 1;
			$sql = "INSERT INTO transaction_firststock
					(
						FirstStockNumber,
						TransactionDate,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						'".$FirstStockNumber."',
						'".$TransactionDate."',
						NOW(),
						'".$_SESSION['UserLogin']."'
					)";
		}
		
		else {
			$State = 2;
			$sql = "UPDATE transaction_firststock
					SET
						FirstStockNumber = '".$FirstStockNumber."',
						TransactionDate = '".$TransactionDate."',
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						FirstStockID = $ID";
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
						MAX(FirstStockID) AS FirstStockID
					FROM 
						transaction_firststock";
		
			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			
			$row = mysql_fetch_array($result);
			$ID = $row['FirstStockID'];
		}
		$State = 3;
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
			echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("ROLLBACK", $dbh);
			return 0;
		}
		for($j=1;$j<=$RecordNew;$j++) {
			if($_POST['hdnFirstStockDetailsID'.$j] == "0") {
				$State = 4;
				$sql = "INSERT INTO transaction_firststockdetails
						(
							FirstStockID,
							TypeID,
							Quantity,
							BuyPrice,
							SalePrice,
							Discount,
							BatchNumber,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(
							".$ID.",
							".$_POST['hdnTypeID'.$j].",
							".$_POST['txtQuantity'.$j].",
							".str_replace(",", "", $_POST['txtBuyPrice'.$j]).",
							".str_replace(",", "", $_POST['txtSalePrice'.$j]).",
							".$_POST['txtDiscount'.$j].",
							'".$_POST['txtBatchNumber'.$j]."',
							NOW(),
							'".$_SESSION['UserLogin']."'
						)";
			}
			else {
				$State = 5;
				$sql = "UPDATE 
							transaction_firststockdetails
						SET
							TypeID = ".$_POST['hdnTypeID'.$j].",
							Quantity = ".$_POST['txtQuantity'.$j].",
							BuyPrice = ".str_replace(",", "", $_POST['txtBuyPrice'.$j]).",
							SalePrice = ".str_replace(",", "", $_POST['txtSalePrice'.$j]).",
							Discount = ".$_POST['txtDiscount'.$j].",
							BatchNumber = '".$_POST['txtBatchNumber'.$j]."',
							ModifiedBy = '".$_SESSION['UserLogin']."'
						WHERE
							FirstStockDetailsID = ".$_POST['hdnFirstStockDetailsID'.$j];
			}

			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($ID, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}

			$sql = "UPDATE 
						master_type
					SET
						BuyPrice = ".str_replace(",", "", $_POST['txtBuyPrice'.$j]).",
						SalePrice = ".str_replace(",", "", $_POST['txtSalePrice'.$j]).",
						ModifiedBy = '".$_SESSION['UserLogin']."'
					WHERE
						TypeID = ".$_POST['hdnTypeID'.$j];
						
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
			"Id" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
