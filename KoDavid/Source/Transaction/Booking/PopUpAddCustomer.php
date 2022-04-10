<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	//include "../../GetPermission.php";
	include "../../DBConfig.php";
	include "../../GetSession.php";
	date_default_timezone_set("Asia/Jakarta");
	$EditFlag = "";
	$DeleteFlag = "";
	
	$sql = "CALL spSelUserMenuPermission('$APPLICATION_PATH', '$RequestedPath', '".$_SESSION['UserID']."')";
				
	if (!$result = mysqli_query($dbh, $sql)) {
		logEvent(mysqli_error($dbh), $RequestedPath, mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
		return 0;
	}
	
	$cek = mysqli_num_rows($result);
	
	if($cek == 0) {
		echo '<input type="hidden" id="hdnPermission" value=0 />
			  <script>
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
?>
<html>
	<head>
		<title>Main App</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<style>
			.ui-button.ui-corner-all.ui-widget.ui-button-icon-only.ui-dialog-titlebar-close {
				display: none;
			}
			
			@media (min-width: 760px) {
				.col-md-12 {
				   float: left !important;
				}
			}
			
			.top-window { z-index: 4002 !important;}
		</style>
	</head>
	<body>
		<div id="FormCustomer" title="Tambah Item" >
			<form class="col-md-12" id="PostFormCustomer" method="POST" action="" >
				<div class="row">
					<div class="col-md-3 labelColumn">
						Kode Pelanggan :
					</div>
					<div class="col-md-7">
						<input id="txtCustomerCodeAdd" name="txtCustomerCodeAdd" type="text" tabindex=70 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Pelanggan" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Nama Pelanggan :
					</div>
					<div class="col-md-7">
						<input id="txtCustomerNameAdd" name="txtCustomerNameAdd" type="text" tabindex=71 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Nama Pelanggan" maxlength="30" required />
					</div>
				</div>				
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Telepon :
					</div>
					<div class="col-md-7">
						<input id="txtTelephoneAdd" name="txtTelephoneAdd" type="text" tabindex=72 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Telepon" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Alamat :
					</div>
					<div class="col-md-7">
						<input id="txtAddressAdd" maxlength="30" name="txtAddressAdd" type="text" tabindex=73 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Alamat" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Kota :
					</div>
					<div class="col-md-7">
						<input id="txtCityAdd" name="txtCityAdd" type="text" tabindex=74 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kota" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Pilihan Harga :
					</div>
					<div class="col-md-7">
						<select id="ddlCustomerPrice" name="ddlCustomerPrice" tabindex=75 class="form-control-custom" placeholder="Pilih Harga" >
							<?php
								$sql = "CALL spSelDDLCustomerPrice('".$_SESSION['UserLogin']."')";
								if (! $result = mysqli_query($dbh, $sql)) {
									logEvent(mysqli_error($dbh), '/Transaction/Booking/PopUpAddCustomer.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
									return 0;
								}
								while($row = mysqli_fetch_array($result)) {
									echo "<option value='".$row['CustomerPriceID']."' >".$row['CustomerPriceName']."</option>";
								}
								mysqli_free_result($result);
								mysqli_next_result($dbh);
							?>
						</select>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Keterangan :
					</div>
					<div class="col-md-7">
						<textarea id="txtRemarksAdd" name="txtRemarksAdd" tabindex=76 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Keterangan"></textarea>
					</div>
				</div>
				<br />
			</form>
		</div>
		<div id="save-confirm-add" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
		</div>
		<div id="loading"></div>
		<script>
			$(document).ready(function() {
				keyFunction();
				enterLikeTab();
			});
		</script>
	</body>
</html>
