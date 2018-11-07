<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <span style="width:50%;display:inline-block;">
							 <h5>Master Data Barang</h5>
						</span>
						<span style="width:49%;display:inline-block;text-align:right;">
							<button id="btnAdd" class="btn btn-primary" onclick="openDialog(0, 0);"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
							<?php
								if($DeleteFlag == true) echo '<button id="btnDelete" class="btn btn-danger" onclick="fnDeleteData();" ><i class="fa fa-close"></i> Hapus</button>';
								echo '<input id="hdnEditFlag" name="hdnEditFlag" type="hidden" value="'.$EditFlag.'" />';
								echo '<input id="hdnDeleteFlag" name="hdnDeleteFlag" type="hidden" value="'.$DeleteFlag.'" />';
								echo '<input id="hdnUserTypeID" name="hdnUserTypeID" type="hidden" value="'.$_SESSION['UserTypeID'].'" />';
							?>
						</span>
					</div>
					<div class="panel-body">
						<div class="table-responsive" style="overflow-x:hidden;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th><input id="select_all" name="select_all" type="checkbox" onclick="chkAll();" /></th>
										<th>No</th>
										<th>Kode</th>
										<th>Nama</th>
										<th>Kategori</th>
										<th>Satuan</th>
										<th>H Beli</th>
										<th>H Ecer</th>
										<th>H1</th>
										<th>QTY1</th>
										<th>H2</th>
										<th>QTY2</th>
										<th>Berat</th>
										<th>Stok Min</th>
										<th>ItemID</th>
										<th>CategoryID</th>
										<th>UnitID</th>
									</tr>
								</thead>
							</table>
						</div>
						<br />
						<div class="row col-md-12" >
							<h5>INSERT = Tambah Data; ENTER/DOUBLE KLIK = Edit; DELETE = Hapus; SPASI = Menandai Data;</h5>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="FormData" title="Tambah Item" style="display: none;">
			<form class="col-md-12" id="PostForm" method="POST" action="" >
				<div id="tabs">
					<ul>
						<li><a href="#item-data">Data Barang</a></li>
						<li><a href="#add-unit" onclick="addTab();" >Tambah Satuan</a></li>
					</ul>
					<div id="item-data">
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn">
								Kode Barang :
								<input id="hdnItemID" name="hdnItemID" type="hidden" value=0 />
								<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
								<input id="hdnTabsCounter" name="hdnTabsCounter" type="hidden" />
							</div>
							<div class="col-md-4">
								<input id="txtItemCode" name="txtItemCode" type="text" tabindex=5 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Barang" required />
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn">
								Nama Barang :
							</div>
							<div class="col-md-4">
								<input id="txtItemName" name="txtItemName" type="text" tabindex=6 class="form-control-custom" onfocus="this.select();" maxlength="37" autocomplete=off placeholder="Nama Barang" required />
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn">
								Satuan Dasar :
							</div>
							<div class="col-md-4">
								<select id="ddlUnit" name="ddlUnit" tabindex=7 class="form-control-custom" placeholder="Pilih Satuan" >
									<?php
										$sql = "CALL spSelDDLUnit('".$_SESSION['UserLogin']."')";
										if (! $result = mysqli_query($dbh, $sql)) {
											logEvent(mysqli_error($dbh), '/Master/Item/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
											return 0;
										}
										while($row = mysqli_fetch_array($result)) {
											echo "<option value='".$row['UnitID']."' >".$row['UnitName']."</option>";
										}
										mysqli_free_result($result);
										mysqli_next_result($dbh);
									?>
								</select>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn">
								Kategori :
							</div>
							<div class="col-md-4">
								<div class="ui-widget" style="width: 100%;">
									<select id="ddlCategory" name="ddlCategory" tabindex=8 class="form-control-custom" placeholder="Pilih Kategori" >
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
							<div class="col-md-2 labelColumn">
								Harga Beli :
							</div>
							<div class="col-md-4">
								<input id="txtBuyPrice" name="txtBuyPrice" type="text" tabindex=9 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Beli" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" required />
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn">
								Harga Ecer :
							</div>
							<div class="col-md-4">
								<input id="txtRetailPrice" name="txtRetailPrice" type="text" tabindex=10 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Ecer" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" required />
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn">
								Harga Grosir 1 :
							</div>
							<div class="col-md-4">
								<input id="txtPrice1" name="txtPrice1" type="text" tabindex=11 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Grosir 1" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" required />
							</div>
							<div class="col-md-2 labelColumn">
								Qty Grosir 1 :
							</div>
							<div class="col-md-2">
								<input id="txtQty1" name="txtQty1" type="number" tabindex=12 class="form-control-custom text-right" value=1 min=1 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 1" onpaste="return false;" required />
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn">
								Harga Grosir 2 :
							</div>
							<div class="col-md-4">
								<input id="txtPrice2" name="txtPrice2" type="text" tabindex=13 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Grosir 2" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" required />
							</div>
							<div class="col-md-2 labelColumn">
								Qty Grosir 2 :
							</div>
							<div class="col-md-2">
								<input id="txtQty2" name="txtQty2" type="number" tabindex=14 class="form-control-custom text-right" value=1 min=1 onfocus="this.select();" autocomplete=off placeholder="Qty Grosir 2" onpaste="return false;" required />
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-md-2 labelColumn">
								Berat (KG) :
							</div>
							<div class="col-md-4">
								<input id="txtWeight" name="txtWeight" type="text" tabindex=15 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Berat (KG)" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertWeight(this.id, this.value);" onpaste="return false;" required />
							</div>
							<div class="col-md-2 labelColumn">
								Stok Minimal :
							</div>
							<div class="col-md-2">
								<input id="txtMinimumStock" name="txtMinimumStock" type="number" tabindex=16 class="form-control-custom text-right" value=0 onfocus="this.select();" autocomplete=off placeholder="Stok Minimal" onpaste="return false;" required />
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
				<div class="col-md-2 labelColumn">
					Kode Barang :
				</div>
				<div class="col-md-4">
					<input id="hdnItemDetailsID_" name="hdnItemDetailsID_" type="hidden" value=0 />
					<input id="txtItemCode_" name="txtItemCode_" type="text" class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="Kode Barang" />
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-md-2 labelColumn">
					Satuan :
				</div>
				<div class="col-md-4">
					<select id="ddlUnit_" name="ddlUnit_" class="form-control-custom" placeholder="Pilih Satuan" >
						<?php
							$sql = "CALL spSelDDLUnit('".$_SESSION['UserLogin']."')";
							if (! $result = mysqli_query($dbh, $sql)) {
								logEvent(mysqli_error($dbh), '/Master/Item/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
								return 0;
							}
							while($row = mysqli_fetch_array($result)) {
								echo "<option value='".$row['UnitID']."' >".$row['UnitName']."</option>";
							}
							mysqli_free_result($result);
							mysqli_next_result($dbh);
						?>
					</select>
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-md-2 labelColumn">
					Konversi Qty :
				</div>
				<div class="col-md-4">
					<input id="txtConversionQty_" name="txtConversionQty_" type="number" onkeypress="isEnterKey(event, 'focusToSave');" class="form-control-custom text-right" value=1 min=1 onfocus="this.select();" autocomplete=off placeholder="Konversi Qty" onpaste="return false;" />
				</div>
			</div>
		</div>
		<script>
			var table;
			var tabsCounter = 0;
			var tabs;
			function focusToSave() {
				$("#btnSaveItem").focus();
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
							tabIndex = i * 10 + j + 10;
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
					tabIndex = tabsCounter * 10 + i + 10;
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

			function loadMultiUnit() {
				var ItemID = $("#hdnItemID").val();
				$.ajax({
					url: "./Master/Item/ItemDetails.php",
					type: "POST",
					data: { ItemID : ItemID },
					dataType: "json",
					success: function(Data) {
						if(Data.FailedFlag == '0') {
							for(var i=0;i<Data.data.length;i++) {
								addTab();
								$("#hdnItemDetailsID_" + (i + 1)).val(Data.data[i][0]);
								$("#txtItemCode_" + (i + 1)).val(Data.data[i][1]);
								$("#ddlUnit_" + (i + 1)).val(Data.data[i][2]);
								$("#txtConversionQty_" + (i + 1)).val(Data.data[i][3]);
								tabs.tabs("option", "active", 0);
							}
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$("#loading").hide();
						var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
						LogEvent(errorMessage, "/Master/Item/index.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
			}
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#FormData").attr("title", "Edit Barang");
					$("#hdnItemID").val(Data[14]);
					$("#txtItemCode").val(Data[2]);
					$("#txtItemName").val(Data[3]);
					$("#ddlCategory").val(Data[15]);
					$("#ddlCategory").next().find("input").val($("#ddlCategory option:selected").text());
					$("#txtBuyPrice").val(returnRupiah(Data[6].toString()));
					$("#txtRetailPrice").val(returnRupiah(Data[7].toString()));
					$("#txtPrice1").val(returnRupiah(Data[8].toString()));
					$("#txtQty1").val(Data[9]);
					$("#txtPrice2").val(returnRupiah(Data[10].toString()));
					$("#txtQty2").val(Data[11]);
					$("#txtWeight").val(returnWeight(Data[12].toString()));
					$("#txtMinimumStock").val(Data[13]);
					$("#ddlUnit").val(Data[16]);
					loadMultiUnit(Data[14]);
				}
				else $("#FormData").attr("title", "Tambah Barang");
				var index = table.cell({ focused: true }).index();
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
						table.keys.disable();
						$("#divModal").show();
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //right arrow
								$("#btnCancelAddItem").focus();
							}
							else if(e.keyCode == 37 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //left arrow
								$("#btnSaveItem").focus();
							}
						});
						setTimeout(function() {
							$("#txtItemCode").focus();
						}, 0);
					},
					
					close: function() {
						$(this).dialog("destroy");
						$("#divModal").hide();
						table.keys.enable();
						if(typeof index !== 'undefined') table.cell(index).focus();
						resetForm();
					},
					resizable: false,
					height: 540,
					width: 780,
					modal: false,
					buttons: [
					{
						text: "Simpan",
						id: "btnSaveItem",
						tabindex: 17,
						click: function() {
							var PassValidate = 1;
							var FirstFocus = 0;
							$(".form-control-custom").each(function() {
								if($(this).hasAttr('required')) {
									if($(this).val() == "") {
										PassValidate = 0;
										$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
										if(FirstFocus == 0) {
											$(this).focus();
											if($(this).attr("id").indexOf("_1") > 0) {
												tabs.tabs("option", "active", 1);
											}
											else if($(this).attr("id").indexOf("_2") > 0) {
												tabs.tabs("option", "active", 2);
											}
											else if($(this).attr("id").indexOf("_3") > 0) {
												tabs.tabs("option", "active", 3);
											}
											else {
												tabs.tabs("option", "active", 0);
											}
											FirstFocus = 1;
										}
									}
								}
							});
							
							if($("#ddlCategory").val() == "") {
								PassValidate = 0;
								$("#ddlCategory").next().find("input").notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $("#ddlCategory").next().find("input").focus();
								FirstFocus = 1;
								tabs.tabs("option", "active", 0);
							}

							if(parseFloat($("#txtQty1").val()) > 1 && parseFloat($("#txtQty2").val()) > 1 && parseFloat($("#txtQty1").val()) > parseFloat($("#txtQty2").val()) ) { 
								PassValidate = 0;
								$("#txtQty1").notify("Qty 1 harus lebih kecil dari Qty 2!", { position:"right", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $("#txtQty1").focus();
								FirstFocus = 1;
								tabs.tabs("option", "active", 0);
							}

							if( parseFloat($("#txtBuyPrice").val().replace(/\,/g, "")) > parseFloat($("#txtRetailPrice").val().replace(/\,/g, "")) ) {
								PassValidate = 0;
								$("#txtRetailPrice").notify("Harga ecer lebih kecil dari harga beli!", { position:"right", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $("#txtRetailPrice").focus();
								FirstFocus = 1;
								tabs.tabs("option", "active", 0);
							}

							if( parseFloat($("#txtBuyPrice").val().replace(/\,/g, "")) > parseFloat($("#txtPrice1").val().replace(/\,/g, "")) ) {
								PassValidate = 0;
								$("#txtPrice1").notify("Harga grosir 1 lebih kecil dari harga beli!", { position:"right", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $("#txtPrice1").focus();
								FirstFocus = 1;
								tabs.tabs("option", "active", 0);
							}

							if( parseFloat($("#txtBuyPrice").val().replace(/\,/g, "")) > parseFloat($("#txtPrice2").val().replace(/\,/g, "")) ) {
								PassValidate = 0;
								$("#txtPrice2").notify("Harga grosir 2 lebih kecil dari harga beli!", { position:"right", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $("#txtPrice2").focus();
								FirstFocus = 1;
								tabs.tabs("option", "active", 0);
							}

							if($("#hdnTabsCounter").val() > 0) {
								for(var j=1;j<=$("#hdnTabsCounter").val();j++) {
									if($("#ddlUnit").val() == $("#ddlUnit_" + j).val()) {
										PassValidate = 0;
										if(FirstFocus == 0) {
											tabs.tabs("option", "active", j);
											$("#ddlUnit_" + j).notify("Tidak boleh sama!", { position:"right", className:"warn", autoHideDelay: 2000 });
											$("#ddlUnit_" + j).focus();
											FirstFocus = 1;
										}
									}
									else if($("#txtItemCode").val() == $("#txtItemCode_" + j).val()) {
										PassValidate = 0;
										if(FirstFocus == 0) {
											tabs.tabs("option", "active", j);
											$("#txtItemCode_" + j).notify("Tidak boleh sama!", { position:"right", className:"warn", autoHideDelay: 2000 });
											$("#txtItemCode_" + j).focus();
											FirstFocus = 1;
										}
									}
								}

								for(var i=1;i<=$("#hdnTabsCounter").val();i++) {
									for(var j=i+1;j<=$("#hdnTabsCounter").val();j++) {
										if($("#ddlUnit_" + i).val() == $("#ddlUnit_" + j).val()) {
											PassValidate = 0;
											if(FirstFocus == 0) {
												tabs.tabs("option", "active", j);
												$("#ddlUnit_" + j).notify("Tidak boleh sama!", { position:"right", className:"warn", autoHideDelay: 2000 });
												$("#ddlUnit_" + j).focus();
												FirstFocus = 1;
											}
										}
										else if($("#txtItemCode_" + i).val() == $("#txtItemCode_" + j).val()) {
											PassValidate = 0;
											if(FirstFocus == 0) {
												tabs.tabs("option", "active", j);
												$("#txtItemCode_" + j).notify("Tidak boleh sama!", { position:"right", className:"warn", autoHideDelay: 2000 });
												$("#txtItemCode_" + j).focus();
												FirstFocus = 1;
											}
										}
									}
								}
							}
							
							if(PassValidate == 1) {
								saveConfirm(function(action) {
									if(action == "Ya") {
										$.ajax({
											url: "./Master/Item/Insert.php",
											type: "POST",
											data: $("#PostForm").serialize(),
											dataType: "json",
											success: function(data) {
												if(data.FailedFlag == '0') {
													$("#loading").hide();
													$("#FormData").dialog("destroy");
													$("#divModal").hide();
													resetForm();
													var counter = 0;
													Lobibox.alert("success",
													{
														msg: data.Message,
														width: 480,
														delay: 2000,
														beforeClose: function() {
															if(counter == 0) {
																table.keys.enable();
																counter = 1;
															}
														},
														shown: function() {
															setTimeout(function() {
																table.ajax.reload(function() {
																	table.keys.enable();
																	if(typeof index !== 'undefined') table.cell(index).focus();
																	table.keys.disable();
																	table.columns.adjust();
																}, false);
															}, 0);
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
																setTimeout(function() {
																	$("#txtItemCode").focus();
																}, 0);
																counter = 1;
															}
														}
													});
													return 0;
												}
											},
											error: function(jqXHR, textStatus, errorThrown) {
												$("#loading").hide();
												var counter = 0;
												var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
												LogEvent(errorMessage, "/Master/Item/index.php");
												Lobibox.alert("error",
												{
													msg: errorMessage,
													width: 480,
													beforeClose: function() {
														if(counter == 0) {
															setTimeout(function() {
																$("#txtItemCode").focus();
															}, 0);
															counter = 1;
														}
													}
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
						}
					},
					{
						text: "Batal",
						id: "btnCancelAddItem",
						tabindex: 18,
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
							table.keys.enable();
							if(typeof index !== 'undefined') table.cell(index).focus();
							resetForm();
							return false;
						}
					}]
				}).dialog("open");
			}
			
			function resetForm() {
				$("#hdnItemID").val(0);
				if( $("#ddlUnit").has("option").length > 0 ) $("#ddlUnit option")[0].selected = true;
				$("#txtItemCode").val("");
				$("#txtItemName").val("");
				$("#ddlCategory").val("");
				$("#ddlCategory").next().find("input").val("");
				$("#txtBuyPrice").val("0");
				$("#txtRetailPrice").val("0");
				$("#txtPrice1").val("0");
				$("#txtQty1").val(0);
				$("#txtPrice2").val("0");
				$("#txtQty2").val(0);
				$("#txtWeight").val("0");
				$("#txtMinimumStock").val(0);
				resetTabs();
			}
			
			function fnDeleteData() {
				var index = table.cell({ focused: true }).index();
				table.keys.disable();
				DeleteData("./Master/Item/Delete.php", function(action) {
					if(action == "success") {
						$("#select_all").prop("checked", false);
						table.ajax.reload(function() {
							table.keys.enable();
							if(typeof index !== 'undefined') {
								try {
									table.cell(index).focus();
								}
								catch (err) {
									$("#grid-data").DataTable().cell( ':eq(0)' ).focus();
								}
							}
							if(table.page.info().page == table.page.info().pages) {
								setTimeout(function() {
									table.page("previous").draw('page');
								}, 0);
							}
							table.columns.adjust();
						}, false);
					}
					else {
						table.keys.enable();
						return false;
					}
				});
			}

			function adjustColumns() {
				//kolom ga cukup dan harus scroll horizontal
				if($(".dataTables_scrollHeadInner").find("table").width() > $(".dataTables_scrollHead").width()) {
					var headerWidth = $(".dataTables_scrollHead").find("table").width() + 2;
					$(".dataTables_scrollBody").find("table").css({
						"width" : headerWidth + "px"
					});

					var tableWidth = $(".dataTables_scrollBody").find("table").width();
					var barWidth = table.settings()[0].oScroll.iBarWidth;
					var newWidth = tableWidth + barWidth + 2;
					setTimeout(function() {
						$(".dataTables_scrollHeadInner").css({
							"width" : newWidth
						});
					}, 0);
				}
				//kalo full 1 layar ga ada scroll horizontal tp ada scroll vertical
				else if($(".dataTables_scrollBody").find("table").height() > 330 && $("#wrapper").width() <= 1280) {
					var headerDiv = $(".dataTables_scrollHead").width();
					var headerWidth = $(".dataTables_scrollHead").find("table").width() + 2;

					$(".dataTables_scrollBody").find("table").css({
						"width" : headerWidth + "px"
					});

					var tableWidth = $(".dataTables_scrollBody").find("table").width();
					var barWidth = table.settings()[0].oScroll.iBarWidth;
					var newWidth = tableWidth - barWidth + 2;
					setTimeout(function() {
						$(".dataTables_scrollHeadInner").css({
							"width" : (headerDiv - barWidth)
						});

						$(".dataTables_scrollHeadInner").find("table").css({
							"width" : (headerDiv - barWidth)
						});

						$(".dataTables_scrollBody").find("table").css({
							"width" : (headerDiv - barWidth)
						});
					}, 0);
				}
				else {
					var headerDiv = $(".dataTables_scrollHead").width();
					var headerWidth = $(".dataTables_scrollHead").find("table").width() + 2;

					$(".dataTables_scrollBody").find("table").css({
						"width" : headerWidth + "px"
					});

					var tableWidth = $(".dataTables_scrollBody").find("table").width();
					var barWidth = table.settings()[0].oScroll.iBarWidth;
					var newWidth = tableWidth + barWidth + 2;
					setTimeout(function() {
						$(".dataTables_scrollHeadInner").css({
							"width" : headerDiv
						});

						$(".dataTables_scrollHeadInner").find("table").css({
							"width" : headerDiv
						});

						$(".dataTables_scrollBody").find("table").css({
							"width" : headerDiv
						});

						$(".dataTables_scrollHeadInner").css({
							"width" : newWidth
						});
					}, 0);
				}
			}

			var waitForFinalEvent = (function () {
		        var timers = {};
		        return function (callback, ms, uniqueId) {
		            if (!uniqueId) {
		                uniqueId = "Don't call this twice without a uniqueId";
		            }
		            if (timers[uniqueId]) {
		                clearTimeout(timers[uniqueId]);
		            }
		            timers[uniqueId] = setTimeout(callback, ms);
		        };
		    })();
			
			$(document).ready(function() {
				$( window ).resize(function() {
					waitForFinalEvent(function () {
		               setTimeout(function() {
							table.columns.adjust().draw();
						}, 0);
		            }, 500, "resizeWindow");
				});
				
				tabs = $( "#tabs" ).tabs({
					activate: function( event, ui ) {
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

				$('#grid-data').on('click', 'input[type="checkbox"]', function() {
					$(this).blur();
				});
				
				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Master/Item/index.php");
					Lobibox.alert("error",
					{
						msg: "Terjadi kesalahan. Memuat ulang halaman.",
						width: 480,
						delay: 2000,
						beforeClose: function() {
							if(counterError == 0) {
								//location.reload();
								counterError = 1;
							}
						}
					});
				};
				
				keyFunction();
				enterLikeTab();
				$("#ddlCategory").combobox();
				var counterItem = 0;
				var UserTypeID = $("#hdnUserTypeID").val();
				var visible = false;
				if(UserTypeID == "1") visible = true;
				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollX":  true,
								"scrollY": "330px",
								"rowId": "ItemID",
								"scrollCollapse": true,
								"order": [],
								"columns": [
									{ "width": "12px", "orderable": false, className: "dt-head-center dt-body-center" },
									{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center dt-body-right", "visible": false },
									{ className: "dt-head-center dt-body-right", "visible": false },
									{ className: "dt-head-center dt-body-right", "visible": false }
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Master/Item/DataSource.php",
								"language": {
									"info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
									"infoFiltered": "",
									"infoEmpty": "",
									"zeroRecords": "Data tidak ditemukan",
									"lengthMenu": "&nbsp;&nbsp;_MENU_ data",
									"search": "Cari",
									"processing": "Memproses",
									"paginate": {
										"next": ">",
										"previous": "<",
										"last": "»",
										"first": "«"
									}
								},
								"drawCallback": function( settings ) {
							       setTimeout(function() {
										adjustColumns();
									}, 0);
							    }
							});
				
				table.on( 'key', function (e, datatable, key, cell, originalEvent) {
					var index = table.cell({ focused: true }).index();
					if(key == 32) { //space
						var checkbox = $(".focus").find("input:checkbox");
						if(checkbox.prop("checked") == true) {
							checkbox.prop("checked", false);
							checkbox.attr("checked", false);
						}
						else {
							checkbox.prop("checked", true);
							checkbox.attr("checked", true);
						}
					}
					else if(counterItem == 0) {
						counterItem = 1;
						var data = datatable.row( cell.index().row ).data();
						if(key == 13) { //enter
							if(($(".ui-dialog").css("display") == "none" || $("#delete-confirm").css("display") == "none") && $("#hdnEditFlag").val() == "1") {
								openDialog(data, 1);
							}
						}
						else if(key == 46 && $("#hdnDeleteFlag").val() == "1") { //delete
							var DeleteID = new Array();
							$("input:checkbox[name=select]:checked").each(function() {
								if($(this).val() != 'all') DeleteID.push($(this).val());
							});
							if(DeleteID.length == 0) {
								table.keys.disable();
								var deletedData = new Array();
								deletedData.push(data[14] + "^" + data[3]);
								SingleDelete("./Master/Item/Delete.php", deletedData, function(action) {
									if(action == "success") {
										table.ajax.reload(function() {
											table.keys.enable();
											if(typeof index !== 'undefined') {
												try {
													table.cell(index).focus();
												}
												catch (err) {
													$("#grid-data").DataTable().cell( ':eq(0)' ).focus();
												}
												if(table.page.info().page == table.page.info().pages) {
													setTimeout(function() {
														table.page("previous").draw('page');
													}, 0);
												}
											}
											table.columns.adjust();
										}, false);
									}
									else {
										table.keys.enable();
										return false;
									}
								});
							}
							else {
								fnDeleteData();
							}
						}
						setTimeout(function() { counterItem = 0; } , 1000);
					}
				});
				
				table.on('page', function() {
					$("#select_all").prop("checked", false);
				});
				
				var counterKeyItem = 0;
				$(document).on("keydown", function (evt) {
					if(counterKeyItem == 0) {
						counterKeyItem = 1;
						var index = table.cell({ focused: true }).index();
						if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0 && $("#FormData").css("display") == "none" && $("#delete-confirm").css("display") == "none") {
							$("#grid-data_wrapper").find("input[type='search']").focus();
						}
						else if (evt.keyCode == 46 && $("#hdnDeleteFlag").val() == "1" && typeof index == 'undefined' && $("#FormData").css("display") == "none") { //delete button
							evt.preventDefault();
							fnDeleteData();
						}
					}
					setTimeout(function() { counterKeyItem = 0; } , 1000);
				});
				
				$('#grid-data tbody').on('dblclick', 'tr', function () {
					var data = table.row(this).data();
					openDialog(data, 1);
				});
			});
		</script>
	</body>
</html>
