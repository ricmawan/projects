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
		<div id="FormItem" title="Tambah Item" >
			<form class="col-md-12" id="PostFormItem" method="POST" action="" >
				<div class="row">
					<div class="col-md-2 col-sm-2 has-float-label">
						<input id="txtItemCodeAdd" name="txtItemCodeAdd" type="text" tabindex=16 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Barang" required readonly />
						<label for="txtItemCodeAdd" class="lblInput" >Kode Barang</label>
					</div>
					<div class="col-md-4 col-sm-4 has-float-label">
						<input id="txtItemNameAdd" name="txtItemNameAdd" type="text" tabindex=17 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Nama Barang" required />
						<label for="txtItemNameAdd" class="lblInput" >Nama Barang</label>
					</div>
					<div class="col-md-6 col-sm-6 has-float-label">
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
						<label for="ddlCategoryAdd" class="lblInput" >Kategori Barang</label>
					</div>
				</div>
				<br />
				<div class="row">
					
					<div class="col-md-6 col-sm-6 has-float-label">
						<input id="txtBuyPriceAdd" name="txtBuyPriceAdd" type="tel" tabindex=19 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Beli" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
						<label for="txtBuyPriceAdd" class="lblInput" >Harga Beli</label>
					</div>
					<div class="col-md-6 col-sm-6 has-float-label">
						<input id="txtRetailPriceAdd" name="txtRetailPriceAdd" type="tel" tabindex=20 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Ecer" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
						<label for="txtRetailPriceAdd" class="lblInput" >Harga Ecer</label>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-4 col-sm-4 has-float-label">
						<input id="txtPrice1Add" name="txtPrice1Add" type="tel" tabindex=21 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Grosir 1" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
						<label for="txtPrice1Add" class="lblInput" >Harga Grosir 1</label>
					</div>
					<div class="col-md-2 col-sm-2 has-float-label">
						<input id="txtQty1Add" name="txtQty1Add" type="number" tabindex=22 class="form-control-custom" value=1 min=1 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 1" required />
						<label for="txtQty1Add" class="lblInput" >Qty Grosir 1</label>
					</div>
					<div class="col-md-4 col-sm-4 has-float-label">
						<input id="txtPrice2Add" name="txtPrice2Add" type="tel" tabindex=23 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Grosir 2" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
						<label for="txtPrice2Add" class="lblInput" >Harga Grosir 2</label>
					</div>
					<div class="col-md-2 col-sm-2 has-float-label">
						<input id="txtQty2Add" name="txtQty2Add" type="number" tabindex=24 class="form-control-custom" value=1 min=1 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 2" required />
						<label for="txtQty2Add" class="lblInput" >Qty Grosir 2</label>
					</div>
				</div>
				<br />
				<div class="row" >
					<div class="col-md-6 col-sm-6 has-float-label">
						<input id="txtWeightAdd" name="txtWeightAdd" type="tel" tabindex=25 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Berat (KG)" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertWeight(this.id, this.value);" required />
						<label for="txtWeightAdd" class="lblInput" >Berat (KG)</label>
					</div>
					<div class="col-md-6 col-sm-6 has-float-label">
						<input id="txtMinimumStockAdd" name="txtMinimumStockAdd" type="number" tabindex=26 class="form-control-custom" value=0 onfocus="this.select();" autocomplete=off placeholder="Stok Minimal" required />
						<label for="txtMinimumStockAdd" class="lblInput" >Stok Minimal</label>
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

				$("#txtBuyPriceAdd, #txtRetailPriceAdd, #txtPrice1Add, #txtPrice2Add").on("input change paste",
				    function filterNumericAndDecimal(event) {
						var formControl;
						formControl = $(event.target);
						formControl.val(formControl.val().replace(/[^0-9]+/g, ""));
					}
				);

				$("#txtWeightAdd, #txtQty1Add, #txtQty2Add, #txtMinimumStockAdd").on("input change paste",
				    function filterNumericAndDecimal(event) {
						var formControl;
						formControl = $(event.target);
						formControl.val(formControl.val().replace(/[^0-9.]+/g, ""));
					}
				);
				
			});
		</script>
	</body>
</html>
