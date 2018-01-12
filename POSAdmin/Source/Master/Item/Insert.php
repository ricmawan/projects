<?php
	if(isset($_POST['hdnItemID'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		include "../../GetPermission.php";
		$ItemID = mysqli_real_escape_string($dbh, $_POST['hdnItemID']);
		$ItemCode = mysqli_real_escape_string($dbh, $_POST['txtItemCode']);
		$ItemName = mysqli_real_escape_string($dbh, $_POST['txtItemName']);
		$CategoryID = mysqli_real_escape_string($dbh, $_POST['ddlCategory']);
		$BuyPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtBuyPrice']));
		$RetailPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtRetailPrice']));
		$Price1 = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtPrice1']));
		$Qty1 = mysqli_real_escape_string($dbh, $_POST['txtQty1']);
		$Price2 = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtPrice2']));
		$Qty2 = mysqli_real_escape_string($dbh, $_POST['txtQty2']);
		$Weight = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtWeight']));
		$MinimumStock = mysqli_real_escape_string($dbh, $_POST['txtMinimumStock']);
		$hdnIsEdit = mysqli_real_escape_string($dbh, $_POST['hdnIsEdit']);
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		echo $sql;
		$sql = "CALL spInsItem( ".$ItemID.",
								'".$ItemCode."',
								'".$ItemName."',
								".$CategoryID.",
								".$BuyPrice.",
								".$RetailPrice.",
								".$Price1.",
								".$Qty1.",
								".$Price2.",
								".$Qty2.",
								".$Weight.",
								".$MinimumStock.",
								".$hdnIsEdit.",
								'".$_SESSION['UserLogin']."'
							  )";
		
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Master/Item/Insert.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			echo returnstate($CategoryID, $Message, $MessageDetail, $FailedFlag, $State);
			return 0;
		}				
		$row=mysqli_fetch_array($result);
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		echo returnstate($row['ID'], $row['Message'], $row['MessageDetail'], $row['FailedFlag'], $row['State']);
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
