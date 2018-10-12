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
			<div class="col-md-12 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading" style="padding: 1px 15px;">
						<h5>Pembayaran Piutang</h5>
						<?php
							echo '<input id="hdnEditFlag" name="hdnEditFlag" type="hidden" value="'.$EditFlag.'" />';
							echo '<input id="hdnDeleteFlag" name="hdnDeleteFlag" type="hidden" value="'.$DeleteFlag.'" />';
						?>
					</div>
					<div class="panel-body">
						<div class="table-responsive" style="overflow-x:hidden;">
							<table id="grid-data" class="table table-striped table-bordered table-hover" >
								<thead>				
									<tr>
										<th>No</th>
										<th>No. Invoice</th>
										<th>Tanggal</th>
										<th>Customer</th>
										<th>Total</th>
										<th>Pembayaran</th>
										<th>Kekurangan</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="FormData" title="Tambah Kategori" style="display: none;">
			<form class="col-md-12 col-sm-12" id="PostForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-3 col-sm-3 has-float-label">
						<input id="hdnTransactionID" name="hdnTransactionID" type="hidden" value=0 />
						<input id="hdnTransactionType" name="hdnTransactionType" type="hidden" value="" />
						<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
						<input id="hdnPaymentDate" name="hdnPaymentDate" type="hidden" />
						<input id="hdnPaymentDetailsID" name="hdnPaymentDetailsID" type="hidden" value=0 />
						<input id="txtPaymentNumber" name="txtPaymentNumber" type="text" class="form-control-custom" placeholder="No. Invoice" readonly />
						<label for="txtPaymentNumber" class="lblInput" >No. Invoice</label>
					</div>
					<div class="col-md-3 col-sm-3 has-float-label">
						<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom mousetrap" onfocus="this.select();" autocomplete=off placeholder="Tanggal" readonly />
						<label for="txtTransactionDate" class="lblInput" >Tanggal</label>
					</div>
					<div class="col-md-3 col-sm-3 has-float-label">
						<input id="txtCustomerName" name="txtCustomerName" type="text" class="form-control-custom mousetrap" onfocus="this.select();" autocomplete=off placeholder="Pelanggan" readonly />
						<label for="txtCustomerName" class="lblInput" >Pelanggan</label>
					</div>
					<div class="col-md-3 col-sm-3 has-float-label">
						<input id="txtPayment" name="txtPayment" type="tel" class="form-control-custom mousetrap text-right" autocomplete=off onchange="updatePayment();" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" placeholder="DP" />
						<label for="txtPayment" class="lblInput" >DP</label>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-3 col-sm-3 has-float-label" >
						<input id="txtPaymentDate" name="txtPaymentDate" type="text" tabindex=6 class="form-control-custom mousetrap" style="width: 87%; display: inline-block;margin-right: 5px;" onfocus="this.select();" autocomplete=off />
						<label for="txtPaymentDate" class="lblInput" >Tanggal Pembayaran</label>
					</div>
					<div class="col-md-3 col-sm-3 has-float-label" >
						<input id="txtAmount" name="txtAmount" type="tel" tabindex=7 class="form-control-custom mousetrap text-right" value="0" autocomplete=off onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" onpaste="return false;" />
						<label for="txtAmount" class="lblInput" >Jumlah</label>
					</div>
					<div class="col-md-6 col-sm-6 has-float-label" >
						<input id="txtRemarks" name="txtRemarks" type="text" tabindex=8 class="form-control-custom mousetrap" style="width: 100%;" value="" autocomplete=off onpaste="return false;" />
						<label for="txtRemarks" class="lblInput" >Keterangan</label>
						<input type="text" tabindex=9 onfocus="addPaymentDetails();" style="height:0; width: 0;opacity:0;" />
					</div>
				</div>
				<hr style="margin-top: 5px; margin-bottom:0;" />
				<div class="row" >
					<div id="divTableContent" class="table-responsive" style="overflow-x:hidden;">
						<table id="grid-transaction" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th>PaymentDetailsID</th>
									<th>TransactionDate</th>
									<th>Tanggal Pembayaran</th>
									<th>Jumlah</th>
									<th>Keterangan</th>
									<th>Opsi</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="row" >
					<h2 style="display: inline-block;float: left;" >TOTAL BAYAR: &nbsp;</h2><span id="lblTotal" >0</span>
					<span id="lblWeight" >0</span><h2 style="display: inline-block;float: right;color: #0006ff;" >TOTAL BELANJA : &nbsp;</h2>
				</div>
			</form>
		</div>
		<script>
			var table;
			var table2;
			var today;
			var rowEdit;
	
			function openDialogEdit(Data) {
				$("#hdnPaymentDetailsID").val(Data[0]);
				$("#hdnPaymentDate").val(Data[1]);
				$("#txtPaymentDate").datepicker("setDate", new Date(Data[1]));
				$("#txtAmount").val(returnRupiah(Data[3].toString()));
				$("#txtRemarks").val(Data[4]);
				setTimeout(function() { $("#txtAmount").focus(); }, 0);
			}

			var counterUpdatePayment = 0;
			function updatePayment() {
				if(counterUpdatePayment == 0) {
					counterUpdatePayment = 1;
					var PassValidate = 1;
					var grandTotal = 0;
					var FirstFocus = 0;
					table2.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
						var data = this.data();
						grandTotal += parseFloat(data[3].replace(/\,/g, ""));
					});

					var totalSale = parseFloat($("#lblWeight").html().replace(/\,/g, ""));
					if((grandTotal + parseFloat($("#txtPayment").val().replace(/\,/g, ""))) > totalSale) {
						PassValidate = 0;
						$("#txtPayment").notify("Pembayaran melebihi pembelanjaan", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtAmount").focus();
						FirstFocus = 1;
					}

					if(PassValidate == 1) {
						var TransactionID = $("#hdnTransactionID").val();
						var TransactionType = $("#hdnTransactionType").val();
						var Payment = $("#txtPayment").val().replace(/\,/g, "");
						$.ajax({
							url: "./Transaction/Payment/UpdatePayment.php",
							type: "POST",
							data: { TransactionID : TransactionID, TransactionType : TransactionType, Payment : Payment },
							dataType: "json",
							success: function(Data) {
								if(Data.FailedFlag == '0') {
									Calculate();
									var transactionDate = new Date();
									transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
									printInvoice(1, parseFloat(Payment), transactionDate);
								}
								else {
									var counter = 0;
									Lobibox.alert("error",
									{
										msg: "Gagal update pembayaran",
										width: 480,
										beforeClose: function() {
											if(counter == 0) {
												setTimeout(function() {
													$("#txtPayment").focus();
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
								LogEvent(errorMessage, "/Transaction/Payment/index.php");
								Lobibox.alert("error",
								{
									msg: errorMessage,
									width: 480
								});
								return 0;
							}
						});
					}
					setTimeout(function() { counterUpdatePayment = 0; } , 1000);
				}
			}
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				$("#FormData").attr("title", "Edit Pembayaran");
				$("#hdnTransactionID").val(Data[7]);
				$("#txtCustomerName").val(Data[3]);
				$("#txtPaymentNumber").val(Data[1]);
				$("#txtTransactionDate").datepicker("setDate", new Date(Data[8]));
				$("#txtPaymentDate").datepicker("setDate", new Date());
				$("#hdnTransactionType").val(Data[9]);
				$("#txtPayment").val(returnRupiah(Data[10]));
				$("#lblWeight").html(Data[4]);
				$("#lblTotal").html(Data[5]);
				getPaymentDetails(Data[7], Data[9]);
				var index = table.cell({ focused: true }).index();
				$("#FormData").dialog({
					autoOpen: false,
					position: {
						my : 'top+10%',
						at : 'top'
					},
					open: function() {
						$("#txtPaymentDate").focus();
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
										{ "width": "20%", "orderable": false, className: "dt-head-center" },
										{ "width": "20%", "orderable": false, className: "dt-head-center dt-body-right" },
										{ "width": "50%", "orderable": false, className: "dt-head-center" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-center" }
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
						var counterPaymentDetails = 0;
						
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
							if(typeof index !== 'undefined') {
								try {
									table.cell(index).focus();
								}
								catch (err) {
									$("#grid-data").DataTable().cell( ':eq(0)' ).focus();
								}
							}
						}, false);
						resetForm();
						table2.destroy();
					},
					resizable: false,
					height: 520,
					width: 840,
					modal: false /*,
					buttons: [
					{
						text: "Tutup",
						tabindex: 14,
						id: "btnCancelAddPayment",
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
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
							resetForm();
							table2.destroy();
							return false;
						}
					}]*/
				}).dialog("open");
			}

			
			function getPaymentDetails(TransactionID, TransactionType) {
				$.ajax({
					url: "./Transaction/Payment/PaymentDetails.php",
					type: "POST",
					data: { TransactionID : TransactionID, TransactionType : TransactionType },
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
						LogEvent(errorMessage, "/Transaction/Payment/index.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
			}

			function deletePayment(PaymentDetailsID) {
				table2.keys.disable();
				var deletedData = new Array();
				deletedData.push(PaymentDetailsID);
				var index = 0;
				table2.rows().eq( 0 ).filter( function (rowIdx) {
				    if(table2.cell( rowIdx, 0 ).data() == PaymentDetailsID) index = rowIdx;
				});
				//console.log(indexes);
				SingleDelete("./Transaction/Payment/DeleteDetails.php", deletedData, function(action) {
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
			
			var counterAddPayment = 0;
			function addPaymentDetails() {
				if(counterAddPayment == 0) {
					counterAddPayment = 1;
					var paymentDate = $("#hdnPaymentDate").val();
					var amount = $("#txtAmount").val();
					var remarks = $("#txtRemarks").val();
					$("#txtRemarks").blur();
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

					if(parseFloat($("#txtAmount").val()) == 0) {
						PassValidate = 0;
						$("#txtAmount").notify("Tidak boleh 0", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtAmount").focus();
						FirstFocus = 1;
					}

					var totalSale = parseFloat($("#lblWeight").html().replace(/\,/g, ""));
					var totalPayment = parseFloat($("#lblTotal").html().replace(/\,/g, ""));
					if((totalPayment + parseFloat($("#txtAmount").val().replace(/\,/g, ""))) > totalSale) {
						PassValidate = 0;
						$("#txtAmount").notify("Pembayaran melebihi pembelanjaan", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $("#txtAmount").focus();
						FirstFocus = 1;
					}

					if(PassValidate == 1) {
						
						$.ajax({
							url: "./Transaction/Payment/Insert.php",
							type: "POST",
							data: $("#PostForm").serialize(),
							dataType: "json",
							success: function(data) {
								if(data.FailedFlag == '0') {
									var paymentDateTemp = new Date(paymentDate);
									var paymentDateShow = ("0" + paymentDateTemp.getDate()).slice(-2) + "-" + ("0" + (paymentDateTemp.getMonth() + 1)).slice(-2) + "-" + paymentDateTemp.getFullYear();
									var deleteData = '<span style="background-color:red;padding:3px 6px 1px 5px;border:1px solid black;color:white;"><i style="cursor:pointer;" class="fa fa-trash fa-2x" acronym title="Hapus Data" onclick="deletePayment(' + data.PaymentDetailsID + ');"></i>';
									if($("#hdnPaymentDetailsID").val() == 0) {
										table2.row.add([
											data.PaymentDetailsID,
											paymentDate,
											paymentDateShow,
											amount,
											remarks,
											deleteData
										]).draw();
									}
									else {
										table2.row(rowEdit).data([
											data.PaymentDetailsID,
											paymentDate,
											paymentDateShow,
											amount,
											remarks,
											deleteData
										]).draw();
										
										table2.keys.enable();
									}
									$("#txtRemarks").val("");
									$("#txtAmount").val("0");
									$("#txtPaymentDate").datepicker("setDate", new Date());
									var transactionDate = new Date();
									transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
									today = transactionDate;
									$("#hdnTransactionDate").val(transactionDate);
									$("#hdnPaymentDetailsID").val(0);
									$("#txtPaymentDate").focus();
									tableWidthAdjust();
									Calculate();
									printInvoice(0, parseFloat(amount.replace(/\,/g, "")), paymentDate);
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
													if(data.Message == "No. Invoice sudah ada") $("#txtPaymentNumber").focus();
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
								LogEvent(errorMessage, "/Transaction/Payment/index.php");
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
				setTimeout(function() { counterAddPayment = 0; }, 1000);
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
				$("#txtRemarks").val("");
				$("#txtAmount").val("0");
				$("#txtPaymentDate").datepicker("setDate", new Date());
				var transactionDate = new Date();
				transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
				today = transactionDate;
				$("#hdnTransactionDate").val(transactionDate);
				$("#hdnPaymentDetailsID").val(0);
				table2.clear().draw();
			}

			function Calculate() {
				var grandTotal = parseFloat($("#txtPayment").val().replace(/\,/g, ""));
				table2.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
					var data = this.data();
					grandTotal += parseFloat(data[3].replace(/\,/g, ""));
				});
				$("#lblTotal").html(returnRupiah(grandTotal.toString()));
			}

			function printInvoice(DPFlag, Amount, TransactionDate) {
				var TotalPayment = $("#lblTotal").html();
				var TotalSale = $("#lblWeight").html();
				var TransactionNumber = $("#txtPaymentNumber").val();
				var CustomerName = $("#txtCustomerName").val();

				$("#loading").show();
				$.ajax({
					url: "./Transaction/Payment/PrintInvoice.php",
					type: "POST",
					data: { DPFlag : DPFlag, TransactionDate : TransactionDate, CustomerName : CustomerName, TransactionNumber : TransactionNumber, TotalPayment : TotalPayment, TotalSale : TotalSale, Amount : Amount },
					dataType: "json",
					success: function(data) {
						if(data.FailedFlag == '0') {
							$("#loading").hide();
							$("#divModal").hide();
							//resetForm();
							//table2.destroy();
							//$("#FormData").dialog("destroy");
							//openDialog(0, 0);
							Lobibox.alert("success",
							{
								msg: data.Message,
								width: 480,
								delay: 2000
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
						LogEvent(errorMessage, "/Transaction/Payment/index.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
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
				$("#txtPayment, #txtAmount").on("input change paste",
				    function filterNumericAndDecimal(event) {
						var formControl;
						formControl = $(event.target);
						formControl.val(formControl.val().replace(/[^0-9]+/g, ""));
					}
				);

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

				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Transaction/Payment/index.php");
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
					monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des" ]
				});
				
				$("#txtTransactionDate").attr("readonly", "true");
				$("#txtTransactionDate").css({
					"background-color": "#FFF",
					"cursor": "text"
				});

				$("#txtTransactionDate").datepicker( "option", "disabled", true );

				$("#txtPaymentDate").datepicker({
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
						$("#hdnPaymentDate").val(transactionDate);
						$("#txtAmount").focus();
					}
				}).datepicker("setDate", new Date());
				
				$("#txtPaymentDate").attr("readonly", "true");
				$("#txtPaymentDate").css({
					"background-color": "#FFF",
					"cursor": "text"
				});
				
				var transactionDate = new Date();
				transactionDate = transactionDate.getFullYear() + "-" + ("0" + (transactionDate.getMonth() + 1)).slice(-2) + "-" + ("0" + transactionDate.getDate()).slice(-2);
				today = transactionDate;
				$("#hdnPaymentDate").val(transactionDate);
				
				keyFunction();
				enterLikeTab();
				var counterPayment = 0;
				table = $("#grid-data").DataTable({
								"keys": true,
								"scrollY": "330px",
								"rowId": "TransactionID",
								"scrollCollapse": true,
								"order": [],
								"columns": [
									{ "width": "25px", "orderable": false, className: "dt-head-center dt-body-right" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ className: "dt-head-center" },
									{ "orderable": false, className: "dt-head-center dt-body-right" },
									{ "orderable": false, className: "dt-head-center dt-body-right" },
									{ "orderable": false, className: "dt-head-center dt-body-right" }
								],
								"processing": true,
								"serverSide": true,
								"ajax": "./Transaction/Payment/DataSource.php",
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
					if(counterPayment == 0) {
						counterPayment = 1;
						var data = datatable.row( cell.index().row ).data();
						if(key == 13) {
							if(($(".ui-dialog").css("display") == "none" || $("#delete-confirm").css("display") == "none") && $("#hdnEditFlag").val() == "1" ) {
								openDialog(data, 1);
							}
						}
						setTimeout(function() { counterPayment = 0; } , 1000);
					}
				});
				
				table.on('page', function() {
					$("#select_all").prop("checked", false);
				});

				var counterKey = 0;
				$(document).on("keydown", function (evt) {
					var index = table.cell({ focused: true }).index();
					if (evt.keyCode == 46 && $("#hdnDeleteFlag").val() == "1" && typeof index == 'undefined' && $("#FormData").css("display") == "none" && $(".lobibox").css("display") != "block") { //delete button
						evt.preventDefault();
						if(counterKey == 0) {
							fnDeleteData();
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