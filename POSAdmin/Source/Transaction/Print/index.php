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
		<br />
		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive col-md-12" style="overflow-x:hidden;">
					<table id="grid-data" class="table table-striped table-bordered table-hover" >
						<thead>				
							<tr>
								<th>No</th>
								<th>No. Invoice</th>
								<th>Tanggal</th>
								<th>Customer</th>
								<th>Total</th>
								<th>SaleID</th>
								<th>CustomerID</th>
								<th>PlainTransactionDate</th>
								<th>RetailFlag</th>
								<th>Weight</th>
								<th>Payment</th>
								<th>Opsi</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
		<div id="FormData" title="Tambah Kategori" style="display: none;">
			<form class="col-md-12" id="PostForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-1 labelColumn">
						No. Invoice :
						<input id="hdnSaleID" name="hdnSaleID" type="hidden" value=0 />
						<input id="hdnSaleDetailsID" name="hdnSaleDetailsID" type="hidden" value=0 />
						<input id="hdnItemID" name="hdnItemID" type="hidden" value=0 />
						<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
						<input id="hdnPayment" name="hdnPayment" type="hidden" value=0 />
					</div>
					<div class="col-md-2">
						<input id="txtSaleNumber" name="txtSaleNumber" type="text" class="form-control-custom" placeholder="No. Invoice" readonly />
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
									logEvent(mysqli_error($dbh), '/Master/Sale/index.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
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
				<div class="row" >
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
									<th>Retail Price</th>
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
					<h5 style="margin-top: 5px !important;margin-bottom: 5px !important;">ESC = Tutup;</h5>
				</div>
			</form>
		</div>
		<div id="finish-dialog" title="Transaksi Selesai" style="display: none;">
			<div class="row col-md-12" >
				<div class="col-md-4 labelColumn">
					Pembayaran :
				</div>
				<div class="col-md-8">
					<select id="ddlPayment" name="ddlPayment" class="form-control-custom" tabindex=14 >
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
			<button type="button" class="btn btn-primary btn-block" onclick="printInvoice();" tabindex=18 >Selesai</button>
			<!--<br />
			<button class="btn btn-danger btn-block" tabindex=16 onclick="printShipment();" >Cetak Surat Jalan</button>-->
		</div>
		<script>
			var table;
			var table2;
			var today;
			var rowEdit;
			
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
					$("#hdnSaleID").val(Data[6]);
					$("#ddlCustomer").val(Data[7]);
					$("#txtSaleNumber").val(Data[2]);
					$("#lblTotal").html(Data[5]);
					$("#txtTransactionDate").datepicker("setDate", new Date(Data[8]));
					getSaleDetails(Data[6]);
					$("#lblWeight").html(Data[10]);
					$("#hdnPayment").val(Data[11]);
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
										{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-center" },
										{ "visible": false },
										{ "visible": false },
										{ "visible": false },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-center" },
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
					height: 580,
					width: 1280,
					modal: false,
					buttons: [
					{
						text: "Cetak Nota",
						tabindex: 12,
						id: "btnPrintInvoice",
						click: function() {
							printInvoice();
						}
					},
					{
						text: "Cetak Surat Jalan",
						tabindex: 13,
						id: "btnPrintShipment",
						click: function() {
							printShipment();
						}
					},
					{
						text: "Tutup",
						tabindex: 14,
						id: "btnCancelAddSale",
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

			function finish(TransactionID, TransactionType) {
				if(TransactionID != 0) {
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
					var bookingID = $("#hdnBookingID").val();
					var BookingNumber = $("#txtBookingNumber").val();
					var Payment = $("#txtPayment").val().replace(/\,/g, "");
					var PaymentType = $("#ddlPayment").val();
					var PrintInvoice = $("#chkPrint").prop("checked");
					var PrintShipment = $("#chkPrintShipment").prop("checked");
					var Change = $("#txtChange").val().replace(/\,/g, "");
					var PaymentMethod = $("#ddlPayment option:selected").text();
					var TransactionDate = $("#hdnTransactionDate").val();
					$("#loading").show();
					$.ajax({
						url: "./Transaction/Booking/PrintInvoice.php",
						type: "POST",
						data: { BookingID : bookingID, Payment : Payment, PaymentType : PaymentType, PrintInvoice : PrintInvoice, Change: Change, BookingNumber : BookingNumber, PaymentMethod : PaymentMethod, TransactionDate : TransactionDate },
						dataType: "json",
						success: function(data) {
							if(data.FailedFlag == '0') {
								$("#loading").hide();
								$("#divModal").hide();
								if(PrintShipment == true) printShipment();
								resetForm();
								table2.destroy();
								$("#finish-dialog").dialog("destroy");
								$("#FormData").dialog("destroy");
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
												table.ajax.reload(function() {
													table.keys.enable();
													if(typeof tableIndex !== 'undefined') table.cell(tableIndex).focus();
												}, false);
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
							$.notify("Koneksi gagal", "error");
						}
					});
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
			
			function Calculate() {
				var grandTotal = 0;
				var weight = 0;
				table2.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
					var data = this.data();
					grandTotal += parseFloat(data[8].replace(/\,/g, "")) * parseFloat(data[7]) - parseFloat(data[9]);
					weight += parseFloat(data[16]) * parseFloat(data[7]);
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
				$("#hdnSaleID").val(0);
				$("#hdnItemID").val(0);
				$("#txtTransactionDate").datepicker("setDate", new Date());
				$("#txtSaleNumber").val("");
				$("#txtItemCode").val("");
				$("#txtItemName").val("");
				$("#txtQTY").val(1);
				$("#txtSalePrice").val(0);
				$("#hdnBuyPrice").val(0);
				$("#hdnPrice1").val(0);
				$("#hdnQty1").val(0);
				$("#hdnQty2").val(0);
				$("#toggle-retail").toggleClass('disabled', false);
				$('#toggle-retail').toggles(true);
				$("#lblTotal").html("0");
				$("#lblWeight").html("0");
				$("#hdnPayment").html("0");
				$("#hdnRetailPrice").val(0);
				table2.clear().draw();
			}
			
			function fnDeleteData() {
				var index = table.cell({ focused: true }).index();
				table.keys.disable();
				DeleteData("./Transaction/Sale/Delete.php", function(action) {
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
						}, 0);
		            }, 500, "resizeWindow");
				});
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
						//Grosir($("#txtQTY").val());
					}
				});
				
				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Transaction/Print/index.php");
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
				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "330px",
								"rowId": "SaleID",
								"scrollCollapse": true,
								"order": [],
								"columns": [
									{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ "orderable": false, className: "dt-head-center dt-body-right" },
									{ "visible": false },
									{ "visible": false },
									{ "visible": false },
									{ "visible": false },
									{ "visible": false },
									{ "visible": false },
									{ "orderable": false, className: "dt-head-center dt-body-center" }
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Transaction/Print/DataSource.php",
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
					else if(counterSale == 0) {
						counterSale = 1;
						var data = datatable.row( cell.index().row ).data();
						if(key == 13) {
							if(($(".ui-dialog").css("display") == "none" || $("#delete-confirm").css("display") == "none") && $("#hdnEditFlag").val() == "1" ) {
								openDialog(data, 1);
							}
						}
						setTimeout(function() { counterSale = 0; } , 1000);
					}
				});
				
				table.on('page', function() {
					$("#select_all").prop("checked", false);
				});

				var counterKey = 0;
				$(document).on("keydown", function (evt) {
					if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0 && $("#FormData").css("display") == "none" && $("#delete-confirm").css("display") == "none") {
						$("#grid-data_wrapper").find("input[type='search']").focus();
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