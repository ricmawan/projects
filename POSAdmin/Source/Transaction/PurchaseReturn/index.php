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
										<th>No. Invoice</th>
										<th>Tanggal</th>
										<th>Supplier</th>
										<th>Total</th>
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
		<div id="FormData" title="Tambah Kategori" style="display: none;">
			<form class="col-md-12" id="PostForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-1 labelColumn">
						No. Invoice :
					</div>
					<div class="col-md-2">
						<input id="txtPurchaseReturnNumber" name="txtPurchaseReturnNumber" type="text" class="form-control-custom" placeholder="No. Invoice" readonly />
					</div>
					<div class="col-md-1 labelColumn">
						Tanggal :
						<input id="hdnPurchaseReturnID" name="hdnPurchaseReturnID" type="hidden" value=0 />
						<input id="hdnPurchaseReturnDetailsID" name="hdnPurchaseReturnDetailsID" type="hidden" value=0 />
						<input id="hdnItemID" name="hdnItemID" type="hidden" value=0 />
						<input id="hdnItemDetailsID" name="hdnItemDetailsID" type="hidden" value=0 />
						<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
						<input id="hdnAvailableUnit" name="hdnAvailableUnit" type="hidden" />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
						<input id="hdnBuyPrice" name="hdnBuyPrice" type="hidden" />
					</div>
					<div class="col-md-2">
						<input id="txtTransactionDate" name="txtTransactionDate" type="text" tabindex=6 class="form-control-custom" style="width: 87%; display: inline-block;margin-right: 5px;" onfocus="this.select();" autocomplete=off placeholder="Tanggal" required />
					</div>
					
					<div class="col-md-1 labelColumn">
						Supplier :
					</div>
					<div class="col-md-2">
						<select id="ddlSupplier" name="ddlSupplier" tabindex=7 class="form-control-custom" style="width: 80%; display: inline-block;margin-right: 5px;" placeholder="Pilih Supplier" onchange="updateHeader();" >
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
						<i class="fa fa-user-plus" style="font-size: 14px;cursor: pointer;" onclick="addNewSupplier();"></i>
					</div>
				</div>
				<br />
				<div class="row">
					<table class="table table-striped table-hover" style="margin-bottom: 5px;" >
						<tbody>
							<tr>
								<td style="width: 10%;" >
									<div class="has-float-label" >
										<select id="ddlBranch" name="ddlBranch" tabindex=8 class="form-control-custom" placeholder="Pilih Cabang" >
											<?php
												$sql = "CALL spSelDDLBranch('".$_SESSION['UserLogin']."')";
												if (! $result = mysqli_query($dbh, $sql)) {
													logEvent(mysqli_error($dbh), '/Transaction/PurchaseReturn/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
													return 0;
												}
												while($row = mysqli_fetch_array($result)) {
													echo "<option value='".$row['BranchID']."' >".$row['BranchCode']." - ".$row['BranchName']."</option>";
												}
												mysqli_free_result($result);
												mysqli_next_result($dbh);
											?>
										</select>
										<label for="ddlBranch" class="lblInput" >Cabang</label>
									</div>
								</td>
								<td style="width: 20%;" >
									<div class="has-float-label" >
										<input id="txtItemCode" name="txtItemCode" type="text" tabindex=9 class="form-control-custom" style="width: 100%;" onfocus="this.select();" onkeypress="isEnterKey(event, 'getItemDetails');" autocomplete=off />
										<label for="txtItemCode" class="lblInput" >Kode Barang</label>
									</div>
								</td>
								<td style="width: 20%;" >
									<div class="has-float-label" >
										<input id="txtItemName" name="txtItemName" type="text" class="form-control-custom" style="width: 100%;" disabled />
										<label for="txtItemName" class="lblInput" >Nama Barang</label>
									</div>
								</td>
								<td style="width: 10%;" >
									<div class="has-float-label" >
										<input id="txtQTY" name="txtQTY" type="number" tabindex=10 class="form-control-custom" style="width: 100%;margin: 0;border: 0;" value=1 min=1 onchange="this.value = validateQTY(this.value);" onpaste="return false;" onfocus="this.select();" />
										<label for="txtQTY" class="lblInput" >Qty</label>
									</div>
								</td>
								<td style="width: 10%;" >
									<div class="has-float-label" >
										<select id="ddlUnit" name="ddlUnit" tabindex=11 class="form-control-custom" onchange="changeItemCode();" >
											<option >--</option>
										</select>
										<label for="ddlUnit" class="lblInput" >Satuan</label>
									</div>
								</td>
								<td style="width: 15%;" >
									<div class="has-float-label" >
										<input id="txtBuyPrice" name="txtBuyPrice" type="text" tabindex=12 class="form-control-custom text-right" value="0" autocomplete=off onchange="CalculatePrice();" onkeypress="isEnterKey(event, 'addPurchaseReturnDetails');return isNumberKey(event, this.id, this.value);" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" />
										<label for="txtBuyPrice" class="lblInput" >Harga Beli</label>
									</div>
								</td>
								<td style="width: 15%;" >
									<div class="has-float-label" >
										<input id="txtSubTotal" name="txtSubTotal" type="text" class="form-control-custom text-right" style="width: 100%;" value="0" autocomplete=off onpaste="return false;" disabled />
										<label for="txtSubTotal" class="lblInput" >Sub Total</label>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
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
									<th>Satuan</th>
									<th>Harga Beli</th>
									<th>Sub Total</th>
									<th>AvailableUnit</th>
									<th>UnitID</th>
									<th>ItemDetailsID</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="row" >
					<h2 style="display: inline-block;float: left;" >TOTAL : &nbsp;</h2><span id="lblTotal" >0</span>
				</div>
				<br />
				<div class="row" >
					<h5 style="margin: 5px 0 0 0;">F10 = Transaksi Selesai; F12 = Daftar Barang; ESC = Tutup; DELETE = Hapus; ENTER/DOUBLE KLIK = Edit;</h5>
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
								<th>Satuan</th>
								<th>H Beli</th>
								<th>H Ecer</th>
								<th>H Grosir 1</th>
								<th>QTY1</th>
								<th>H Grosir 2</th>
								<th>QTY2</th>
								<th>Toko</th>
								<th>Gudang</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
			<div id="divBranch" style="display:none;">
				<select class="form-control-custom" placeholder="Pilih Cabang" onchange="branchChange(this.value);" >
					<!--<option value=0 selected >-- Semua Cabang --</option>-->
					<?php
						$sql = "CALL spSelDDLBranch('".$_SESSION['UserLogin']."')";
						if (! $result = mysqli_query($dbh, $sql)) {
							logEvent(mysqli_error($dbh), '/Transaction/PurchaseReturn/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
							return 0;
						}
						while($row = mysqli_fetch_array($result)) {
							echo "<option value='".$row['BranchID']."' >".$row['BranchCode']." - ".$row['BranchName']."</option>";
						}
						mysqli_free_result($result);
						mysqli_next_result($dbh);
					?>
				</select>
				<input type="hidden" id="hdnBranchID" name="hdnBranchID" value=1 />
			</div>
		</div>
		
		<script>
			var table;
			var table2;
			var table3;
			var today;
			var rowEdit;

			function changeItemCode() {
				var itemDetailsID  = $("#ddlUnit option:selected").attr("itemdetailsid");
				var itemCode = $("#ddlUnit option:selected").attr("itemcode");
				var buyPrice = $("#ddlUnit option:selected").attr("buyprice");
				var conversionQuantity = $("#ddlUnit option:selected").attr("conversionQuantity");

				$("#hdnItemDetailsID").val(itemDetailsID);
				$("#txtItemCode").val(itemCode);
				$("#txtBuyPrice").val(returnRupiah((parseFloat(buyPrice) * parseFloat(conversionQuantity)).toString()));

				$("#hdnBuyPrice").val(buyPrice);

				CalculateSubTotal();
			}

			function CalculateSubTotal() {
				var buyPrice = parseFloat($("#txtBuyPrice").val().replace(/\,/g, ""));
				var QTY = parseFloat($("#txtQTY").val());
				var SubTotal = buyPrice * QTY;
				$("#txtSubTotal").val(returnRupiah(SubTotal.toString()));
			}

			function CalculatePrice() {
				var conversionQuantity = parseFloat($("#ddlUnit option:selected").attr("conversionQuantity"));
				var buyPrice = parseFloat($("#txtBuyPrice").val().replace(/\,/g, ""));
				
				$("#hdnBuyPrice").val(buyPrice/conversionQuantity);
			}
			
			function openDialogEdit(Data) {
				$("#hdnPurchaseReturnDetailsID").val(Data[0]);
				$("#hdnItemID").val(Data[1]);
				$("#ddlBranch").val(Data[2]);
				$("#txtItemCode").val(Data[4]);
				$("#txtItemName").val(Data[5]);
				$("#txtQTY").val(Data[6]);
				$("#txtBuyPrice").val(Data[8]);
				$("#txtSubTotal").val(Data[9]);
				$("#hdnAvailableUnit").val(Data[10]);
				$("#hdnItemDetailsID").val(Data[12]);
				var availableUnit = JSON.parse(Data[10]);
				if(availableUnit.length > 0) {
					$("#ddlUnit").find('option').remove();
					for(var i=0;i<availableUnit.length;i++) {
						$("#ddlUnit").append("<option value=" + availableUnit[i][0] + " itemdetailsid='" + availableUnit[i][2] + "' itemcode='" + availableUnit[i][3] + "' buyprice='" + availableUnit[i][4] + "' conversionQuantity='" + availableUnit[i][5] + "' >" + availableUnit[i][1] + "</option>");
					}
				}
				$("#ddlUnit").val(Data[11]);
				setTimeout(function() { $("#txtItemCode").focus(); }, 0);
			}
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#FormData").attr("title", "Edit Retur Pembelian");
					$("#hdnPurchaseReturnID").val(Data[6]);
					$("#ddlSupplier").val(Data[7]);
					$("#lblTotal").html(Data[5]);
					$("#txtTransactionDate").datepicker("setDate", new Date(Data[8]));
					$("#hdnTransactionDate").val(Data[8]);
					$("#txtPurchaseReturnNumber").val(Data[2])
					getPurchaseReturnDetails(Data[6]);
				}
				else $("#FormData").attr("title", "Tambah Retur Pembelian");
				var index = table.cell({ focused: true }).index();
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
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
										{ "width": "10%", "orderable": false, className: "dt-head-center" },
										{ "width": "20%", "orderable": false, className: "dt-head-center" },
										{ "width": "20%", "orderable": false, className: "dt-head-center" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "10%", "orderable": false, className: "dt-head-center" },
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

						$("#txtTransactionDate").focus();
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
					height: 560,
					width: 1280,
					modal: false /*,
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
					}]*/
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
									//if($("#hdnItemID").val() != data.ItemID) {
										$("#hdnItemID").val(data.ItemID);
										$("#txtItemName").val(data.ItemName);
										$("#txtBuyPrice").val(returnRupiah((parseFloat(data.BuyPrice) * parseFloat(data.ConversionQuantity)).toString()));
										$("#txtQTY").focus();
										$("#txtSubTotal").val(returnRupiah((parseFloat(data.BuyPrice) * parseFloat(data.ConversionQuantity)).toString()));
										$("#hdnAvailableUnit").val(JSON.stringify(data.AvailableUnit));
										$("#hdnItemDetailsID").val(data.ItemDetailsID);
										$("#hdnBuyPrice").val(data.BuyPrice);
										if(data.AvailableUnit.length > 0) {
											$("#ddlUnit").find('option').remove();
											for(var i=0;i<data.AvailableUnit.length;i++) {
												$("#ddlUnit").append("<option value=" + data.AvailableUnit[i][0] + " itemdetailsid='" + data.AvailableUnit[i][2] + "' itemcode='" + data.AvailableUnit[i][3] + "' buyprice='" + data.AvailableUnit[i][4] + "' conversionQuantity='" + data.AvailableUnit[i][5] + "' >" + data.AvailableUnit[i][1] + "</option>");
											}
										}
										$("#ddlUnit").val(data.UnitID);
									//}
									//else $("#txtQTY").focus();
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
					var unitID = $("#ddlUnit").val();
					var unitName = $("#ddlUnit option:selected").text();
					var itemID = $("#hdnItemID").val();
					var itemCode = $("#txtItemCode").val();
					var itemName = $("#txtItemName").val();
					var Qty = $("#txtQTY").val();
					var buyPrice = returnRupiah($("#txtBuyPrice").val());
					var availableUnit = $("#hdnAvailableUnit").val();
					var unitID = $("#ddlUnit").val();
					var itemDetailsID = $("#hdnItemDetailsID").val();
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

					if($("#ddlSupplier").val() == "" || !$("#ddlSupplier").val()) {
						PassValidate = 0;
						$("#ddlSupplier").notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#ddlSupplier").focus();
						FirstFocus = 1;
					}
					
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
										$("#txtPurchaseReturnNumber").val(data.PurchaseReturnNumber);
										table2.row.add([
											data.PurchaseReturnDetailsID,
											itemID,
											branchID,
											branchName,
											itemCode,
											itemName,
											Qty,
											unitName,
											buyPrice,
											returnRupiah((parseFloat(buyPrice.replace(/\,/g, "")) * parseFloat(Qty)).toString()),
											availableUnit,
											unitID,
											itemDetailsID
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
											unitName,
											buyPrice,
											returnRupiah((parseFloat(buyPrice.replace(/\,/g, "")) * parseFloat(Qty)).toString()),
											availableUnit,
											unitID,
											itemDetailsID
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
									$("#txtSubTotal").val(0);
									$("#ddlUnit").find('option').remove();
									$("#hdnBuyPrice").val(0);
									$("#ddlUnit").append("<option>--</option>");
									$("#hdnAvailableUnit").val("");
									$("#hdnItemDetailsID").val(0);
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

			function updateHeader() {
				if($("#hdnPurchaseReturnID").val() != 0) {
					var PurchaseReturnID = $("#hdnPurchaseReturnID").val();
					var TransactionDate = $("#hdnTransactionDate").val();
					var SupplierID = $("#ddlSupplier").val();
					$.ajax({
						url: "./Transaction/PurchaseReturn/UpdateHeader.php",
						type: "POST",
						data: { PurchaseReturnID : PurchaseReturnID, TransactionDate : TransactionDate, SupplierID : SupplierID },
						dataType: "json",
						success: function(data) {
							$("#loading").hide();
							if(data.FailedFlag == '0') {
								
							}
							else {
								
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

			function addNewSupplier() {
				Lobibox.window({
					title: 'Tambah Supplier',
					url: 'Transaction/PurchaseReturn/PopUpAddSupplier.php',
					width: 680,
					height: 400,
					buttons: {
						Simpan: {
							'class': 'ui-button ui-corner-all ui-widget',
							text: "Simpan"
						},
						Batal: {
							'class': 'ui-button ui-corner-all ui-widget',
							text: "Batal",
							closeOnClick: true
						}
					},
					buttonsAlign: 'right',
					onShow: function() {
						setTimeout(function() {
							if($("#hdnPermission").length == 0) {
								$("#txtSupplierCodeAdd").focus();
								$("#btnSimpan").attr("tabindex", 76);
								$(document).on('keydown', function(e) {
									if (e.keyCode == 39 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //right arrow
										$("#btnBatal").focus();
									}
									else if(e.keyCode == 37 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //left arrow
										$("#btnSimpan").focus();
									}
								});
							}
							else {
								$("#btnOK").focus();
								$("#btnSimpan").css("visibility", "hidden");
								$("#btnBatal").css("visibility", "hidden");
							}
						}, 0);
					},
					callback: function(lobibox, type){
						var btnType;
						if (type === 'Simpan'){
							var PassValidate = 1;
							var FirstFocus = 0;

							$("#FormSupplier .form-control-custom").each(function() {
								if($(this).hasAttr('required')) {
									if($(this).val() == "") {
										PassValidate = 0;
										$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
										if(FirstFocus == 0) $(this).focus();
										FirstFocus = 1;
									}
								}
							});
							if(PassValidate == 1) {
								$("#save-confirm-add").dialog({
									autoOpen: false,
									dialogClass: "top-window",
									open: function() {
										$(document).on('keydown', function(e) {
											if (e.keyCode == 39) { //right arrow
												 $("#btnNo").focus();
											}
											else if (e.keyCode == 37) { //left arrow
												 $("#btnYes").focus();
											}
										});
										setTimeout(function() {
											$("#btnYes").focus();
										}, 0);
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
											$("#loading").show();
											$(this).dialog("destroy");
											//callback("Ya");
											$.ajax({
												url: "./Transaction/PurchaseReturn/InsertNewSupplier.php",
												type: "POST",
												data: $("#PostFormSupplier").serialize(),
												dataType: "json",
												success: function(data) {
													if(data.FailedFlag == '0') {
														$("#loading").hide();
														//$("#FormData").dialog("destroy");
														//$("#divModal").hide();
														var counter = 0;
														Lobibox.alert("success",
														{
															msg: data.Message,
															width: 480,
															delay: 2000,
															beforeClose: function() {
																setTimeout(function() {
																	//$("#txtQTY").focus();
																}, 0);
															},
															shown: function() {
																setTimeout(function() {
																	$("#ddlSupplier").append("<option value=" + data.ID + " >" + $("#txtSupplierCodeAdd").val() + " - " + $("#txtSupplierNameAdd").val() + "</option>");
																	$("#ddlSupplier").val(data.ID);
																	updateHeader();

																	var lobibox = $('.lobibox-window').data('lobibox');
																	lobibox.destroy();
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
																		$("#txtSupplierCodeAdd").focus();
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
													LogEvent(errorMessage, "/Transaction/PurchaseReturn/index.php");
													Lobibox.alert("error",
													{
														msg: errorMessage,
														width: 480,
														beforeClose: function() {
															if(counter == 0) {
																setTimeout(function() {
																	$("#txtSupplierCodeAdd").focus();
																}, 0);
																counter = 1;
															}
														}
													});
													return 0;
												}
											});
										}
									},
									{
										text: "Tidak",
										id: "btnNo",
										click: function() {
											$(this).dialog("destroy");
											$("#txtSupplierCodeAdd").focus();
											//callback("Tidak");
										}
									}]
								}).dialog("open");
							}
						}
					}
				});
			}
			
			function Calculate() {
				var grandTotal = 0;
				table2.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
					var data = this.data();
					grandTotal += parseFloat(data[8].replace(/\,/g, "")) * parseFloat(data[6]);
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
				$("#hdnPurchaseReturnDetailsID").val(0);
				$("#hdnItemID").val(0);
				$("#txtTransactionDate").datepicker("setDate", new Date());
				var transactionDate = new Date();
				transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
				today = transactionDate;
				$("#hdnTransactionDate").val(transactionDate);
				$("#txtPurchaseReturnNumber").val("");
				$("#txtItemCode").val("");
				$("#txtItemName").val("");
				$("#txtQTY").val(1);
				$("#txtBuyPrice").val(0);
				$("#lblTotal").html("0");
				$("#txtPurchaseReturnNumber").val("");
				if( $("#ddlSupplier").has("option").length > 0 ) $("#ddlSupplier option")[0].selected = true;
				$("#ddlUnit").find('option').remove();
				$("#ddlUnit").append("<option>--</option>");
				$("#hdnAvailableUnit").val("");
				$("#txtSubTotal").val(0);
				$("#hdnItemDetailsID").val(0);
				$("#hdnBuyPrice").val(0);
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

			function branchChange(BranchID) {
				$("#ddlBranch").val(BranchID);
				table3.ajax.reload();
			}
			
			function itemList() {
				$("#itemList-dialog").dialog({
					autoOpen: false,
					open: function() {
						table.keys.disable();
						table2.keys.disable();
						table3 = $("#grid-item").DataTable({
									"keys": true,
									"scrollY": "280px",
									"scrollX": false,
									"scrollCollapse": false,
									"paging": true,
									"lengthChange": false,
									"pageLength": 25,
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
									"ajax": {
										"url": "./Transaction/Sale/ItemList.php" /*,
										"data": function ( d ) {
											d.BranchID = $("#hdnBranchID").val()
										}*/
									},
									"processing": true,
									"serverSide": true,
									"language": {
										"info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
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
									} /*,
									"sDom": '<"toolbar">frtip' */
								});

						/*$(".toolbar").css({
							"display" : "inline-block"
						});

						$("div.toolbar").html($("#divBranch").html());*/

						var counterPickItem = 0;
						table3.on( 'key', function (e, datatable, key, cell, originalEvent) {
							if(key == 13 && $("#itemList-dialog").css("display") == "block") {
								if(counterPickItem == 0) {
									counterPickItem = 1;
									var data = table3.row($(table3.cell({ focused: true }).node()).parent('tr')).data();
									$("#txtItemCode").val(data[0]);
									getItemDetails(0);
									$("#itemList-dialog").dialog("destroy");
									table3.destroy();
									//table.keys.enable();
									table2.keys.enable();
									setTimeout(function() { counterPickItem = 0; } , 1000);
								}
							}
						});
						
						$('#grid-item tbody').on('dblclick', 'tr', function () {
							if( $("#itemList-dialog").css("display") == "block") {
								var data = table3.row(this).data();
								$("#txtItemCode").val(data[0]);
								getItemDetails(0);
								$("#itemList-dialog").dialog("destroy");
								table3.destroy();
								//table.keys.enable();
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
								else if(evt.keyCode == 27 && $("#itemList-dialog").css("display") == "block") {
									$("#itemList-dialog").dialog("destroy");
									table3.destroy();
									table2.keys.enable();
								}
							}
							setTimeout(function() { counterKeyItem = 0; } , 1000);
						});
					},
					
					close: function() {
						$(this).dialog("destroy");
						table3.destroy();
						//table.keys.enable();
						table2.keys.enable();
						$("#txtItemCode").focus();
					},
					resizable: false,
					height: 480,
					width: 1280,
					modal: true /*,
					buttons: [
					{
						text: "Tutup",
						tabindex: 18,
						id: "btnCancelPickItem",
						click: function() {
							$(this).dialog("destroy");
							table3.destroy();
						//	table.keys.enable();
							table2.keys.enable();
							return false;
						}
					}]*/
				}).dialog("open");
			}

			function finish() {
				if($("#hdnPurchaseReturnID").val() != 0) {
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
							setTimeout(function() {
								$("#btnYes").focus();
							}, 0);
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

				$("#txtQTY").spinner({
					change: function() {
						CalculateSubTotal();
					}
				});
				
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
						updateHeader();
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
								"order": [],
								"columns": [
									{ "width": "20px", "orderable": false, className: "dt-head-center dt-body-center" },
									{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center" },
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