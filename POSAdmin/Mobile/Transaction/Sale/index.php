<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			#divTableContent {
				min-height: 335px;
				max-height: 335px;
				overflow-y: auto;
			}
			
			.btn {
				font-size: 12px !important;
				padding: 0 5px !important;
			}

			.btn-mobile {
				height:45px;
				width:49%;
			}
		</style>
	</head>
	<body>
		<div class="row">			
			<div id="FormData" class="col-md-12" >
				<div class="panel panel-default">
					<div class="panel-heading">
						<span style="width:50%;display:inline-block;">
							<h5 id="headTitle" style="margin-bottom: 5px;margin-top: 5px;">Transaksi Penjualan Eceran</h5>
						</span>
						<span style="width:49%;display:inline-block;text-align:right;">
							<b>No Invoice: <span id="lblSaleNumber" ></span></b>
						</span>
					</div>
					<div class="panel-body" style="margin-top: 5px;">
						
						<form id="PostForm" method="POST" action="" >
							<input id="hdnSaleID" name="hdnSaleID" type="hidden" value=0 />
							<input id="hdnSaleDetailsID" name="hdnSaleDetailsID" type="hidden" value=0 />
							<input id="hdnSaleNumber" name="hdnSaleNumber" type="hidden" value="" />
							<input id="hdnItemID" name="hdnItemID" type="hidden" value=0 />
							<input id="hdnItemDetailsID" name="hdnItemDetailsID" type="hidden" value=0 />
							<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
							<input id="hdnAvailableUnit" name="hdnAvailableUnit" type="hidden" />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
							<input id="hdnPayment" name="hdnPayment" type="hidden" value=0 />
							<input id="hdnPaymentType" name="hdnPaymentType" type="hidden" value=0 />
							<input id="hdnMobilePath" name="hdnMobilePath" type="hidden" value='<?php echo $MOBILE_PATH; ?>' />
							<input type="hidden" id="hdnIsRetail" name="hdnIsRetail" value=1 />
							<div id="leftSide" class="col-md-12" style="display:inline-block;width:20%;border-right:3px double black;float:left;font-size: 10px !important;" >
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<select id="ddlCustomer" name="ddlCustomer" tabindex=7 class="form-control-custom" placeholder="Pilih Pelanggan" style="width: 80%; display: inline-block;margin-right: 5px;" onchange="updateHeader();" >
											<?php
												$sql = "CALL spSelDDLCustomer('".$_SESSION['UserLogin']."')";
												if (! $result = mysqli_query($dbh, $sql)) {
													logEvent(mysqli_error($dbh), '/Transaction/Sale/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
													return 0;
												}
												while($row = mysqli_fetch_array($result)) {
													echo "<option value='".$row['CustomerID']."' >".$row['CustomerCode']." - ".$row['CustomerName']."</option>";
												}
												mysqli_free_result($result);
												mysqli_next_result($dbh);
											?>
										</select>
										<label for="txtCustomer" class="lblInput" >Pelanggan</label>
										<i class="fa fa-user-plus" style="font-size: 14px;cursor: pointer;" onclick="addNewCustomer();"></i>
									</div>
								</div>
								<br />
								
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<input tabindex=8 id="txtItemCode" name="txtItemCode" type="text" class="form-control-custom" onfocus="this.select();" onkeypress="isEnterKey(event, 'getItemDetails');" onchange="getItemDetails();" autocomplete=off />
										<label for="txtItemCode" class="lblInput" >Kode Barang</label>
									</div>
								</div>
								<br />
								
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<input disabled id="txtItemName" name="txtItemName" type="text" class="form-control-custom" />
										<label for="txtItemName" class="lblInput" >Nama Barang</label>
									</div>
								</div>
								<br />
								
								<div class="row" >
									<div class="col-md-6 has-float-label" >
										<input tabindex=9 id="txtQTY" onfocus="this.select();" name="txtQTY" type="number" class="form-control-custom" style="border: 1px solid #ccc !important;margin: 0;" value=1 min=1 onchange="this.value = validateQTY(this.value);Grosir(this.value);" onpaste="return false;" onfocus="this.select();" />
										<label for="txtQTY" class="lblInput" >Qty</label>
									</div>

									<div class="col-md-6 has-float-label" >
										<select id="ddlUnit" name="ddlUnit" tabindex=10 class="form-control-custom mousetrap" onchange="changeItemCode();" >
											<option >--</option>
										</select>
										<label for="ddlUnit" class="lblInput" >Satuan</label>
									</div>
								</div>
								<br />
								
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<input id="hdnBuyPrice" name="hdnBuyPrice" type="hidden" value=0 />
										<input id="hdnSalePrice" name="hdnSalePrice" type="hidden" value=0 />
										<input id="hdnRetailPrice" name="hdnRetailPrice" type="hidden" value=0 />
										<input id="hdnPrice1" name="hdnPrice1" type="hidden" value=0 />
										<input id="hdnQty1" name="hdnQty1" type="hidden" value=0 />
										<input id="hdnPrice2" name="hdnPrice2" type="hidden" value=0 />
										<input id="hdnQty2" name="hdnQty2" type="hidden" value=0 />
										<input id="hdnBranchID" name="hdnBranchID" type="hidden" value=1 />
										<input id="hdnWeight" name="hdnWeight" type="hidden" value=0 />
										<input id="hdnConversionQty" name="hdnConversionQty" type="hidden" value=0 />
										<input id="hdnStock" name="hdnStock" type="hidden" value=0 />
										<input id="txtSalePrice" name="txtSalePrice" class="form-control-custom text-right" value="0" autocomplete=off onfocus="this.select();" onpaste="return false;" readonly />
										<label for="txtSalePrice" class="lblInput" >Harga</label>
									</div>
								</div>
								<br />
								
								<div class="row" >
									<div class="col-md-12 has-float-label" >
										<input id="txtDiscount" name="txtDiscount" type="number" lang="en-150" tabindex=11 class="form-control-custom text-right" value="0" autocomplete=off onkeypress="isEnterKey(event, 'addSaleDetails');return isNumberKey(event, this.id, this.value);" onchange="addSaleDetails();" onfocus="this.select();" onpaste="return false;" />
										<label for="txtDiscount" class="lblInput" >Diskon</label>
									</div>
								</div>
								<input type="text" tabindex=12 onfocus="addSaleDetails();" style="height:0; width: 0;opacity:0;" />
								<br />
								<button type="button" class="btn btn-default btn-mobile" value="Simpan" onclick="finish();" ><i class="fa fa-save fa-2x"></i><br /> Selesai</button>
								<button type="button" class="btn btn-default btn-mobile" value="Cetak" onclick="itemList();" ><i class="fa fa-list fa-2x"></i><br /> List Barang</button>
								<!--<br />
								<button type="button" class="btn btn-default btn-mobile" value="Cetak" onclick="printInvoice();" ><i class="fa fa-print fa-2x"></i> <br />Cetak Nota</button>
								<button type="button" class="btn btn-default btn-mobile" value="Cetak" onclick="printShipment();" ><i class="fa fa-truck fa-2x"></i> <br />Surat Jalan</button>-->
								<br />
								<button type="button" class="btn btn-default btn-mobile" value="Cancel" onclick="newWindow();" ><i class="fa fa-window-restore fa-2x"></i> <br />Nota Baru</button>
								<button id="btnGrosir" type="button" class="btn btn-default btn-mobile" value="Cetak" ><i class="fa fa-cart-arrow-down fa-2x"></i> Grosir</button>
	</button>
							</div>
							<div class="col-md-12" style="display:inline-block;width:80%;float:left;" >
								<div class="row" style="max-height: 400px;overflow-y:auto;" >
									<div class="col-md-12" >
										<div id="divTableContent" class="table-responsive" style="overflow-x:hidden;">
											<table id="grid-transaction" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
												<thead>
													<tr>
														<th><input id="select_all" name="select_all" type="checkbox" onclick="chkAll();" style="margin: 0;" /></th>
														<th>SaleDetailsID</th>
														<th>ItemID</th>
														<th>BranchID</th>
														<th>Cabang</th>
														<th>Kode Barang</th>
														<th>Nama Barang</th>
														<th>Qty Only</th>
														<th>Qty</th>
														<th>Harga</th>
														<th>Disc</th>
														<th>Total</th>
														<th>BuyPrice</th>
														<th>Price1</th>
														<th>Qty1</th>
														<th>Price2</th>
														<th>Qty2</th>
														<th>Weight</th>
														<th>Retail Price</th>
														<th>AvailableUnit</th>
														<th>UnitID</th>
														<th>ItemDetailsID</th>
														<th>ConversionQty</th>
														<th>Opsi</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
								</div>
								<br />
								<div class="row col-md-12" >
									<h2 style="display: inline-block;float: left;" >TOTAL : &nbsp;</h2><span id="lblTotal" >0</span>
									</h2><span id="lblWeight" >0</span><h2 style="display: inline-block;float: right;color: #0006ff;" >Berat(KG) : &nbsp;
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
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
		<div id="finish-dialog" title="Transaksi Selesai" style="display: none;">
			<div class="row col-md-12" >
				<div class="col-md-4 labelColumn">
					Pembayaran :
				</div>
				<div class="col-md-8">
					<select id="ddlPayment" name="ddlPayment" class="form-control-custom" tabindex=14 onchange="PaymentTypeChange();" >
						<option value=1 >Tunai</option>
						<option value=2 >Tempo</option>
					</select>
				</div>
			</div>
			<br />
			<div class="row col-md-12" >
				<div class="col-md-4 labelColumn">
					Total :
				</div>
				<div class="col-md-8">
					<input id="txtTotal" name="txtTotal" type="text" class="form-control-custom text-right" value="0" autocomplete=off placeholder="Total" readonly />
				</div>
			</div>
			<br />
			<div class="row col-md-12" >
				<div class="col-md-4 labelColumn">
					Bayar :
				</div>
				<div class="col-md-8">
					<input id="txtPayment" name="txtPayment" type="text" tabindex=15 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Bayar" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" onchange="Change();" />
				</div>
			</div>
			<br />
			<div class="row col-md-12" >
				<div class="col-md-4 labelColumn">
					<label id="lblChange" style="font-weight: normal;" >Kembali :</label>
				</div>
				<div class="col-md-8">
					<input id="txtChange" name="txtChange" type="text" class="form-control-custom text-right" value="0" readonly />
				</div>
			</div>
			<br />
			<div class="row col-md-12" >
				<label class="checkboxContainer" >Cetak Nota
					<input type="checkbox" id="chkPrint" name="chkPrint" value=1 tabindex=16 checked />
					<span class="checkmark"></span>
				</label>
			</div>
			<br />
			<div class="row col-md-12" >
				<label class="checkboxContainer">Cetak Surat Pengambilan
					<input type="checkbox" id="chkPrintShipment" name="chkPrintShipment" value=1 tabindex=17 checked />
					<span class="checkmark"></span>
				</label>
			</div>
			<br />
			<button type="button" class="btn btn-primary btn-block" onclick="printInvoice();" style="padding: 6px 12px !important;" tabindex=18 >Selesai</button>
			<!--<br />
			<button class="btn btn-danger btn-block" tabindex=16 onclick="printShipment();" >Cetak Surat Jalan</button>-->
		</div>
		<div id="divBranch" style="display:none;">
			<select class="form-control-custom" placeholder="Pilih Cabang" onchange="branchChange(this.value);" >
				<!--<option value=0 selected >-- Semua Cabang --</option>-->
				<?php
					$sql = "CALL spSelDDLBranch('".$_SESSION['UserLogin']."')";
					if (! $result = mysqli_query($dbh, $sql)) {
						logEvent(mysqli_error($dbh), '/Report/Sale/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
						return 0;
					}
					while($row = mysqli_fetch_array($result)) {
						echo "<option value='".$row['BranchID']."' >".$row['BranchCode']." - ".$row['BranchName']."</option>";
					}
					mysqli_free_result($result);
					mysqli_next_result($dbh);
				?>
			</select>
		</div>
		<div id="code-dialog" title="Konfirmasi Kode" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Stok barang tidak mencukupi, masukkan kode di bawah jika tetap ingin menambahkan!</p>
			<div class="row col-md-12" >
				<div class="col-md-4 labelColumn">
					Kode :
				</div>
				<div class="col-md-8">
					<label id="lblCode" name="lblCode" ></label>
				</div>
			</div>
			<br />
			<div class="row col-md-12" >
				<div class="col-md-4 labelColumn">
					Masukkan Kode :
				</div>
				<div class="col-md-6">
					<input id="txtCode" name="txtCode" type="number" max="999999" tabindex=60 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Kode" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="this.select();" onpaste="return false;" />
				</div>
			</div>
		</div>
		<script>
			var table2;
			var table3;
			var today;
			var rowEdit;

			function changeItemCode() {
				var itemCode = $("#ddlUnit option:selected").attr("itemcode");
				$("#txtItemCode").val(itemCode);
				getItemDetails();
			}

			function CalculateSubTotal() {
				var salePrice = parseFloat($("#txtSalePrice").val().replace(/\,/g, ""));
				var discount = parseFloat($("#txtDiscount").val().replace(/\,/g, ""));
				var QTY = parseFloat($("#txtQTY").val());
				var SubTotal = (salePrice - discount) * QTY;
				$("#txtSubTotal").val(returnRupiah(SubTotal.toString()));
			}

			function CalculatePrice() {
				var conversionQuantity = parseFloat($("#ddlUnit option:selected").attr("conversionQuantity"));
				var salePrice = parseFloat($("#txtSalePrice").val().replace(/\,/g, ""));
				$("#hdnSalePrice").val(salePrice/conversionQuantity);
			}
			
			function openDialogEdit(Data) {
				$("#hdnSaleDetailsID").val(Data[1]);
				$("#hdnItemID").val(Data[2]);
				$("#hdnBranchID").val(Data[3]);
				$("#txtItemCode").val(Data[5]);
				$("#txtItemName").val(Data[6]);
				$("#txtQTY").val(Data[7]);
				$("#txtSalePrice").val(Data[9]);
				$("#hdnSalePrice").val(parseFloat(Data[9].replace(/\,/g, "")) / parseFloat(Data[22]));
				$("#txtDiscount").val(Data[10]);
				$("#txtSubTotal").val(Data[11]);
				$("#hdnBuyPrice").val(Data[12]);
				$("#hdnPrice1").val(Data[13]);
				$("#hdnQty1").val(Data[14]);
				$("#hdnPrice2").val(Data[15]);
				$("#hdnQty2").val(Data[16]);
				$("#hdnWeight").val(Data[17]);
				$("#hdnRetailPrice").val(Data[18]);

				$("#hdnAvailableUnit").val(Data[19]);
				$("#hdnItemDetailsID").val(Data[21]);
				$("#hdnConversionQty").val(Data[22]);

				var availableUnit = JSON.parse(Data[19]);
				if(availableUnit.length > 0) {
					$("#ddlUnit").find('option').remove();
					for(var i=0;i<availableUnit.length;i++) {
						$("#ddlUnit").append("<option value=" + availableUnit[i][0] + " itemdetailsid='" + availableUnit[i][2] + "' itemcode='" + availableUnit[i][3] + "' buyprice='" + availableUnit[i][4] + "' retailprice='" + availableUnit[i][5] + "' price1='" + availableUnit[i][6] + "' price2='" + availableUnit[i][7] + "' qty1='" + availableUnit[i][8] + "'  qty2='" + availableUnit[i][9] + "' conversionQuantity='" + availableUnit[i][10] + "' >" + availableUnit[i][1] + "</option>");
					}
				}
				$("#ddlUnit").val(Data[20]);

				setTimeout(function() { $("#txtItemCode").focus(); }, 0);
			}
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				//$("#txtTransactionDate").focus();
				table2 = $("#grid-transaction").DataTable({
							"keys": true,
							"scrollY": "290px",
							"scrollX": false,
							"scrollCollapse": false,
							"paging": false,
							"searching": false,
							"order": [],
							"columns": [
								{ "width": "3%", "orderable": false, className: "dt-head-center dt-body-center" },
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "width": "15%", "orderable": false, className: "dt-head-center dt-body-center" },
								{ "visible": false },
								{ "width": "25%", "orderable": false, className: "dt-head-center" },
								{ "visible": false },
								{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
								{ "width": "15%", "orderable": false, className: "dt-head-center dt-body-right" },
								{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
								{ "width": "15%", "orderable": false, className: "dt-head-center dt-body-right" },
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "width" : "7%", "orderable" : false, className: "dt-head-center dt-body-center" }
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
							},
							"initComplete": function(settings, json) {
								setTimeout(function() {
									$("#grid-transaction").find("input:checkbox").remove();
								}, 0);
							}
						});

				table2.columns.adjust();
				var counterSaleDetails = 0;
				
				$('#grid-transaction tbody').on('dblclick', 'tr', function () {
					var data = table2.row(this).data();
					table2.cell.blur();
					table2.keys.disable();
					rowEdit = table2.row(this);
					openDialogEdit(data);
				});
				
				tableWidthAdjust();
			}

			var counterGetItem = 0;
			function getItemDetails() {
				if(counterGetItem == 0) {
					counterGetItem = 1;
					var itemCode = $("#txtItemCode").val();
					if(itemCode == "") $("#txtItemCode").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					else {
						$.ajax({
							url: "./Transaction/Sale/CheckItem.php",
							type: "POST",
							data: { itemCode : itemCode, branchID : $("#hdnBranchID").val() },
							dataType: "json",
							success: function(data) {
								if(data.FailedFlag == '0') {
									//if($("#hdnItemID").val() != data.ItemID) {
										if($("#hdnSaleDetailsID").val() != 0) {
											var itemDetailsTemp = table2.row( rowEdit ).data()[21];
											if(itemDetailsTemp == "") itemDetailsTemp = null;
										}

										if($("#hdnSaleDetailsID").val() != 0 && itemDetailsTemp === data.ItemDetailsID && table2.row( rowEdit ).data()[2] == data.ItemID) {
											$("#hdnStock").val((parseFloat(data.Stock) + parseFloat(table2.row( rowEdit ).data()[7])).toFixed(2));
											//console.log("mlebu 1");
										}
										else if($("#hdnSaleDetailsID").val() != 0 && itemDetailsTemp !== data.ItemDetailsID && table2.row( rowEdit ).data()[2] == data.ItemID) {
											var baseQTY = parseFloat(table2.row( rowEdit ).data()[22]) * parseFloat(table2.row( rowEdit ).data()[7]);
											$("#hdnStock").val((parseFloat(data.Stock) + (baseQTY / parseFloat(data.ConversionQty)) ).toFixed(2));
											//console.log("mlebu 2");
										}
										else {
											$("#hdnStock").val(parseFloat(data.Stock));
										}
										$("#hdnItemID").val(data.ItemID);
										$("#txtItemName").val(data.ItemName);
										$("#txtSalePrice").val(returnRupiah(data.RetailPrice));
										$("#hdnSalePrice").val(data.RetailPrice);
										$("#hdnBuyPrice").val(data.BuyPrice);
										$("#hdnRetailPrice").val(data.RetailPrice);
										$("#hdnPrice1").val(data.Price1);
										$("#hdnQty1").val(data.Qty1);
										$("#hdnPrice2").val(data.Price2);
										$("#hdnQty2").val(data.Qty2);
										$("#hdnWeight").val(data.Weight);
										$("#txtSubTotal").val(returnRupiah(data.RetailPrice));
										$("#hdnAvailableUnit").val(JSON.stringify(data.AvailableUnit));
										$("#hdnItemDetailsID").val(data.ItemDetailsID);
										$("#hdnConversionQty").val(data.ConversionQty);
										if(data.AvailableUnit.length > 0) {
											$("#ddlUnit").find('option').remove();
											for(var i=0;i<data.AvailableUnit.length;i++) {
												$("#ddlUnit").append("<option value=" + data.AvailableUnit[i][0] + " itemdetailsid='" + data.AvailableUnit[i][2] + "' itemcode='" + data.AvailableUnit[i][3] + "' buyprice='" + data.AvailableUnit[i][4] + "' retailprice='" + data.AvailableUnit[i][5] + "' price1='" + data.AvailableUnit[i][6] + "' price2='" + data.AvailableUnit[i][7] + "' qty1='" + data.AvailableUnit[i][8] + "' qty2='" + data.AvailableUnit[i][9] + "' conversionQuantity='" + data.AvailableUnit[i][10] + "' >" + data.AvailableUnit[i][1] + "</option>");
											}
										}
										$("#ddlUnit").val(data.UnitID);
										Grosir($("#txtQTY").val());
										CalculateSubTotal();
										$("#txtQTY").focus();
									//}
									//else $("#txtQTY").focus();
								}
								else {
									//add new item
									if(data.ErrorMessage == "") {
										$("#add-confirm").dialog({
											autoOpen: false,
											open: function() {
												$(document).on('keydown', function(e) {
													if (e.keyCode == 39) { //right arrow
														 $("#btnAddNo").focus();
													}
													else if (e.keyCode == 37) { //left arrow
														 $("#btnAddYes").focus();
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
											},
											resizable: false,
											height: "auto",
											width: 400,
											modal: true,
											buttons: [
											{
												text: "Ya",
												id: "btnAddYes",
												click: function() {
													$(this).dialog("destroy");
													addNewItem();
												}
											},
											{
												text: "Tidak",
												id: "btnAddNo",
												click: function() {
													$(this).dialog("destroy");
													$("#txtItemCode").focus();
												}
											}]
										}).dialog("open");
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
								LogEvent(errorMessage, "/Transaction/Sale/index.php");
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
			
			function Grosir(Quantity) {
				if($("#hdnItemID").val() != 0) {
					var retailPrice = parseFloat($("#hdnRetailPrice").val());
					var conversionQuantity = parseFloat($("#ddlUnit option:selected").attr("conversionQuantity"));
					if($("#hdnIsRetail").val() == 0) {
						var qty1 = parseFloat($("#hdnQty1").val());
						var price1 = parseFloat($("#hdnPrice1").val());
						var qty2 = parseFloat($("#hdnQty2").val());
						var price2 = parseFloat($("#hdnPrice2").val());
						if((parseFloat(Quantity) * conversionQuantity) >= qty2 && qty2 > 1) {
							$("#txtSalePrice").val(returnRupiah((price2 * conversionQuantity).toString()));
							$("#hdnSalePrice").val(price2);
						}
						else if((parseFloat(Quantity) * conversionQuantity) >= qty1 && qty1 > 1) {
							$("#txtSalePrice").val(returnRupiah((price1 * conversionQuantity).toString()));
							$("#hdnSalePrice").val(price1);
						}
						else {
							$("#txtSalePrice").val(returnRupiah((retailPrice * conversionQuantity).toString()));
							$("#hdnSalePrice").val(retailPrice);
						}
					}
					else {
						$("#txtSalePrice").val(returnRupiah((retailPrice * conversionQuantity).toString()));
						$("#hdnSalePrice").val(retailPrice);
					}
					CalculateSubTotal();
				}
			}
			
			function getSaleDetails(SaleID) {
				$.ajax({
					url: "./Transaction/Sale/SaleDetails.php",
					type: "POST",
					data: { SaleID : SaleID },
					dataType: "json",
					success: function(Data) {
						if(Data.FailedFlag == '0') {
							for(var i=0;i<Data.data.length;i++) {
								table2.row.add(Data.data[i]);
							}
							table2.draw();
							tableWidthAdjust();
							
							for(var i=0;i<Data.data.length;i++) {
								$("#toggle-branch-" + Data.data[i][1]).toggles({
									drag: true, // allow dragging the toggle between positions
									click: true, // allow clicking on the toggle
									text: {
										on: 'Toko', // text for the ON position
										off: 'Gudang' // and off
									},
									on: true, // is the toggle ON on init
									animate: 250, // animation time (ms)
									easing: 'swing', // animation transition easing function
									checkbox: null, // the checkbox to toggle (for use in forms)
									clicker: null, // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
									width: 80, // width used if not set in css
									height: 18, // height if not set in css
									type: 'compact' // if this is set to 'select' then the select style toggle will be used
								});
								
								if(Data.data[i][3] == 1) $("#toggle-branch-" + Data.data[i][1]).toggles(true);
								else $("#toggle-branch-" + Data.data[i][1]).toggles(false);
										
							}
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
						LogEvent(errorMessage, "/Transaction/Sale/index.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
			}

			function deleteItem(SaleDetailsID) {
				table2.keys.disable();
				var deletedData = new Array();
				deletedData.push(SaleDetailsID);
				var index = 0;
				table2.rows().eq( 0 ).filter( function (rowIdx) {
				    if(table2.cell( rowIdx, 1 ).data() == SaleDetailsID) index = rowIdx;
				});
				//console.log(indexes);
				SingleDelete("./Transaction/Sale/DeleteDetails.php", deletedData, function(action) {
					if(action == "success") {
						table2.row( index ).remove().draw();
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
			
			function promptCode() {
				var code = Math.floor(100000 + Math.random() * 900000);
				$("#lblCode").html(code);
				$("#code-dialog").dialog({
					autoOpen: false,
					open: function() {
						$("#divModal").show();
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //right arrow
								$("#btnCancelPromptCode").focus();
							}
							else if(e.keyCode == 37 && $("input:focus").length == 0 && $("#btnOK:focus").length == 0) { //left arrow
								$("#btnPromptCode").focus();
							}
						});
					},
					
					close: function() {
						$(this).dialog("destroy");
						$("#divModal").hide();
						$("#txtItemCode").focus();
					},
					resizable: false,
					height: 250,
					width: 450,
					modal: false,
					buttons: [
					{
						text: "Konfirmasi Kode",
						id: "btnPromptCode",
						tabindex: 61,
						click: function() {
							var txtCode = $("#txtCode").val();
							if(txtCode == code) {
								$(this).dialog("destroy");
								$("#divModal").hide();
								$("#txtCode").val("");
								var itemID = $("#hdnItemID").val();
								var itemCode = $("#txtItemCode").val();
								var itemName = $("#txtItemName").val();
								var unitID = $("#ddlUnit").val();
								var unitName = $("#ddlUnit option:selected").text();
								var Qty = $("#txtQTY").val();
								var salePrice = $("#txtSalePrice").val();
								var buyPrice = $("#hdnBuyPrice").val();
								var price1 = $("#hdnPrice1").val();
								var qty1 = $("#hdnQty1").val();
								var price2 = $("#hdnPrice2").val();
								var qty2 = $("#hdnQty2").val();
								var discount = $("#txtDiscount").val();
								var branchID = $("#hdnBranchID").val();
								var weight = $("#hdnWeight").val();
								var retailPrice = $("#hdnRetailPrice").val();
								var availableUnit = $("#hdnAvailableUnit").val();
								var unitID = $("#ddlUnit").val();
								var itemDetailsID = $("#hdnItemDetailsID").val();
								$("#txtDiscount").blur();
								var PassValidate = 1;
								var FirstFocus = 0;
								var ConversionQty = $("#hdnConversionQty").val();
								var Stock = $("#hdnStock").val();
								$.ajax({
									url: "./Transaction/Sale/Insert.php",
									type: "POST",
									data: $("#PostForm").serialize(),
									dataType: "json",
									success: function(data) {
										if(data.FailedFlag == '0') {
											if($("#hdnSaleDetailsID").val() == 0) {
												//$("#toggle-retail").toggleClass('disabled', true);
												$("#hdnSaleNumber").val(data.SaleNumber);
												$("#lblSaleNumber").html(data.SaleNumber);
												var toggleBranch = "<div id='toggle-branch-" + data.SaleDetailsID + "' onclick='updateBranch(this.id)' class='div-center toggle-modern' ></div>";
												var checkboxData = "<input type='checkbox' class='chkSaleDetails' name='select' value='" + data.SaleDetailsID + "' style='margin:0;' />"
												var deleteData = '<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;"><i style="cursor:pointer;" class="fa fa-trash fa-2x" acronym title="Hapus Data" onclick="deleteItem(' + data.SaleDetailsID + ');"></i>';

												table2.row.add([
													checkboxData,
													data.SaleDetailsID,
													itemID,
													branchID,
													toggleBranch,
													itemCode,
													itemName,
													Qty,
													Qty + " " + unitName,
													salePrice,
													returnRupiah(discount.toString()),
													returnRupiah(((parseFloat(salePrice.replace(/\,/g, "")) - parseFloat(discount.replace(/\,/g, "")) ) * parseFloat(Qty)).toString()),
													buyPrice,
													price1,
													qty1,
													price2,
													qty2,
													weight,
													retailPrice,
													availableUnit,
													unitID,
													itemDetailsID,
													ConversionQty,
													deleteData
												]).draw();
												
												$("#toggle-branch-" + data.SaleDetailsID).toggles({
													drag: true, // allow dragging the toggle between positions
													click: true, // allow clicking on the toggle
													text: {
														on: 'Toko', // text for the ON position
														off: 'Gudang' // and off
													},
													on: true, // is the toggle ON on init
													animate: 250, // animation time (ms)
													easing: 'swing', // animation transition easing function
													checkbox: null, // the checkbox to toggle (for use in forms)
													clicker: null, // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
													width: 80, // width used if not set in css
													height: 18, // height if not set in css
													type: 'compact' // if this is set to 'select' then the select style toggle will be used
												});
												if($("#hdnBranchID").val() == 1) {
													$("#toggle-branch-" + data.SaleDetailsID).toggles(true);
												}
												else {
													$("#toggle-branch-" + data.SaleDetailsID).toggles(false);
												}
											}
											else {
												var toggles = $('#toggle-branch-' + data.SaleDetailsID).data('toggles').active;
												var checkboxData = "<input type='checkbox' class='chkSaleDetails' name='select' value='" + data.SaleDetailsID + "' style='margin:0;' />"
												var deleteData = '<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;"><i style="cursor:pointer;" class="fa fa-trash fa-2x" acronym title="Hapus Data" onclick="deleteItem(' + data.SaleDetailsID + ');"></i>';
												table2.row(rowEdit).data([
													checkboxData,
													data.SaleDetailsID,
													itemID,
													branchID,
													table2.row( rowEdit ).data()[4],
													itemCode,
													itemName,
													Qty,
													Qty + " " + unitName,
													salePrice,
													returnRupiah(discount.toString()),
													returnRupiah(((parseFloat(salePrice.replace(/\,/g, "")) - parseFloat(discount.replace(/\,/g, "")) ) * parseFloat(Qty)).toString()),
													buyPrice,
													price1,
													qty1,
													price2,
													qty2,
													weight,
													retailPrice,
													availableUnit,
													unitID,
													itemDetailsID,
													ConversionQty,
													deleteData
												]).draw();
												
												$("#toggle-branch-" + data.SaleDetailsID).toggles({
													drag: true, // allow dragging the toggle between positions
													click: true, // allow clicking on the toggle
													text: {
														on: 'Toko', // text for the ON position
														off: 'Gudang' // and off
													},
													on: true, // is the toggle ON on init
													animate: 250, // animation time (ms)
													easing: 'swing', // animation transition easing function
													checkbox: null, // the checkbox to toggle (for use in forms)
													clicker: null, // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
													width: 80, // width used if not set in css
													height: 18, // height if not set in css
													type: 'compact' // if this is set to 'select' then the select style toggle will be used
												});
												
												$("#toggle-branch-" + data.SaleDetailsID).toggles(toggles);
												
												table2.keys.enable();
											}
											$("#txtItemCode").val("");
											$("#txtItemName").val("");
											$("#txtQTY").val(1);
											$("#txtSalePrice").val(0);
											$("#txtDiscount").val(0);
											$("#hdnBuyPrice").val(0);
											$("#hdnSalePrice").val(0);
											$("#hdnPrice1").val(0);
											$("#hdnQty1").val(0);
											$("#hdnPrice2").val(0);
											$("#hdnQty2").val(0);
											$("#hdnBranchID").val(1);
											$("#txtItemCode").focus();
											$("#hdnSaleID").val(data.ID);
											$("#hdnSaleDetailsID").val(0);
											$("#hdnItemID").val(0);
											$("#hdnWeight").val(0);
											$("#hdnRetailPrice").val(0);
											$("#txtSubTotal").val(0);
											$("#hdnStock").val(0);
											$("#ddlUnit").find('option').remove();
											$("#ddlUnit").append("<option>--</option>");
											$("#hdnAvailableUnit").val("");
											$("#hdnItemDetailsID").val(0);
											$("#hdnConversionQty").val(0);
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
															if(data.Message == "No. Invoice sudah ada") $("#txtSaleNumber").focus();
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
										$("#divModal").hide();
										var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
										LogEvent(errorMessage, "/Transaction/Sale/index.php");
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
								$("#txtCode").notify("Kode salah!", { position:"right", className:"warn", autoHideDelay: 2000 });
								$("#txtCode").focus();
							}
						}
					},
					{
						text: "Batal",
						id: "btnCancelPromptCode",
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
							$("#txtItemCode").focus();
							return false;
						}
					}]
				}).dialog("open");
			}

			var counterAddSale = 0;
			function addSaleDetails() {
				if(counterAddSale == 0) {
					counterAddSale = 1;
					var itemID = $("#hdnItemID").val();
					var itemCode = $("#txtItemCode").val();
					var itemName = $("#txtItemName").val();
					var unitID = $("#ddlUnit").val();
					var unitName = $("#ddlUnit option:selected").text();
					var Qty = parseFloat($("#txtQTY").val());
					var salePrice = $("#txtSalePrice").val();
					var buyPrice = $("#hdnBuyPrice").val();
					var price1 = $("#hdnPrice1").val();
					var qty1 = $("#hdnQty1").val();
					var price2 = $("#hdnPrice2").val();
					var qty2 = $("#hdnQty2").val();
					var discount = $("#txtDiscount").val();
					var branchID = $("#hdnBranchID").val();
					var weight = $("#hdnWeight").val();
					var retailPrice = $("#hdnRetailPrice").val();
					var availableUnit = $("#hdnAvailableUnit").val();
					var unitID = $("#ddlUnit").val();
					var itemDetailsID = $("#hdnItemDetailsID").val();
					$("#txtDiscount").blur();
					var PassValidate = 1;
					var FirstFocus = 0;
					var Stock = parseFloat($("#hdnStock").val());
					var ConversionQty = $("#hdnConversionQty").val();
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

					if($("#ddlCustomer").val() == "" || !$("#ddlCustomer").val()) {
						PassValidate = 0;
						$("#ddlCustomer").notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#ddlCustomer").focus();
						FirstFocus = 1;
					}
					
					if($("#hdnItemID").val() == 0) {
						PassValidate = 0;
						$("#txtItemCode").notify("Kode barang tidak valid!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtItemCode").focus();
					}

					if( (parseFloat(buyPrice) * parseFloat(ConversionQty)) > (parseFloat(salePrice.replace(/\,/g, "")) - parseFloat(discount.replace(/\,/g, ""))) ) {
						PassValidate = 0;
						$("#txtDiscount").notify("Harga sesudah diskon lebih kecil dari harga beli!", { position:"bottom right", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) {
							setTimeout(function() {
								$("#txtDiscount").focus();
							}, 0);
						}
						FirstFocus = 1;
					}
					
					if(PassValidate == 1) {
						if((Stock - Qty) < 0) {
							promptCode();
						}
						else {
							$.ajax({
								url: "./Transaction/Sale/Insert.php",
								type: "POST",
								data: $("#PostForm").serialize(),
								dataType: "json",
								success: function(data) {
									if(data.FailedFlag == '0') {
										if($("#hdnSaleDetailsID").val() == 0) {
											//$("#toggle-retail").toggleClass('disabled', true);
											$("#hdnSaleNumber").val(data.SaleNumber);
											$("#lblSaleNumber").html(data.SaleNumber);
											var toggleBranch = "<div id='toggle-branch-" + data.SaleDetailsID + "' onclick='updateBranch(this.id)' class='div-center toggle-modern' ></div>";
											var checkboxData = "<input type='checkbox' class='chkSaleDetails' name='select' value='" + data.SaleDetailsID + "' style='margin:0;' />"
											var deleteData = '<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;"><i style="cursor:pointer;" class="fa fa-trash fa-2x" acronym title="Hapus Data" onclick="deleteItem(' + data.SaleDetailsID + ');"></i>';
												
											table2.row.add([
												checkboxData,
												data.SaleDetailsID,
												itemID,
												branchID,
												toggleBranch,
												itemCode,
												itemName,
												Qty,
												Qty + " " + unitName,
												salePrice,
												returnRupiah(discount.toString()),
												returnRupiah(((parseFloat(salePrice.replace(/\,/g, "")) - parseFloat(discount.replace(/\,/g, "")) ) * parseFloat(Qty)).toString()),
												buyPrice,
												price1,
												qty1,
												price2,
												qty2,
												weight,
												retailPrice,
												availableUnit,
												unitID,
												itemDetailsID,
												ConversionQty,
												deleteData
											]).draw();
											
											$("#toggle-branch-" + data.SaleDetailsID).toggles({
												drag: true, // allow dragging the toggle between positions
												click: true, // allow clicking on the toggle
												text: {
													on: 'Toko', // text for the ON position
													off: 'Gudang' // and off
												},
												on: true, // is the toggle ON on init
												animate: 250, // animation time (ms)
												easing: 'swing', // animation transition easing function
												checkbox: null, // the checkbox to toggle (for use in forms)
												clicker: null, // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
												width: 80, // width used if not set in css
												height: 18, // height if not set in css
												type: 'compact' // if this is set to 'select' then the select style toggle will be used
											});

											if($("#hdnBranchID").val() == 1) {
												$("#toggle-branch-" + data.SaleDetailsID).toggles(true);
											}
											else {
												$("#toggle-branch-" + data.SaleDetailsID).toggles(false);
											}
										}
										else {
											var toggles = $('#toggle-branch-' + data.SaleDetailsID).data('toggles').active;
											var checkboxData = "<input type='checkbox' class='chkSaleDetails' name='select' value='" + data.SaleDetailsID + "' style='margin:0;' />"
											var deleteData = '<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;"><i style="cursor:pointer;" class="fa fa-trash fa-2x" acronym title="Hapus Data" onclick="deleteItem(' + data.SaleDetailsID + ');"></i>';
												
											table2.row(rowEdit).data([
												checkboxData,
												data.SaleDetailsID,
												itemID,
												branchID,
												table2.row( rowEdit ).data()[4],
												itemCode,
												itemName,
												Qty,
												Qty + " " + unitName,
												salePrice,
												returnRupiah(discount.toString()),
												returnRupiah(((parseFloat(salePrice.replace(/\,/g, "")) - parseFloat(discount.replace(/\,/g, "")) ) * parseFloat(Qty)).toString()),
												buyPrice,
												price1,
												qty1,
												price2,
												qty2,
												weight,
												retailPrice,
												availableUnit,
												unitID,
												itemDetailsID,
												ConversionQty,
												deleteData
											]).draw();
											
											$("#toggle-branch-" + data.SaleDetailsID).toggles({
												drag: true, // allow dragging the toggle between positions
												click: true, // allow clicking on the toggle
												text: {
													on: 'Toko', // text for the ON position
													off: 'Gudang' // and off
												},
												on: true, // is the toggle ON on init
												animate: 250, // animation time (ms)
												easing: 'swing', // animation transition easing function
												checkbox: null, // the checkbox to toggle (for use in forms)
												clicker: null, // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
												width: 80, // width used if not set in css
												height: 18, // height if not set in css
												type: 'compact' // if this is set to 'select' then the select style toggle will be used
											});
											
											$("#toggle-branch-" + data.SaleDetailsID).toggles(toggles);
											
											table2.keys.enable();
										}
										$("#txtItemCode").val("");
										$("#txtItemName").val("");
										$("#txtQTY").val(1);
										$("#txtSalePrice").val(0);
										$("#txtDiscount").val(0);
										$("#hdnBuyPrice").val(0);
										$("#hdnSalePrice").val(0);
										$("#hdnPrice1").val(0);
										$("#hdnQty1").val(0);
										$("#hdnPrice2").val(0);
										$("#hdnQty2").val(0);
										$("#hdnBranchID").val(1);
										$("#txtItemCode").focus();
										$("#hdnSaleID").val(data.ID);
										$("#hdnSaleDetailsID").val(0);
										$("#hdnItemID").val(0);
										$("#hdnWeight").val(0);
										$("#hdnRetailPrice").val(0);
										$("#txtSubTotal").val(0);
										$("#hdnStock").val(0);
										$("#ddlUnit").find('option').remove();
										$("#ddlUnit").append("<option>--</option>");
										$("#hdnAvailableUnit").val("");
										$("#hdnItemDetailsID").val(0);
										$("#hdnConversionQty").val(0);
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
														if(data.Message == "No. Invoice sudah ada") $("#txtSaleNumber").focus();
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
									LogEvent(errorMessage, "/Transaction/Sale/index.php");
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
				}
				setTimeout(function() { counterAddSale = 0; }, 1000);
			}

			function updateHeader() {
				if($("#hdnSaleID").val() != 0) {
					var SaleID = $("#hdnSaleID").val();
					var TransactionDate = $("#hdnTransactionDate").val();
					var CustomerID = $("#ddlCustomer").val();
					$.ajax({
						url: "./Transaction/Sale/UpdateHeader.php",
						type: "POST",
						data: { SaleID : SaleID, TransactionDate : TransactionDate, CustomerID : CustomerID },
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
			}
			
			function updateBranch(SaleDetailsID) {
				setTimeout(function() {
					var BranchID = 1;
					var str = SaleDetailsID.split("-");
					if($('#toggle-branch-' + str[2]).data('toggles').active == false) BranchID = 2;
					$("#loading").show();
					$.ajax({
						url: "./Transaction/Sale/UpdateBranch.php",
						type: "POST",
						data: { SaleDetailsID : str[2], BranchID : BranchID },
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
				}, 0);
			}
			
			function addNewItem() {
				var itemCode = $("#txtItemCode").val();
				Lobibox.window({
					title: 'Tambah Barang',
					url: 'Transaction/Sale/PopUpAddItem.php',
					width: 780,
					height: 560,
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
								$("#txtItemNameAdd").focus();
								$("#txtItemCodeAdd").val(itemCode);
								$("#btnSimpan").attr("tabindex", 28);
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
							
							$("#FormItem .form-control-custom").each(function() {
								if($(this).hasAttr('required')) {
									if($(this).val() == "") {
										PassValidate = 0;
										$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
										if(FirstFocus == 0) $(this).focus();
										FirstFocus = 1;
									}
								}
							});

							if($("#ddlCategoryAdd").val() == "") {
								PassValidate = 0;
								$("#ddlCategoryAdd").next().find("input").notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $("#ddlCategoryAdd").next().find("input").focus();
								FirstFocus = 1;
							}

							if($("#hdnTabsCounter").val() > 0) {
								for(var j=1;j<=$("#hdnTabsCounter").val();j++) {
									if($("#ddlUnitAdd").val() == $("#ddlUnit_" + j).val()) {
										PassValidate = 0;
										if(FirstFocus == 0) {
											tabs.tabs("option", "active", j);
											$("#ddlUnit_" + j).notify("Tidak boleh sama!", { position:"right", className:"warn", autoHideDelay: 2000 });
											$("#ddlUnit_" + j).focus();
											FirstFocus = 1;
										}
									}
									else if($("#txtItemCodeAdd").val() == $("#txtItemCode_" + j).val()) {
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
												url: "./Transaction/Sale/InsertNewItem.php",
												type: "POST",
												data: $("#PostFormItem").serialize(),
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
																	getItemDetails();
																}, 0);
															},
															shown: function() {
																setTimeout(function() {
																	var lobibox = $('.lobibox-window').data('lobibox');
																	lobibox.destroy();
																	$("#btnOK").focus();
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
																		$("#txtItemNameAdd").focus();
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
									},
									{
										text: "Tidak",
										id: "btnNo",
										click: function() {
											$(this).dialog("destroy");
											$("#txtItemNameAdd").focus();
											//callback("Tidak");
										}
									}]
								}).dialog("open");
							}
						}
					}
				});
			}
			
			function addNewCustomer() {
				Lobibox.window({
					title: 'Tambah Pelanggan',
					url: 'Transaction/Sale/PopUpAddCustomer.php',
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
								$("#txtCustomerCodeAdd").focus();
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

							$("#FormCustomer .form-control-custom").each(function() {
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
												url: "./Transaction/Sale/InsertNewCustomer.php",
												type: "POST",
												data: $("#PostFormCustomer").serialize(),
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
																	$("#ddlCustomer").append("<option value=" + data.ID + " >" + $("#txtCustomerCodeAdd").val() + " - " + $("#txtCustomerNameAdd").val() + "</option>");
																	$("#ddlCustomer").val(data.ID);
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
																		$("#txtCustomerCodeAdd").focus();
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
													LogEvent(errorMessage, "/Transaction/Sale/index.php");
													Lobibox.alert("error",
													{
														msg: errorMessage,
														width: 480,
														beforeClose: function() {
															if(counter == 0) {
																setTimeout(function() {
																	$("#txtCustomerCodeAdd").focus();
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
											$("#txtCustomerCodeAdd").focus();
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
				var weight = 0;
				table2.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
					var data = this.data();
					grandTotal += (parseFloat(data[9].replace(/\,/g, "")) - parseFloat(data[10].replace(/\,/g, ""))) * parseFloat(data[7]);
					weight += (parseFloat(data[17]) * parseFloat(data[7]) * parseFloat(data[22]));
				});
				$("#lblTotal").html(returnRupiah(grandTotal.toString()));
				$("#lblWeight").html(returnWeight(weight.toFixed(2).toString()));
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
				$("#hdnSaleID").val(0);
				$("#hdnSaleDetailsID").val(0);
				$("#hdnItemID").val(0);
				$("#txtTransactionDate").datepicker("setDate", new Date());
				$("#txtSaleNumber").val("");
				$("#hdnSaleNumber").val("");
				$("#txtItemCode").val("");
				$("#txtItemName").val("");
				$("#txtQTY").val(1);
				$("#txtSalePrice").val(0);
				$("#hdnBuyPrice").val(0);
				$("#hdnSalePrice").val(0);
				$("#hdnWeight").val(0);
				$("#hdnPrice1").val(0);
				$("#hdnQty1").val(0);
				$("#hdnQty2").val(0);
				$("#hdnBranchID").val(1);
				//$("#toggle-retail").toggleClass('disabled', false);
				$('#toggle-retail').toggles(true);
				$("#lblTotal").html("0");
				$("#lblWeight").html("0");
				$("#hdnPayment").html("0");
				$("#hdnRetailPrice").val(0);
				if( $("#ddlCustomer").has("option").length > 0 ) $("#ddlCustomer option")[0].selected = true;
				$("#ddlUnit").find('option').remove();
				$("#ddlUnit").append("<option>--</option>");
				$("#hdnAvailableUnit").val("");
				$("#hdnItemDetailsID").val(0);
				$("#txtSubTotal").val(0);
				$("#hdnStock").val(0);
				$("#hdnConversionQty").val(0);
				table2.clear().draw();
			}

			function branchChange(BranchID) {
				$("#hdnBranchID").val(BranchID);
				table3.ajax.reload();
			}
			
			function itemList() {
				$("#itemList-dialog").dialog({
					autoOpen: false,
					open: function() {
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
									"ajax": "./Transaction/Sale/ItemList.php",
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
								getItemDetails();
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
					},
					resizable: false,
					height: 500,
					width: 960,
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

			function newWindow() {
				var mobilePath = $("#hdnMobilePath").val();
				window.open(mobilePath, "", "width=1000");
				document.documentElement.webkitRequestFullscreen();
			}

			function finish() {
				if($("#hdnSaleID").val() != 0) {
					$("#finish-dialog").dialog({
						autoOpen: false,
						open: function() {
							//$("#divModal").show();
							table2.keys.disable();
							var Total = $("#lblTotal").html().replace(/\,/g, "");
							var Payment = $("#hdnPayment").val();
							var PaymentType = $("#hdnPaymentType").val();
							var Change = 0;
							if(parseFloat(Payment) != 0) {
								if(PaymentType == 1) {
									Change = parseFloat(Payment) - parseFloat(Total);
									$("#lblChange").html("Kembali :");
								}
								else {
									Change = parseFloat(Total) - parseFloat(Payment);
									$("#lblChange").html("Kekurangan :");
								}
								$("#txtChange").val(returnRupiah(Change.toString()));
							}
							if(parseFloat(PaymentType) == 0) PaymentType = 1;
							$("#txtTotal").val($("#lblTotal").html());
							$("#txtPayment").val(returnRupiah(Payment.toString()));
							$("#ddlPayment").val(PaymentType);
							$("#ddlPayment").focus();
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
							table2.keys.enable();
							//$("#divModal").hide();
							$("#txtPayment").val(0);
							$("#ddlPayment").val(1);
							$("#txtChange").val(0);
						},
						resizable: false,
						height: 340,
						width: 420,
						modal: true /*,
						buttons: [
						{
							text: "Tutup",
							tabindex: 19,
							id: "btnCancelPickItem",
							click: function() {
								$(this).dialog("destroy");
								table2.keys.enable();
								$("#divModal").hide();
								return false;
							}
						}]*/
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

			function PaymentTypeChange() {
				var Total = $("#txtTotal").val().replace(/\,/g, "");
				var Payment = $("#txtPayment").val().replace(/\,/g, "");
				var PaymentType = $("#ddlPayment").val();
				if(PaymentType == 1) {
					$("#lblChange").html("Kembali :");
					if(parseFloat(Payment) != 0) {
						if(parseFloat(Total) > parseFloat(Payment)) {
							$("#txtPayment").notify("Pembayaran Kurang!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							setTimeout(function() {
								$("#txtPayment").focus();
							}, 0);
						}
						else {
							var Change = parseFloat(Payment) - parseFloat(Total);
							$("#txtChange").val(returnRupiah(Change.toString()));
						}
					}
				}
				else {
					$("#lblChange").html("Kekurangan :");
					if(parseFloat(Total) < parseFloat(Payment)) {
						$("#txtPayment").notify("Pembayaran Lebih!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						setTimeout(function() {
							$("#txtPayment").focus();
						}, 0);
					}
					else {
						var Change = parseFloat(Total) - parseFloat(Payment);
						$("#txtChange").val(returnRupiah(Change.toString()));
					}
				}
			}

			function Change() {
				var Total = $("#txtTotal").val().replace(/\,/g, "");
				var Payment = $("#txtPayment").val().replace(/\,/g, "");
				var PaymentType = $("#ddlPayment").val();
				if(PaymentType == 1) {
					$("#lblChange").html("Kembali :");
					if(parseFloat(Payment) == 0) {
						$("#txtPayment").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						setTimeout(function() {
							$("#txtPayment").focus();
						}, 0);
					}
					else {
						if(parseFloat(Total) > parseFloat(Payment)) {
							$("#txtPayment").notify("Pembayaran Kurang!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							setTimeout(function() {
								$("#txtPayment").focus();
							}, 0);
						}
						else {
							var Change = parseFloat(Payment) - parseFloat(Total);
							$("#txtChange").val(returnRupiah(Change.toString()));
						}
					}
				}
				else {
					$("#lblChange").html("Kekurangan :");
					if(parseFloat(Total) < parseFloat(Payment)) {
						$("#txtPayment").notify("Pembayaran Lebih!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						setTimeout(function() {
							$("#txtPayment").focus();
						}, 0);
					}
					else {
						var Change = parseFloat(Total) - parseFloat(Payment);
						$("#txtChange").val(returnRupiah(Change.toString()));
					}
				}
			}

			function printInvoice() {
				var Total = $("#txtTotal").val().replace(/\,/g, "");
				var Payment = $("#txtPayment").val().replace(/\,/g, "");
				var PaymentType = $("#ddlPayment").val();
				var PassValidate = 1;
				if(PaymentType == 1) {
					$("#lblChange").html("Kembali :");
					if(parseFloat(Payment) == 0) {
						$("#txtPayment").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						setTimeout(function() {
							$("#txtPayment").focus();
						}, 0);
						PassValidate = 0;
					}
					else {
						$("#lblChange").html("Kekurangan :");
						if(parseFloat(Total) > parseFloat(Payment)) {
							$("#txtPayment").notify("Pembayaran Kurang!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							setTimeout(function() {
								$("#txtPayment").focus();
							}, 0);
							PassValidate = 0;
						}
						else {
							var Change = parseFloat(Payment) - parseFloat(Total);
							$("#txtChange").val(returnRupiah(Change.toString()));
						}
					}
				}
				else {
					if(parseFloat(Total) < parseFloat(Payment)) {
						$("#txtPayment").notify("Pembayaran Lebih!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						setTimeout(function() {
							$("#txtPayment").focus();
						}, 0);
						PassValidate = 0;
					}
					else {
						var Change = parseFloat(Total) - parseFloat(Payment);
						$("#txtChange").val(returnRupiah(Change.toString()));
					}
				}

				if(PassValidate == 1) {
					var saleID = $("#hdnSaleID").val();
					var SaleNumber = $("#hdnSaleNumber").val();
					var Payment = $("#txtPayment").val().replace(/\,/g, "");
					var PaymentType = $("#ddlPayment").val();
					var PrintInvoice = $("#chkPrint").prop("checked");
					var PrintShipment = $("#chkPrintShipment").prop("checked");
					var Change = $("#txtChange").val().replace(/\,/g, "");
					var PaymentMethod = $("#ddlPayment option:selected").text();
					var TransactionDate = $("#hdnTransactionDate").val();
					$("#loading").show();
					$.ajax({
						url: "./Transaction/Sale/PrintInvoice.php",
						type: "POST",
						data: { SaleID : saleID, Payment : Payment, PaymentType : PaymentType, PrintInvoice : PrintInvoice, Change: Change, SaleNumber : SaleNumber, PaymentMethod : PaymentMethod, TransactionDate : TransactionDate },
						dataType: "json",
						success: function(data) {
							if(data.FailedFlag == '0') {
								$("#loading").hide();
								$("#divModal").hide();
								if(PrintShipment == true) printShipment();
								resetForm();
								//table2.destroy();
								$("#finish-dialog").dialog("destroy");
								//$("#FormData").dialog("destroy");
								var paymentInfo = "<table><tr><td align='right'>Pembayaran :&nbsp;</td><td>" + $("#ddlPayment option:selected").text() + "</td></tr>";
								paymentInfo += "<tr><td align='right'>Total :&nbsp;</td><td align='right'>" + returnRupiah(Total) + "</td></tr>";
								paymentInfo += "<tr><td align='right'>Bayar :&nbsp;</td><td align='right'>" + returnRupiah(Payment) + "</td></tr>";
								if(PaymentType == 1) paymentInfo += "<tr><td align='right'>Kembali :&nbsp;</td><td align='right'>" + $("#txtChange").val() + "</td></tr></table>";
								else paymentInfo += "<tr><td align='right'>Kekurangan :&nbsp;</td><td align='right'>" + $("#txtChange").val() + "</td></tr></table>";
								var counter = 0;
								$("#txtPayment").val(0);
								$("#ddlPayment").val(1);
								$("#txtChange").val(0);
								Lobibox.alert("success",
								{
									msg: paymentInfo,
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
							else {
								$("#loading").hide();
								$("#divModal").hide();
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
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$("#loading").hide();
							$("#divModal").hide();
							var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
							LogEvent(errorMessage, "/Transaction/Sale/index.php");
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

			function printShipment() {
				//alert("print shipment");
				var SaleDetailsID = new Array();
				var SaleID = $("#hdnSaleID").val();
				$("input:checkbox[name=select]:checked").each(function() {
					if($(this).val() != 'all') SaleDetailsID.push($(this).val());
				});
				if(SaleDetailsID.length > 0) {
					$("#loading").show();
					$.ajax({
						url: "./Transaction/Sale/PrintShipment.php",
						type: "POST",
						data: { SaleDetailsID : SaleDetailsID, SaleID : SaleID },
						dataType: "json",
						success: function(data) {
							$("#loading").hide();
						},
						error: function(data) {
							$("#loading").hide();
							var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
							LogEvent(errorMessage, "/Transaction/Sale/index.php");
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

		    function firstBalance() {
				$("#first-balance").dialog({
					autoOpen: false,
					open: function() {
						$("#divModal").show();
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0) { //right arrow
								 $("#btnCancelFirstBalance").focus();
							}
							else if (e.keyCode == 37 && $("input:focus").length == 0) { //left arrow
								 $("#btnSaveFirstBalance").focus();
							}
						});
						$("#txtFirstBalance").focus();
					},
					
					close: function() {
						$(this).dialog("destroy");
						$("#divModal").hide();
					},
					resizable: false,
					height: 150,
					width: 400,
					modal: false,
					buttons: [
					{
						text: "Simpan",
						tabindex: 51,
						id: "btnSaveFirstBalance",
						click: function() {
							$.ajax({
								url: "./InsertFirstBalance.php",
								type: "POST",
								data: $("#FirstBalanceForm").serialize(),
								dataType: "json",
								success: function(data) {
									$("#divModal").hide();
									if(data.FailedFlag == '0') {
										//$.notify(data.Message, "success");
										$("#first-balance").dialog("destroy");
										$("#txtFirstBalance").val("0.00");
										$("#divModal").hide();
										Lobibox.alert("success",
										{
											msg: data.Message,
											width: 480,
											delay: 2000
										});
									}
									else {
										$("#divModal").hide();
										Lobibox.alert("warning",
										{
											msg: data.Message,
											width: 480,
											delay: false
										});
									}
									
								},
								error: function(jqXHR, textStatus, errorThrown) {
									$("#divModal").hide();
									var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
									LogEvent(errorMessage, "Home.php (fnFirstBalance)");
									Lobibox.alert("error",
									{
										msg: errorMessage,
										width: 480
									});
									return 0;
								}
							});
						}
					},
					{
						text: "Tutup",
						tabindex: 52,
						id: "btnCancelFirstBalance",
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
							return false;
						}
					}]
				}).dialog("open");
			}

			$(document).ready(function() {
				$("#txtQTY").spinner();
				$('#grid-transaction').on('click', 'input[type="checkbox"]', function() {
					$(this).blur();
				});
				$("#txtQTY").spinner();
				openDialog(0, 0);
				/*$('#toggle-retail').toggles({
					drag: true, // allow dragging the toggle between positions
					click: true, // allow clicking on the toggle
					text: {
						on: 'Eceran', // text for the ON position
						off: 'Grosir' // and off
					},
					on: true, // is the toggle ON on init
					animate: 250, // animation time (ms)
					easing: 'swing', // animation transition easing function
					checkbox: null, // the checkbox to toggle (for use in forms)
					clicker: null, // element that can be clicked on to toggle. removes binding from the toggle itself (use nesting)
					width: 80, // width used if not set in css
					height: 23, // height if not set in css
					type: 'compact' // if this is set to 'select' then the select style toggle will be used
				});
				
				$('#toggle-retail').on('toggle', function(e, active) {
					if (active) {
						$("#hdnIsRetail").val(1);
						//if($("#FormData").css("display") == "block") $('#FormData').dialog('option', 'title', 'Tambah Penjualan Eceran');
					} else {
						$("#hdnIsRetail").val(0);
						//if($("#FormData").css("display") == "block") $('#FormData').dialog('option', 'title', 'Tambah Penjualan Grosir');
						Grosir($("#txtQTY").val());
					}
				});*/

				$("#txtQTY").spinner({
					change: function() {
						CalculateSubTotal();
						Grosir($("#txtQTY").val());
					}
				});

				$("#btnGrosir").on("click", function() {
					if ($("#hdnIsRetail").val() == 0) {
						$("#hdnIsRetail").val(1);
						$('#headTitle').html('Transaksi Penjualan Eceran');
						$(this).html('<i class="fa fa-cart-arrow-down fa-2x"></i> Grosir');
						Grosir($("#txtQTY").val());
					} else {
						$("#hdnIsRetail").val(0);
						$('#headTitle').html('Transaksi Penjualan Grosir');
						Grosir($("#txtQTY").val());
						$(this).html('<i class="fa fa-cart-arrow-down fa-2x"></i> Eceran');
					}
				});
				
				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Transaction/Sale/index.php");
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
				var counterSale = 0;

				$.ajax({
					url: "./FirstBalance.php",
					type: "POST",
					data: { },
					dataType: "json",
					success: function(Data) {
						if(Data.FailedFlag == '0') {
							if(Data.IsFilled == 0) firstBalance();
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
						LogEvent(errorMessage, "/Home.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
			});
		</script>
	</body>
</html>