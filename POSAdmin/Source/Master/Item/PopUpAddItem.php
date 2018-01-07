<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";
?>
<html>
	<head>
		<title>Main App</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		
		<link href="../../assets/css/bootstrap.css" rel="stylesheet" />
		<link href="../../assets/css/font-awesome.css" rel="stylesheet" />
		<link href="../../assets/css/jquery-ui.css" rel="stylesheet" />
		<link href="../../assets/css/jquery-ui.structure.css" rel="stylesheet" />
		<link href="../../assets/css/jquery-ui.theme.css" rel="stylesheet" />
		<link href="../../assets/css/custom.css" rel="stylesheet" />
		<link href="../../assets/css/lobibox.css" rel="stylesheet" />
		<style>
			.ui-button.ui-corner-all.ui-widget.ui-button-icon-only.ui-dialog-titlebar-close {
				display: none;
			}
			
			@media (min-width: 760px) {
				.col-md-12 {
				   float: left !important;
				}
			}
		</style>
	</head>
	<body>
		<div id="FormData" title="Tambah Item" style="display: none;">
			<form class="col-md-12" id="PostForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-3 labelColumn">
						Kode Barang :
						<input id="hdnItemID" name="hdnItemID" type="hidden" value=0 />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
					</div>
					<div class="col-md-4">
						<input id="txtItemCode" name="txtItemCode" type="text" tabindex=5 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Barang" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Nama Barang :
					</div>
					<div class="col-md-4">
						<input id="txtItemName" name="txtItemName" type="text" tabindex=6 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Nama Barang" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Kategori Barang :
					</div>
					<div class="col-md-4">
						<div class="ui-widget" style="width: 100%;">
							<select id="ddlCategory" name="ddlCategory" tabindex=7 class="form-control-custom" placeholder="Pilih Kategori" >
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
						<input id="txtBuyPrice" name="txtBuyPrice" type="text" tabindex=8 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Beli" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Harga Ecer :
					</div>
					<div class="col-md-4">
						<input id="txtRetailPrice" name="txtRetailPrice" type="text" tabindex=9 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Ecer" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Harga Grosir 1 :
					</div>
					<div class="col-md-4">
						<input id="txtPrice1" name="txtPrice1" type="text" tabindex=10 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Grosir 1" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
					<div class="col-md-2 labelColumn">
						Qty Grosir 1 :
					</div>
					<div class="col-md-2">
						<input id="txtQty1" name="txtQty1" type="number" tabindex=11 class="form-control-custom" value=0 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 1" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Harga Grosir 2 :
					</div>
					<div class="col-md-4">
						<input id="txtPrice2" name="txtPrice2" type="text" tabindex=12 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Grosir 2" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
					<div class="col-md-2 labelColumn">
						Qty Grosir 2 :
					</div>
					<div class="col-md-2">
						<input id="txtQty2" name="txtQty2" type="number" tabindex=13 class="form-control-custom" value=0 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 2" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Berat (KG) :
					</div>
					<div class="col-md-4">
						<input id="txtWeight" name="txtWeight" type="text" tabindex=14 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Berat (KG)" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
					<div class="col-md-2 labelColumn">
						Stok Minimal :
					</div>
					<div class="col-md-2">
						<input id="txtMinimumStock" name="txtMinimumStock" type="number" tabindex=15 class="form-control-custom" value=0 onfocus="this.select();" autocomplete=off placeholder="Stok Minimal" required />
					</div>
				</div>
			</form>
		</div>
		<div id="save-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
		</div>
		<div id="loading"></div>
		<script src="../../assets/js/jquery-1.12.4.js"></script>
		<script src="../../assets/js/bootstrap.js"></script>
		<script src="../../assets/js/jquery-ui.js"></script>
		<script src="../../assets/js/notify.js"></script>
		<script src="../../assets/js/lobibox.js"></script>
		<script src="../../assets/js/global.js"></script>
		<script>
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				$("#FormData").attr("title", "Tambah Barang");
				//console.log(index);
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
						$("#divModal").show();
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //right arrow
								$("#btnCancelAddItem").focus();
							}
							else if(e.keyCode == 37 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //left arrow
								$("#btnSaveItem").focus();
							}
						});
					},
					show: {
						effect: "fade",
						duration: 500
					},
					hide: {
						effect: "fade",
						duration: 500
					},
					close: function() {
						window.close();
					},
					resizable: false,
					height: 440,
					width: 780,
					modal: false,
					buttons: [
					{
						text: "Simpan",
						id: "btnSaveItem",
						tabindex: 16,
						click: function() {
							console.log("test");
							saveConfirm(function(action) {
								if(action == "Ya") {
									$.ajax({
										url: "./Insert.php",
										type: "POST",
										data: $("#PostForm").serialize(),
										dataType: "json",
										success: function(data) {
											if(data.FailedFlag == '0') {
												$("#loading").hide();
												var counter = 0;
												Lobibox.alert("success",
												{
													msg: data.Message,
													width: 480,
													delay: 2000,
													beforeClose: function() {
														if(counter == 0) {
															window.close();
															counter = 1;
														}
													}
												});
											}
											else {
												$("#loading").hide();
												var counter = 0;
												Lobibox.alert("warning",
												{
													msg: data.Message,
													width: 480,
													delay: false,
													beforeClose: function() {
														if(counter == 0) {
															$("#txtItemCode").focus();
															counter = 1;
														}
													}
												});
												return 0;
											}
										},
										error: function(jqXHR, textStatus, errorThrown) {
											$("#loading").hide();
											var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
											LogEvent(errorMessage, "/Master/Item/PopUpAddItem.php");
											Lobibox.alert("error",
											{
												msg: errorMessage,
												width: 480
											});
											return 0;
										}
									});
								}
								else {
									$("#txtItemCode").focus();
									return false;
								}
							});
						}
					},
					{
						text: "Batal",
						id: "btnCancelAddItem",
						click: function() {
							//$(this).dialog("destroy");
							window.close();
							//return false;
						}
					}]
				}).dialog("open");
			}

			$(document).ready(function() {
				keyFunction();
				enterLikeTab();
				$("#ddlCategory").combobox();
				var counterItem = 0;
				openDialog(0, 0);
			});
		</script>
	</body>
</html>
