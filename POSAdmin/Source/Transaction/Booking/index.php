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
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <span style="width:50%;display:inline-block;">
							 <h5>Pemesanan</h5>
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
										<th>Customer</th>
										<th>Total</th>
									</tr>
								</thead>
							</table>
						</div>
						<br />
						<div class="row col-md-12" >
							<h5 style="margin-top:5px !important;margin-bottom: 5px !important;">INSERT = Tambah Data; ENTER/DOUBLE KLIK = Edit; DELETE = Hapus; SPASI = Menandai Data;</h5>
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
						<input id="hdnBookingID" name="hdnBookingID" type="hidden" value=0 />
						<input id="hdnBookingDetailsID" name="hdnBookingDetailsID" type="hidden" value=0 />
						<input id="hdnItemID" name="hdnItemID" type="hidden" value=0 />
						<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
					</div>
					<div class="col-md-2">
						<input id="txtBookingNumber" name="txtBookingNumber" type="text" tabindex=5 class="form-control-custom" onfocus="this.select();" autocomplete=off placeholder="No. Invoice" readonly />
					</div>
					
					<div class="col-md-1 labelColumn">
						Tanggal :
					</div>
					<div class="col-md-2">
						<input id="txtTransactionDate" name="txtTransactionDate" type="text" tabindex=6 class="form-control-custom" style="width: 87%; display: inline-block;margin-right: 5px;" onfocus="this.select();" autocomplete=off placeholder="Tanggal" required />
					</div>
					
					<div class="col-md-1 labelColumn">
						Pelanggan :
					</div>
					<div class="col-md-2">
						<select id="ddlCustomer" name="ddlCustomer" tabindex=7 class="form-control-custom" placeholder="Pilih Pelanggan" >
							<?php
								$sql = "CALL spSelDDLCustomer('".$_SESSION['UserLogin']."')";
								if (! $result = mysqli_query($dbh, $sql)) {
									logEvent(mysqli_error($dbh), '/Master/Booking/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
									return 0;
								}
								while($row = mysqli_fetch_array($result)) {
									echo "<option value='".$row['CustomerID']."' >".$row['CustomerCode']." - ".$row['CustomerName']."</option>";
								}
								mysqli_free_result($result);
								mysqli_next_result($dbh);
							?>
						</select>
					</div>
					<div class="col-md-1">
						<div id="toggle-retail" class="toggle-modern" ></div>
						<input type="hidden" id="hdnIsRetail" name="hdnIsRetail" value=1 />
					</div>
				</div>
				<hr style="margin: 10px 0;" />
				<div class="row">
					<table class="table table-striped table-hover" style="margin-bottom: 5px;width:80%;" >
						<thead>
							<tr>
								<th style="width: 25%;text-align: center;" >Kode Barang</th>
								<th style="width: 25%;text-align: center;" >Nama Barang</th>
								<th style="width: 10%;text-align: center;" >Qty</th>
								<th style="width: 20%;text-align: center;">Harga Jual</th>
								<th style="width: 20%;text-align: center;">Diskon</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="width: 25%;" ><input id="txtItemCode" name="txtItemCode" type="text" tabindex=8 class="form-control-custom" style="width: 100%;" onfocus="this.select();" onkeypress="isEnterKey(event, 'getItemDetails');" onchange="getItemDetails();" autocomplete=off placeholder="Kode Barang" /></td>
								<td style="width: 25%;" ><input id="txtItemName" name="txtItemName" type="text" class="form-control-custom" style="width: 100%;" disabled /></td>
								<td style="width: 10%;" ><input id="txtQTY" name="txtQTY" type="number" tabindex=9 class="form-control-custom" style="width: 100%;" value=1 min=1 onchange="this.value = validateQTY(this.value);Grosir(this.value);" onpaste="return false;" onfocus="this.select();" /></td>
								<td style="width: 20%;" >
									<input id="hdnBuyPrice" name="hdnBuyPrice" type="hidden" value=0 />
									<input id="hdnRetailPrice" name="hdnRetailPrice" type="hidden" value=0 />
									<input id="hdnPrice1" name="hdnPrice1" type="hidden" value=0 />
									<input id="hdnQty1" name="hdnQty1" type="hidden" value=0 />
									<input id="hdnPrice2" name="hdnPrice2" type="hidden" value=0 />
									<input id="hdnQty2" name="hdnQty2" type="hidden" value=0 />
									<input id="hdnBranchID" name="hdnBranchID" type="hidden" value=1 />
									<input id="hdnWeight" name="hdnWeight" type="hidden" value=0 />
									<input id="txtBookingPrice" name="txtBookingPrice" type="text" tabindex=10 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Harga Jual" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" />
								</td>
								<td style="width: 20%;" ><input id="txtDiscount" name="txtDiscount" type="text" tabindex=11 class="form-control-custom text-right" value="0" autocomplete=off placeholder="Diskon" onkeypress="isEnterKey(event, 'addBookingDetails');return isNumberKey(event, this.id, this.value);" onchange="addBookingDetails();" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" /></td>
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
									<th>BookingDetailsID</th>
									<th>ItemID</th>
									<th>BranchID</th>
									<th>Cabang</th>
									<th>Kode Barang</th>
									<th>Nama Barang</th>
									<th>Qty</th>
									<th>Harga Jual</th>
									<th>Diskon</th>
									<th>Sub Total</th>
									<th>BuyPrice</th>
									<th>Price1</th>
									<th>Qty1</th>
									<th>Price2</th>
									<th>Qty2</th>
									<th>Weight</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="row" >
					<h2 style="display: inline-block;float: left;" >TOTAL : &nbsp;</h2><span id="lblTotal" >0</span>
					</h2><span id="lblWeight" >0</span><h2 style="display: inline-block;float: right;color: #0006ff;" >Berat(KG) : &nbsp;
				</div>
				<br />
				<div class="row" >
					<h5 style="margin-top: 5px !important;margin-bottom: 5px !important;">F12 = Daftar Barang; F10 = Transaksi Selesai; ESC = Tutup; DELETE = Hapus; ENTER/DOUBLE KLIK = Edit;</h5>
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
								<th>Harga Ecer</th>
								<th>Harga Grosir 1</th>
								<th>QTY Grosir 1</th>
								<th>Harga Grosir 2</th>
								<th>QTY Grosir 2</th>
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
				$("#hdnBookingDetailsID").val(Data[0]);
				$("#hdnItemID").val(Data[1]);
				$("#hdnBranchID").val(Data[2]);
				$("#txtItemCode").val(Data[4]);
				$("#txtItemName").val(Data[5]);
				$("#txtQTY").val(Data[6]);
				$("#txtBookingPrice").val(Data[7]);
				$("#txtDiscount").val(Data[8]);
				$("#hdnBuyPrice").val(Data[10]);
				$("#hdnPrice1").val(Data[11]);
				$("#hdnQty1").val(Data[12]);
				$("#hdnPrice2").val(Data[13]);
				$("#hdnQty2").val(Data[14]);
				$("#hdnWeight").val(Data[15]);
				$("#hdnRetailPrice").val(Data[16]);

				setTimeout(function() { $("#txtItemCode").focus(); }, 0);
			}
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				if(EditFlag == 1) {
					$("#toggle-retail").toggleClass('disabled', true);
					if(Data[9] == 1) {
						$("#FormData").attr("title", "Edit Penjualan Eceran");
						$('#toggle-retail').toggles(true);
					}
					else {
						$("#FormData").attr("title", "Edit Penjualan Grosir");
						$('#toggle-retail').toggles(false);
					}
					$("#hdnBookingID").val(Data[6]);
					$("#ddlCustomer").val(Data[7]);
					$("#txtBookingNumber").val(Data[2]);
					$("#lblTotal").html(Data[5]);
					$("#txtTransactionDate").datepicker("setDate", new Date(Data[8]));
					getBookingDetails(Data[6]);
					$("#lblWeight").html(Data[10]);
				}
				else $("#FormData").attr("title", "Tambah Penjualan Eceran");
				var index = table.cell({ focused: true }).index();
				$("#FormData").dialog({
					autoOpen: false,
					open: function() {
						$("#txtTransactionDate").focus();
						table.keys.disable();
						table2 = $("#grid-transaction").DataTable({
									"keys": true,
									"scrollY": "290px",
									"scrollX": false,
									"scrollCollapse": false,
									"paging": false,
									"searching": false,
									"order": [],
									"columns": [
										{ "visible": false },
										{ "visible": false },
										{ "visible": false },
										{ "width": "15%", "orderable": false, className: "dt-head-center dt-body-center" },
										{ "width": "20%", "orderable": false, className: "dt-head-center" },
										{ "width": "25%", "orderable": false, className: "dt-head-center" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "visible": false },
										{ "visible": false },
										{ "visible": false },
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
						var counterBookingDetails = 0;
						table2.on( 'key', function (e, datatable, key, cell, originalEvent) {
							var index = table2.cell({ focused: true }).index();
							if(counterBookingDetails == 0) {
								counterBookingDetails = 1;
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
									SingleDelete("./Transaction/Booking/DeleteDetails.php", deletedData, function(action) {
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
								setTimeout(function() { counterBookingDetails = 0; } , 1000);
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
					height: 660,
					width: 1280,
					modal: false,
					buttons: [
					{
						text: "Tutup",
						tabindex: 12,
						id: "btnCancelAddBooking",
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
							url: "./Transaction/Booking/CheckItem.php",
							type: "POST",
							data: { itemCode : itemCode },
							dataType: "json",
							success: function(data) {
								if(data.FailedFlag == '0') {
									if($("#hdnItemID").val() != data.ItemID) {
										$("#hdnItemID").val(data.ItemID);
										$("#txtItemName").val(data.ItemName);
										$("#txtBookingPrice").val(returnRupiah(data.RetailPrice));
										$("#hdnBuyPrice").val(data.BuyPrice);
										$("#hdnRetailPrice").val(data.RetailPrice);
										$("#hdnPrice1").val(data.Price1);
										$("#hdnQty1").val(data.Qty1);
										$("#hdnPrice2").val(data.Price2);
										$("#hdnQty2").val(data.Qty2);
										$("#hdnWeight").val(data.Weight);
										$("#txtQTY").focus();
									}
									else $("#txtQTY").focus();
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
												duration: 500
											},
											hide: {
												effect: "fade",
												duration: 500
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
								LogEvent(errorMessage, "/Transaction/Booking/index.php");
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
					var retailPrice = $("#hdnRetailPrice").val();
					if($('#toggle-retail').data('toggles').active == false) {
						var qty1 = $("#hdnQty1").val();
						var price1 = $("#hdnPrice1").val();
						var qty2 = $("#hdnQty2").val();
						var price2 = $("#hdnPrice2").val();
						if(parseFloat(Quantity) >= parseFloat(qty2)) {
							$("#txtBookingPrice").val(returnRupiah(price2));
						}
						else if(parseFloat(Quantity) < parseFloat(qty2) && parseFloat(Quantity) >= parseFloat(qty1)) {
							$("#txtBookingPrice").val(returnRupiah(price1));
						}
						else {
							$("#txtBookingPrice").val(returnRupiah(retailPrice));
						}
					}
					else {
						$("#txtBookingPrice").val(returnRupiah(retailPrice));
					}
				}
			}
			
			function getBookingDetails(BookingID) {
				$.ajax({
					url: "./Transaction/Booking/BookingDetails.php",
					type: "POST",
					data: { BookingID : BookingID },
					dataType: "json",
					success: function(Data) {
						if(Data.FailedFlag == '0') {
							for(var i=0;i<Data.data.length;i++) {
								table2.row.add(Data.data[i]);
							}
							table2.draw();
							tableWidthAdjust();
							
							for(var i=0;i<Data.data.length;i++) {
								$("#toggle-branch-" + Data.data[i][0]).toggles({
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
								
								if(Data.data[i][2] == 1) $("#toggle-branch-" + Data.data[i][0]).toggles(true);
								else $("#toggle-branch-" + Data.data[i][0]).toggles(false);
										
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
						LogEvent(errorMessage, "/Transaction/Booking/index.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
			}
			
			var counterAddBooking = 0;
			function addBookingDetails() {
				if(counterAddBooking == 0) {
					counterAddBooking = 1;
					var itemID = $("#hdnItemID").val();
					var itemCode = $("#txtItemCode").val();
					var itemName = $("#txtItemName").val();
					var Qty = $("#txtQTY").val();
					var salePrice = $("#txtBookingPrice").val();
					var buyPrice = $("#hdnBuyPrice").val();
					var price1 = $("#hdnPrice1").val();
					var qty1 = $("#hdnQty1").val();
					var price2 = $("#hdnPrice2").val();
					var qty2 = $("#hdnQty2").val();
					var discount = $("#txtDiscount").val();
					var branchID = $("#hdnBranchID").val();
					var weight = $("#hdnWeight").val();
					$("#txtDiscount").blur();
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
							url: "./Transaction/Booking/Insert.php",
							type: "POST",
							data: $("#PostForm").serialize(),
							dataType: "json",
							success: function(data) {
								if(data.FailedFlag == '0') {
									if($("#hdnBookingDetailsID").val() == 0) {
										$("#toggle-retail").toggleClass('disabled', true);
										$("#txtBookingNumber").val(data.BookingNumber);
										var toggleBranch = "<div id='toggle-branch-" + data.BookingDetailsID + "' onclick='updateBranch(this.id)' class='div-center toggle-modern' ></div>";
										table2.row.add([
											data.BookingDetailsID,
											itemID,
											branchID,
											toggleBranch,
											itemCode,
											itemName,
											Qty,
											salePrice,
											discount,
											returnRupiah((parseFloat(salePrice.replace(/\,/g, "")) * parseFloat(Qty) - parseFloat(discount)).toString()),
											buyPrice,
											price1,
											qty1,
											price2,
											qty2,
											weight
										]).draw();
										
										$("#toggle-branch-" + data.BookingDetailsID).toggles({
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
									}
									else {
										var toggles = $('#toggle-branch-' + data.BookingDetailsID).data('toggles').active;
										table2.row(rowEdit).data([
											data.BookingDetailsID,
											itemID,
											branchID,
											table2.row( rowEdit ).data()[3],
											itemCode,
											itemName,
											Qty,
											salePrice,
											discount,
											returnRupiah((parseFloat(salePrice.replace(/\,/g, "")) * parseFloat(Qty) - parseFloat(discount)).toString()),
											buyPrice,
											price1,
											qty1,
											price2,
											qty2,
											weight
										]).draw();
										
										$("#toggle-branch-" + data.BookingDetailsID).toggles({
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
										
										$("#toggle-branch-" + data.BookingDetailsID).toggles(toggles);
										
										table2.keys.enable();
									}
									$("#txtItemCode").val("");
									$("#txtItemName").val("");
									$("#txtQTY").val(1);
									$("#txtBookingPrice").val(0);
									$("#hdnBuyPrice").val(0);
									$("#hdnPrice1").val(0);
									$("#hdnQty1").val(0);
									$("#hdnPrice2").val(0);
									$("#hdnQty2").val(0);
									$("#txtItemCode").focus();
									$("#hdnBookingID").val(data.ID);
									$("#hdnBookingDetailsID").val(0);
									$("#hdnItemID").val(0);
									$("#hdnWeight").val(0);
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
													if(data.Message == "No. Invoice sudah ada") $("#txtBookingNumber").focus();
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
								LogEvent(errorMessage, "/Transaction/Booking/index.php");
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
				setTimeout(function() { counterAddBooking = 0; }, 1000);
			}
			
			function updateBranch(BookingDetailsID) {
				setTimeout(function() {
					var BranchID = 1;
					var str = BookingDetailsID.split("-");
					if($('#toggle-branch-' + str[2]).data('toggles').active == false) BranchID = 2;
					$("#loading").show();
					$.ajax({
						url: "./Transaction/Booking/UpdateBranch.php",
						type: "POST",
						data: { BookingDetailsID : str[2], BranchID : BranchID },
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
					url: 'Master/Item/PopUpAddItem.php',
					width: 780,
					height: 460,
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
								$("#btnSimpan").attr("tabindex", 27);
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
										duration: 500
									},
									hide: {
										effect: "fade",
										duration: 500
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
												url: "./Master/Item/InsertFromPurchase.php",
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
																	$("#txtQTY").focus();
																}, 0);
															},
															shown: function() {
																setTimeout(function() {
																	$("#hdnItemID").val(data.ID);
																	$("#txtItemName").val($("#txtItemNameAdd").val());
																	$("#hdnBuyPrice").val($("#txtBuyPriceAdd").val());
																	$("#txtBookingPrice").val($("#txtRetailPriceAdd").val());
																	$("#hdnPrice1").val($("#txtPrice1Add").val());
																	$("#hdnQty1").val($("#txtQty1Add").val());
																	$("#hdnPrice2").val($("#txtPrice2Add").val());
																	$("#hdnQty2").val($("#txtQty2Add").val());
																	$("#hdnWeight").val($("#txtWeightAdd").val());
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
			
			function Calculate() {
				var grandTotal = 0;
				var weight = 0;
				table2.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
					var data = this.data();
					grandTotal += parseFloat(data[7].replace(/\,/g, "")) * parseFloat(data[6]) - parseFloat(data[8]);
					weight += parseFloat(data[15]) * parseFloat(data[6]);
				});
				$("#lblTotal").html(returnRupiah(grandTotal.toString()));
				$("#lblWeight").html(returnWeight(weight.toString()));
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
				$("#hdnBookingID").val(0);
				$("#hdnItemID").val(0);
				$("#txtTransactionDate").datepicker("setDate", new Date());
				$("#txtBookingNumber").val("");
				$("#txtItemCode").val("");
				$("#txtItemName").val("");
				$("#txtQTY").val(1);
				$("#txtBookingPrice").val(0);
				$("#hdnBuyPrice").val(0);
				$("#hdnPrice1").val(0);
				$("#hdnQty1").val(0);
				$("#hdnQty2").val(0);
				$("#toggle-retail").toggleClass('disabled', false);
				$('#toggle-retail').toggles(true);
				$("#lblTotal").html("0");
				$("#lblWeight").html("0");
				table2.clear().draw();
			}
			
			function fnDeleteData() {
				var index = table.cell({ focused: true }).index();
				table.keys.disable();
				DeleteData("./Transaction/Booking/Delete.php", function(action) {
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
										{ "width": "25%", "orderable": false, className: "dt-head-center" },
										{ "width": "11%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "11%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "11%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "11%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "11%", "orderable": false, className: "dt-head-center dt-body-right" }
									],
									"ajax": "./Transaction/Booking/ItemList.php",
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
				$('#grid-data').on('click', 'input[type="checkbox"]', function() {
				    $(this).blur();
				});
				
				$('#toggle-retail').toggles({
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
						if($("#FormData").css("display") == "block") $('#FormData').dialog('option', 'title', 'Tambah Penjualan Eceran');
					} else {
						$("#hdnIsRetail").val(0);
						if($("#FormData").css("display") == "block") $('#FormData').dialog('option', 'title', 'Tambah Penjualan Grosir');
						Grosir($("#txtQTY").val());
					}
				});
				
				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Transaction/Booking/index.php");
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
				var counterBooking = 0;
				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "330px",
								"rowId": "BookingID",
								"scrollCollapse": true,
								"order": [2, "asc"],
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
								"ajax": "./Transaction/Booking/DataSource.php",
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
					else if(counterBooking == 0) {
						counterBooking = 1;
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
								SingleDelete("./Transaction/Booking/Delete.php", deletedData, function(action) {
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
						setTimeout(function() { counterBooking = 0; } , 1000);
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