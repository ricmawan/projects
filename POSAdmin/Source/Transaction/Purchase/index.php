<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			#divTableContent {
				min-height: 360px;
				max-height: 360px;
				overflow-y: auto;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <span style="width:50%;display:inline-block;">
							 <h5>Pembelian</h5>
						</span>
						<span style="width:49%;display:inline-block;text-align:right;">
							<button id="btnAdd" class="btn btn-primary" onclick="openDialog(0, 0);"><i class="fa fa-plus "></i> Tambah</button>&nbsp;
							<?php
								if($DeleteFlag == true) echo '<button id="btnDelete" class="btn btn-danger" onclick="fnDeleteData();" ><i class="fa fa-close"></i> Hapus</button>';
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
										<th>No. Invoice</th>
										<th>Tanggal</th>
										<th>Supplier</th>
										<th>Total</th>
									</tr>
								</thead>
							</table>
						</div>
						<br />
					</div>
				</div>
			</div>
		</div>
		<div id="FormData" title="Tambah Kategori" style="display: none;">
			<form class="col-md-12" id="PostForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-1 labelColumn">
						No. Invoice :
						<input id="hdnPurchaseID" name="hdnPurchaseID" type="hidden" value=0 />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
					</div>
					<div class="col-md-2">
						<input id="txtPurchaseNumber" name="txtPurchaseNumber" type="text" tabindex=5 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="No. Invoice" required />
					</div>
					<div class="col-md-1 labelColumn">
						Supplier :
					</div>
					<div class="col-md-2">
						<select id="ddlSupplier" name="ddlSupplier" tabindex=6 class="form-control-custom" placeholder="Pilih Supplier" >
							<?php
								$sql = "CALL spSelDDLSupplier('".$_SESSION['UserLogin']."')";
								if (! $result = mysqli_query($dbh, $sql)) {
									logEvent(mysqli_error($dbh), '/Master/Purchase/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
									return 0;
								}
								while($row = mysqli_fetch_array($result)) {
									echo "<option value='".$row['SupplierID']."' >".$row['SupplierCode']." - ".$row['SupplierName']."</option>";
								}
								mysqli_free_result($result);
								mysqli_next_result($dbh);
							?>
						</select>
					</div>
					<div class="col-md-1 labelColumn">
						Tanggal :
					</div>
					<div class="col-md-2">
						<input id="txtCategoryName" name="txtCategoryName" type="text" tabindex=7 class="form-control-custom"onfocus="this.select();" autocomplete=off placeholder="Tanggal" required />
					</div>
				</div>
				<hr style="margin: 10px 0;" />
				<div class="row">
					<table class="table table-striped table-hover" style="margin-bottom: 5px;" >
						<thead>
							<tr>
								<th style="width: 15%;" >Kode Barang</th>
								<th style="width: 20%;" >Nama Barang</th>
								<th style="width: 7%;" >Qty</th>
								<th style="width: 14.5%;">Harga Beli</th>
								<th style="width: 14.5%;">Harga Ecer</th>
								<th style="width: 14.5%;">Harga Grosir 1</th>
								<th style="width: 14.5%;">Harga Grosir 2</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="width: 15%;" ><input id="txtItemCode" name="txtItemCode" type="text" tabindex=8 class="form-control-custom" style="width: 100%;" onfocus="this.select();" onchange="getItemDetails(this.value);" autocomplete=off placeholder="Kode Barang" /></td>
								<td style="width: 20%;" ><input id="txtItemName" name="txtItemName" type="text" class="form-control-custom" style="width: 100%;" disabled /></td>
								<td style="width: 7%;" ><input id="txtQTY" name="txtQTY" type="number" tabindex=9 class="form-control-custom" style="width: 100%;" value=1 /></td>
								<td style="width: 14.5%;" ><input id="txtBuyPrice" name="txtBuyPrice" type="text" tabindex=10 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Beli" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" /></td>
								<td style="width: 14.5%;" ><input id="txtRetailPrice" name="txtRetailPrice" type="text" tabindex=11 class="form-control-custom text-right" style="width: 100%;" value="0" autocomplete=off placeholder="Harga Ecer" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" /></td>
								<td style="width: 14.5%;" ><input id="txtPrice1" name="txtPrice1" type="text" tabindex=12 class="form-control-custom text-right" style="width: 100%;" value="0" autocomplete=off placeholder="Harga Grosir 1" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" /></td>
								<td style="width: 14.5%;" ><input id="txtPrice2" name="txtPrice2" type="text" tabindex=13 class="form-control-custom text-right" style="width: 100%;" value="0" autocomplete=off placeholder="Harga Grosir 2" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" /></td>
							</tr>
						</tbody>
					</table>
				</div>
				<hr style="margin: 10px 0;" />
				<div class="row" >
					<div id="divTableContent" class="table-responsive" style="overflow-x:hidden;">
						<table id="grid-transaction" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th>Kode Barang</th>
									<th>Nama Barang</th>
									<th>Qty</th>
									<th>Harga Beli</th>
									<th>Harga Ecer</th>
									<th>Harga Grosir 1</th>
									<th>Harga Grosir 2</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Test</td>
									<td>Test</td>
									<td>Test</td>
									<td>Test</td>
									<td>Test</td>
									<td>Test</td>
									<td>Test</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row" >
					<h2 style="display: inline-block;float: left;" >TOTAL : &nbsp;</h2><span style="width: 20%;display:inline-block;float:left;height:5%;min-height:45px;margin:0 0 0 auto;text-align:right;background-color:black;color:white;font-size:32px;font-weight:bold;">5.500.000</span>
				</div>
			</form>
		</div>
		<script>
			var table;
			var table2;
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#FormData").attr("title", "Edit Kategori");
					$("#hdnCategoryID").val(Data[4]);
					$("#txtCategoryCode").val(Data[2]);
					$("#txtCategoryName").val(Data[3]);
				}
				else $("#FormData").attr("title", "Tambah Kategori");
				var index = table.cell({ focused: true }).index();
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
						table.keys.disable();
						//console.log(table2.settings().oScroll);
						table2 = $("#grid-transaction").DataTable({
								"keys": true,
								"scrollY": "310px",
								"scrollX": false,
								"scrollCollapse": true,
								"paging": false,
								"searching": false,
								"order": [],
								"columns": [
									{ "width": "15%", "orderable": false },
									{ "width": "20%", "orderable": false },
									{ "width": "7%", "orderable": false },
									{ "width": "14.5%", "orderable": false },
									{ "width": "14.5%", "orderable": false },
									{ "width": "14.5%", "orderable": false },
									{ "width": "14.5%", "orderable": false }
								],
								"processing": false,
								"serverSide": false,
								"language": {
									"info": "",
									"infoFiltered": "",
									"infoEmpty": "",
									"zeroRecords": "Data tidak ditemukan",
									"lengthMenu": "&nbsp;&nbsp;_MENU_ data",
									"search": "Cari",
									"processing": "",
									"paginate": {
										"next": ">",
										"previous": "<",
										"last": "»",
										"first": "«"
									}
								}
							});
						table2.columns.adjust();
						var tableWidth = $("#divTableContent").find("table").width();
						var barWidth = table2.settings()[0].oScroll.iBarWidth;
						var newWidth = tableWidth - barWidth + 2;
						$("#divTableContent").find("table").css({
							"width": newWidth + "px"
						});
						$("#divModal").show();
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //right arrow
								$("#btnCancelAddPurchase").focus();
							}
							else if (e.keyCode == 37 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //left arrow
								$("#btnSavePurchase").focus();
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
						resetForm();
						table2.destroy();
					},
					resizable: false,
					height: 650,
					width: 1280,
					modal: false,
					buttons: [
					{
						text: "Simpan",
						id: "btnSavePurchase",
						tabindex: 14,
						click: function() {
							saveConfirm(function(action) {
								if(action == "Ya") {
									$.ajax({
										url: "./Transaction/Purchase/Insert.php",
										type: "POST",
										data: $("#PostForm").serialize(),
										dataType: "json",
										success: function(data) {
											if(data.FailedFlag == '0') {
												$("#loading").hide();
												$("#FormData").dialog("destroy");
												$("#divModal").hide();
												resetForm();
												table2.destroy();
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
															$("#txtPurchaseNumber").focus();
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
											LogEvent(errorMessage, "/Transaction/Purchase/index.php");
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
									$("#txtPurchaseNumber").focus();
									return false;
								}
							});
						}
					},
					{
						text: "Batal",
						id: "btnCancelAddPurchase",
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
							table.keys.enable();
							if(typeof index !== 'undefined') table.cell(index).focus();
							resetForm();
							table2.destroy();
							return false;
						}
					}]
				}).dialog("open");
			}
			
			function getItemDetails(itemCode) {
				$.ajax({
					url: "./Transaction/Purchase/CheckItem.php",
					type: "POST",
					data: { itemCode : itemCode },
					dataType: "json",
					success: function(data) {
						if(data.FailedFlag == '0') {
							alert("found");
						}
						else {
							//add new item
							alert("not found");
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$("#loading").hide();
						var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
						LogEvent(errorMessage, "/Transaction/Purchase/index.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
			}
			
			function resetForm() {
				$("#hdnCategoryID").val(0);
				$("#txtCategoryCode").val("");
				$("#txtCategoryName").val("");
			}
			
			function fnDeleteData() {
				var index = table.cell({ focused: true }).index();
				table.keys.disable();
				DeleteData("./Master/Category/Delete.php", function(action) {
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
							if(table.page.info().page == table.page.info().pages) {
								setTimeout(function() {
									table.page("previous").draw('page');
								}, 0);
							}
						}, false);
					}
					else {
						table.keys.enable();
						return false;
					}
				});
			}

			$(document).ready(function() {
				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Transaction/Purchase/index.php");
					Lobibox.alert("error",
					{
						msg: "Terjadi kesalahan. Memuat ulang halaman.",
						width: 480,
						//delay: 2000,
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
				var counterPurchase = 0;
				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "330px",
								"rowId": "PurchaseID",
								"scrollCollapse": true,
								"order": [2, "asc"],
								"columns": [
									{ "width": "20px", "orderable": false, className: "text-center" },
									{ "width": "25px", "orderable": false },
									null,
									null,
									null,
									{ "orderable": false }
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Transaction/Purchase/DataSource.php",
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
					else if(counterPurchase == 0) {
						counterPurchase = 1;
						var data = datatable.row( cell.index().row ).data();
						if(key == 13) {
							if(($(".ui-dialog").css("display") == "none" || $("#delete-confirm").css("display") == "none") && $("#hdnEditFlag").val() == "1" ) {
								openDialog(data, 1);
							}
						}
						else if(key == 46 && $("#hdnDeleteFlag").val() == "1") {
							var DeleteID = new Array();
							$("input:checkbox[name=select]:checked").each(function() {
								if($(this).val() != 'all') DeleteID.push($(this).val());
							});
							if(DeleteID.length == 0) {
								table.keys.disable();
								var deletedData = new Array();
								deletedData.push(data[4] + "^" + data[3]);
								SingleDelete("./Master/Purchase/Delete.php", deletedData, function(action) {
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
											if(table.page.info().page == table.page.info().pages) {
												setTimeout(function() {
													table.page("previous").draw('page');
												}, 0);
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
								fnDeleteData();
							}
						}
						setTimeout(function() { counterPurchase = 0; } , 1000);
					}
				});
				
				table.on('page', function() {
					$("#select_all").prop("checked", false);
				});
				
				$(document).on("keydown", function (evt) {
					var index = table.cell({ focused: true }).index();
					if (evt.keyCode == 46 && $("#hdnDeleteFlag").val() == "1" && typeof index == 'undefined') { //delete button
						evt.preventDefault();
						fnDeleteData();
					}
				});
				
				$('#grid-data tbody').on('dblclick', 'tr', function () {
					var data = table.row(this).data();
					openDialog(data, 1);
				});
			});
		</script>
	</body>
</html>
