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
								if($DeleteFlag == true) echo '<button id="btnDelete" class="btn btn-danger" onclick="DeleteData(\'./Master/Item/Delete.php\');" ><i class="fa fa-close"></i> Hapus</button>';
								echo '<input id="hdnEditFlag" name="hdnEditFlag" type="hidden" value="'.$EditFlag.'" />';
								echo '<input id="hdnDeleteFlag" name="hdnDeleteFlag" type="hidden" value="'.$DeleteFlag.'" />';
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
										<th>Harga Beli</th>
										<th>Harga Ecer</th>
										<th>Harga 1</th>
										<th>QTY 1</th>
										<th>Harga 2</th>
										<th>QTY 2</th>
										<th>Berat</th>
										<th>Stok Min</th>
									</tr>
								</thead>
							</table>
						</div>
						<br />
					</div>
				</div>
			</div>
		</div>
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
						<input id="txtBuyPrice" name="txtBuyPrice" type="text" tabindex=8 class="form-control-custom text-right" value="0.00" autocomplete=off placeholder="Harga Beli" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Harga Ecer :
					</div>
					<div class="col-md-4">
						<input id="txtRetailPrice" name="txtRetailPrice" type="text" tabindex=9 class="form-control-custom text-right" value="0.00" autocomplete=off placeholder="Harga Ecer" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 labelColumn">
						Harga Grosir 1 :
					</div>
					<div class="col-md-4">
						<input id="txtPrice1" name="txtPrice1" type="text" tabindex=10 class="form-control-custom text-right" value="0.00" autocomplete=off placeholder="Harga Grosir 1" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
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
						<input id="txtPrice2" name="txtPrice2" type="text" tabindex=12 class="form-control-custom text-right" value="0.00" autocomplete=off placeholder="Harga Grosir 2" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
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
						<input id="txtWeight" name="txtWeight" type="text" tabindex=14 class="form-control-custom text-right" value="0.00" autocomplete=off placeholder="Berat (KG)" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" required />
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
		<script>
			var table;
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#FormData").attr("title", "Edit Barang");
					$("#hdnItemID").val(Data[13]);
					$("#txtItemCode").val(Data[2]);
					$("#txtItemName").val(Data[3]);
					$("#ddlCategory").val(Data[14]);
					$("#ddlCategory").next().find("input").val($("#ddlCategory option:selected").text());
					$("#txtBuyPrice").val(returnRupiah(Data[5].toString()));
					$("#txtRetailPrice").val(returnRupiah(Data[6].toString()));
					$("#txtPrice1").val(returnRupiah(Data[7].toString()));
					$("#txtQty1").val(Data[8]);
					$("#txtPrice2").val(returnRupiah(Data[9].toString()));
					$("#txtQty2").val(Data[10]);
					$("#txtWeight").val(returnRupiah(Data[11].toString()));
					$("#txtMinimumStock").val(Data[12]);
				}
				else $("#FormData").attr("title", "Tambah Barang");
				var index = table.cell({ focused: true }).index();
				//console.log(index);
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
						$(this).dialog("destroy");
						$("#divModal").hide();
						table.keys.enable();
						if(typeof index !== 'undefined') table.cell(index).focus();
						$("#hdnItemID").val(0);
						$("#txtItemCode").val("");
						$("#txtItemName").val("");
						$("#ddlCategory").val("");
						$("#ddlCategory").next().find("input").val("");
						$("#txtBuyPrice").val("0.00");
						$("#txtRetailPrice").val("0.00");
						$("#txtPrice1").val("0.00");
						$("#txtQty1").val(0);
						$("#txtPrice2").val("0.00");
						$("#txtQty2").val(0);
						$("#txtWeight").val("0.00");
						$("#txtMinimumStock").val(0);
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
												$("#hdnItemID").val(0);
												$("#txtItemCode").val("");
												$("#txtItemName").val("");
												$("#ddlCategory").val("");
												$("#ddlCategory").next().find("input").val("");
												$("#txtBuyPrice").val("0.00");
												$("#txtRetailPrice").val("0.00");
												$("#txtPrice1").val("0.00");
												$("#txtQty1").val(0);
												$("#txtPrice2").val("0.00");
												$("#txtQty2").val(0);
												$("#txtWeight").val("0.00");
												$("#txtMinimumStock").val(0);
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
							$(this).dialog("destroy");
							$("#divModal").hide();
							table.keys.enable();
							if(typeof index !== 'undefined') table.cell(index).focus();
							$("#hdnItemID").val(0);
							$("#txtItemCode").val("");
							$("#txtItemName").val("");
							$("#ddlCategory").val("");
							$("#ddlCategory").next().find("input").val("");
							$("#txtBuyPrice").val("0.00");
							$("#txtRetailPrice").val("0.00");
							$("#txtPrice1").val("0.00");
							$("#txtQty1").val(0);
							$("#txtPrice2").val("0.00");
							$("#txtQty2").val(0);
							$("#txtWeight").val("0.00");
							$("#txtMinimumStock").val(0);
							return false;
						}
					}]
				}).dialog("open");
			}

			$(document).ready(function() {
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
								location.reload();
								counterError = 1;
							}
						}
					});
				};
				
				keyFunction();
				enterLikeTab();
				$("#ddlCategory").combobox();
				var counterItem = 0;
				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "330px",
								"rowId": "ItemID",
								"scrollCollapse": true,
								"order": [2, "asc"],
								"columns": [
									{ "width": "12px", "orderable": false, className: "text-center" },
									{ "width": "25px", "orderable": false },
									null,
									null,
									null,
									{ "orderable": false, className: "text-right" },
									{ "orderable": false, className: "text-right" },
									{ "orderable": false, className: "text-right" },
									{ "orderable": false, className: "text-right" },
									{ "orderable": false, className: "text-right" },
									{ "orderable": false, className: "text-right" },
									{ "orderable": false, className: "text-right" },
									{ "orderable": false, className: "text-right" }							
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
								}
							});
				
				table.on( 'key', function (e, datatable, key, cell, originalEvent) {
					var index = table.cell({ focused: true }).index();
					console.log(counterItem);
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
								deletedData.push(data[13] + "^" + data[3]);
								SingleDelete("./Master/Item/Delete.php", deletedData, function(action) {
									if(action == "Ya") {
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
										}, false);
									}
									else {
										table.keys.enable();
										return false;
									}
								});
							}
							else {
								table.keys.disable();
								DeleteData("./Master/Item/Delete.php", function(action) {
									if(action == "Ya") {
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
										}, false);
									}
									else {
										table.keys.enable();
										return false;
									}
								});
							}
						}
						setTimeout(function() { counterItem = 0; } , 1000);
					}
				});
				
				table.on('page', function() {
					$("#select_all").prop("checked", false);
				});
				
				$(document).on("keydown", function (evt) {		
					var index = table.cell({ focused: true }).index();
					if (evt.keyCode == 46 && $("#hdnDeleteFlag").val() == "1" && typeof index == 'undefined') { //delete button
						evt.preventDefault();
						table.keys.disable();
						DeleteData("./Master/Item/Delete.php", function(action) {
							if(action == "Ya") {
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
								}, false);
							}
							else {
								table.keys.enable();
								return false;
							}
						});
					}
				});
			});
		</script>
	</body>
</html>