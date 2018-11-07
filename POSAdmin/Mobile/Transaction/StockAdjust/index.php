<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			#divTableContent {
				min-height: 330px;
				max-height: 330px;
				overflow-y: auto;
			}

			.btn-mobile {
				padding: 2px 12px;
				vertical-align: baseline;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<span style="width:50%;display:inline-block;">
							 <h5 style="margin-bottom: 5px;margin-top: 5px;">Stock Opname</h5>
						</span>
						<span style="width:49%;display:inline-block;text-align:right;">
							<button id="btnSave" class="btn btn-default btn-mobile" onclick="finish();"><i class="fa fa-save "></i> Simpan</button>&nbsp;
							<button id="btnTransaction" class="btn btn-default btn-mobile" onclick="itemList();" ><i class="fa fa-list"></i> Daftar Barang</button>
						</span>
					</div>
					<div class="panel-body">
						<br />
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 col-sm-2 has-float-label" ">
									<input id="txtItemCode" name="txtItemCode" type="text" class="form-control-custom" onfocus="this.select();" onchange="getItemDetails(0);" autocomplete=off />
									<label for="txtItemCode" class="lblInput" >Kode Barang</label>
									<input id="hdnStockAdjustID" name="hdnStockAdjustID" type="hidden" value=0 />
									<input id="hdnStockAdjustDetailsID" name="hdnStockAdjustDetailsID" type="hidden" value=0 />
									<input id="hdnItemID" name="hdnItemID" type="hidden" value=0 />
									<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
									<input id="hdnAvailableUnit" name="hdnAvailableUnit" type="hidden" />
									<input id="hdnItemDetailsID" name="hdnItemDetailsID" type="hidden" value=0 />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
								</div>
								<div class="col-md-4 col-sm-4 has-float-label" ">
									<input id="txtItemName" name="txtItemName" type="text" class="form-control-custom" readonly />
									<label for="txtItemName" class="lblInput" >Nama Barang</label>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-3 col-sm-3 has-float-label" ">
									<input id="txtShopStock" name="txtShopStock" type="text" class="form-control-custom" readonly />
									<label for="txtShopStock" class="lblInput" >Stok Toko</label>
								</div>
								<div class="col-md-3 col-sm-3 has-float-label" ">
									<input id="txtInventoryStock" name="txtInventoryStock" type="text" class="form-control-custom" readonly />
									<label for="txtInventoryStock" class="lblInput" >Stok Gudang</label>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-3 col-sm-3 has-float-label" ">
									<input id="txtAdjustShopStock" name="txtAdjustShopStock" type="text" class="form-control-custom" />
									<label for="txtAdjustShopStock" class="lblInput" >Penyesuaian Stok Toko</label>
								</div>
								<div class="col-md-3 col-sm-3 has-float-label" ">
									<input id="txtAdjustInventoryStock" name="txtAdjustInventoryStock" type="text" class="form-control-custom" />
									<label for="txtAdjustInventoryStock" class="lblInput" >Penyesuaian Stok Gudang</label>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-3 col-sm-3 has-float-label" ">
									<input id="txtBuyPrice" name="txtBuyPrice" type="text" class="form-control-custom" />
									<label for="txtBuyPrice" class="lblInput" >Harga Beli</label>
								</div>
								<div class="col-md-3 col-sm-3 has-float-label" ">
									<input id="txtRetailPrice" name="txtRetailPrice" type="text" class="form-control-custom" />
									<label for="txtRetailPrice" class="lblInput" >Harga Ecer</label>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-4 col-sm-4 has-float-label" ">
									<input id="txtPrice1" name="txtPrice1" type="text" class="form-control-custom" />
									<label for="txtPrice1" class="lblInput" >Harga 1</label>
								</div>
								<div class="col-md-2 col-sm-2 has-float-label" ">
									<input id="txtQty1" name="txtQty1" type="text" style="border: 1px solid #ccc !important;margin: 0;" class="form-control-custom" value=0 />
									<label for="txtQty1" class="lblInput" >Qty 1</label>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-4 col-sm-4 has-float-label" ">
									<input id="txtPrice2" name="txtPrice2" type="text" class="form-control-custom" />
									<label for="txtPrice2" class="lblInput" >Harga 2</label>
								</div>
								<div class="col-md-2 col-sm-2 has-float-label" ">
									<input id="txtQty2" name="txtQty2" type="text" style="border: 1px solid #ccc !important;margin: 0;" class="form-control-custom" value=0 />
									<label for="txtQty2" class="lblInput" >Qty 2</label>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="itemList-dialog" title="Daftar Barang" style="display: none;">
			<div class="row col-md-12 col-sm-12" >
				<div id="divTableItem" class="table-responsive" style="overflow-x:hidden;">
					<table id="grid-item" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
						<thead>
							<tr>
								<th>Kode</th>
								<th>Nama Barang</th>
								<th>Satuan</th>
								<th>H Beli</th>
								<th>H Ecer</th>
								<th>Harga 1</th>
								<th>QTY1</th>
								<th>Harga 2</th>
								<th>QTY2</th>
								<th>Toko</th>
								<th>Gudang</th>
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

			function changeItemCode() {
				var itemCode = $("#ddlUnit option:selected").attr("itemcode");
				$("#txtItemCode").val(itemCode);
				getItemDetails();
			}
			
			function openDialogEdit(Data) {
				$("#hdnStockAdjustDetailsID").val(Data[1]);
				$("#hdnItemID").val(Data[2]);
				$("#ddlBranch").val(Data[3]);
				$("#txtItemCode").val(Data[5]);
				$("#txtItemName").val(Data[6]);
				$("#txtQTY").val(Data[8]);
				$("#txtAdjustedQTY").val(Data[9]);

				$("#hdnAvailableUnit").val(Data[11]);
				$("#hdnItemDetailsID").val(Data[12]);
				var availableUnit = JSON.parse(Data[11]);
				if(availableUnit.length > 0) {
					$("#ddlUnit").find('option').remove();
					for(var i=0;i<availableUnit.length;i++) {
						$("#ddlUnit").append("<option value=" + availableUnit[i][0] + " itemdetailsid='" + availableUnit[i][2] + "' >" + availableUnit[i][1] + "</option>");
					}
				}


				$("#ddlUnit").val(Data[10]);

				setTimeout(function() { $("#txtItemCode").focus(); }, 0);
			}
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#FormData").attr("title", "Edit Adjust Stok");
					$("#hdnStockAdjustID").val(Data[9]);
					$("#txtItemCode").val(Data[4]);
					$("#txtItemName").val(Data[5]);
					$("#ddlBranch").val(Data[13]);
					//$("#lblTotal").html(Data[5]);
					$("#txtTransactionDate").datepicker("setDate", new Date(Data[10]));
					$("#hdnTransactionDate").val(Data[10]);
					getStockAdjustDetails(Data[9], Data[10]);
					var itemCode = $("#txtItemCode").val();
					var branchID = $("#ddlBranch").val();
					$.ajax({
						url: "./Transaction/StockAdjust/CheckItem.php",
						type: "POST",
						data: { itemCode : itemCode, branchID : branchID },
						dataType: "json",
						success: function(data) {
							if(data.FailedFlag == '0') {
								//if($("#hdnItemID").val() != data.ItemID) {
									$("#hdnAvailableUnit").val(JSON.stringify(data.AvailableUnit));
									$("#hdnItemDetailsID").val(data.ItemDetailsID);
									if(data.AvailableUnit.length > 0) {
										$("#ddlUnit").find('option').remove();
										for(var i=0;i<data.AvailableUnit.length;i++) {
											$("#ddlUnit").append("<option value=" + data.AvailableUnit[i][0] + " itemdetailsid='" + data.AvailableUnit[i][2] + "' itemcode='" + data.AvailableUnit[i][3] + "' >" + data.AvailableUnit[i][1] + "</option>");
										}
									}

									$("#hdnStockAdjustDetailsID").val(Data[10]);
									$("#hdnItemID").val(Data[12]);
									$("#txtQTY").val(Data[7]);
									$("#txtAdjustedQTY").val(Data[8]);
									$("#ddlBranch").val(Data[13]);
									$("#ddlUnit").val(Data[14]);
									$("#txtAdjustedQTY").focus();
								//}
								//else $("#txtAdjustedQTY").focus();
							}
							else {
								//add new item
								if(data.ErrorMessage == "") {
									 $("#txtItemCode").notify("Kode tidak valid!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
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
							LogEvent(errorMessage, "/Transaction/StockAdjust/index.php");
							Lobibox.alert("error",
							{
								msg: errorMessage,
								width: 480
							});
							return 0;
						}
					});
					setTimeout(function() { $("#txtItemCode").focus(); }, 0);
				}
				else {
					$("#FormData").attr("title", "Tambah Adjust Stok");
				}
				var index = table.cell({ focused: true }).index();
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
						$("#txtTransactionDate").focus();
						table.keys.disable();
						table2 = $("#grid-transaction").DataTable({
									"keys": true,
									"scrollY": "280px",
									"scrollX": false,
									"scrollCollapse": false,
									"paging": false,
									"searching": false,
									"order": [],
									"columns": [
										{ "visible": false },
										{ "visible": false },
										{ "visible": false },
										{ "visible": false },
										{ "width": "10%", "orderable": false, className: "dt-head-center" },
										{ "width": "20%", "orderable": false, className: "dt-head-center" },
										{ "width": "25%", "orderable": false, className: "dt-head-center" },
										{ "width": "15%", "orderable": false, className: "dt-head-center" },
										{ "width": "15%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "15%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "visible": false },
										{ "visible": false },
										{ "visible": false }
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
						var counterStockAdjustDetails = 0;
						table2.on( 'key', function (e, datatable, key, cell, originalEvent) {
							var index = table2.cell({ focused: true }).index();
							if(counterStockAdjustDetails == 0) {
								counterStockAdjustDetails = 1;
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
									SingleDelete("./Transaction/StockAdjust/DeleteDetails.php", deletedData, function(action) {
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
											//Calculate();
										}
										else {
											table2.keys.enable();
											return false;
										}
									});
								}
								setTimeout(function() { counterStockAdjustDetails = 0; } , 1000);
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
					height: 500,
					width: 1280,
					modal: false /*,
					buttons: [
					{
						text: "Tutup",
						tabindex: 11,
						id: "btnCancelAddStockAdjust",
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
					}]*/
				}).dialog("open");
			}
			
			var counterGetItem = 0;
			function getItemDetails() {
				if(counterGetItem == 0) {
					counterGetItem = 1;
					var itemCode = $("#txtItemCode").val();
					var branchID = $("#ddlBranch").val();
					if(itemCode == "") $("#txtItemCode").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					else {
						$.ajax({
							url: "./Transaction/StockAdjust/CheckItem.php",
							type: "POST",
							data: { itemCode : itemCode, branchID : branchID },
							dataType: "json",
							success: function(data) {
								if(data.FailedFlag == '0') {
									//if($("#hdnItemID").val() != data.ItemID) {
										if($("#hdnStockAdjustDetailsID").val() != 0) {
											$("#txtQTY").val(table2.row( rowEdit ).data()[8]);
											$("#txtAdjustedQTY").val(table2.row( rowEdit ).data()[8]);
										}
										else {
											$("#txtQTY").val(data.Quantity);
											$("#txtAdjustedQTY").val(data.Quantity);
											//console.log("mlebu 3");
										}

										$("#hdnItemID").val(data.ItemID);
										$("#txtItemName").val(data.ItemName);
										//$("#txtSalePrice").val(returnRupiah(data.RetailPrice));
										$("#ddlUnit").focus();

										$("#hdnAvailableUnit").val(JSON.stringify(data.AvailableUnit));
										$("#hdnItemDetailsID").val(data.ItemDetailsID);
										if(data.AvailableUnit.length > 0) {
											$("#ddlUnit").find('option').remove();
											for(var i=0;i<data.AvailableUnit.length;i++) {
												$("#ddlUnit").append("<option value=" + data.AvailableUnit[i][0] + " itemdetailsid='" + data.AvailableUnit[i][2] + "' itemcode='" + data.AvailableUnit[i][3] + "' >" + data.AvailableUnit[i][1] + "</option>");
											}
										}
										$("#ddlUnit").val(data.UnitID);
									//}
									//else $("#txtAdjustedQTY").focus();
								}
								else {
									//add new item
									if(data.ErrorMessage == "") {
										 $("#txtItemCode").notify("Kode tidak valid!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
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
								LogEvent(errorMessage, "/Transaction/StockAdjust/index.php");
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
			
			function getStockAdjustDetails(StockAdjustID, StockAdjustDetailsID) {
				$.ajax({
					url: "./Transaction/StockAdjust/StockAdjustDetails.php",
					type: "POST",
					data: { StockAdjustID : StockAdjustID },
					dataType: "json",
					success: function(Data) {
						if(Data.FailedFlag == '0') {
							for(var i=0;i<Data.data.length;i++) {
								table2.row.add(Data.data[i]);
							}
							table2.draw();
							tableWidthAdjust();
							rowEdit = table2.rows().eq( 0 ).filter( function (rowIdx) {
							    return table2.cell( rowIdx, 1 ).data() == StockAdjustDetailsID ? true : false;
							});
							//Calculate();
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
						LogEvent(errorMessage, "/Transaction/StockAdjust/index.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
			}
			
			var counterAddStockAdjust = 0;
			function addStockAdjustDetails() {
				if(counterAddStockAdjust == 0) {
					counterAddStockAdjust = 1;
					var itemID = $("#hdnItemID").val();
					var itemCode = $("#txtItemCode").val();
					var itemName = $("#txtItemName").val();
					var unitID = $("#ddlUnit").val();
					var unitName = $("#ddlUnit option:selected").text();
					var branchID = $("#ddlBranch").val();
					var branchName = $("#ddlBranch option:selected").text();
					var Qty = parseFloat($("#txtQTY").val()).toFixed(2);
					var adjustedQty = $("#txtAdjustedQTY").val();
					var salePrice = $("#txtSalePrice").val();
					$("#txtDiscount").blur();
					var PassValidate = 1;
					var FirstFocus = 0;
					var availableUnit = $("#hdnAvailableUnit").val();
					var itemDetailsID = $("#hdnItemDetailsID").val();
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
							url: "./Transaction/StockAdjust/Insert.php",
							type: "POST",
							data: $("#PostForm").serialize(),
							dataType: "json",
							success: function(data) {
								if(data.FailedFlag == '0') {
									if($("#hdnStockAdjustDetailsID").val() == 0) {
										table2.row.add([
											data.ID,
											data.StockAdjustDetailsID,
											itemID,
											branchID,
											branchName,
											itemCode,
											itemName,
											unitName,
											Qty,
											adjustedQty,
											unitID,
											availableUnit,
											itemDetailsID
										]).draw();
									}
									else {
										table2.row(rowEdit).data([
											data.ID,
											data.StockAdjustDetailsID,
											itemID,
											branchID,
											branchName,
											itemCode,
											itemName,
											unitName,
											Qty,
											adjustedQty,
											unitID,
											availableUnit,
											itemDetailsID
										]).draw();
										table2.keys.enable();
									}
									$("#txtItemCode").val("");
									$("#txtItemName").val("");
									$("#txtQTY").val(1);
									$("#txtAdjustedQTY").val(1);
									//$("#txtSalePrice").val(0);
									$("#txtItemCode").focus();
									$("#hdnStockAdjustID").val(data.ID);
									$("#hdnStockAdjustDetailsID").val(0);
									$("#hdnItemID").val(0);
									$("#ddlUnit").find('option').remove();
									$("#ddlUnit").append("<option>--</option>");
									$("#hdnAvailableUnit").val("");
									$("#hdnItemDetailsID").val(0);
									tableWidthAdjust();
									//Calculate();
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
								var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
								LogEvent(errorMessage, "/Transaction/StockAdjust/index.php");
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
				setTimeout(function() { counterAddStockAdjust = 0; }, 1000);
			}
			
			/*function Calculate() {
				var grandTotal = 0;
				var weight = 0;
				table2.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
					var data = this.data();
					grandTotal += parseFloat(data[7].replace(/\,/g, "")) * parseFloat(data[6]) - parseFloat(data[8]);
					weight += parseFloat(data[15]) * parseFloat(data[6]);
				});
				$("#lblTotal").html(returnRupiah(grandTotal.toString()));
				$("#lblWeight").html(returnWeight(weight.toString()));
			}*/

			function tableWidthAdjust() {
				var tableWidth = $("#divTableContent").find("table").width();
				var barWidth = table2.settings()[0].oScroll.iBarWidth;
				var newWidth = tableWidth - barWidth + 2;
				$("#divTableContent").find("table").css({
					"width": newWidth + "px"
				});
			}
			
			function resetForm() {
				$("#hdnStockAdjustID").val(0);
				$("#hdnStockAdjustDetailsID").val(0);
				$("#hdnItemID").val(0);
				$("#txtTransactionDate").datepicker("setDate", new Date());
				$("#txtItemCode").val("");
				$("#txtItemName").val("");
				$("#txtQTY").val(1);
				$("#txtAdjustedQTY").val(1);
				$("#ddlUnit").find('option').remove();
				$("#ddlUnit").append("<option>--</option>");
				$("#hdnAvailableUnit").val("");
				$("#hdnItemDetailsID").val(0);
				//$("#txtSalePrice").val(0);
				//$("#lblTotal").html("0");
				table2.clear().draw();
			}
			
			function fnDeleteData() {
				var index = table.cell({ focused: true }).index();
				table.keys.disable();
				DeleteData("./Transaction/StockAdjust/Delete.php", function(action) {
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
			
			function branchChange(BranchID) {
				$("#ddlBranch").val(BranchID);
				table3.ajax.reload();
			}

			function itemList() {
				$("#itemList-dialog").dialog({
					autoOpen: false,
					position: {
						my : 'top+12.5%',
						at : 'top'
					},
					open: function() {
						table2.keys.disable();
						if ( $.fn.dataTable.isDataTable( '#grid-item' ) ) {
							table3 = $('#grid-item').DataTable();
						}
						else {
							table3 = $("#grid-item").DataTable({
										"keys": true,
										"scrollY": "280px",
										"scrollX": false,
										"scrollCollapse": false,
										"paging": false,
										"searching": true,
										"order": [],
										"columns": [
											{ "width": "15%", "orderable": false, className: "dt-head-center" },
											{ "width": "20%", "orderable": false, className: "dt-head-center" },
											{ "width": "5%", "orderable": false, className: "dt-head-center" },
											{ "width": "7.5%", "visible": false, "orderable": false, className: "dt-head-center dt-body-right" },
											{ "width": "7.5%", "orderable": false, className: "dt-head-center dt-body-right" },
											{ "width": "7.5%", "orderable": false, className: "dt-head-center dt-body-right" },
											{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-right" },
											{ "width": "7.5%", "orderable": false, className: "dt-head-center dt-body-right" },
											{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-right" },
											{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-right" },
											{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-right" }
										],
										"ajax": "./Transaction/StockAdjust/ItemList.php",
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
						}
						var counterPickItem = 0;
						table3.on( 'key', function (e, datatable, key, cell, originalEvent) {
							//var index = table3.cell({ focused: true }).index();
							if(counterPickItem == 0) {
								counterPickItem = 1;
								var data = datatable.row( cell.index().row ).data();
								if(key == 13 && $("#itemList-dialog").css("display") == "block") {
									$("#txtItemCode").val(data[0]);
									getItemDetails(0);
									$("#itemList-dialog").dialog("destroy");
									table3.destroy();
									table2.keys.enable();
								}
								setTimeout(function() { counterPickItem = 0; } , 1000);
							}
						});
						
						/*var index = 0;
						$("#grid-item tbody").on("click", 'tr', function(e) {
							if(index == $(e.currentTarget).index()) {
								if( $("#itemList-dialog").css("display") == "block") {
									var data = table3.row(this).data();
									$("#txtItemCode").val(data[0]);
									getItemDetails();
									$("#itemList-dialog").dialog("destroy");
									table3.destroy();
									table2.keys.enable();
								}
							}
							index = table3.cell({ focused: true }).index().row;
						});*/
						$('#grid-item tbody').on('dblclick', 'tr', function () {
							if( $("#itemList-dialog").css("display") == "block") {
								var data = table3.row(this).data();
								$("#txtItemCode").val(data[0]);
								getItemDetails(0);
								$("#itemList-dialog").dialog("destroy");
								table3.destroy();
								table2.keys.enable();
							}
						});
						
						table3.on( 'search.dt', function () {
							setTimeout(function() { $("#grid-item").DataTable().cell( ':eq(0)' ).focus(); }, 1000 );
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
					
					close: function() {
						$(this).dialog("destroy");
						table3.destroy();
						table2.keys.enable();
						$("#txtItemCode").focus();
					},
					resizable: false,
					height: 420,
					width: 840,
					modal: true/*,
					buttons: [
					{
						text: "Tutup",
						tabindex: 13,
						id: "btnCancelPickItem",
						click: function() {
							$(this).dialog("destroy");
							table3.destroy();
							table2.keys.enable();
							return false;
						}
					}]*/
				}).dialog("open");
			}

			function finish() {
				if($("#hdnStockAdjustID").val() != 0) {
					$("#save-confirm").dialog({
						autoOpen: false,
						open: function() {
							$(document).on('keydown', function(e) {
								if (e.keyCode == 39) { //right arrow
									 $("#btnNo").focus();
								}
								else if (e.keyCode == 37) { //left arrow
									 $("#btnYes").focus();
								}
							});
						},
						show: {
							effect: "fade",
							duration: 0
						},
						hide: {
							effect: "fade",
							duration: 0
						},
						close: function() {
							$(this).dialog("destroy");
							//callback("Tidak");
						},
						resizable: false,
						height: "auto",
						width: 400,
						modal: true,
						buttons: [
						{
							text: "Ya",
							id: "btnYes",
							click: function() {
								$(this).dialog("destroy");
								$("#FormData").dialog("destroy");
								$("#divModal").hide();
								table.ajax.reload(function() {
									table.keys.enable();
									if(typeof index !== 'undefined') table.cell(index).focus();
								}, false);
								resetForm();
								table2.destroy();
								//$(this).dialog("destroy");
								//callback("Ya");
							}
						},
						{
							text: "Tidak",
							id: "btnNo",
							click: function() {
								$(this).dialog("destroy");
								//callback("Tidak");
							}
						}]
					}).dialog("open");
				}
				else {
					var counter = 0;
					Lobibox.alert("error",
					{
						msg: "Silahkan tambahkan barang terlebih dahulu!",
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
							if ( $.fn.DataTable.isDataTable( '#grid-transaction' ) ) {
								tableWidthAdjust();
							}
							if ( $.fn.DataTable.isDataTable( '#grid-item' ) ) {
								table3.columns.adjust().draw();
							}
						}, 0);
		            }, 500, "resizeWindow");
				});
				
				$('#grid-data').on('click', 'input[type="checkbox"]', function() {
				   $(this).blur();
				});

				$("#txtQty1").spinner();
				$("#txtQty2").spinner();
				
				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Transaction/StockAdjust/index.php");
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
					monthNames: [ "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember" ],
					monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des" ],
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
				var counterStockAdjust = 0;
				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "330px",
								"rowId": "StockAdjustID",
								"scrollCollapse": true,
								"order": [],
								"columns": [
									{ "width": "20px", "orderable": false, className: "dt-head-center dt-body-center" },
									{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-cente dt-body-right" },
									{ className: "dt-head-center dt-body-right" },
									{ "visible" : false },
									{ "visible" : false },
									{ "visible" : false },
									{ "visible" : false },
									{ "visible" : false },
									{ "visible" : false }
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Transaction/StockAdjust/DataSource.php",
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
					else if(counterStockAdjust == 0) {
						counterStockAdjust = 1;
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
								deletedData.push(data[10]);
								SingleDelete("./Transaction/StockAdjust/Delete.php", deletedData, function(action) {
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
						setTimeout(function() { counterStockAdjust = 0; } , 1000);
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
					else if(evt.keyCode == 121 && $("#itemList-dialog").css("display") == "none"  && $("#save-confirm").css("display") == "none" && $("#FormData").css("display") == "block"  && $(".lobibox").css("display") != "block") {
						evt.preventDefault();
						if(counterKey == 0) {
							finish();
							counterKey = 1;
						}
					}
					else if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0 && $("#FormData").css("display") == "none" && $("#delete-confirm").css("display") == "none") {
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