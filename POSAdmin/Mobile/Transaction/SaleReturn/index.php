<?php
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
?>
<html>
	<head>
		<style>
			#divTableContent {
				min-height: 380px;
				max-height: 380px;
				overflow-y: auto;
			}

			.chkSaleDetails {
				margin-top : 0 !important;
			}
			.ui-spinner {
				width: 100%;
			}

			.btn-mobile {
				padding: 2px 12px;
				vertical-align: baseline;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<span style="width:50%;display:inline-block;">
							 <h5 style="margin-bottom: 5px;margin-top: 5px;">Retur Penjualan</h5>
						</span>
						<span style="width:49%;display:inline-block;text-align:right;">
							<button id="btnSave" class="btn btn-default btn-mobile" onclick="finish();"><i class="fa fa-save "></i> Simpan</button>&nbsp;
							<button id="btnTransaction" class="btn btn-default btn-mobile" onclick="transactionList();" ><i class="fa fa-list"></i> Daftar Transaksi</button>
						</span>
					</div>
					<div class="panel-body">
						<form class="col-md-12 col-sm-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-3 col-sm-3 has-float-label">
									<input id="hdnSaleReturnID" name="hdnSaleReturnID" type="hidden" value=0 />
									<input id="hdnSaleID" name="hdnSaleID" type="hidden" value=0 />
									<input id="hdnTransactionDate" name="hdnTransactionDate" type="hidden" />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" />
									<input id="txtSaleNumber" name="txtSaleNumber" type="text" tabindex=5 class="form-control-custom" onfocus="this.select();" onkeypress="isEnterKey(event, 'getSaleDetails');" onchange="getSaleDetails();" autocomplete=off placeholder="No. Invoice" />
									<label for="txtSaleNumber" class="lblInput" >No. Invoice</label>
								</div>
								
								<div class="col-md-3 col-sm-3 has-float-label">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" tabindex=6 class="form-control-custom" style="width: 87%; display: inline-block;margin-right: 5px;" onfocus="this.select();" autocomplete=off placeholder="Tanggal" required />
									<label for="txtTransactionDate" class="lblInput" >Tanggal</label>
								</div>
								
								<div class="col-md-3 col-sm-3 has-float-label">
									<input id="txtCustomerName" name="txtCustomerName" type="text" class="form-control-custom" readonly />
									<label for="txtCustomerName" class="lblInput" >Pelanggan</label>
								</div>
							</div>
							<hr style="margin: 5px 0 0 0;" />
							<div class="row" >
								<div id="divTableContent" class="table-responsive" style="overflow-x:hidden;">
									<table id="grid-transaction" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
										<thead>
											<tr>
												<th>SaleReturnDetailsID</th>
												<th>ItemID</th>
												<th>BranchID</th>
												<th><input id="select_all_salereturn" name="select_all_salereturn" type="checkbox" onclick="checkAllSaleReturn();" tabindex=7 /></th>
												<th>Cabang</th>
												<th>Kode Barang</th>
												<th>Nama Barang</th>
												<th>Qty</th>
												<th>Satuan</th>
												<th>Harga Jual</th>
												<th>Sub Total</th>
												<th>BuyPrice</th>
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
				</div>
			</div>
		</div>
		<div id="transactionList-dialog" title="Daftar Transaksi" style="display: none;">
			<div class="row col-md-12 col-sm-12" >
				<div id="divTableItem" class="table-responsive" style="overflow-x:hidden;">
					<table id="grid-sale" style="width: 100% !important;" class="table table-striped table-bordered table-hover" >
						<thead>
							<tr>
								<th>No. Invoice</th>
								<th>Tanggal</th>
								<th>Customer</th>
								<th>Total</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
		<script>
			var table2;
			var table3;
			var today;
			var rowEdit;
			
			function openDialog(Data, EditFlag) {
				$("#hdnIsEdit").val(EditFlag);
				$("#txtSaleNumber").focus();
				table2 = $("#grid-transaction").DataTable({
							"keys": false,
							"scrollY": "330px",
							"scrollX": false,
							"scrollCollapse": false,
							"paging": false,
							"searching": false,
							"order": [],
							"columns": [
								{ "visible": false },
								{ "visible": false },
								{ "visible": false },
								{ "width": "5%", "orderable": false, className: "dt-head-center dt-body-center" },
								{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-center" },
								{ "width": "15%", "orderable": false, className: "dt-head-center" },
								{ "width": "20%", "orderable": false, className: "dt-head-center" },
								{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
								{ "width": "10%", "orderable": false, className: "dt-head-center" },
								{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
								{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" },
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
							},
							"initComplete": function(settings, json) {
								setTimeout(function() {
									$("#grid-transaction").find("#select_all_salereturn").first().remove()
								}, 0);
							}
						});
				table2.columns.adjust();
			}

			function updateBranch(SaleDetailsID) {
				setTimeout(function() {
					var BranchID = 1;
					var str = SaleDetailsID.split("-");
					if($('#toggle-branch-' + str[2]).data('toggles').active == false) BranchID = 2;
					$("#hdnBranchID" + str[2]).val(BranchID);
				}, 0);
			}

			function checkAllSaleReturn() {
				if($("#select_all_salereturn").prop("checked") == true) {
					$("input:checkbox[class=chkSaleDetails]").each(function() {
						if($(this).attr("disabled") == false) {
							$(this).prop("checked", true);
							$(this).attr("checked", true);
						}
					});
				}
				else {
					$("input:checkbox[class=chkSaleDetails]").each(function() {
						$(this).prop("checked", false);
						$(this).attr("checked", false);
					});
				}
				Calculate();
			}
			
			function getSaleReturnDetails(SaleReturnID) {
				$.ajax({
					url: "./Transaction/SaleReturn/SaleReturnDetails.php",
					type: "POST",
					data: { SaleReturnID : SaleReturnID },
					dataType: "json",
					success: function(Data) {
						if(Data.FailedFlag == '0') {
							for(var i=0;i<Data.data.length;i++) {
								table2.row.add(Data.data[i]);
							}
							table2.draw();
							tableWidthAdjust();
							setTimeout(function() {
								$("#grid-transaction").find("#select_all_salereturn").first().remove()
							}, 0);
							$("#btnSaveSaleReturn").attr("tabindex", Data.tabindex);
							$("#btnCancelAddSaleReturn").attr("tabindex", (parseFloat(Data.tabindex) + 1));

							$(".txtQTY").spinner();
							
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

							$("#select_all_salereturn").click();
						}
						else {
							var counter = 0;
							Lobibox.alert("error",
							{
								msg: "Gagal memuat data",
								width: 480
							});
							return 0;
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$("#loading").hide();
						var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
						LogEvent(errorMessage, "/Transaction/SaleReturn/index.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
			}

			var saleDetailsCounter = 0;
			function getSaleDetails() {
				if(saleDetailsCounter == 0 && $("#txtSaleNumber").prop("readonly") == false) 
				{
					saleDetailsCounter = 1;
					var saleNumber = $("#txtSaleNumber").val();
					$.ajax({
						url: "./Transaction/SaleReturn/SaleDetails.php",
						type: "POST",
						data: { SaleNumber : saleNumber },
						dataType: "json",
						success: function(Data) {
							if(Data.FailedFlag == '0') {
								table2.clear().draw();

								if(Data.data.length > 0) {
									for(var i=0;i<Data.data.length;i++) {
										if(i == 0) {
											$("#hdnSaleID").val(Data.data[i][13]);
											$("#txtCustomerName").val(Data.data[i][12]);
										}
										table2.row.add(Data.data[i]);
									}
									$("#btnSaveSaleReturn").attr("tabindex", Data.tabindex);
									$("#btnCancelAddSaleReturn").attr("tabindex", (parseFloat(Data.tabindex) + 1));
									table2.draw();
									tableWidthAdjust();
									setTimeout(function() {
										$("#grid-transaction").find("#select_all_salereturn").first().remove()
									}, 0);

									$(".txtQTY").spinner();

									$("#select_all_salereturn").prop("checked", false);								
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
												
										if(Data.data[i][13] == 0) {
											$("#toggle-branch-" + Data.data[i][0]).toggles().toggleClass('disabled', true);;
										}
									}
									//Calculate();
									setTimeout(function() {
										$("#grid-transaction").DataTable().cell( ':eq(3)' ).focus();
									}, 0);
									$("#txtTransactionDate").focus();
								}
								else {
									var counter = 0;
									Lobibox.alert("error",
									{
										msg: "No. Invoice tidak valid!",
										width: 480,
										beforeClose: function() {
											if(counter == 0) {
												setTimeout(function() {
													$("#txtSaleNumber").focus();
												}, 0);
												counter = 1;
											}
										}
									});
									return 0;
								}
							}
							else {
								var counter = 0;
								Lobibox.alert("error",
								{
									msg: "Gagal memuat data",
									width: 480
								});
								return 0;
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$("#loading").hide();
							var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
							LogEvent(errorMessage, "/Transaction/SaleReturn/index.php");
							Lobibox.alert("error",
							{
								msg: errorMessage,
								width: 480
							});
							return 0;
						}
					});
				}
				else if($("#txtSaleNumber").prop("readonly") == true) {
					$("#txtTransactionDate").focus();
				}
				setTimeout(function() {
					saleDetailsCounter = 0;
				}, 1000);
			}

			function validateQTY2(el) {
				if(parseFloat(el.value) > parseFloat(el.max)) {
					el.value = el.max;
					$(el).notify("Maksimal: " + el.max, { position:"right", className:"warn", autoHideDelay: 2000 });
				}
				else if(parseFloat(el.value) <=0 ) {
					el.value = 1;
					$(el).notify("Minimal: 1", { position:"right", className:"warn", autoHideDelay: 2000 });
				}
				Calculate();
			}
			
			function Calculate() {
				var grandTotal = 0;
				$("input:checkbox[class=chkSaleDetails]:checked").each(function() {
					var qty = parseFloat($(this).closest("tr").find("input[type=number]").val());
					var salePrice = parseFloat($(this).closest("tr").find("label").html().replace(/\,/g, ""));
					var subTotal = qty * salePrice; 
					grandTotal += subTotal;
					$(this).closest("tr").find("td").last().html(returnRupiah(subTotal.toString()));
				});
				$("input:checkbox[class=chkSaleDetails]:not(:checked)").each(function() {
					$(this).closest("tr").find("td").last().html("0");
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
				$("#hdnSaleReturnID").val(0);
				$("#hdnSaleID").val(0);
				$("#txtTransactionDate").datepicker("setDate", new Date());
				$("#txtSaleNumber").val("");
				$("#lblTotal").html("0");
				table2.clear().draw();
				$("#select_all_salereturn").prop("checked", false);
				$("#select_all_salereturn").attr("checked", false);
			}
			
			function transactionList() {
				$("#transactionList-dialog").dialog({
					autoOpen: false,
					position: {
						my : 'top+20%',
						at : 'top'
					},
					open: function() {
						table2.keys.disable();
						table3 = $("#grid-sale").DataTable({
									"keys": true,
									"scrollY": "280px",
									"scrollX": false,
									"scrollCollapse": false,
									"paging": false,
									"searching": true,
									"order": [],
									"columns": [
										{ "width": "30%", "orderable": false, className: "dt-head-center" },
										{ "width": "30%", "orderable": false, className: "dt-head-center" },
										{ "width": "30%", "orderable": false, className: "dt-head-center" },
										{ "width": "10%", "orderable": false, className: "dt-head-center dt-body-right" }
									],
									"ajax": "./Transaction/SaleReturn/TransactionList.php",
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
										$("#grid-sale").DataTable().cell( ':eq(0)' ).focus();
									}
								});
						var counterPickTransaction = 0;
						table3.on( 'key', function (e, datatable, key, cell, originalEvent) {
							if(counterPickTransaction == 0) {
								counterPickTransaction = 1;
								var data = datatable.row( table3.cell({ focused: true }).index().row ).data();
								if(key == 13 && $("#transactionList-dialog").css("display") == "block") {
									$("#txtSaleNumber").val(data[0]);
									getSaleDetails();
									$("#transactionList-dialog").dialog("destroy");
									table3.destroy();
									table2.keys.enable();
								}
								setTimeout(function() { counterPickTransaction = 0; } , 1000);
							}
						});
						
						$('#grid-sale tbody').on('dblclick', 'tr', function () {
							if( $("#transactionList-dialog").css("display") == "block") {
								var data = table3.row(this).data();
								$("#txtSaleNumber").val(data[0]);
								getSaleDetails();
								$("#transactionList-dialog").dialog("destroy");
								table3.destroy();
								table2.keys.enable();
							}
						});
						
						table3.on( 'search.dt', function () {
							setTimeout(function() { $("#grid-sale").DataTable().cell( ':eq(0)' ).focus(); }, 100 );
						});
						
						var counterKeyTransaction = 0;
						$(document).on("keydown", function (evt) {
							if(counterKeyTransaction == 0) {
								counterKeyTransaction = 1;
								if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0) {
									$("#transactionList-dialog").find("input[type='search']").focus();
								}
								else if(evt.keyCode == 27 && $("#transactionList-dialog").css("display") == "block") {
									$("#transactionList-dialog").dialog("destroy");
									table3.destroy();
									table2.keys.enable();
								}
							}
							setTimeout(function() { counterKeyTransaction = 0; } , 1000);
						});
					},
					
					close: function() {
						$(this).dialog("destroy");
						table3.destroy();
						//table.keys.enable();
						table2.keys.enable();
						$("#txtSaleNumber").focus();
					},
					resizable: false,
					height: 420,
					width: 960,
					modal: true /*,
					buttons: [
					{
						text: "Tutup",
						tabindex: 15,
						id: "btnCancelPickTransaction",
						click: function() {
							$(this).dialog("destroy");
							table3.destroy();
							table.keys.enable();
							table2.keys.enable();
							return false;
						}
					}]*/
				}).dialog("open");
			}

			function finish() {
				if($("#hdnSaleID").val() != 0) {
					if($("input:checkbox[class=chkSaleDetails]:checked").length > 0)
					{
						saveConfirm(function(action) {
							if(action == "Ya") {
								$("#loading").show();
								$.ajax({
									url: "./Transaction/SaleReturn/Insert.php",
									type: "POST",
									data: $("#PostForm").serialize(),
									dataType: "json",
									success: function(data) {
										if(data.FailedFlag == '0') {
											$("#loading").hide();
											$("#FormData").dialog("destroy");
											$("#divModal").hide();
											resetForm();
											//table2.destroy();
											var counter = 0;
											Lobibox.alert("success",
											{
												msg: data.Message,
												width: 480,
												delay: 2000
											});
										}
										else {
											$("#loading").hide();
											var counter = 0;
											Lobibox.alert("warning",
											{
												msg: data.Message,
												width: 480,
												delay: false
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
											width: 480
										});
										return 0;
									}
								});
							}
							else {
								return false;
							}
						});
					}
					else {
						var counter = 0;
						Lobibox.alert("error",
						{
							msg: "Minimal pilih 1 data!",
							width: 480,
							beforeClose: function() {
								if(counter == 0) {
									setTimeout(function() {
										$("#select_all_salereturn").focus();
									}, 0);
									counter = 1;
								}
							}
						});
					}
				}
				else {
					var counter = 0;
					Lobibox.alert("error",
					{
						msg: "Silahkan input No. Invoice!",
						width: 480,
						beforeClose: function() {
							if(counter == 0) {
								setTimeout(function() {
									$("#txtSaleNumber").focus();
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
				
				$.fn.dataTable.ext.errMode = function(settings, techNote, message) { 
					$("#loading").hide();
					var errorMessage = "DataTables Error : " + techNote + " (" + message + ")";
					var counterError = 0;
					LogEvent(errorMessage, "/Transaction/SaleReturn/index.php");
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
				var counterSaleReturn = 0;
				
				var counterKey = 0;
				$(document).on("keydown", function (evt) {
					if(((evt.keyCode >= 48 && evt.keyCode <= 57) || (evt.keyCode >= 65 && evt.keyCode <= 90)) && $("input:focus").length == 0 && $("#FormData").css("display") == "none" && $("#delete-confirm").css("display") == "none") {
						$("#grid-data_wrapper").find("input[type='search']").focus();
					}
					setTimeout(function() { counterKey = 0; } , 1000);
				});
				
				openDialog(0, 0);
			});
		</script>
	</body>
</html>