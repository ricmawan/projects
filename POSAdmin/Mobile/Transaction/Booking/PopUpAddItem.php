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
		logEvent(mysqli_error($dbh), $RequestedPath, mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
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
			<form class="col-md-12 col-sm-12" id="PostFormItem" method="POST" action="" >
				<div id="tabs" style="background-color: #f5f8fd;" >
					<ul>
						<li><a href="#item-data">Data Barang</a></li>
						<li><a href="#add-unit" onclick="addTab();" >Tambah Satuan</a></li>
					</ul>
					<div id="item-data">
						<br />
						<div class="row">
							<div class="col-md-3 col-sm-3 has-float-label">
								<input id="hdnTabsCounter" name="hdnTabsCounter" type="hidden" />
								<input id="txtItemCodeAdd" name="txtItemCodeAdd" type="text" tabindex=16 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Barang" required readonly />
								<label for="txtItemCodeAdd" class="lblInput" >Kode Barang</label>
							</div>
							<div class="col-md-3 col-sm-3 has-float-label">
								<input id="txtItemNameAdd" name="txtItemNameAdd" type="text" tabindex=17 class="form-control-custom" onfocus="this.select();" maxlength="37" autocomplete=off placeholder="Nama Barang" required />
								<label for="txtItemNameAdd" class="lblInput" >Nama Barang</label>
							</div>
							<div class="col-md-3 col-sm-3 has-float-label">
								<select id="ddlUnitAdd" name="ddlUnitAdd" tabindex=18 class="form-control-custom" placeholder="Pilih Satuan" >
									<?php
										$sql = "CALL spSelDDLUnit('".$_SESSION['UserLoginMobile']."')";
										if (! $result = mysqli_query($dbh, $sql)) {
											logEvent(mysqli_error($dbh), '/Master/Item/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
											return 0;
										}
										while($row = mysqli_fetch_array($result)) {
											echo "<option value='".$row['UnitID']."' >".$row['UnitName']."</option>";
										}
										mysqli_free_result($result);
										mysqli_next_result($dbh);
									?>
								</select>
								<label for="ddlUnitAdd" class="lblInput" >Satuan Dasar</label>
							</div>
							<div class="col-md-3 col-sm-3 has-float-label">
								<div class="ui-widget" style="width: 100%;">
									<select id="ddlCategoryAdd" name="ddlCategoryAdd" tabindex=19 class="form-control-custom" placeholder="Pilih Kategori" >
										<option value="" selected> </option>
										<?php
											$sql = "CALL spSelDDLCategory('".$_SESSION['UserLoginMobile']."')";
											if (! $result = mysqli_query($dbh, $sql)) {
												logEvent(mysqli_error($dbh), '/Master/Item/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
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
								<label for="ddlCategoryAdd" class="lblInput" >Kategori</label>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-3 col-sm-3 has-float-label">
								<input id="txtWeightAdd" name="txtWeightAdd" type="tel" tabindex=20 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Berat (KG)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertWeight(this.id, this.value);" required />
								<label for="txtWeightAdd" class="lblInput" >Berat (KG)</label>
							</div>
							<div class="col-md-3 col-sm-3 has-float-label">
								<input id="txtMinimumStockAdd" name="txtMinimumStockAdd" type="tel" tabindex=21 class="form-control-custom text-right" value=0 onfocus="this.select();" autocomplete=off placeholder="Stok Minimal" required />
								<label for="txtMinimumStockAdd" class="lblInput" >Stok Minimal</label>
							</div>
							<div class="col-md-3 col-sm-3 has-float-label">
								<input id="txtBuyPriceAdd" name="txtBuyPriceAdd" type="tel" tabindex=22 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Beli" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
								<label for="txtBuyPriceAdd" class="lblInput" >Harga Beli</label>
							</div>
							<div class="col-md-3 col-sm-3 has-float-label">
								<input id="txtRetailPriceAdd" name="txtRetailPriceAdd" type="tel" tabindex=23 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Ecer" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
								<label for="txtRetailPriceAdd" class="lblInput" >Harga Ecer</label>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-4 col-sm-4 has-float-label">
								<input id="txtPrice1Add" name="txtPrice1Add" type="tel" tabindex=24 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Grosir 1" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
								<label for="txtPrice1Add" class="lblInput" >Harga Grosir 1</label>
							</div>
							<div class="col-md-2 col-sm-2 has-float-label">
								<input id="txtQty1Add" name="txtQty1Add" type="tel" tabindex=25 class="form-control-custom text-right" value=1 min=1 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 1" required />
								<label for="txtQty1Add" class="lblInput" >Qty Grosir 1</label>
							</div>
							<div class="col-md-4 col-sm-4 has-float-label">
								<input id="txtPrice2Add" name="txtPrice2Add" type="tel" tabindex=26 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Grosir 2" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
								<label for="txtPrice2Add" class="lblInput" >Harga Grosir 2</label>
							</div>
							<div class="col-md-2 col-sm-2 has-float-label">
								<input id="txtQty2Add" name="txtQty2Add" type="tel" tabindex=27 class="form-control-custom text-right" value=1 min=1 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 2" required />
								<label for="txtQty2Add" class="lblInput" >Qty Grosir 2</label>
							</div>
						</div>
					</div>
					<div id="add-unit" class="addUnit">

					</div>
				</div>
			</form>
		</div>
		<div id="addUnitTemplate" style="display: none;" >
			<br />
			<div class="row">
				<div class="col-md-4 col-sm-4 has-float-label">
					<input id="hdnItemDetailsID_" name="hdnItemDetailsID_" type="hidden" value=0 />
					<input id="txtItemCode_" name="txtItemCode_" type="text" class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Barang" />
					<label for="txtItemCode_" class="lblInput" >Kode Barang</label>
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-md-4 col-sm-4 has-float-label">
					<select id="ddlUnit_" name="ddlUnit_" class="form-control-custom" placeholder="Pilih Satuan" >
						<?php
							$sql = "CALL spSelDDLUnit('".$_SESSION['UserLoginMobile']."')";
							if (! $result = mysqli_query($dbh, $sql)) {
								logEvent(mysqli_error($dbh), '/Master/Item/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLoginMobile']));
								return 0;
							}
							while($row = mysqli_fetch_array($result)) {
								echo "<option value='".$row['UnitID']."' >".$row['UnitName']."</option>";
							}
							mysqli_free_result($result);
							mysqli_next_result($dbh);
						?>
					</select>
					<label for="ddlUnit_" class="lblInput" >Satuan</label>
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-md-4 col-sm-4 has-float-label">
					<input id="txtConversionQty_" name="txtConversionQty_" onkeypress="isEnterKey(event, 'focusToSave');" type="number" class="form-control-custom text-right" value=1 min=1 onfocus="this.select();" autocomplete=off placeholder="Konversi Qty" onpaste="return false;" />
					<label for="txtConversionQty_" class="lblInput" >Konversi Qty</label>
				</div>
			</div>
		</div>
		<div id="save-confirm-add" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
		</div>
		<div id="loading"></div>
		<script>
			var tabsCounter = 0;
			var tabs;
			function focusToSave() {
				$("#btnSimpan").focus();
			}

			function resetTabs() {
				var i = 0;
				tabsCounter = 0;
				$("#hdnTabsCounter").val(0);
				$(".ui-tabs-nav").find( "li" ).each(function() {
					if(i > 0) {
						var panelId = $(this).remove().attr( "aria-controls" );
						$( "#" + panelId ).remove();
					}
					i++;
				});

				var label = "Tambah Satuan",
		        id = "add-unit",
		        li = '<li><a href="#add-unit" onclick="addTab();" >Tambah Satuan</a></li>';
		        tabContentHtml = "";
		 
				tabs.find( ".ui-tabs-nav" ).append( li );
				tabs.append( "<div id='" + id + "'><p>" + tabContentHtml + "</p></div>" );
				tabs.tabs("refresh");
			}

			function refreshTabNumber() {
				if(tabsCounter > 0) {
					var i = 0;
					$(".ui-tabs-nav").find("a").each(function() {
						if(i > 0 && i < (tabsCounter + 1)) {
							$(this).html("Satuan " + (i + 1));
							$(this).attr("href", "#add-unit-" + i);
						}
						i++;
					});

					i = 1;
					var tabIndex = 0;
					var j = 0;
					$(".addUnit").each(function() {
						j = 0; 
						$(this).attr("id", "add-unit-" + i);
						$(this).find("input, select").each(function() {
							tabIndex = i * 10 + j + 20;
							$(this).attr("id", $(this).attr("id").substring(0, $(this).attr("id").lastIndexOf('_') + 1) + i);
							$(this).attr("name", $(this).attr("id").substring(0, $(this).attr("id").lastIndexOf('_') + 1) + i);
							$(this).attr("tabindex", parseInt(tabIndex));
							$(this).attr("required", true);
							j++;
						});
						i++;
					});
				}
			}

			function addTab() {
				tabsCounter = tabsCounter + 1;
				$("#hdnTabsCounter").val(tabsCounter);
				$("#add-unit").html($("#addUnitTemplate").html());
				$("a[href='#add-unit']").html("Satuan " + (tabsCounter + 1));
				$("a[href='#add-unit']").attr("onclick", "");
				$("a[href='#add-unit']").attr("href", "#add-unit-" + tabsCounter);
				$("#add-unit").attr("id", "add-unit-" + tabsCounter);
				tabs.tabs( "option", "active", tabsCounter );

				var i = 0;
				var tabIndex = 0;
				$("#add-unit-" + tabsCounter).find("input, select").each(function() {
					tabIndex = tabsCounter * 10 + i + 20;
					$(this).attr("id", $(this).attr("id") + tabsCounter);
					$(this).attr("name", $(this).attr("name") + tabsCounter);
					$(this).attr("tabindex", parseInt(tabIndex));
					$(this).attr("required", true);
					i++;
				});

				$("#add-unit-" + tabsCounter).addClass("addUnit");

				$("a[href='#add-unit-" + tabsCounter + "']").after("<span class='ui-icon ui-icon-close' role='presentation' style='margin-top:4px;cursor:pointer;' acronym title='Hapus Satuan'>Remove Tab</span>");

				if(tabsCounter < 3) {
					var label = "Tambah Satuan",
			        id = "add-unit",
			        li = '<li><a href="#add-unit" onclick="addTab();" >Tambah Satuan</a></li>';
			        tabContentHtml = "";
			 
					tabs.find( ".ui-tabs-nav" ).append( li );
					tabs.append( "<div id='" + id + "'><p>" + tabContentHtml + "</p></div>" );
				}
				tabs.tabs( "refresh" );
			}

			$(document).ready(function() {
				$("#txtItemNameAdd").on("keydown", function(evt) {
					if(evt.keyCode == 222) {
						evt.stopImmediatePropagation();
						return true;
					}
				});
				
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
				
				tabs = $( "#tabs" ).tabs({
					activate: function( event, ui ) {
						var active = $("#tabs" ).tabs( "option", "active" );
						if(active > 0) {
							setTimeout(function() {
								$("#txtItemCode_" + active).focus();
							}, 0);
						}
						else {
							$("#txtItemCodeAdd").focus();
						}
					}
				});

				tabs.tabs( "option", "active", 0 );

				$("#txtItemCode").focus();
				var counterClose = 0;
				// Close icon: removing the tab on click
				tabs.on( "click", "span.ui-icon-close", function() {
			    	if(counterClose == 0) {
			    		counterClose = 1;
				    	var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
						$( "#" + panelId ).remove();
						var tabsBefore = tabsCounter;
						tabsCounter = tabsCounter - 1;
						$("#hdnTabsCounter").val(tabsCounter);
						//tabs.tabs( "refresh" );
						if(tabsCounter > 0) refreshTabNumber();
						if(tabsBefore == 3) {
							var label = "Tambah Satuan",
					        id = "add-unit",
					        li = '<li><a href="#add-unit" onclick="addTab();" >Tambah Satuan</a></li>';
					        tabContentHtml = "";
					 
							tabs.find( ".ui-tabs-nav" ).append( li );
							tabs.append( "<div id='" + id + "'><p>" + tabContentHtml + "</p></div>" );
						}
						tabs.tabs("refresh");
						var active = $("#tabs" ).tabs( "option", "active" );
						if(active > 0) {
							setTimeout(function() {
								$("#txtItemCode_" + active).focus();
							}, 0);
						}
						else {
							$("#txtItemCode").focus();
						}
			    	}
			    	setTimeout(function() {
			    		counterClose = 0;
			    	}, 1000)
			    });
			});
		</script>
	</body>
</html>
