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
							 <h5>Retur Pembelian</h5>
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
						Tanggal :
						<input id="hdnPurchaseReturnID" name="hdnPurchaseReturnID" type="hidden" value=0 />
						<input id="hdnPurchaseReturnDetailsID" name="hdnPurchaseReturnDetailsID" type="hidden" value=0 />
						<input id="hdnItemID" name="hdnItemID" type="hidden" value=0 />
						<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
					</div>
					<div class="col-md-2">
						<input id="txtTransactionDate" name="txtTransactionDate" type="text" tabindex=6 class="form-control-custom" style="width: 87%; display: inline-block;margin-right: 5px;" onfocus="this.select();" autocomplete=off placeholder="Tanggal" required />
					</div>
					
					<div class="col-md-1 labelColumn">
						Supplier :
					</div>
					<div class="col-md-2">
						<select id="ddlSupplier" name="ddlSupplier" tabindex=7 class="form-control-custom" placeholder="Pilih Supplier" >
							<?php
								$sql = "CALL spSelDDLSupplier('".$_SESSION['UserLogin']."')";
								if (! $result = mysqli_query($dbh, $sql)) {
									logEvent(mysqli_error($dbh), '/Master/PurchaseReturn/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
				</div>
				<hr style="margin: 10px 0;" />
				<div class="row">
					<table class="table table-striped table-hover" style="margin-bottom: 5px;" >
						<thead>
							<tr>
								<th style="width: 20%;text-align: center;" >Cabang</th>
								<th style="width: 20%;text-align: center;" >Kode Barang</th>
								<th style="width: 30%;text-align: center;" >Nama Barang</th>
								<th style="width: 10%;text-align: center;" >Qty</th>
								<th style="width: 20%;text-align: center;">Harga Beli</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="width: 20%;" >
									<select id="ddlBranch" name="ddlBranch" tabindex=8 class="form-control-custom" placeholder="Pilih Cabang" >
										<?php
											$sql = "CALL spSelDDLBranch('".$_SESSION['UserLogin']."')";
											if (! $result = mysqli_query($dbh, $sql)) {
												logEvent(mysqli_error($dbh), '/Master/PurchaseReturn/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
												return 0;
											}
											while($row = mysqli_fetch_array($result)) {
												echo "<option value='".$row['BranchID']."' >".$row['BranchCode']." - ".$row['BranchName']."</option>";
											}
											mysqli_free_result($result);
											mysqli_next_result($dbh);
										?>
									</select>
								</td>
								<td style="width: 20%;" ><input id="txtItemCode" name="txtItemCode" type="text" tabindex=9 class="form-control-custom" style="width: 100%;" onfocus="this.select();" onkeypress="isEnterKey(event, 'getItemDetails');" onchange="getItemDetails();" autocomplete=off placeholder="Kode Barang" /></td>
								<td style="width: 30%;" ><input id="txtItemName" name="txtItemName" type="text" class="form-control-custom" style="width: 100%;" disabled /></td>
								<td style="width: 10%;" ><input id="txtQTY" name="txtQTY" type="number" tabindex=10 class="form-control-custom" style="width: 100%;" value=1 onchange="this.value = validateQTY(this.value);" onpaste="return false;" onfocus="this.select();" /></td>
								<td style="width: 20%;" ><input id="txtBuyPrice" name="txtBuyPrice" type="text" tabindex=11 class="form-control-custom text-right" style="width: 100%;" value="0" autocomplete=off placeholder="Harga Beli" onkeypress="isEnterKey(event, 'addPurchaseReturnDetails');return isNumberKey(event, this.id, this.value);" onchange="addPurchaseDetails();" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" /></td>
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
									<th>PurchaseReturnDetailsID</th>
									<th>ItemID</th>
									<th>BranchID</th>
									<th>Cabang</th>
									<th>Kode Barang</th>
									<th>Nama Barang</th>
									<th>Qty</th>
									<th>Harga Beli</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="row" >
					<h2 style="display: inline-block;float: left;" >TOTAL : &nbsp;</h2><span id="lblTotal" >0</span>
				</div>
			</form>
		</div>
		<div id="add-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Kode tidak valid, apakah ingin menambahkan barang?</p>
		</div>
		<div id="itemList-dialog" title="Daftar Barang" style="display: none;">
			<div class="row col-md-12" >
				<div id="divTableItem" class="table-responsive" style="overflow-x:hidden;">
					<table id="grid-item" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
						<thead>
							<tr>
								<th>Kode Barang</th>
								<th>Nama Barang</th>
								<th>Harga Beli</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
		
		<script>
			var table;
			var table2;
			var table3;
			var today;
			var rowEdit;
			
			function openDialogEdit(Data) {
				$("#hdnPurchaseReturnDetailsID").val(Data[0]);
				$("#hdnItemID").val(Data[1]);
				$("#ddlBranch").val(Data[2]);
				$("#txtItemCode").val(Data[4]);
				$("#txtItemName").val(Data[5]);
				$("#txtQTY").val(Data[6]);
				$("#txtBuyPrice").val(Data[7]);
				setTimeout(function() { $("#txtItemCode").focus(); }, 0);
			}
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#FormData").attr("title", "Edit Retur Pembelian");
					$("#hdnPurchaseReturnID").val(Data[5]);
					$("#ddlSupplier").val(Data[6]);
					$("#lblTotal").html(Data[4]);
					$("#txtTransactionDate").datepicker("setDate", new Date(Data[7]));
					getPurchaseReturnDetails(Data[5]);
				}
				else $("#FormData").attr("title", "Tambah Retur Pembelian");
				var index = table.cell({ focused: true }).index();
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
						table.keys.disable();
						table2 = $("#grid-transaction").DataTable({
									"keys": true,
									"scrollY": "295px",
									"scrollX": false,
									"scrollCollapse": false,
									"paging": false,
									"searching": false,
									"order": [],
									"columns": [
										{ "visible": false },
										{ "visible": false },
										{ "visible": false },
										{ "width": "20%", "orderable": false, className: "dt-head-center" },
										{ "width": "20%", "orderable": false, className: "dt-head-center" },
										{ "width": "30%", "orderable": false, className: "dt-head-center" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "20%", "orderable": false, className: "dt-head-center dt-body-right" }
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
						var counterPurchaseReturnDetails = 0;
						table2.on( 'key', function (e, datatable, key, cell, originalEvent) {
							var index = table2.cell({ focused: true }).index();
							if(counterPurchaseReturnDetails == 0) {
								counterPurchaseReturnDetails = 1;
								var data = datatable.row( cell.index().row ).data();
								if(key == 13) {
									if(($("#FormEdit").css("display") == "none" || $("#delete-confirm").css("display") == "none") && $("#hdnEditFlag").val() == "1" ) {
										table2.cell.blur();
										table2.keys.disable();
										rowEdit = datatable.row( cell.index().row );
										openDialogEdit(data);
									}
								}
								else if(key == 46 && $("#hdnDeleteFlag").val() == "1") {
									table2.keys.disable();
									var deletedData = new Array();
									deletedData.push(data[0]);
									SingleDelete("./Transaction/PurchaseReturn/DeleteDetails.php", deletedData, function(action) {
										if(action == "success") {
											datatable.row( cell.index().row ).remove().draw();
											table2.keys.enable();
											if(typeof index !== 'undefined') {
												try {
													table2.cell(index).focus();
												}
												catch (err) {
													$("#grid-transaction").DataTable().cell( ':eq(0)' ).focus();
												}
											}
											tableWidthAdjust();
											Calculate();
										}
										else {
											table2.keys.enable();
											return false;
										}
									});
								}
								setTimeout(function() { counterPurchaseReturnDetails = 0; } , 1000);
							}
						});
						
						$('#grid-transaction tbody').on('dblclick', 'tr', function () {
							var data = table2.row(this).data();
							table2.cell.blur();
							table2.keys.disable();
							rowEdit = table2.row(this);
							openDialogEdit(data);
						});
						
						tableWidthAdjust();
						$("#divModal").show();
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //right arrow
								//$("#btnCancelAddPurchaseReturn").focus();
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
						table.ajax.reload(function() {
							table.keys.enable();
							if(typeof index !== 'undefined') table.cell(index).focus();
						}, false);
						resetForm();
						table2.destroy();
					},
					resizable: false,
					height: 650,
					width: 1280,
					modal: false,
					buttons: [
					{
						text: "Tutup",
						tabindex: 15,
						id: "btnCancelAddPurchaseReturn",
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
							table.ajax.reload(function() {
								table.keys.enable();
								if(typeof index !== 'undefined') table.cell(index).focus();
							}, false);
							resetForm();
							table2.destroy();
							return false;
						}
					}]
				}).dialog("open");
			}
			
			var counterGetItem = 0;
			function getItemDetails() {
				if(counterGetItem == 0) {
					counterGetItem = 1;
					var itemCode = $("#txtItemCode").val();
					if(itemCode == "") $("#txtItemCode").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					else {
						$.ajax({
							url: "./Transaction/PurchaseReturn/CheckItem.php",
							type: "POST",
							data: { itemCode : itemCode },
							dataType: "json",
							success: function(data) {
								if(data.FailedFlag == '0') {
									if($("#hdnItemID").val() != data.ItemID) {
										$("#hdnItemID").val(data.ItemID);
										$("#txtItemName").val(data.ItemName);
										$("#txtBuyPrice").val(returnRupiah(data.BuyPrice));
										$("#txtQTY").focus();
									}
									else $("#txtQTY").focus();
								}
								else {
									//item code doesn't valid
									if(data.ErrorMessage == "") {
										$("#txtItemCode").notify("Kode barang tidak valid!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
									}
									else {
										var counter = 0;
										Lobibox.alert("error",
										{
											msg: data.ErrorMessage,
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
								}
							},
							error: function(jqXHR, textStatus, errorThrown) {
								$("#loading").hide();
								var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
								LogEvent(errorMessage, "/Transaction/PurchaseReturn/index.php");
								Lobibox.alert("error",
								{
									msg: errorMessage,
									width: 480
								});
								return 0;
							}
						});
					}
				}
				setTimeout(function() { counterGetItem = 0; }, 1000);
			}
			
			function getPurchaseReturnDetails(PurchaseReturnID) {
				$.ajax({
					url: "./Transaction/PurchaseReturn/PurchaseReturnDetails.php",
					type: "POST",
					data: { PurchaseReturnID : PurchaseReturnID },
					dataType: "json",
					success: function(Data) {
						if(Data.FailedFlag == '0') {
							for(var i=0;i<Data.data.length;i++) {
								table2.row.add(Data.data[i]);
							}
							table2.draw();
							tableWidthAdjust();
						}
						else {
							var counter = 0;
							Lobibox.alert("error",
							{
								msg: "Gagal memuat data",
								width: 480,
								beforeClose: function() {
									if(counter == 0) {
										setTimeout(function() {
											//$("#txtItemCode").focus();
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
						var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
						LogEvent(errorMessage, "/Transaction/PurchaseReturn/index.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
			}
			
			var counterAddPurchaseReturn = 0;
			function addPurchaseReturnDetails() {
				if(counterAddPurchaseReturn == 0) {
					counterAddPurchaseReturn = 1;
					var branchID = $("#ddlBranch").val();
					var branchName = $("#ddlBranch option:selected").text();
					var itemID = $("#hdnItemID").val();
					var itemCode = $("#txtItemCode").val();
					var itemName = $("#txtItemName").val();
					var Qty = $("#txtQTY").val();
					var buyPrice = returnRupiah($("#txtBuyPrice").val());
					$("#txtBuyPrice").blur();
					var PassValidate = 1;
					var FirstFocus = 0;
					$("#FormData .form-control-custom").each(function() {
						if($(this).hasAttr('required')) {
							if($(this).val() == "") {
								PassValidate = 0;
								$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $(this).focus();
								FirstFocus = 1;
							}
						}
					});
					
					if($("#hdnItemID").val() == 0) {
						PassValidate = 0;
						$("#txtItemCode").notify("Kode barang tidak valid!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						$("#txtItemCode").focus();
					}
					
					if(PassValidate == 1) {
						$.ajax({
							url: "./Transaction/PurchaseReturn/Insert.php",
							type: "POST",
							data: $("#PostForm").serialize(),
							dataType: "json",
							success: function(data) {
								if(data.FailedFlag == '0') {
									if($("#hdnPurchaseReturnDetailsID").val() == 0) {
										table2.row.add([
											data.PurchaseReturnDetailsID,
											itemID,
											branchID,
											branchName,
											itemCode,
											itemName,
											Qty,
											buyPrice
										]).draw();
									}
									else {
										table2.row(rowEdit).data([
											data.PurchaseReturnDetailsID,
											itemID,
											branchID,
											branchName,
											itemCode,
											itemName,
											Qty,
											buyPrice
										]).draw();
										
										table2.keys.enable();
									}
									$("#txtItemCode").val("");
									$("#txtItemName").val("");
									$("#txtQTY").val(1);
									$("#txtBuyPrice").val(0);
									$("#txtItemCode").focus();
									$("#hdnPurchaseReturnID").val(data.ID);
									$("#hdnPurchaseReturnDetailsID").val(0);
									$("#hdnItemID").val(0);
									tableWidthAdjust();
									Calculate();
								}
								else {
									var counter = 0;
									Lobibox.alert("error",
									{
										msg: data.Message,
										width: 480,
										beforeClose: function() {
											if(counter == 0) {
												setTimeout(function() {
													if(data.Message == "No. Invoice sudah ada") $("#txtPurchaseReturnNumber").focus();
													else $("#txtItemCode").focus();
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
								var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
								LogEvent(errorMessage, "/Transaction/PurchaseReturn/index.php");
								Lobibox.alert("error",
								{
									msg: errorMessage,
									width: 480
								});
								return 0;
							}
						});
					}
				}
				setTimeout(function() { counterAddPurchaseReturn = 0; }, 1000);
			}
			
			function Calculate() {
				var grandTotal = 0;
				table2.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
					var data = this.data();
					grandTotal += parseFloat(data[7].replace(/\,/g, "")) * parseFloat(data[6]);
				});
				$("#lblTotal").html(returnRupiah(grandTotal.toString()));
			}
			
			function tableWidthAdjust() {
				var tableWidth = $("#divTableContent").find("table").width();
				var barWidth = table2.settings()[0].oScroll.iBarWidth;
				var newWidth = tableWidth - barWidth + 2;
				$("#divTableContent").find("table").css({
					"width": newWidth + "px"
				});
			}
			
			function resetForm() {
				$("#hdnPurchaseReturnID").val(0);
				$("#hdnItemID").val(0);
				$("#txtTransactionDate").datepicker("setDate", new Date());
				$("#txtPurchaseReturnNumber").val("");
				$("#txtItemCode").val("");
				$("#txtItemName").val("");
				$("#txtQTY").val(1);
				$("#txtBuyPrice").val(0);
				table2.clear().draw();
			}
			
			function fnDeleteData() {
				var index = table.cell({ focused: true }).index();
				table.keys.disable();
				DeleteData("./Transaction/PurchaseReturn/Delete.php", function(action) {
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
						}, false);
					}
					else {
						table.keys.enable();
						return false;
					}
				});
			}
			
			function itemList() {
				$("#itemList-dialog").dialog({
					autoOpen: false,
					open: function() {
						table.keys.disable();
						table2.keys.disable();
						table3 = $("#grid-item").DataTable({
									"keys": true,
									"scrollY": "295px",
									"scrollX": false,
									"scrollCollapse": false,
									"paging": false,
									"searching": true,
									"order": [],
									"columns": [
										{ "width": "20%", "orderable": false, className: "dt-head-center" },
										{ "width": "7%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "11%", "orderable": false, className: "dt-head-center dt-body-right" }
									],
									"ajax": "./Transaction/PurchaseReturn/ItemList.php",
									"processing": true,
									"serverSide": true,
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
									},
									"initComplete": function(settings, json) {
										table3.columns.adjust();
										$("#grid-item").DataTable().cell( ':eq(0)' ).focus();
									}
								});
						var counterPickItem = 0;
						table3.on( 'key', function (e, datatable, key, cell, originalEvent) {
							//var index = table3.cell({ focused: true }).index();
							if(counterPickItem == 0) {
								counterPickItem = 1;
								var data = datatable.row( cell.index().row ).data();
								if(key == 13 && $("#itemList-dialog").css("display") == "block") {
									$("#txtItemCode").val(data[0]);
									getItemDetails();
									$("#itemList-dialog").dialog("destroy");
									table3.destroy();
									table.keys.enable();
									table2.keys.enable();
								}
								setTimeout(function() { counterPickItem = 0; } , 1000);
							}
						});
						
						$('#grid-item tbody').on('dblclick', 'tr', function () {
							if( $("#itemList-dialog").css("display") == "block") {
								var data = table3.row(this).data();
								$("#txtItemCode").val(data[0]);
								getItemDetails();
								$("#itemList-dialog").dialog("destroy");
								table3.destroy();
								table.keys.enable();
								table2.keys.enable();
							}
						});
						
						table3.on( 'search.dt', function () {
							setTimeout(function() { $("#grid-item").DataTable().cell( ':eq(0)' ).focus(); }, 100 );
						});
						
						var counterKeyItem = 0;
						$(document).on("keydown", function (evt) {
							if(counterKeyItem == 0) {
								counterKeyItem = 1;
								if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0) {
									$("#itemList-dialog").find("input[type='search']").focus();
								}
							}
							setTimeout(function() { counterKeyItem = 0; } , 1000);
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
						table3.destroy();
						table.keys.enable();
						table2.keys.enable();
					},
					resizable: false,
					height: 500,
					width: 1280,
					modal: true,
					buttons: [
					{
						text: "Tutup",
						tabindex: 15,
						id: "btnCancelPickItem",
						click: function() {
							$(this).dialog("destroy");
							table3.destroy();
							table.keys.enable();
							table2.keys.enable();
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
					LogEvent(errorMessage, "/Transaction/PurchaseReturn/index.php");
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
				
				$("#txtTransactionDate").datepicker({
					dateFormat: 'DD, dd M yy',
					dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu" ],
					monthNames: [ "Jan", "Feb", "Mar", "Apr", "Mey", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des" ],
					maxDate : "+0D",
					showOn: "button",
					buttonImage: "./assets/img/calendar.gif",
					buttonImageOnly: true,
					buttonText: "Pilih Tanggal",
					onSelect: function(dateText, obj) {
						transactionDate = obj.selectedYear + "-" + ("0" + (obj.selectedMonth + 1)).slice(-2) + "-" + ("0" + obj.selectedDay).slice(-2);
						$("#hdnTransactionDate").val(transactionDate);
					}
				}).datepicker("setDate", new Date());
				
				$("#txtTransactionDate").attr("readonly", "true");
				$("#txtTransactionDate").css({
					"background-color": "#FFF",
					"cursor": "text"
				});
				
				var transactionDate = new Date();
				transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
				today = transactionDate;
				$("#hdnTransactionDate").val(transactionDate);
				
				keyFunction();
				enterLikeTab();
				var counterPurchaseReturn = 0;
				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "330px",
								"rowId": "PurchaseReturnID",
								"scrollCollapse": true,
								"order": [2, "asc"],
								"columns": [
									{ "width": "20px", "orderable": false, className: "dt-head-center dt-body-center" },
									{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ "orderable": false, className: "dt-head-center dt-body-right" }
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Transaction/PurchaseReturn/DataSource.php",
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
					else if(counterPurchaseReturn == 0) {
						counterPurchaseReturn = 1;
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
								deletedData.push(data[6] + "^" + data[2]);
								SingleDelete("./Transaction/PurchaseReturn/Delete.php", deletedData, function(action) {
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
						setTimeout(function() { counterPurchaseReturn = 0; } , 1000);
					}
				});
				
				table.on('page', function() {
					$("#select_all").prop("checked", false);
				});
				
				var counterKey = 0;
				$(document).on("keydown", function (evt) {
					var index = table.cell({ focused: true }).index();
					if (evt.keyCode == 46 && $("#hdnDeleteFlag").val() == "1" && typeof index == 'undefined' && $("#FormData").css("display") == "none") { //delete button
						evt.preventDefault();
						if(counterKey == 0) {
							fnDeleteData();
							counterKey = 1;
						}
					}
					else if(evt.keyCode == 123 && $("#itemList-dialog").css("display") == "none" && $("#FormData").css("display") == "block") {
						evt.preventDefault();
						if(counterKey == 0) {
							itemList();
							counterKey = 1;
						}
					}
					else if(evt.keyCode == 123) {
						evt.preventDefault();
					}
					else if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0) {
						$("#grid-data_wrapper").find("input[type='search']").focus();
					}
					setTimeout(function() { counterKey = 0; } , 1000);
				});
				
				$('#grid-data tbody').on('dblclick', 'tr', function () {
					var data = table.row(this).data();
					openDialog(data, 1);
				});
			});
		</script>
	</body>
</html>