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
	
	$sql = "CALL spSelUserMenuPermission('$DESKTOP_PATH', '$RequestedPath', '".$_SESSION['UserID']."')";
				
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
		<div id="FormItem" title="Tambah Item" >
			<form class="col-md-12" id="PostFormItem" method="POST" action="" >
				<div class="row">
					<div class="col-md-3 labelColumn">
						Kode Barang :
					</div>
					<div class="col-md-4">
						<input id="txtItemCodeAdd" name="txtItemCodeAdd" type="text" tabindex=16 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Barang" required readonly />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Nama Barang :
					</div>
					<div class="col-md-4">
						<input id="txtItemNameAdd" name="txtItemNameAdd" type="text" tabindex=17 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Nama Barang" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Kategori Barang :
					</div>
					<div class="col-md-4">
						<div class="ui-widget" style="width: 100%;">
							<select id="ddlCategoryAdd" name="ddlCategoryAdd" tabindex=18 class="form-control-custom" placeholder="Pilih Kategori" >
								<option value="" selected> </option>
								<?php
									$sql = "CALL spSelDDLCategory('".$_SESSION['UserLogin']."')";
									if (! $result = mysqli_query($dbh, $sql)) {
										logEvent(mysqli_error($dbh), '/Master/Item/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
										return 0;
									}
									while($row = mysqli_fetch_array($result)) {
										echo "<option value='".$row['CategoryID']."' >".$row['CategoryCode']." - ".$row['CategoryName']."</option>";
									}
									mysqli_free_result($result);
									mysqli_next_result($dbh);
								?>
							</select>
						</div>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Harga Beli :
					</div>
					<div class="col-md-4">
						<input id="txtBuyPriceAdd" name="txtBuyPriceAdd" type="text" tabindex=19 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Beli" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Harga Ecer :
					</div>
					<div class="col-md-4">
						<input id="txtRetailPriceAdd" name="txtRetailPriceAdd" type="text" tabindex=20 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Ecer" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Harga Grosir 1 :
					</div>
					<div class="col-md-4">
						<input id="txtPrice1Add" name="txtPrice1Add" type="text" tabindex=21 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Grosir 1" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
					<div class="col-md-2 labelColumn">
						Qty Grosir 1 :
					</div>
					<div class="col-md-2">
						<input id="txtQty1Add" name="txtQty1Add" type="number" tabindex=22 class="form-control-custom" value=1 min=1 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 1" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Harga Grosir 2 :
					</div>
					<div class="col-md-4">
						<input id="txtPrice2Add" name="txtPrice2Add" type="text" tabindex=23 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Grosir 2" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
					<div class="col-md-2 labelColumn">
						Qty Grosir 2 :
					</div>
					<div class="col-md-2">
						<input id="txtQty2Add" name="txtQty2Add" type="number" tabindex=24 class="form-control-custom" value=1 min=1 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 2" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Berat (KG) :
					</div>
					<div class="col-md-4">
						<input id="txtWeightAdd" name="txtWeightAdd" type="text" tabindex=25 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Berat (KG)" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertWeight(this.id, this.value);" required />
					</div>
					<div class="col-md-2 labelColumn">
						Stok Minimal :
					</div>
					<div class="col-md-2">
						<input id="txtMinimumStockAdd" name="txtMinimumStockAdd" type="number" tabindex=26 class="form-control-custom" value=0 onfocus="this.select();" autocomplete=off placeholder="Stok Minimal" required />
					</div>
				</div>
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
				$("#ddlCategoryAdd").combobox();
			});
		</script>
	</body>
</html>
