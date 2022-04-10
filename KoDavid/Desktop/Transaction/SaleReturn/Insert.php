<?php
	if(isset($_POST['hdnSaleID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$SaleID = mysqli_real_escape_string($dbh, $_POST['hdnSaleID']);
		$SaleReturnID = mysqli_real_escape_string($dbh, $_POST['hdnSaleReturnID']);
		$TransactionDate = mysqli_real_escape_string($dbh, $_POST['hdnTransactionDate']);
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$hdnIsEdit = mysqli_real_escape_string($dbh, $_POST['hdnIsEdit']);
		$SaleReturnData = array();
		if(ISSET($_POST['chkSaleDetails'])) {
			foreach($_POST['chkSaleDetails'] as $selected){
				$SaleReturnData[] = "(".$SaleReturnID.", ".$_POST['hdnItemID'.$selected].", ".$_POST['hdnBranchID'.$selected].", ".$_POST['txtQty'.$selected].", ".$_POST['hdnBuyPrice'.$selected].", ".$_POST['hdnSalePrice'.$selected].", ".$_POST['hdnSaleDetailsID'.$selected].", NOW(), UserLogin)";
			}
		}

		$sql = "CALL spInsSaleReturn(".$SaleReturnID.", ".$SaleID.", '".$TransactionDate."', '".implode(",", $SaleReturnData)."', ".$hdnIsEdit.", '".$_SESSION['UserLoginKasir']."')";

		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Transaction/SaleReturn/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginKasir']));
			echo returnstate($SaleReturnID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
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
