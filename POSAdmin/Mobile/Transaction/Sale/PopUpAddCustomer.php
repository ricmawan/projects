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
	
	$sql = "CALL spSelUserMenuPermission('$MOBILE_PATH', '$RequestedPath', '".$_SESSION['UserID']."')";
				
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

			br {
				content: "";
				margin: 4em;
				display: block;
				font-size: 24%
			}
		</style>
	</head>
	<body>
		<div id="FormCustomer" title="Tambah Item" >
			<form class="col-md-12 col-sm-12" id="PostFormCustomer" method="POST" action="" >
				<div class="row">
					<div class="col-md-5 col-sm-5 has-float-label">
						<input id="txtCustomerCodeAdd" name="txtCustomerCodeAdd" type="text" tabindex=70 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Pelanggan" required />
						<label for="txtCustomerCodeAdd" class="lblInput" >Kode Pelanggan</label>
					</div>
					<div class="col-md-7 col-sm-7 has-float-label">
						<input id="txtCustomerNameAdd" name="txtCustomerNameAdd" type="text" tabindex=71 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Nama Pelanggan" maxlength="30" required />
						<label for="txtCustomerNameAdd" class="lblInput" >Nama Pelanggan</label>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-12 col-sm-12 has-float-label">
						<input id="txtTelephoneAdd" name="txtTelephoneAdd" type="tel" tabindex=72 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Telepon" />
						<label for="txtTelephoneAdd" class="lblInput" >Telepon</label>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-8 col-sm-8 has-float-label">
						<input id="txtAddressAdd" maxlength="30" name="txtAddressAdd" type="text" tabindex=73 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Alamat" />
						<label for="txtAddressAdd" class="lblInput" >Alamat</label>
					</div>
					<div class="col-md-4 col-sm-4 has-float-label">
						<input id="txtCityAdd" name="txtCityAdd" type="text" tabindex=74 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kota" />
						<label for="txtCityAdd" class="lblInput" >Kota</label>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-12 col-sm-12 has-float-label">
						<input id="txtRemarksAdd" name="txtRemarksAdd" type="text" tabindex=75 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Keterangan" />
						<label for="txtRemarksAdd" class="lblInput" >Keterangan</label>
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
