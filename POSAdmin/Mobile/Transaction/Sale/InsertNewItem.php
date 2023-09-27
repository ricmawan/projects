<?php
	if(isset($_POST['txtItemCodeAdd'])) {
		$RequestedPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestedPath);
		$RequestedPath = str_replace($file, "", $RequestedPath);
		//include "../../GetPermission.php";
		include "../../DBConfig.php";
		include "../../GetSession.php";
		date_default_timezone_set("Asia/Jakarta");
		$EditFlag = "";
		$DeleteFlag = "";
		
		$sql = "CALL spSelUserMenuPermission('$MOBILE_PATH', '$RequestedPath', '".$_SESSION['UserID']."')";
					
		if (!$result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), $RequestedPath, mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
			return 0;
		}
		
		$cek = mysqli_num_rows($result);
		
		if($cek == 0) {
			echo '<script>
					var counterError = 0;
					Lobibox.alert("error",
					{
						msg: "User tidak memiliki akses untuk menu ini.",
						width: 480,
						delay: false,
						beforeClose: function() {
							if(counterError == 0) {
								//location.reload();
								counterError = 1;
								var lobibox = $(".lobibox-window").data("lobibox");
								lobibox.destroy();
							}
						}
					});
				</script>';
			exit;
		}
		else {
			$row = mysqli_fetch_array($result);
			$EditFlag = $row['EditFlag'];
			$DeleteFlag = $row['DeleteFlag'];
		}
		mysqli_free_result($result);
		mysqli_next_result($dbh);
		$ItemID = 0;
		$ItemCode = mysqli_real_escape_string($dbh, $_POST['txtItemCodeAdd']);
		$ItemName = mysqli_real_escape_string($dbh, htmlspecialchars($_POST['txtItemNameAdd'], ENT_QUOTES));
		$CategoryID = mysqli_real_escape_string($dbh, $_POST['ddlCategoryAdd']);
		$UnitID = mysqli_real_escape_string($dbh, $_POST['ddlUnitAdd']);
		$BuyPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtBuyPriceAdd']));
		$RetailPrice = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtRetailPriceAdd']));
		$Price1 = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtPrice1Add']));
		$Qty1 = mysqli_real_escape_string($dbh, $_POST['txtQty1Add']);
		$Price2 = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtPrice2Add']));
		$Qty2 = mysqli_real_escape_string($dbh, $_POST['txtQty2Add']);
		$Weight = mysqli_real_escape_string($dbh, str_replace(",", "", $_POST['txtWeightAdd']));
		$MinimumStock = mysqli_real_escape_string($dbh, $_POST['txtMinimumStockAdd']);
		$hdnTabsCounter = mysqli_real_escape_string($dbh, $_POST['hdnTabsCounter']);
		$hdnIsEdit = 0;
		$Message = "Terjadi Kesalahan Sistem!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;

		$itemDetails = array();
		for($i=0;$i<$hdnTabsCounter;$i++) {
			$itemDetails[] = "(".mysqli_real_escape_string($dbh, $_POST['hdnItemDetailsID_'.($i+1)]).",
								".$ItemID.",
								\'".mysqli_real_escape_string($dbh, $_POST['txtItemCode_'.($i+1)])."\',
								".mysqli_real_escape_string($dbh, $_POST['ddlUnit_'.($i+1)]).",
								".mysqli_real_escape_string($dbh, $_POST['txtConversionQty_'.($i+1)])."
							  )";
		}


		$sql = "CALL spInsItem( ".$ItemID.",
								'".$ItemCode."',
								'".$ItemName."',
								".$CategoryID.",
								".$UnitID.",
								".$BuyPrice.",
								".$RetailPrice.",
								".$Price1.",
								".$Qty1.",
								".$Price2.",
								".$Qty2.",
								".$Weight.",
								".$MinimumStock.",
								'".implode(",", $itemDetails)."',
								".$hdnIsEdit.",
								'".$_SESSION['UserLoginMobile']."'
							  )";
		
		if (! $result=mysqli_query($dbh, $sql)) {
			$MessageDetail = mysqli_error($dbh);
			$FailedFlag = 1;
			logEvent(mysqli_error($dbh), '/Master/Item/InsertFromPurchase.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
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
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>